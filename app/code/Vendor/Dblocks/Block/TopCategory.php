<?php
namespace Vendor\Dblocks\Block;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;

class TopCategory extends Template
{
    protected $categoryCollectionFactory;
    protected $storeManager;

    public function __construct(
        Template\Context $context,
        CollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getCategories()
    {
        $storeId = $this->storeManager->getStore()->getId();

        return $this->categoryCollectionFactory->create()
            ->addAttributeToSelect(['name', 'url_key', 'image', 'is_active'])
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('level', 2) // top-level
            ->setStoreId($storeId);
    }

    public function getChildren($category)
    {
        return $category->getChildrenCategories()
            ->addAttributeToSelect(['name', 'url_key', 'image'])
            ->addAttributeToFilter('is_active', 1);
    }

    public function getRootCategoryId()
    {
        return $this->storeManager->getStore()->getRootCategoryId();
    }

    public function getStoreUrl($route = '', $params = [])
    {
        return $this->getUrl($route, $params);
    }
}
