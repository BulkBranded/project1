<?php
namespace LR\ProductDataApi\Controller\Adminhtml\ProductDataApi;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use LR\ProductDataApi\Model\ProductDataApiFactory;
use LR\ProductDataApi\Model\ResourceModel\ProductDataApi\CollectionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class MassStatus extends Action implements HttpPostActionInterface
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
     * Created construct object
     *
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
        parent::__construct($context);
        $this->filter = $filter;
        $this->productDataApiFactory = $productDataApiFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Will load collection and process it
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            foreach ($collection as $item) {
                $model = $this->productDataApiFactory->create()->load($item['entity_id']);
                $model->setData('status', $this->getRequest()->getParam('status'));
                $model->save();
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', $collection->getSize()));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
