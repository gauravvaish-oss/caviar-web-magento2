<?php
namespace Vendor\Dblocks\Block;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\View\Element\Template;

class CategoryTabs extends Template
{
    protected $categoryRepository;
    protected $productCollectionFactory;

    public function __construct(
        Template\Context $context,
        CategoryRepository $categoryRepository,
        ProductCollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getParentCategoryId()
    {
        return (int) $this->getData('parent_category_id') ?: 2; // fallback to 2 (Default category) if not set
    }

    /**
     * Return child categories of given parent
     *
     * @return \Magento\Catalog\Model\Category[]
     */
    public function getChildCategories()
    {
        $parentId = $this->getParentCategoryId();
        try {
            $parentCategory = $this->categoryRepository->get($parentId);
            // getChildrenCategories returns collection of category objects (if indexing and data present)
            if (method_exists($parentCategory, 'getChildrenCategories')) {
                return $parentCategory->getChildrenCategories();
            }
            // fallback: load children ids and return simple array of category objects
            $children = [];
            $childrenIds = $parentCategory->getChildren();
            if ($childrenIds) {
                foreach (explode(',', $childrenIds) as $id) {
                    $id = trim($id);
                    if ($id) {
                        $children[] = $this->categoryRepository->get($id);
                    }
                }
            }
            return $children;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Return product collection for category
     *
     * @param int $categoryId
     * @param int $limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductsByCategory($categoryId, $limit = 8)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addCategoriesFilter(['in' => (int)$categoryId])
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', [2,3,4]) // catalog, search, catalog + search
            ->setPageSize((int)$limit);
        return $collection;
    }

    /**
     * Helper to build product image url (small_image)
     */
    public function getProductImageUrl($product)
    {
        $mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
        $img = $product->getSmallImage();
        if (!$img || $img === 'no_selection') {
            return $this->getViewFileUrl('Vendor_Dblocks::images/default.png');
        }
        return rtrim($mediaUrl, '/') . '/catalog/product' . $img;
    }

    /**
     * Get AJAX URL for products
     */
    public function getProductsAjaxUrl()
    {
        return $this->getUrl('dblocks/category/products');
    }
}
