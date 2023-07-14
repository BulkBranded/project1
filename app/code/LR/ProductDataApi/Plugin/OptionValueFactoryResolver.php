<?php
namespace LR\ProductDataApi\Plugin;

use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;

class OptionValueFactoryResolver
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Product instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Group instance name to create
     *
     * @var string
     */
    protected $groupInstanceName = null;

    /**
     * Factory constructor
     *
     * @param Registry $registry
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param string $groupInstanceName
     */
    public function __construct(
        Registry $registry,
        ObjectManagerInterface $objectManager,
        $instanceName = '\\Magento\\Catalog\\Api\\Data\\ProductCustomOptionValuesInterface',
        $groupInstanceName = '\\MageWorx\\OptionTemplates\\Model\\Group\\Option\\Value'
    ) {
        $this->registry          = $registry;
        $this->objectManager     = $objectManager;
        $this->instanceName      = $instanceName;
        $this->groupInstanceName = $groupInstanceName;
    }

    /**
     * @param ProductCustomOptionValuesInterfaceFactory $subject
     * @param \Closure $proceed
     * @param array $data
     * @return ProductCustomOptionValuesInterface|
     */
    public function aroundCreate(
        ProductCustomOptionValuesInterfaceFactory $subject,
        \Closure $proceed,
        $data = []
    ) {
        if ($this->registry->registry("mageworx_template_create_process")) {
            return $this->objectManager->create($this->groupInstanceName, $data);
        }
        return $proceed();
    }
}
