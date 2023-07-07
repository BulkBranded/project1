<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Request a Quote Base for Magento 2
 */

namespace Amasty\RequestQuote\Api;

/**
 * Interface RemarkServiceInterface
 */
interface RemarkServiceInterface
{
    /**
     * @param string $remark
     *
     * @return void
     */
    public function save($remark);
}
