<?php
namespace LR\ProductDataApi\Cron;

use LR\ProductDataApi\Helper\Data as ProductDataApiHelper;

class FetchProducts
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
     * Will fetch Data From API and Store into Database
     *
     * @return void
     */
    public function execute()
    {
        $this->productDataApiHelper->fetchProductSkus();
    }
}
