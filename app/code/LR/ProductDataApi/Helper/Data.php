<?php
namespace LR\ProductDataApi\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Module\Dependency\Parser\Composer\Json;
use LR\ProductDataApi\Model\ProductDataApiFactory;
use LR\ProductDataApi\Model\Config\Source\Status;
use \Magento\Framework\App\ResourceConnection;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Eav\Setup\EavSetupFactory;
use \Magento\Eav\Api\AttributeRepositoryInterface;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Framework\Filesystem\Io\File;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \MageWorx\OptionTemplates\Model\GroupFactory;
use \MageWorx\OptionTemplates\Model\OptionSaver;
use \Magento\Store\Model\Store;
use \Magento\Store\Model\StoreFactory;
use \Magento\Framework\Registry;
use \MageWorx\OptionTemplates\Model\Group\OptionFactory;

use function Aws\filter;

class Data extends AbstractHelper
{
    public const SM_API_URL = 'https://webapi-v2.sourcingmachine.co.uk/';
    public const SM_TOKEN = '08405754b3d7febdad2f00efddd1c048';
    public const SM_REFERER_URL = 'https://www.icreviews.org/';
    public const SM_APPLICATION_JSON = 'application/json';
    public const SM_APPLICATION_FORM = 'application/x-www-form-urlencoded';

    /**
     * @var Curl $curl
     */
    protected $curl;

    /**
     * @var ProductDataApiFactory $productDataApiFactory
     */
    protected $productDataApiFactory;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepositoryInterface;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepositoryInterface;

    /**
     * @var File
     */
    private $file;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var GroupFactory
     */
    private $groupFactory;

    /**
     * @var OptionSaver
     */
    private $optionSaver;

    /**
     * @var StoreFactory
     */
    private $storeFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var OptionFactory
     */
    private $optionFactory;

    /**
     * @param Context $context
     * @param Curl $curl
     * @param ProductDataApiFactory $productDataApiFactory
     * @param ProductFactory $productFactory
     * @param ResourceConnection $resourceConnection
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeRepositoryInterface $attributeRepositoryInterface
     * @param ProductRepositoryInterface $productRepositoryInterface
     * @param File $file
     * @param DirectoryList $directoryList
     * @param GroupFactory $groupFactory
     * @param OptionSaver $optionSaver
     * @param StoreFactory $storeFactory
     * @param Registry $registry
     * @param OptionFactory $optionFactory
     */
    public function __construct(
        Context $context,
        Curl $curl,
        ProductDataApiFactory $productDataApiFactory,
        ProductFactory $productFactory,
        ResourceConnection $resourceConnection,
        EavSetupFactory $eavSetupFactory,
        AttributeRepositoryInterface $attributeRepositoryInterface,
        ProductRepositoryInterface $productRepositoryInterface,
        File $file,
        DirectoryList $directoryList,
        GroupFactory $groupFactory,
        OptionSaver $optionSaver,
        StoreFactory $storeFactory,
        Registry $registry,
        OptionFactory $optionFactory
    ) {
        parent::__construct($context);
        $this->curl = $curl;
        $this->productDataApiFactory = $productDataApiFactory;
        $this->productFactory = $productFactory;
        $this->resourceConnection = $resourceConnection;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeRepositoryInterface = $attributeRepositoryInterface;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->file = $file;
        $this->directoryList = $directoryList;
        $this->groupFactory = $groupFactory;
        $this->optionSaver = $optionSaver;
        $this->storeFactory = $storeFactory;
        $this->registry = $registry;
        $this->optionFactory = $optionFactory;
    }

    /**
     * Common Curl Request function
     *
     * @param string $requestPath
     * @param string $requestType
     * @param string $postvalue
     * @return Json
     */
    public function prepareCurlRequest($requestPath, $requestType, $postvalue = null)
    {
        $apiRequestUrl = self::SM_API_URL . $requestPath;

        //set curl header
        $this->curl->addHeader("Accept", self::SM_APPLICATION_JSON);
        $this->curl->addHeader("Token", self::SM_TOKEN);
        $this->curl->addHeader("Referer", self::SM_REFERER_URL);

        //set options
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        if ($requestType == "GET") {
            $this->curl->addHeader("Content-Type", self::SM_APPLICATION_JSON);
            $this->curl->setOption(CURLOPT_AUTOREFERER, true);
        }
        $this->curl->setOption(CURLOPT_ENCODING, '');
        $this->curl->setOption(CURLOPT_MAXREDIRS, 10);
        $this->curl->setOption(CURLOPT_TIMEOUT, 10);
        $this->curl->setOption(CURLOPT_FOLLOWLOCATION, true);
        $this->curl->setOption(CURLOPT_CUSTOMREQUEST, $requestType);
        if ($requestType == "POST") {
            $this->curl->addHeader("Content-Type", self::SM_APPLICATION_FORM);
            $this->curl->setOption(CURLOPT_POSTFIELDS, $postvalue);
        }

        //get request with url
        $this->curl->get($apiRequestUrl);

        //read response
        $response = $this->curl->getBody();
        $returnResponse = json_decode($response, true);
        return $returnResponse;
    }

    /**
     * Will fetch product SKU's and store to Database
     *
     * @return void
     */
    public function fetchProductSkus()
    {
        $smProductCode = $this->prepareCurlRequest("smcode/lists", "GET");
        if (!empty($smProductCode) && isset($smProductCode['data']) && $smProductCode['status'] == 1) {
            $counter = 0;
            foreach ($smProductCode['data'] as $productCode) {
                try {
                    $model = $this->productDataApiFactory->create();
                    $model->setData('SMCode', $productCode['SMCode']);
                    $model->save();
                } catch (Exception $e) {
                    $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Fetch_SKU.log');
                    $logger = new \Zend_Log();
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
            }
        }
    }

    /**
     * Will fetch Product Data ans Store to DB
     *
     * @return void
     */
    public function fetchProductData()
    {
        $collection = $this->productDataApiFactory->create()->getCollection()
                            ->addFieldToFilter('status', Status::STATUS_ENABLE)
                            ->addFieldToFilter('ProductReferenceID', array('null' => true))
                            ->setPageSize(100);

        if (count($collection->getData()) > 0) {
            foreach ($collection->getData() as $productCode) {

                /// Api call for get Product Data
                $smProductData = $this->prepareCurlRequest(
                    "product/information",
                    "POST",
                    "smcode=".$productCode['SMCode']
                );

                if (!empty($smProductData) && isset($smProductData['data']) && $smProductData['status'] == 1) {
                    $productDataArray = $smProductData['data'][0];
                }

                /// Api call for get Pricelist Data
                $smProductPriceList = $this->prepareCurlRequest(
                    "smcode/pricelists",
                    "POST",
                    "smcode=".$productCode['SMCode']
                );

                if (!empty($smProductPriceList) && isset($smProductPriceList['data']) && $smProductPriceList['status'] == 1) {
                    $productPriceListArray = $smProductPriceList['data'][0];
                }

                /// Api call for get Product Image Data
                $smProductImageData = $this->prepareCurlRequest(
                    "product/images",
                    "POST",
                    "smcode=".$productCode['SMCode']
                );

                if (!empty($smProductImageData) && isset($smProductImageData['data']) && $smProductImageData['status'] == 1) {
                    $productImageArray = $smProductImageData['data'][0];
                }

                $productMerge1 = array_merge($productDataArray, $productPriceListArray);
                $productFullData = array_merge($productMerge1, $productImageArray);

                $model = $this->productDataApiFactory->create();
                $model->setData($productFullData)
                    ->setEntityId($productCode['entity_id']);
                $model->setStatus(Status::STATUS_PROCESSING)->save();
            }
        }
    }

    /**
     * Will remove Product Records which is completed
     *
     * @return void
     */
    public function clearProductData()
    {
        $collection = $this->productDataApiFactory->create()->getCollection()
                            ->addFieldToFilter('status', Status::STATUS_COMPLETED)->load();
        if (count($collection->getData()) > 0) {
            foreach ($collection as $productCode) {
                $productCode->delete();
            }
        }
    }

    /**
     * Will Will Fetch Processing Product and will create Product and Temaplte at Magento
     *
     * @return void
     */
    public function createProductAndTemplate()
    {
        $collection = $this->productDataApiFactory->create()->getCollection()
                            ->addFieldToFilter('status', Status::STATUS_PROCESSING)
                            // ->addFieldToFilter('SMCode', ["in" => ["SM4730818"]])
                            ->setPageSize(50);

        if (count($collection->getData()) > 0) {
            foreach ($collection->getData() as $productCode) {

                if ($productCode['ActualColours'] == null || $productCode['PriceRange'] == "Please Call") {
                    $model = $this->productDataApiFactory->create();
                    $model->setData($productCode)
                        ->setStatus(Status::STATUS_DATAERROR)
                        ->save();
                    continue;
                }

                $associatedproductids = $this->createSimpleProduct($productCode);

                $configProduct = $this->productFactory->create();
                $urlKey = $productCode['ProductName'] . " " . $productCode['SMCode'];
                $urlKey = str_replace(" ", "-", preg_replace('/[^a-zA-Z0-9]/s', ' ', $urlKey));
                $configProduct->setSku($productCode['SMCode'])
                    ->setName($productCode['ProductName'])
                    ->setAttributeSetId(4)
                    ->setStatus(1)
                    ->setTypeId('configurable')
                    ->setPrice(100)
                    ->setVisibility(4)
                    ->setWebsiteIds([1])
                    ->setCategoryIds([2,3,4,5,6,7,8,9])
                    ->setData('priceRange', $productCode['PriceRange'])
                    ->setData('leadTime', $productCode['LeadTime'])
                    ->setData('printMethod', $productCode['PrintMethod'])
                    ->setDescription($productCode['ProductDescription'])
                    ->setUrlKey($urlKey)
                    ->setStockData([
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => 1,
                        'qty' => 100
                    ]);

                $colorAttrId = $configProduct->getResource()->getAttribute('color')->getId();
                $configProduct->getTypeInstance()->setUsedProductAttributeIds([$colorAttrId], $configProduct);
                $configurableAttributesData = $configProduct->getTypeInstance()
                    ->getConfigurableAttributesAsArray($configProduct);
                $configProduct->setCanSaveConfigurableAttributes(true);
                $configProduct->setConfigurableAttributesData($configurableAttributesData);
                $configurableProductsData = [];
                $configProduct->setConfigurableProductsData($configurableProductsData);
                try {
                    $configProduct->setAssociatedProductIds($associatedproductids);
                    $configProduct->setCanSaveConfigurableAttributes(true);
                    if (isset($productCode['ImageURL']) && $productCode['ImageURL']) {
                        $tmpDir = $this->directoryList->getPath(DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
                        $this->file->checkAndCreateFolder($tmpDir);
                        $newFileName = $tmpDir . baseName(trim($productCode['ImageURL'], '"'));
                        $result = $this->file->read(trim($productCode['ImageURL'], '"'), $newFileName);
                        if ($result) {
                            $configProduct->addImageToMediaGallery(
                                $newFileName,
                                ['image', 'small_image', 'thumbnail'],
                                false,
                                false
                            );
                        }
                    }
                    $configProduct->save();

                    $this->addCustomOptions($configProduct, $productCode);

                    $model = $this->productDataApiFactory->create();
                    $model->setData($productCode)
                        ->setStatus(Status::STATUS_COMPLETED)
                        ->save();

                } catch (Exception $ex) {
                    $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Product_Create.log');
                    $logger = new \Zend_Log();
                    $logger->addWriter($writer);
                    $logger->info($productCode['SMCode']. " ". $ex->getMessage());
                } finally {
                    $this->registry->unregister('mageworx_template_create_process');
                }
            }
            $this->registry->unregister('mageworx_template_create_process');
        }
    }

    /**
     * Will create a simple product with data
     *
     * @param array $rowData
     * @return array
     */
    public function createSimpleProduct($rowData)
    {
        if (!isset($rowData['ActualColours']) || (isset($rowData['ActualColours']) && !$rowData['ActualColours'])) {
            return [];
        }
        $colors = explode(',', $rowData['ActualColours']);
        $colors = array_map('trim', $colors);
        $newColors = array_filter(array_diff($colors, $this->getExistingOptions()));

        if (count($newColors)) {
            $colorAttrId = $this->attributeRepositoryInterface->get(
                \Magento\Catalog\Model\Product::ENTITY,
                'color'
            )->getAttributeId();

            foreach ($newColors as $key => $option) {
                $eavSetup = $this->eavSetupFactory->create();
                $data = [
                    'attribute_id' => $colorAttrId,
                    'value' => [
                        [
                            0 => trim($option)
                        ]
                    ]
                ];
                $eavSetup->addAttributeOption($data);
            }
        }

        $simpleProductIds = [];
        $colorOptions = array_flip($this->getExistingOptions());
        foreach ($colors as $key => $color) {
            $product = $this->productFactory->create();
            $urlKey = $rowData['ProductName']."-".$color . " " . $rowData['SMCode']."-". $color;
            $urlKey = str_replace(" ", "-", preg_replace('/[^a-zA-Z0-9]/s', ' ', $urlKey));
            $product->setSku($rowData['SMCode']."-".$color)
                ->setName($rowData['ProductName']." ". $color)
                ->setAttributeSetId(4)
                ->setStatus(1)
                ->setVisibility(1)
                ->setTypeId('simple')
                ->setPrice(100)
                ->setWebsiteIds([1])
                ->setCategoryIds([2,3,4,5,6,7,8,9])
                ->setData('color', $colorOptions[$color])
                ->setUrlKey($urlKey)
                ->setStockData([
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 100
                ]);
            try {
                $product = $this->productRepositoryInterface->save($product);
                $simpleProductIds[] = $product->getId();
            } catch (Exception $ex) {
                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Product_Create.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info($rowData['SMCode']. " ". $ex->getMessage());
            }
        }

        return $simpleProductIds;
    }

    /**
     * Will create a configurable product with data
     *
     * @param array $associatedproductids
     * @return self
     */
    public function createConfigurableProduct($associatedproductids)
    {

    }

    /**
     * Return Existing product options
     *
     * @return array
     */
    private function getExistingOptions() : array
    {
        $productEntity = \Magento\Catalog\Model\Product::ENTITY;
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
                ->from(['EA' => "eav_attribute"], ["EAO.option_id", "EAOV.value"])
                ->join(
                    ['EET' => 'eav_entity_type'],
                    "EET.entity_type_id = EA.entity_type_id AND EET.entity_type_code = '{$productEntity}'",
                    []
                )
                ->join(
                    ['EAO' => 'eav_attribute_option'],
                    "EAO.attribute_id = EA.attribute_id",
                    []
                )
                ->join(
                    ['EAOV' => 'eav_attribute_option_value'],
                    "EAOV.option_id = EAO.option_id",
                    []
                )
                ->where("EA.attribute_code = 'color'");

        return $connection->fetchPairs($select);
    }

    /**
     * Add Custom Options Group to Product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $apiData
     * @return self
     */
    public function addCustomOptions($product, $apiData) : self
    {
        // Start Preparing custom options Data
        $groupData = [
            "group_id" => "",
            "title" => $product->getSku(),
            "productskus" => "",
            "absolute_cost" => 0,
            "absolute_price" => 0,
            "absolute_weight" => 0,
            "assign_type" => 2,
            "affect_product_custom_options" => 1,
            "option_link_fields" => [
                "cost" => "cost",
                "weight" => "weight",
                "price" => "price"
            ],
        ];

        $options = [];

        $tierData = (isset($apiData['QuantityBreak']) && $apiData['QuantityBreak'])
            ? json_decode($apiData['QuantityBreak'], true) : [];

        // Geting max price from price-rang to set price in options value
        $valuePrice = explode("-", $apiData['PriceRange']);
        $valuePrice = preg_replace('/[^.0-9]/s', "", $valuePrice[1]);

        if ((isset($apiData['IsPlain']) && (bool)$apiData['IsPlain'])) {
            if (!isset($options['branding'])) {
                $options['branding'] = $this->getOptionTemplateData("Branding", 0);
            }
            $tierPriceData = (isset($apiData['Plain']) && $apiData['Plain'])
            ? json_decode($apiData['Plain'], true) : [];

            $options['branding']["values"][] = $this->getValueData("Is Plain", 0, $tierData, $tierPriceData, $valuePrice, 0);
        }

        if ((isset($apiData['IsColour1']) && (bool)$apiData['IsColour1'])) {
            if (!isset($options['branding'])) {
                $options['branding'] = $this->getOptionTemplateData("Branding", 0);
            }
            $tierPriceData = (isset($apiData['OneColour']) && $apiData['OneColour'])
            ? json_decode($apiData['OneColour'], true) : [];

            $options['branding']["values"][] = $this->getValueData("1-Colour", 1, $tierData, $tierPriceData, $valuePrice, 0);
        }

        if ((isset($apiData['IsColour2']) && (bool)$apiData['IsColour2'])) {
            if (!isset($options['branding'])) {
                $options['branding'] = $this->getOptionTemplateData("Branding", 0);
            }
            $tierPriceData = (isset($apiData['TwoColour']) && $apiData['TwoColour'])
            ? json_decode($apiData['TwoColour'], true) : [];

            $options['branding']["values"][] = $this->getValueData("2-Colour", 2, $tierData, $tierPriceData, $valuePrice, 0);
        }

        if ((isset($apiData['IsColour3']) && (bool)$apiData['IsColour3'])) {
            if (!isset($options['branding'])) {
                $options['branding'] = $this->getOptionTemplateData("Branding", 0);
            }
            $tierPriceData = (isset($apiData['ThreeColour']) && $apiData['ThreeColour'])
            ? json_decode($apiData['ThreeColour'], true) : [];

            $options['branding']["values"][] = $this->getValueData("3-Colour", 3, $tierData, $tierPriceData, $valuePrice, 0);
        }

        if ((isset($apiData['IsColour4']) && (bool)$apiData['IsColour4'])) {
            if (!isset($options['branding'])) {
                $options['branding'] = $this->getOptionTemplateData("Branding", 0);
            }
            $tierPriceData = (isset($apiData['FullColour']) && $apiData['FullColour'])
            ? json_decode($apiData['FullColour'], true) : [];

            $options['branding']["values"][] = $this->getValueData("Full Colour", 4, $tierData, $tierPriceData, $valuePrice, 0);
        }

        $dependency = [
            ["0","1"],
            ["0","2"],
            ["0","3"],
            ["0","4"]
        ];

        if ((isset($apiData['PrintArea']) && $apiData['PrintArea'])) {
            if (!isset($options['PrintArea'])) {
                $options['PrintArea'] = $this->getOptionTemplateData("Print Area", "1");
            }
            $options['PrintArea']["values"][] = $this->getValueData("280x310mm", 0, [], [], $valuePrice, $dependency, 1);
        }

        if ((isset($apiData['PrintSize']) && $apiData['PrintSize'])) {
            if (!isset($options['PrintSize'])) {
                $options['PrintSize'] = $this->getOptionTemplateData("Print Size", 2);
            }
            $options['PrintSize']["values"][] = $this->getValueData("380mmL x 420mmH", 0, [], [], $valuePrice, $dependency, 1);
        }

        foreach ($options as $key => $data) {
            $groupData['options'][] = $data;
        }

        // End data preparation
        $this->registry->register('mageworx_template_create_process', true);
        $this->registry->register('mageworx_optiontemplates_group_save', true);
        $group = $this->groupFactory->create();
        $storeId = Store::DEFAULT_STORE_ID;
        $store = $this->storeFactory->create()->load($storeId);
        $group->setStoreId($storeId);

        $this->registry->register('mageworx_optiontemplates_group', $group);
        $this->registry->register('current_store', $store);

        // Filter prepred Data
        $data = $this->filterData($groupData);
        $group->addData($data);

        // Adding product options to group
        $options = $this->mergeProductOptions(
            $data['options'],
            [],
            []
        );
        $group->setOptions($options);
        $group->setData('options', $options);
        $group->setCanSaveCustomOptions(1);
        // Set Product Ids in group
        $group->setProductsIds([$product->getId()]);
        $this->registry->unregister('mageworx_optiontemplates_group_id');
        $this->registry->unregister('mageworx_optiontemplates_group_option_ids');
        try {
            // Save Group
            $group->save();
            // Save Custom Options and dependancy
            $this->optionSaver->saveProductOptions(
                $group,
                [],
                OptionSaver::SAVE_MODE_UPDATE
            );
        } catch (\Exception $ex) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom_attribute.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info($ex->getMessage());
            $logger->info($ex->getTraceAsString());
        } finally {
            $this->registry->__destruct();
        }
        return $this;
    }

    /**
     * Filter Group Options data
     *
     * @param array $data
     * @return array
     */
    protected function filterData($data)
    {
        if (isset($data['group_id']) && !$data['group_id']) {
            unset($data['group_id']);
        }

        if (isset($data['options'])) {
            $updatedOptions = [];
            foreach ($data['options'] as $key => $option) {
                if (!isset($option['option_id'])) {
                    continue;
                }

                $optionId = $option['option_id'];
                if (!$optionId && !empty($option['record_id'])) {
                    $optionId = $option['record_id'] . '_';
                }
                $updatedOptions[$optionId] = $option;
                if (empty($option['values'])) {
                    continue;
                }

                $values                              = $option['values'];
                $updatedOptions[$optionId]['values'] = [];
                foreach ($values as $valueKey => $value) {
                    if (!isset($value['option_type_id'])) {
                        $valueId                                       = $value['record_id'] . '_';
                        $updatedOptions[$optionId]['values'][$valueId] = $value;
                    } else {
                        $valueId                                       = $value['option_type_id'];
                        $updatedOptions[$optionId]['values'][$valueId] = $value;
                    }
                }
            }

            $data['options'] = $updatedOptions;
        }

        return $data;
    }

    /**
     * Merge original options, if template is not new, form options and default options for group
     *
     * @param array $productOptions form options
     * @param array $originalOptions original template options
     * @param array $overwriteOptions default value options
     * @return array
     */
    protected function mergeProductOptions($productOptions, $originalOptions, $overwriteOptions)
    {
        if (!is_array($productOptions)) {
            $productOptions = [];
        }
        if (is_array($overwriteOptions)) {
            $options = array_replace_recursive($productOptions, $overwriteOptions);
            array_walk_recursive(
                $options,
                function (&$item) {
                    if ($item === "") {
                        $item = null;
                    }
                }
            );
        } else {
            $options = $productOptions;
        }

        $currentOptionIds      = [];
        $currentOptionValueIds = [];

        $recordIdCounter = 0;
        foreach ($options as $optionKey => $option) {
            if (!isset($option['record_id'])) {
                $options[$optionKey]['record_id'] = 'r' . $recordIdCounter;
            }
            $recordIdCounter++;
            if (!empty($option['option_id'])) {
                $currentOptionIds[$option['option_id']] = $option['option_id'];
            }
            if (!empty($option['values']) && is_array($option['values'])) {
                foreach ($option['values'] as $valueKey => $value) {
                    if (!isset($value['record_id'])) {
                        $options[$optionKey]['values'][$valueKey]['record_id'] = 'r' . $recordIdCounter;
                    }
                    $recordIdCounter++;
                    if (!empty($value['option_type_id'])) {
                        $currentOptionValueIds[$value['option_type_id']] = $value['option_type_id'];
                    }
                }
            }
        }

        foreach ($originalOptions as $originalOption) {
            foreach ($options as $optionKey => $option) {
                if (empty($option['option_id']) || empty($originalOption['option_id'])) {
                    continue;
                }
                if ($option['option_id'] != $originalOption['option_id']) {
                    if (!isset($currentOptionIds[$originalOption['option_id']])) {
                        $originalOption->setData('is_delete', 1);
                        $originalOption->setData('record_id', $originalOption['option_id']);
                        $options[]                                      = $originalOption->getData();
                        $currentOptionIds[$originalOption['option_id']] = true;
                        break;
                    }
                } else {
                    if (empty($originalOption->getValues()) || empty($option['values'])) {
                        continue;
                    }
                    foreach ($originalOption->getValues() as $originalOptionValue) {
                        foreach ($option['values'] as $optionValue) {
                            if (empty($optionValue['option_type_id']) || empty($originalOptionValue['option_type_id'])) {
                                continue;
                            }
                            $originalOptionValueId = $originalOptionValue['option_type_id'];
                            if ($optionValue['option_type_id'] != $originalOptionValueId) {
                                if (!isset($currentOptionValueIds[$originalOptionValueId])) {
                                    $originalOptionValue['is_delete']              = 1;
                                    $originalOptionValue['record_id']              = $originalOptionValueId;
                                    $options[$optionKey]['values'][]               = $originalOptionValue->getData();
                                    $currentOptionValueIds[$originalOptionValueId] = true;
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        $processedOptions = [];
        foreach ($options as $option) {
            $processedOptions[] = $this->optionFactory->create()->setData($option);
        }

        return $processedOptions;
    }

    /**
     * Return Options Data
     *
     * @param string $title
     * @param int $recordId
     * @return array
     */
    protected function getOptionTemplateData($title, $recordId) : array
    {
        $optionData = [];

        $optionData = [
            "sort_order" => (string)($recordId + 1),
            "option_id" => "",
            "is_require" => 1,
            "disabled" => 0,
            "disabled_by_values" => "",
            "custom_class" => "",
            "one_time" => 0,
            "record_id" => $recordId,
            "type" => "drop_down",
            "title" => $title,
        ];

        return $optionData;
    }

    /**
     * Return Options Value Data
     *
     * @param string $title
     * @param int $recordId
     * @param array $tierData
     * @param array $tierPriceData
     * @param int $valuePrice
     * @param array $dependency
     * @param integer $isDefault
     * @return array
     */
    protected function getValueData(
        $title,
        $recordId,
        $tierData,
        $tierPriceData,
        $valuePrice,
        $dependency = [],
        $isDefault = 0
    ) : array {
        $valueData = [];
        $tierPrice = [];

        if (!empty($tierData) && !empty($tierPriceData)) {
            $i = 0;
            // Preparing tier price data for options value
            foreach ($tierData as $key => $qty) {
                if (isset($tierPriceData[$key]) && $tierPriceData[$key]) {
                    $price = preg_replace('/[^.0-9]/s', "", $tierPriceData[$key]);
                    $tierPrice[] = [
                        "record_id" => $i,
                        "customer_group_id" => 32000,
                        "qty" => $qty,
                        "price" => $price,
                        "price_type" => "fixed",
                        "date_from" => "",
                        "date_to" => ""
                    ];
                    $i++;
                }
            }
        }

        $valueData = [
            "record_id" => $recordId,
            "title" => $title,
            "price_type" => "fixed",
            "sort_order" => (string)($recordId + 1),
            "disabled" => 0,
            "tier_price" => json_encode($tierPrice),
            "price" => $valuePrice,
            "is_default" => $isDefault,
            "manage_stock" => 0,
        ];

        // Add Dependency to Values
        if (!empty($dependency)) {
            $valueData['dependency_type'] = 0;
            $valueData['dependency'] = json_encode($dependency);
        }

        return $valueData;
    }
}
