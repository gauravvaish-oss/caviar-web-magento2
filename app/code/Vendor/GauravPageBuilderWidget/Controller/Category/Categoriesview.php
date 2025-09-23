<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Controller\Category;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;

class Categoriesview extends Action
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
            return $result->setData(['error' => true, 'message' => 'Category ID is missing']);
        }

        try {
            $storeId = $this->storeManager->getStore()->getId();
            $category = $this->categoryRepository->get($categoryId, $storeId);

            $categoryName = $category->getName();
            $categoryImage = $category->getImageUrl(); // full image URL
            $categoryUrl   = $category->getUrl();
            $productCount  = $category->getProductCount();

            // $productCollection = $this->productCollectionFactory->create();
            // $productCollection->addAttributeToSelect(['name', 'price', 'small_image'])
            //                   ->addCategoryFilter($category)
            //                   ->setPageSize(6)
            //                   ->setCurPage(1);

            // $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            // $products = [];
            // foreach ($productCollection as $product) {
            //     $imagePath = $product->getSmallImage();

            //     $products[] = [
            //         'id'    => $product->getId(),
            //         'name'  => $product->getName(),
            //         'price' => $product->getPrice(),
            //         'url'   => $product->getProductUrl(),
            //         'image' => $imagePath && $imagePath !== 'no_selection'
            //             ? $mediaUrl . 'catalog/product' . $imagePath
            //             : $mediaUrl . 'catalog/product/placeholder/image.jpg'
            //     ];
            // }

            return $result->setData([
                'success'        => true,
                'category_id'    => $categoryId,
                'category_name'  => $categoryName,
                'category_image' => $categoryImage,
                'category_url'   => $categoryUrl,
                'product_count'  => $productCount
            ]);

        } catch (\Exception $e) {
            return $result->setData([
                'error'   => true,
                'message' => $e->getMessage()
            ]);
        }
    }
}
