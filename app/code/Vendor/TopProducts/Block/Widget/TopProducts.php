<?php
namespace Vendor\TopProducts\Block\Widget;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class TopProducts extends Template implements BlockInterface
{
    protected $_template = "widget/top_products.phtml";

    protected $categoryRepository;
    protected $productCollectionFactory;

    public function __construct(
        Template\Context $context,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getCategoryIds()
    {
        $ids = $this->getData('category_ids');
        return $ids ? array_map('trim', explode(',', $ids)) : [];
    }

    public function getItemsCount()
    {
        return (int) ($this->getData('items_count') ?: 5);
    }

    public function getCategory($categoryId)
    {
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getProductsByCategory($categoryId)
    {
        $limit = $this->getItemsCount();

        $collection = $this->productCollectionFactory->create();
        $collection->addCategoriesFilter(['in' => $categoryId])
            ->addAttributeToSelect('entity_id')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', [2, 3, 4])
            ->setPageSize($limit);

        return $collection;
    }

    public function getCategoryImage($category)
    {
        if ($category && $category->getImageUrl()) {
            return $category->getImageUrl();
        }
        return $this->getViewFileUrl('Vendor_TopProducts::images/placeholder.png');
    }
}
