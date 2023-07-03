<?php

declare(strict_types=1);

namespace LR\ProductDataApi\Ui\Component\Form\ProductDataApi\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinue implements ButtonProviderInterface
{
    /**
     * Get Buttons data
     *
     * @return void
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 30,
        ];
    }
}
