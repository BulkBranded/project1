<?php
namespace LR\ProductDataApi\Model\ResourceModel\ProductDataApi;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Define resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'LR\ProductDataApi\Model\ProductDataApi',
            'LR\ProductDataApi\Model\ResourceModel\ProductDataApi'
        );
    }
}
