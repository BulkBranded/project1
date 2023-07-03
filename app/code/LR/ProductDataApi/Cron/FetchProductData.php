<?php
namespace LR\ProductDataApi\Cron;

use LR\ProductDataApi\Helper\Data as ProductDataApiHelper;

class FetchProductData
{
    /**
     * @var ProductDataApiHelper
     */
    protected $productDataApiHelper;

    /**
     * Create objects
     *
     * @param ProductDataApiHelper $productDataApiHelper
     */
    public function __construct(
        ProductDataApiHelper $productDataApiHelper,
    ) {
        $this->productDataApiHelper = $productDataApiHelper;
    }

    /**
     * Will fetch Product Data From API and Store into Database
     *
     * @return void
     */
    public function execute()
    {
        $this->productDataApiHelper->fetchProductData();
    }
}
