<?php
/**
 * Copyright © Pivotal. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace LR\Customisation\Model\Attribute\OptionValue;

use MageWorx\OptionBase\Model\Product\Option\AbstractAttribute;

class CustomClass extends AbstractAttribute
{

    public const KEY_CUSTOM_CLASS = 'custom_class';

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::KEY_CUSTOM_CLASS;
    }

    /**
     * @inheritdoc
     */
    public function prepareDataForFrontend($object)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function importTemplateMageOne($data)
    {
        return 0;
    }
}
