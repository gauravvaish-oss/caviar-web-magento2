<?php
namespace Vendor\Dblocks\Controller\Category;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Review\Model\Review\SummaryFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Store\Model\StoreManagerInterface;

class Products extends Action
{
    protected $resultJsonFactory;
    protected $productCollectionFactory;
    protected $imageHelper;
    protected $reviewSummaryFactory;
    protected $storeManager;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ProductCollectionFactory $productCollectionFactory,
        ImageHelper $imageHelper,
        SummaryFactory $reviewSummaryFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->imageHelper = $imageHelper;
        $this->reviewSummaryFactory = $reviewSummaryFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryId = (int) $this->getRequest()->getParam('category_id', 2);
        $limit = (int) $this->getRequest()->getParam('limit', 20);
        $storeId = (int) $this->storeManager->getStore()->getId();

        // Load product collection
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addCategoriesFilter(['in' => $categoryId])
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->addAttributeToFilter('visibility', [2,3,4])
            ->setPageSize($limit)
            ->setOrder('created_at', 'DESC');

        $productIds = $collection->getAllIds();
        // Load review summaries in bulk
        $summaryCollection = $this->reviewSummaryFactory->create()->getCollection()
            ->addFieldToFilter('entity_pk_value', ['in' => $productIds])
            ->addStoreFilter($storeId);

        $summaryData = [];
        foreach ($summaryCollection as $summary) {
            $summaryData[$summary->getEntityPkValue()] = [
                'rating_summary' => $summary->getRatingSummary(),
                'reviews_count'  => $summary->getReviewsCount()
            ];
        }

        // Build product data
        $products = [];
        foreach ($collection as $product) {

            $pid = $product->getId();
            $ratingSummary = $summaryData[$pid]['rating_summary'] ?? 0;
            $reviewCount = $summaryData[$pid]['reviews_count'] ?? 0;

            $products[] = [
                'id' => $pid,
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'image' => $this->getProductImageUrl($product),
                'average_rating' => round($ratingSummary / 20, 1), // convert 0-100% to 5-star
                'review_count' => $reviewCount
            ];
        }

        return $this->resultJsonFactory->create()->setData($products);
    }

    /**
     * Get product image URL with proper fallback
     */
    protected function getProductImageUrl($product, $imageId = 'category_page_grid')
    {
        try {
            $imageUrl = $this->imageHelper->init($product, $imageId)->getUrl();

            // fallback for 'no_selection' or empty images
            if (!$imageUrl || strpos($imageUrl, 'no_selection') !== false) {
                $imageUrl = $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA])
                    . 'catalog/product/placeholder/small_image.jpg';
            }

            return $imageUrl;
        } catch (\Exception $e) {
            // fallback on error
            return $this->_url->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA])
                . 'catalog/product/placeholder/small_image.jpg';
        }
    }
}
