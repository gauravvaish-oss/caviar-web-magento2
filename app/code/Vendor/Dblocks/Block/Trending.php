<?php
namespace Vendor\Dblocks\Block;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\View\Element\Template;

class Trending extends Template
{
    protected $productCollectionFactory;
    protected $imageBuilder;
    protected $priceRender;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        ImageBuilder $imageBuilder,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageBuilder = $imageBuilder;
        parent::__construct($context, $data);
    }

    /** --- Product Collections --- **/
    public function getNewProducts($limit = 6)
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('news_from_date', ['or'=>[
                0 => ['date' => true, 'to' => $todayEnd],
                1 => ['is' => new \Zend_Db_Expr('null')],
            ]], 'left')
            ->addAttributeToFilter('news_to_date', ['or'=>[
                0 => ['date' => true, 'from' => $todayStart],
                1 => ['is' => new \Zend_Db_Expr('null')],
            ]], 'left')
            ->addAttributeToSort('entity_id','desc')
            ->setPageSize($limit);

        return $collection;
    }

    public function getFeaturedProducts($limit = 6)
    {
        // Needs custom product attribute "is_featured"
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('is_featured', 1)
            ->setPageSize($limit);

        return $collection;
    }

    public function getBestSellerProducts($limit = 6)
    {
        // Basic collection (replace with sales join for real top sellers)
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->setPageSize($limit);

        return $collection;
    }

    /** --- Image Helper --- **/
    public function getImage($product, $imageId = 'category_page_grid')
    {
        return $this->imageBuilder->setProduct($product)
                                  ->setImageId($imageId)
                                  ->create();
    }

    /** --- Price Render --- **/
    public function getProductPriceHtml($product)
    {
        return $this->getLayout()->createBlock(\Magento\Framework\Pricing\Render::class)
            ->setData('price_render', 'product.price.render.default')
            ->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                ['zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST]
            );
    }
}
