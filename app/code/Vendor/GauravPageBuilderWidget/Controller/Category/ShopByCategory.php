<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Controller\Category;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;

class ShopByCategory extends Action
{
    protected $categoryRepository;
    protected $productCollectionFactory;
    protected $resultJsonFactory;
    protected $storeManager;

    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        ProductCollectionFactory $productCollectionFactory,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $categoryId = (int) $this->getRequest()->getParam('category_id');

        if (!$categoryId) {
            return $result->setData([
                'error' => true,
                'message' => 'Category ID is missing'
            ]);
        }

        try {
            $storeId = $this->storeManager->getStore()->getId();
            $category = $this->categoryRepository->get($categoryId, $storeId);

            $categoryName = $category->getName();
            $categoryImage = $category->getImageUrl();
            $categoryUrl = $category->getUrl();
            $productCount = $category->getProductCount();

            /**
             * ✅ Get child categories (subcategories)
             */
            $subcategories = [];
            $childrenCategories = $category->getChildrenCategories();

            if ($childrenCategories && $childrenCategories->count()) {
                foreach ($childrenCategories as $childCategory) {
                    // Only include active & visible categories
                    if ($childCategory->getIsActive()) {
                        $subcategories[] = [
                            'id'   => $childCategory->getId(),
                            'name' => $childCategory->getName(),
                            'url'  => $childCategory->getUrl(),
                        ];
                    }
                }
            }

            return $result->setData([
                'success'        => true,
                'category_id'    => $categoryId,
                'category_name'  => $categoryName,
                'category_image' => $categoryImage,
                'category_url'   => $categoryUrl,
                'product_count'  => $productCount,
                'subcategories'  => $subcategories // ✅ Added here
            ]);

        } catch (\Exception $e) {
            return $result->setData([
                'error'   => true,
                'message' => $e->getMessage()
            ]);
        }
    }
}
