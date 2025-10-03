<?php
namespace Vendor\GauravPageBuilderWidget\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Search extends Action
{
    protected $productCollectionFactory;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        JsonFactory $jsonFactory
    ){
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $query = $this->getRequest()->getParam('q');
        $categoryId = $this->getRequest()->getParam('category');

        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['name','url_key'])
            ->addAttributeToFilter('name',['like'=>'%'.$query.'%'])
            ->setPageSize(10);

        if ($categoryId && $categoryId != 'all') {
            $collection->addCategoriesFilter(['in' => $categoryId]);
        }

        $result = [];
        foreach($collection as $product){
            $result[] = [
                'name' => $product->getName(),
                'url'  => $product->getProductUrl()
            ];
        }

        return $this->jsonFactory->create()->setData($result);
    }
}
