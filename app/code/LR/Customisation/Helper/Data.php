<?php
namespace LR\Customisation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\SessionFactory;

class Data extends AbstractHelper
{
    /**
     * @var Magento\Customer\Model\SessionFactory
     */
    protected $customerSessionFactory;

    /**
     *  Helper Data object declared
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param  \Magento\Customer\Model\SessionFactory $customerSessionFactory
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSessionFactory
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
        parent::__construct($context);
    }

    /**
     * Will check is customer LoggedIn or not
     *
     * @return string
     */
    public function isCustomerLoggedIn()
    {
        $customerSession = $this->customerSessionFactory->create();
        return $customerSession->isLoggedIn();
    }
}
