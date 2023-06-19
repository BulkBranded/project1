<?php
/**
 * Logicrays
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Logicrays
 * @package     Logicrays_EmailQuote
 * @copyright   Copyright (c) Logicrays (https://www.logicrays.com/)
 */
declare(strict_types=1);

namespace LR\Customisation\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use LR\Customisation\Helper\Data;

/**
 * allow passing data and additional functionality to the template file
 */
class Customer implements ArgumentInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Return Object of Data Class.
     *
     * @return object
     */
    public function getLoggedIn()
    {
        return $this->helperData->isCustomerLoggedIn();
    }
}
