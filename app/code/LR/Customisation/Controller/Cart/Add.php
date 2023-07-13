<?php
namespace LR\Customisation\Controller\Cart;

use Amasty\RequestQuote\Controller\Cart\Add as QuoteAdd;
use Magento\Framework\Controller\ResultFactory;

class Add extends QuoteAdd
{
    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Amasty\RequestQuote\Api\QuoteRepositoryInterface
     */
    protected $amastyQuoteRepo;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Amasty\RequestQuote\Model\Quote\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Amasty\RequestQuote\Model\Cart $cart
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\EncoderInterface $encoder
     * @param \Amasty\RequestQuote\Helper\Cart $cartHelper
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Amasty\RequestQuote\Model\Email\Sender $emailSender
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Amasty\RequestQuote\Helper\Data $configHelper
     * @param \Amasty\RequestQuote\Model\Email\AdminNotification $adminNotification
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Model\AuthenticationInterface $authentication
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager
     * @param \Amasty\RequestQuote\Model\HidePrice\Provider $hidePriceProvider
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Customer\Model\CustomerExtractor $customerExtractor
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Amasty\RequestQuote\Model\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Amasty\RequestQuote\Model\UrlResolver $urlResolver
     * @param \Magento\Framework\Filter\LocalizedToNormalized|null $localizedToNormalized
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Amasty\RequestQuote\Api\QuoteRepositoryInterface $amastyQuoteRepo
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\RequestQuote\Model\Quote\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Amasty\RequestQuote\Model\Cart $cart,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Amasty\RequestQuote\Helper\Cart $cartHelper,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Amasty\RequestQuote\Model\Email\Sender $emailSender,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Amasty\RequestQuote\Helper\Data $configHelper,
        \Amasty\RequestQuote\Model\Email\AdminNotification $adminNotification,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\AuthenticationInterface $authentication,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager,
        \Amasty\RequestQuote\Model\HidePrice\Provider $hidePriceProvider,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Customer\Model\CustomerExtractor $customerExtractor,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\RequestQuote\Model\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Amasty\RequestQuote\Model\UrlResolver $urlResolver,
        \Magento\Framework\Filter\LocalizedToNormalized $localizedToNormalized = null,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Amasty\RequestQuote\Api\QuoteRepositoryInterface $amastyQuoteRepo
    ) {
        $this->redirect = $context->getRedirect();
        $this->localizedToNormalized = $localizedToNormalized
            ?? ObjectManager::getInstance()->get(LocalizedToNormalized::class);
        $this->resultFactory = $resultFactory;
        $this->urlHelper = $urlHelper;
        $this->amastyQuoteRepo = $amastyQuoteRepo;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $localeResolver,
            $resultPageFactory,
            $encoder,
            $cartHelper,
            $dataObjectFactory,
            $emailSender,
            $customerSessionFactory,
            $priceCurrency,
            $configHelper,
            $adminNotification,
            $accountManagement,
            $customerUrl,
            $authentication,
            $cookieMetadataFactory,
            $cookieManager,
            $hidePriceProvider,
            $timezone,
            $customerExtractor,
            $logger,
            $registry,
            $dateTime,
            $urlResolver,
            $this->localizedToNormalized
        );
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultJson->setData(["suceess" => false]);
            return $resultJson;
        }

        $params = $this->getRequest()->getParams();

        try {
            if (isset($params['qty'])) {
                $params['qty'] = $this->getLocateFilter()->filter($params['qty']);
            }

            $productId = (int)$this->getRequest()->getParam('product', false);
            $related = $this->getRequest()->getParam('related_product');

            try {
                $product = $this->cart->getProductById($productId);
            } catch (NoSuchEntityException $e) {
                return $this->goBack();
            }

            $this->cart->addProduct($productId, $params);

            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->save();

            if (!$this->getCheckoutSession()->getNoCartRedirect(true)) {
                $quote = $this->submitAction();

                $pdfUrl = $this->_url->getUrl("amasty_quote/quote/pdf", ['quote_id' => $quote->getId()]);
                $quoteUrl = $this->_url->getUrl("request_quote/account/view", ['quote_id' => $quote->getId()]);
                $quote = $this->amastyQuoteRepo->get($quote->getId());
                $resultJson->setData(
                    [
                        "suceess" => true,
                        "pdfUrl" => $pdfUrl,
                        "uenc" => $this->urlHelper->getEncodedUrl($quoteUrl),
                        "quoteUrl" => $quoteUrl,
                        "increment_id" => $quote->getIncrementId()
                    ]
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->getCheckoutSession()->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->getEscaper()->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->getEscaper()->escapeHtml($message)
                    );
                }
            }

            $url = $this->getCheckoutSession()->getRedirectUrl(true);

            if (!$url) {
                $cartUrl = $this->cartHelper->getCartUrl();
                $url = $this->_redirect->getRedirectUrl($cartUrl);
            }

            $resultJson->setData(["suceess" => false]);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t add this item to your quote cart right now.'));
            return $this->goBack();
        }

        return $resultJson;
    }

    /**
     * @inheritDoc
     */
    protected function submitAction()
    {
        $quote = $this->checkoutSession->getQuote();

        $this->_eventManager->dispatch('amasty_request_quote_submit_before', ['quote' => $quote]);

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        foreach ($quote->getAllItems() as $quoteItem) {
            $priceOption = $this->dataObjectFactory->create(
                []
            )->setCode(
                'amasty_quote_price'
            )->setValue(
                $quoteItem->getPrice()
            )->setProduct(
                $quoteItem->getProduct()
            );
            $quoteItem->addOption($priceOption)->saveItemOptions();
        }

        $quote->setSubmitedDate($this->dateTime->gmtDate());
        $quote->setStatus(\Amasty\RequestQuote\Model\Source\Status::PENDING);
        $quote->save();
        $quote->setTotalsCollectedFlag(false)->collectTotals();
        $this->registry->register(\Amasty\RequestQuote\Model\RegistryConstants::AMASTY_QUOTE, $quote);
        // $this->notifyCustomer();
        $this->notifyAdmin($quote->getId());
        $this->checkoutSession->setLastQuoteId($this->checkoutSession->getQuoteId());
        $this->checkoutSession->setQuoteId(null);

        $this->_eventManager->dispatch('amasty_request_quote_submit_after', ['quote' => $quote]);

        return $quote;
    }

    private function notifyCustomer(): void
    {
        $quote = $this->checkoutSession->getQuote();
        $quote['created_date_formatted'] = $quote->getCreatedAtFormatted(\IntlDateFormatter::MEDIUM);
        $quote['submitted_date_formatted'] = $quote->getSubmitedDateFormatted(\IntlDateFormatter::MEDIUM);
        $this->emailSender->sendEmail(
            \Amasty\RequestQuote\Helper\Data::CONFIG_PATH_CUSTOMER_SUBMIT_EMAIL,
            $this->getCustomerSession()->getCustomer()->getEmail(),
            [
                'viewUrl' => $this->urlResolver->getViewUrl((int)$this->checkoutSession->getQuoteId()),
                'quote' => $quote,
                'remarks' => $this->cartHelper->retrieveCustomerNote(
                    $this->checkoutSession->getQuote()->getRemarks()
                )
            ],
            \Amasty\RequestQuote\Model\Source\CustomerNotificationTemplates::SUBMITTED_QUOTE
        );
    }

    /**
     * @param int $quoteId
     */
    private function notifyAdmin($quoteId)
    {
        if ($this->getConfigHelper()->isAdminNotificationsInstantly()) {
            $this->getAdminNotification()->sendNotification([$quoteId]);
        }
    }
}
