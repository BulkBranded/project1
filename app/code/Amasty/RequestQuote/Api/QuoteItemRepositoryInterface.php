<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Request a Quote Base for Magento 2
 */

namespace Amasty\RequestQuote\Api;

interface QuoteItemRepositoryInterface
{
    /**
     * @param int $quoteItemId
     * @param string $note
     * @return bool
     */
    public function addCustomerNote($quoteItemId, $note);

    /**
     * @param int $quoteItemId
     * @param string $note
     * @return bool
     */
    public function addAdminNote($quoteItemId, $note);

    /**
     * @param int $cartId
     * @param int $itemId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function deleteById(int $cartId, int $itemId): bool;
}
