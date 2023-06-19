<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace LR\Customisation\Ui\DataProvider\Product\Form\Modifier;

use MageWorx\OptionFeatures\Ui\DataProvider\Product\Form\Modifier\Features as MageWorxFeature;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\DataType\Text;
use MageWorx\OptionFeatures\Helper\Data as Helper;

class Features extends MageWorxFeature
{
    public const KEY_CUSTOM_CLASS = 'custom_class';

    /**
     * Create additional custom options fields
     *
     * @return array
     */
    protected function getOptionFeaturesFieldsConfig()
    {
        $fields[self::KEY_CUSTOM_CLASS] = $this->getCustomClassFieldConfig(70);

        if ($this->helper->isOneTimeEnabled()) {
            $fields[Helper::KEY_ONE_TIME] = $this->getOneTimeConfig(60);
        }

        if ($this->helper->isQtyInputEnabled()) {
            $fields[Helper::KEY_QTY_INPUT] = $this->getQtyInputConfig(65);
        }

        $fields[Helper::KEY_IS_HIDDEN] = $this->getIsHiddenConfig(66);

        return $fields;
    }

    /**
     * Create additional custom options fields config
     *
     * @param string| $sortOrder
     * @return array
     */
    protected function getCustomClassFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Custom Class'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => self::KEY_CUSTOM_CLASS,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                    ],
                ],
            ],
        ];
    }
}
