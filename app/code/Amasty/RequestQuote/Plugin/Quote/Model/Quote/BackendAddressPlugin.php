<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Request a Quote Base for Magento 2
 */

namespace Amasty\RequestQuote\Plugin\Quote\Model\Quote;

use Amasty\RequestQuote\Api\QuoteRepositoryInterface;
use Amasty\RequestQuote\Model\Quote\Backend\Edit\QuoteManagement;
use Amasty\RequestQuote\Model\Quote\Backend\Session as BackendSession;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;

class BackendAddressPlugin
{
    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var BackendSession
     */
    private $backendSession;

    public function __construct(
        BackendSession $backendSession,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->backendSession = $backendSession;
    }

    /**
     * Saving same_as_billing flag with setData method
     *
     * @param Address $subject
     * @param callable $proceed
     * @param int $flag
     * @return Address
     */
    public function aroundSetSameAsBilling(Address $subject, callable $proceed, $flag): Address
    {
        if (!$this->isAmastyQuote($subject->getQuote())
            || $subject->getAddressType() !== Address::ADDRESS_TYPE_SHIPPING
        ) {
            $proceed($flag);
        }

        return $subject;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    private function isAmastyQuote(Quote $quote): bool
    {
        $quoteId = (int) $quote->getId();

        return $quote->getData(QuoteManagement::AMASTY_QUOTE_FLAG)
            || $this->getBackendSession()->getParentId($quoteId)
            || $this->getQuoteRepository()->isAmastyQuote($quoteId);
    }

    /**
     * @return QuoteRepositoryInterface
     */
    public function getQuoteRepository(): QuoteRepositoryInterface
    {
        return $this->quoteRepository;
    }

    /**
     * @return BackendSession
     */
    public function getBackendSession(): BackendSession
    {
        return $this->backendSession;
    }
}
