<?php
namespace LR\ProductDataApi\Helper;

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
        DirectoryList $directoryList
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
            foreach ($smProductCode['data'] as $productCode) {
                $collection = $this->productDataApiFactory->create()->getCollection()
                            ->addFieldToFilter('SMCode', $productCode['SMCode']);
                if (empty($collection->getData())) {
                    $model = $this->productDataApiFactory->create();
                    $model->setData('SMCode', $productCode['SMCode']);
                    $model->save();
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
                            ->setPageSize(200);

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

                try {
                    $model = $this->productDataApiFactory->create()->load($productCode['entity_id']);
                    $model->setData($productFullData);
                    $model->setEntityId($productCode['entity_id']);
                    $model->setStatus(Status::STATUS_PROCESSING)->save();
                } catch(\Exception $e){
                    echo "Error: " . $e->getMessage();exit;
                }
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
        /* $collection = $this->productDataApiFactory->create()->getCollection()
                            ->addFieldToFilter('status', Status::STATUS_COMPLETED)->load();
        if (count($collection->getData()) > 0) {
            foreach ($collection as $productCode) {
                $productCode->delete();
            }
        } */
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
                            ->setPageSize(50);

        if (count($collection->getData()) > 0) {

            foreach ($collection->getData() as $productCode) {
                $associatedproductids = $this->createSimpleProduct($productCode);

                $configProduct = $this->productFactory->create();
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
                    ->setData('printArea', $productCode['PrintArea'])
                    ->setData('printSize', $productCode['PrintSize'])
                    ->setData('printMethod', $productCode['PrintMethod'])
                    ->setDescription($productCode['ProductDescription'])
                    ->setStockData([
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'is_in_stock' => 1,
                        'qty' => 100
                    ]);

                $colorAttrId = $configProduct->getResource()->getAttribute('color')->getId();
                $configProduct->getTypeInstance()->setUsedProductAttributeIds([$colorAttrId], $configProduct);
                $configurableAttributesData = $configProduct->getTypeInstance()->getConfigurableAttributesAsArray($configProduct);
                $configProduct->setCanSaveConfigurableAttributes(true);
                $configProduct->setConfigurableAttributesData($configurableAttributesData);
                $configurableProductsData = [];
                $configProduct->setConfigurableProductsData($configurableProductsData);
                try {
                    $configProduct->setAssociatedProductIds($associatedproductids);
                    $configProduct->setCanSaveConfigurableAttributes(true);
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
                    try {
                        $configProduct->save();
                    } catch(\Exception $e) {

                    }

                    $model = $this->productDataApiFactory->create()->load($productCode['entity_id']);
                    $model->setStatus(Status::STATUS_COMPLETED)->save();

                } catch (Exception $ex) {
                    echo '<pre>';
                    print_r($ex->getMessage());
                    exit;
                }
            }
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
        $newColors = array_diff($colors, $this->getExistingOptions());
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

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
                echo '<pre>';
                print_r($ex->getMessage());
                exit;
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
}
