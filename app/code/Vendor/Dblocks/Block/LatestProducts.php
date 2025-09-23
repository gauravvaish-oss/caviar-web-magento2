<?php
namespace Vendor\Dblocks\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class LatestProducts extends Template
{
    protected $productCollectionFactory;
    protected $imageHelper;
    protected $productRepository;
    protected $priceCurrency;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->productRepository = $productRepository;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    public function getLatestProducts()
    {
        $limit = $this->getData('limit') ?: 3; // default limit
        $categoryId = $this->getData('category_id'); // optional param

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'price', 'small_image'])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', ['neq' => 1]) // exclude Not Visible Individually
            ->setOrder('created_at', 'desc')
            ->setPageSize($limit);

        if ($categoryId) {
            $collection->joinField(
                'category_id',
                'catalog_category_product',
                'category_id',
                'product_id = entity_id',
                null,
                'left'
            )->addAttributeToFilter('category_id', ['eq' => $categoryId]);
        }

        return $collection;
    }

    public function getImageUrl($product, $imageType = 'product_small_image')
    {
        return $this->imageHelper->init($product, $imageType)->getUrl();
    }

    public function formatPrice($price)
    {
        return $this->priceCurrency->convertAndFormat($price);
    }
}
