<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Request a Quote Base for Magento 2
 */

namespace Amasty\RequestQuote\Api\Data;

interface QuoteItemInterface
{
    public const ADMIN_NOTE_KEY = 'admin_note';
    public const CUSTOMER_NOTE_KEY = 'customer_note';
    public const REQUESTED_PRICE = 'requested_price';
    public const CUSTOM_PRICE = 'requested_custom_price';
    public const HIDE_ORIGINAL_PRICE = 'hide_original_price';
}
