<?php
namespace LR\ProductDataApi\Controller\Adminhtml\ProductDataApi;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \LR\ProductDataApi\Model\ProductDataApiFactory
     */
    protected $productDataApiFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \LR\ProductDataApi\Model\ProductDataApiFactory $productDataApiFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \LR\ProductDataApi\Model\ProductDataApiFactory $productDataApiFactory
    ) {
        parent::__construct($context);
        $this->productDataApiFactory = $productDataApiFactory;
    }

    /**
     * Will chec and redirect to router page
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('productdataapi/productdataapi/edit');
            return;
        }
        try {
            $rowData = $this->productDataApiFactory->create();
            $rowData->setData($data);
            $rowData->save();
            $this->messageManager->addSuccess(__('Data has been successfully saved.'));

            if ($this->getRequest()->getParam('back')) {
                return $this->_redirect('*/*/edit', ['entity_id' => $rowData->getEntityId(), '_current' => true]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        
        $this->_redirect('productdataapi/productdataapi/index');
    }
}
