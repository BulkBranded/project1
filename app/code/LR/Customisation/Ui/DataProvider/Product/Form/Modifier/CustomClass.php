<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace LR\Customisation\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use MageWorx\OptionBase\Ui\DataProvider\Product\Form\Modifier\ModifierInterface;

class CustomClass extends AbstractModifier implements ModifierInterface
{
    public const FIELD_CUSTOM_CLASS_NAME = 'custom_class';

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * Get sort order of modifier to load modifiers in the right order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return 35;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $this->addLRFields();

        return $this->meta;
    }

    /**
     * Create additional custom options fields
     *
     * @return array
     */
    protected function addLRFields()
    {
        $groupCustomOptionsName = CustomOptions::GROUP_CUSTOM_OPTIONS_NAME;
        $optionContainerName       = CustomOptions::CONTAINER_OPTION;

        // Add fields to the values
        $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
        [$optionContainerName]['children']['values']['children']['record']['children'] = array_replace_recursive(
            $this->meta[$groupCustomOptionsName]['children']['options']['children']['record']['children']
            [$optionContainerName]['children']['values']['children']['record']['children'],
            $this->getCustomClassFields()
        );
    }

    /**
     * Create additional custom options fields
     *
     * @return array
     */
    protected function getCustomClassFields()
    {
        $fields = [
            'custom_class' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Custom Class'),
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'formElement' => \Magento\Ui\Component\Form\Element\Input::NAME,
                            'dataScope' => static::FIELD_CUSTOM_CLASS_NAME,
                            'dataType' => \Magento\Ui\Component\Form\Element\DataType\Text::NAME,
                            'sortOrder' => 140,
                        ]
                    ],
                ],
            ]
        ];

        return $fields;
    }

    /**
     * Check is current modifier for the product only
     *
     * @return bool
     */
    public function isProductScopeOnly()
    {
        return false;
    }
}
