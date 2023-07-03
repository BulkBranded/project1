<?php
namespace LR\ProductDataApi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Helper\Context;
use Magento\Setup\Module\Dependency\Parser\Composer\Json;
use LR\ProductDataApi\Model\ProductDataApiFactory;
use LR\ProductDataApi\Model\Config\Source\Status;

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
     * Init Objects
     *
     * @param Context $context
     * @param Curl $curl
     * @param ProductDataApiFactory $productDataApiFactory
     */
    public function __construct(
        Context $context,
        Curl $curl,
        ProductDataApiFactory $productDataApiFactory
    ) {
        parent::__construct($context);
        $this->curl = $curl;
        $this->productDataApiFactory = $productDataApiFactory;
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
                $model = $this->productDataApiFactory->create();
                $model->setData('SMCode', $productCode['SMCode']);
                $model->save();
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
                            ->setPageSize(20);

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

                $productMerge1 = array_merge($productDataArray,$productPriceListArray);
                $productFullData = array_merge($productMerge1,$productImageArray);

                $model = $this->productDataApiFactory->create();
                $model->setData($productFullData);
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
                            ->addFieldToFilter('status', Status::STATUS_PROCESSING);//->setPageSize(20);
        echo "<pre/>";
        print_r($collection->getData());exit;

        if (count($collection->getData()) > 0) {

            foreach ($collection->getData() as $productCode) {
                $productObject = $objectManager->create('\Magento\Catalog\Model\Product');
                $configurable_product->setSku($productCode['SMCode']);
                $configurable_product->setName($productCode['ProductName']);
                $configurable_product->setAttributeSetId(4);
                $configurable_product->setStatus(1);
                $configurable_product->setTypeId('configurable');
                $configurable_product->setPrice(100);
                $configurable_product->setWebsiteIds(array(1));
                $configurable_product->setCategoryIds(array(2));
                $configurable_product->setStockData(array(
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                        )
                );

                $color_attr_id = $configurable_product->getResource()->getAttribute('color')->getId();
                $configurable_product->getTypeInstance()->setUsedProductAttributeIds(array($color_attr_id), $configurable_product);
                $configurableAttributesData = $configurable_product->getTypeInstance()->getConfigurableAttributesAsArray($configurable_product);
                $configurable_product->setCanSaveConfigurableAttributes(true);
                $configurable_product->setConfigurableAttributesData($configurableAttributesData);
                $configurableProductsData = array();
                $configurable_product->setConfigurableProductsData($configurableProductsData);
                try {
                    $configurable_product->save();
                } catch (Exception $ex) {
                    echo '<pre>';
                    print_r($ex->getMessage());
                    exit;
                }

                $productId = $configurable_product->getId();

                $this->createSimpleProduct();

                echo "<pre/>";
                print_r($productCode);exit;
            }
        }
    }

    /**
     * Will create a simple product with data
     *
     * @return void
     */
    public function createSimpleProduct()
    {

    }

    /**
     * Will create a configurable product with data
     *
     * @return void
     */
    public function createConfigurableProduct()
    {

    }
}
