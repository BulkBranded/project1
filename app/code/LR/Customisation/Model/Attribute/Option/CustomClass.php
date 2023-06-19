<?php
/**
 * Copyright © Pivotal. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace LR\Customisation\Model\Attribute\Option;

use MageWorx\OptionBase\Model\Product\Option\AbstractAttribute;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionBase\Api\AttributeInterface;

class CustomClass extends AbstractAttribute implements AttributeInterface
{

    public const KEY_CUSTOM_CLASS = 'custom_class';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::KEY_CUSTOM_CLASS;
    }
}
