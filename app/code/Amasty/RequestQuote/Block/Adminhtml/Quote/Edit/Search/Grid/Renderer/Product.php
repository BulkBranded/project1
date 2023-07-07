<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Request a Quote Base for Magento 2
 */

namespace Amasty\RequestQuote\Block\Adminhtml\Quote\Edit\Search\Grid\Renderer;

class Product extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $rendered = parent::render($row);
        if($isConfigurable = $row->canConfigure()) {
            $style = $isConfigurable ? '' : 'disabled';
            $prodAttributes = $isConfigurable ? sprintf(
                'list_type = "product_to_add" product_id = %s',
                $row->getId()
            ) : 'disabled="disabled"';
            return sprintf(
                    '<a href="javascript:void(0)" class="action-configure %s" %s>%s</a>&nbsp;',
                    $style,
                    $prodAttributes,
                    __('Configure')
                ) . $rendered;
        }
        return $rendered;
    }
}
