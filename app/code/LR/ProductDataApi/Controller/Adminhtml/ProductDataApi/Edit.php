<?php
namespace LR\ProductDataApi\Controller\Adminhtml\ProductDataApi;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
   /**
    * @var \Magento\Framework\Registry
    */
    private $coreRegistry;

    /**
     * Created a construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \LR\ProductDataApi\Model\ProductDataApiFactory $productDataApiFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \LR\ProductDataApi\Model\ProductDataApiFactory $productDataApiFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->productDataApiFactory = $productDataApiFactory;
    }

    /**
     * Will load productdataapi data and process it
     *
     * @return object
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('entity_id');
        $productDataApi = $this->productDataApiFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $productDataApi = $productDataApi->load($rowId);
            $rowTitle = $productDataApi->getTitle();
            if (!$productDataApi->getEntityId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                $this->_redirect('productdataapi/productdataapi/index');
                return;
            }
        }

        $this->coreRegistry->register('productdataapi_data', $productDataApi);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Row Data ').$rowTitle : __('Add Row Data');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
