<?php
namespace Vendor\Dblocks\Controller\Trending;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Vendor\Dblocks\Block\Trending;

class NewProducts extends Action
{
    protected $resultJsonFactory;
    protected $trendingBlock;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Trending $trendingBlock
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->trendingBlock = $trendingBlock;
        parent::__construct($context);
    }

    public function execute()
    {
        $html = $this->trendingBlock->setTemplate("Vendor_Dblocks::product/items.phtml")
                                    ->setData('collection', $this->trendingBlock->getFeaturedProducts())
                                    ->toHtml();

        return $this->resultJsonFactory->create()->setData(['html' => $html]);
    }
}
