<?php
namespace Vendor\Dblocks\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Image as ImageHelper;

class FeaturedProducts extends Template
{
    protected $productCollectionFactory;
    protected $imageHelper;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public function getFeaturedProducts()
    {
        $limit = $this->getData('limit') ?: 2; // default 5

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'small_image'])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', ['neq' => 1]) // exclude Not Visible Individually
            ->addAttributeToFilter('is_featured', 1) // only featured
            ->setPageSize($limit);

        return $collection;
    }

    public function getImageUrl($product, $imageType = 'product_small_image')
    {
        return $this->imageHelper->init($product, $imageType)->getUrl();
    }
}
