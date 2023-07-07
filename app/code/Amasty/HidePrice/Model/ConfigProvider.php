<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Hide Price (Call for Price) for Magento 2
 */

namespace Amasty\HidePrice\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    private const ENABLE = 'general/enabled';
    private const HIDE_WISHLIST = 'information/hide_wishlist';
    private const HIDE_COMPARE = 'information/hide_compare';
    private const HIDE_ADD_TO_CART = 'information/hide_button';

    /**
     * @var string
     */
    protected $pathPrefix = 'amasty_hide_price/';

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::ENABLE, $storeId);
    }

    public function isHideWishlist(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::HIDE_WISHLIST, $storeId);
    }

    public function isHideCompare(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::HIDE_COMPARE, $storeId);
    }

    public function isHideAddToCart(?int $storeId = null): bool
    {
        return $this->isSetFlag(self::HIDE_ADD_TO_CART, $storeId);
    }
}
