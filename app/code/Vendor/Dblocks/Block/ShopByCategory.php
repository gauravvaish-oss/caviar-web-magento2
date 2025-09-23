<?php
namespace Vendor\Dblocks\Block;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\View\Element\Template;

class ShopByCategory extends Template
{
    protected $_template = "Vendor_Dblocks::category_slider.phtml";

    protected $categoryCollectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getCategories()
    {
        $ids = $this->getData('category_ids');
        if (!$ids) {
            return [];
        }

        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect(['name', 'image', 'url_key'])
            ->addFieldToFilter('entity_id', ['in' => explode(',', $ids)])
            ->addIsActiveFilter();

        return $collection;
    }

    public function getSubCategories(\Magento\Catalog\Model\Category $category)
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect(['name', 'url_key'])
            ->addFieldToFilter('parent_id', $category->getId())
            ->addIsActiveFilter();

        return $collection;
    }
}
