<?php
namespace Vendor\GauravPageBuilderWidget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductCountdown extends Template
{
    protected $productRepository;

    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    public function getProductBySku($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
            return $product;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    public function getProductData($sku)
    {
        $product = $this->getProductBySku($sku);
        if (!$product) return null;

        return [
            'name' => $product->getName(),
            'image' => $this->getImageUrl($product),
            'current_price' => $product->getFinalPrice(),
            'original_price' => $product->getPrice(),
            'rating' => $this->getRatingStars($product)
        ];
    }

    public function getImageUrl($product)
    {
        return $this->getUrl('pub/media/catalog/product') . $product->getData('image');
    }

    public function getRatingStars($product)
    {
        // You can calculate based on Magento review module
        $rating = 4; // Example: 4 stars
        $empty = 5 - $rating;
        return ['filled' => $rating, 'empty' => $empty];
    }
}
