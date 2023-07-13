<?php
namespace LR\Customisation\Block\Account\Quote;

class Items extends \Amasty\RequestQuote\Block\Account\Quote\Items
{
    /**
     * Get item row html
     *
     * @param   \Magento\Framework\DataObject $item
     * @param   int $pos
     * @return  string
     */
    public function getItemHtml(\Magento\Framework\DataObject $item, $pos = 0)
    {
        $type = $this->_getItemType($item);
        $itemRendererTypeResolver = $this->getData($type . '_renderer_type_resolver');
        if ($itemRendererTypeResolver instanceof ItemRendererTypeResolverInterface) {
            $type = $itemRendererTypeResolver->resolve($item) ?? $type;
        }

        $block = $this->getItemRenderer($type)->setItem($item)->setPos($pos);
        $this->_prepareItem($block);
        return $block->toHtml();
    }
}
