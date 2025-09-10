<?php
namespace Vendor\Module\Block;

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
        return $this->getData('parent_category_id'); // pass via block or CMS directive
    }

    public function getChildCategories()
    {
        $parentId = $this->getParentCategoryId();
        $parentCategory = $this->categoryRepository->get($parentId);
        return $parentCategory->getChildrenCategories();
    }

    public function getProductsByCategory($categoryId, $limit = 6)
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'small_image'])
            ->addCategoriesFilter(['in' => $categoryId])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', [2, 3, 4]) // catalog, search
            ->setPageSize($limit);

        return $collection;
    }
}
