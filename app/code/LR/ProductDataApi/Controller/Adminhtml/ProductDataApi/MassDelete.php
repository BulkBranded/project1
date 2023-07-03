<?php
namespace LR\ProductDataApi\Controller\Adminhtml\ProductDataApi;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use LR\ProductDataApi\Model\ProductDataApiFactory;
use LR\ProductDataApi\Model\ResourceModel\ProductDataApi\CollectionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class MassDelete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Massactions filter.​_
     * @var Filter
     */
    protected $filter;

    /**
     * @var ProductDataApiFactory
     */
    protected $productDataApiFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param ProductDataApiFactory $productDataApiFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ProductDataApiFactory $productDataApiFactory,
        CollectionFactory $collectionFactory
    ) {

        $this->filter = $filter;
        $this->productDataApiFactory = $productDataApiFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

     /**
      * Will load a collection and process it
      *
      * @return object
      */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = $collection->getSize();
            foreach ($collection as $record) {
                $deleteItem = $this->productDataApiFactory->create()->load($record->getEntityId());
                $deleteItem->delete();
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }

        return $this->resultFactory
        ->create(ResultFactory::TYPE_REDIRECT)->setPath('productdataapi/productdataapi/index');
    }
}
