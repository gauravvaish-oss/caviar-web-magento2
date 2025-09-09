<?php
namespace Vendor\Dblocks\Block;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Image as ImageHelper;
class ProductView extends Template
{
    protected $productRepository;
    protected $imageHelper;

    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get product by ID
     */
    public function getProduct()
    {
        $productId = $this->getData('product_id');
        if ($productId) {
            try {
                return $this->productRepository->getById($productId);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Get countdown end date
     */
    public function getCountdownDate()
    {
        return $this->getData('countdown'); // e.g. "2025-12-31 23:59:59"
    }


    public function getImageUrl($product, $imageType = 'product_base_image')
    {
        return $this->imageHelper->init($product, $imageType)->getUrl();
    }
}
