<?php
namespace LR\ProductDataApi\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \LR\ProductDataApi\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \LR\ProductDataApi\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \LR\ProductDataApi\Helper\Data $helperData
    ) {
        $this->_pageFactory = $pageFactory;
        $this->helperData = $helperData;
        return parent::__construct($context);
    }
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->helperData->createProductAndTemplate();
        //$this->helperData->clearProductData();
        //$this->helperData->fetchProductData();
        //$this->helperData->fetchProductSkus();
        //$this->helperData->initCurlRequest();
        return $this->_pageFactory->create();
    }
}
