<?php
namespace LR\ProductDataApi\Model;

use LR\ProductDataApi\Api\Data\ProductDataApiInterface;

class ProductDataApi extends \Magento\Framework\Model\AbstractModel implements ProductDataApiInterface
{
    /**
     * CMS page cache tag.
     */
    public const CACHE_TAG = 'lr_productdataapi';

    /**
     * @var string
     */
    protected $_cacheTag = 'lr_productdataapi';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'lr_productdataapi';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(
            'LR\ProductDataApi\Model\ResourceModel\ProductDataApi'
        );
    }
    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Set EntityId.
     *
     * @param int $entityId
     * @return void
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * Get SMCode.
     *
     * @return string
     */
    public function getSMCode()
    {
        return $this->getData(self::SMCODE);
    }

    /**
     * Set SMCode.
     *
     * @param int $SMCode
     * @return void
     */
    public function setSMCode($SMCode)
    {
        return $this->setData(self::SMCODE, $SMCode);
    }

    /**
     * Get ProductReferenceID
     *
     * @return string
     */
    public function getProductReferenceID()
    {
        return $this->getData(self::PRODUCT_REFERANCE_ID);
    }

    /**
     * Set ProductReferenceID
     *
     * @param string $ProductReferenceID
     * @return void
     */
    public function setProductReferenceID($ProductReferenceID)
    {
        return $this->setData(self::PRODUCT_REFERANCE_ID, $ProductReferenceID);
    }

    /**
     * Get ProductName.
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getData(self::PRODUCT_NAME);
    }

    /**
     * Will set ProductName
     *
     * @param string $ProductName
     * @return void
     */
    public function setProductName($ProductName)
    {
        return $this->setData(self::PRODUCT_NAME, $ProductName);
    }

    /**
     * Get MainColours.
     *
     * @return string
     */
    public function getMainColours()
    {
        return $this->getData(self::MAIN_COLORS);
    }

    /**
     * Will set MainColours
     *
     * @param string $MainColours
     * @return void
     */
    public function setMainColours($MainColours)
    {
        return $this->setData(self::MAIN_COLORS, $MainColours);
    }

    /**
     * Get ActualColours.
     *
     * @return string
     */
    public function getActualColours()
    {
        return $this->getData(self::ACTUAL_COLORS);
    }

    /**
     * Will set ActualColours
     *
     * @param string $ActualColours
     * @return void
     */
    public function setActualColours($ActualColours)
    {
        return $this->setData(self::ACTUAL_COLORS, $ActualColours);
    }

    /**
     * Get PriceRangeID.
     *
     * @return string
     */
    public function getPriceRangeID()
    {
        return $this->getData(self::PRICE_RANGE_ID);
    }

    /**
     * Will set PriceRangeID
     *
     * @param string $PriceRangeID
     * @return void
     */
    public function setPriceRangeID($PriceRangeID)
    {
        return $this->setData(self::PRICE_RANGE_ID, $PriceRangeID);
    }

    /**
     * Get PriceRange.
     *
     * @return string
     */
    public function getPriceRange()
    {
        return $this->getData(self::PRICE_RANGE);
    }

    /**
     * Will set PriceRange
     *
     * @param string $PriceRange
     * @return void
     */
    public function setPriceRange($PriceRange)
    {
        return $this->setData(self::PRICE_RANGE, $PriceRange);
    }

    /**
     * Get LeadTime.
     *
     * @return string
     */
    public function getLeadTime()
    {
        return $this->getData(self::LEAD_TIME);
    }

    /**
     * Will set LeadTime
     *
     * @param string $LeadTime
     * @return void
     */
    public function setLeadTime($LeadTime)
    {
        return $this->setData(self::LEAD_TIME, $LeadTime);
    }

    /**
     * Get PrintArea.
     *
     * @return string
     */
    public function getPrintArea()
    {
        return $this->getData(self::PRINT_AREA);
    }

    /**
     * Will set PrintArea
     *
     * @param string $PrintArea
     * @return void
     */
    public function setPrintArea($PrintArea)
    {
        return $this->setData(self::PRINT_AREA, $PrintArea);
    }

    /**
     * Get PrintSize.
     *
     * @return string
     */
    public function getPrintSize()
    {
        return $this->getData(self::PRINT_SIZE);
    }

    /**
     * Will set PrintSize
     *
     * @param string $PrintSize
     * @return void
     */
    public function setPrintSize($PrintSize)
    {
        return $this->setData(self::PRINT_SIZE, $PrintSize);
    }

    /**
     * Get PrintMethod.
     *
     * @return string
     */
    public function getPrintMethod()
    {
        return $this->getData(self::PRINT_METHOD);
    }

    /**
     * Will set PrintMethod
     *
     * @param string $PrintMethod
     * @return void
     */
    public function setPrintMethod($PrintMethod)
    {
        return $this->setData(self::PRINT_METHOD, $PrintMethod);
    }

    /**
     * Get ProductDescription.
     *
     * @return varchar
     */
    public function getProductDescription()
    {
        return $this->getData(self::PRODUCT_DESCRIPTION);
    }

    /**
     * Will set ProductDescription
     *
     * @param varchar $ProductDescription
     * @return void
     */
    public function setProductDescription($ProductDescription)
    {
        return $this->setData(self::PRODUCT_DESCRIPTION, $ProductDescription);
    }

    /**
     * Get AdditionalInformation.
     *
     * @return string
     */
    public function getAdditionalInformation()
    {
        return $this->getData(self::ADDITIONAL_INFORMATION);
    }

    /**
     * Will set AdditionalInformation
     *
     * @param string $AdditionalInformation
     * @return void
     */
    public function setAdditionalInformation($AdditionalInformation)
    {
        return $this->setData(self::ADDITIONAL_INFORMATION, $AdditionalInformation);
    }

    /**
     * Get Unit.
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->getData(self::UNIT);
    }

    /**
     * Will set Unit
     *
     * @param string $Unit
     * @return void
     */
    public function setUnit($Unit)
    {
        return $this->setData(self::UNIT, $Unit);
    }

    /**
     * Get IsPlain.
     *
     * @return string
     */
    public function getIsPlain()
    {
        return $this->getData(self::IS_PLAIN);
    }

    /**
     * Will set IsPlain
     *
     * @param string $IsPlain
     * @return void
     */
    public function setIsPlain($IsPlain)
    {
        return $this->setData(self::IS_PLAIN, $IsPlain);
    }

    /**
     * Get IsColour1.
     *
     * @return string
     */
    public function getIsColour1()
    {
        return $this->getData(self::IS_COLOUR1);
    }

    /**
     * Will set IsColour1
     *
     * @param string $IsColour1
     * @return void
     */
    public function setIsColour1($IsColour1)
    {
        return $this->setData(self::IS_COLOUR1, $IsColour1);
    }

    /**
     * Get IsColour2.
     *
     * @return string
     */
    public function getIsColour2()
    {
        return $this->getData(self::IS_COLOUR2);
    }

    /**
     * Will set IsColour2
     *
     * @param string $IsColour2
     * @return void
     */
    public function setIsColour2($IsColour2)
    {
        return $this->setData(self::IS_COLOUR2, $IsColour2);
    }

    /**
     * Get IsColour3.
     *
     * @return string
     */
    public function getIsColour3()
    {
        return $this->getData(self::IS_COLOUR3);
    }

    /**
     * Will set IsColour3
     *
     * @param string $IsColour3
     * @return void
     */
    public function setIsColour3($IsColour3)
    {
        return $this->setData(self::IS_COLOUR3, $IsColour3);
    }

    /**
     * Get IsColour4.
     *
     * @return string
     */
    public function getIsColour4()
    {
        return $this->getData(self::IS_COLOUR4);
    }

    /**
     * Will set IsColour4
     *
     * @param string $IsColour4
     * @return void
     */
    public function setIsColour4($IsColour4)
    {
        return $this->setData(self::IS_COLOUR4, $IsColour4);
    }

    /**
     * Get IsEEE.
     *
     * @return string
     */
    public function getIsEEE()
    {
        return $this->getData(self::IS_EEE);
    }

    /**
     * Will set IsEEE
     *
     * @param string $IsEEE
     * @return void
     */
    public function setIsEEE($IsEEE)
    {
        return $this->setData(self::IS_EEE, $IsEEE);
    }

    /**
     * Get EEEHeading.
     *
     * @return string
     */
    public function getEEEHeading()
    {
        return $this->getData(self::EEE_HEADING);
    }

    /**
     * Will set EEEHeading
     *
     * @param string $EEEHeading
     * @return void
     */
    public function setEEEHeading($EEEHeading)
    {
        return $this->setData(self::EEE_HEADING, $EEEHeading);
    }

    /**
     * Get QuantityBreak.
     *
     * @return varchar
     */
    public function getQuantityBreak()
    {
        return $this->getData(self::QUANTITY_BREAK);
    }

    /**
     * Will set QuantityBreak
     *
     * @param varchar $QuantityBreak
     * @return void
     */
    public function setQuantityBreak($QuantityBreak)
    {
        return $this->setData(self::QUANTITY_BREAK, $QuantityBreak);
    }

    /**
     * Get Plain.
     *
     * @return varchar
     */
    public function getPlain()
    {
        return $this->getData(self::PLAIN);
    }

    /**
     * Will set Plain
     *
     * @param varchar $Plain
     * @return void
     */
    public function setPlain($Plain)
    {
        return $this->setData(self::PLAIN, $Plain);
    }

    /**
     * Get OneColour.
     *
     * @return varchar
     */
    public function getOneColour()
    {
        return $this->getData(self::ONE_COLOUR);
    }

    /**
     * Will set OneColour
     *
     * @param varchar $OneColour
     * @return void
     */
    public function setOneColour($OneColour)
    {
        return $this->setData(self::ONE_COLOUR, $OneColour);
    }

    /**
     * Get TwoColour.
     *
     * @return varchar
     */
    public function getTwoColour()
    {
        return $this->getData(self::TWO_COLOUR);
    }

    /**
     * Will set TwoColour
     *
     * @param varchar $TwoColour
     * @return void
     */
    public function setTwoColour($TwoColour)
    {
        return $this->setData(self::TWO_COLOUR, $TwoColour);
    }

    /**
     * Get ThreeColour.
     *
     * @return varchar
     */
    public function getThreeColour()
    {
        return $this->getData(self::THREE_COLOUR);
    }

    /**
     * Will set ThreeColour
     *
     * @param varchar $ThreeColour
     * @return void
     */
    public function setThreeColour($ThreeColour)
    {
        return $this->setData(self::THREE_COLOUR, $ThreeColour);
    }

    /**
     * Get FullColour.
     *
     * @return varchar
     */
    public function getFullColour()
    {
        return $this->getData(self::FULL_COLOUR);
    }

    /**
     * Will set FullColour
     *
     * @param varchar $FullColour
     * @return void
     */
    public function setFullColour($FullColour)
    {
        return $this->setData(self::FULL_COLOUR, $FullColour);
    }

    /**
     * Get EEE.
     *
     * @return varchar
     */
    public function getEEE()
    {
        return $this->getData(self::EEE);
    }

    /**
     * Will set EEE
     *
     * @param varchar $EEE
     * @return void
     */
    public function setEEE($EEE)
    {
        return $this->setData(self::EEE, $EEE);
    }

    /**
     * Get ImageURL.
     *
     * @return varchar
     */
    public function getImageURL()
    {
        return $this->getData(self::IMAGE_URL);
    }

    /**
     * Will set ImageURL
     *
     * @param varchar $ImageURL
     * @return void
     */
    public function setImageURL($ImageURL)
    {
        return $this->setData(self::IMAGE_URL, $ImageURL);
    }

    /**
     * Get Status.
     *
     * @return varchar
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set Status.
     *
     * @param string $Status
     * @return void
     */
    public function setStatus($Status)
    {
        return $this->setData(self::STATUS, $Status);
    }

    /**
     * Get UpdatedAt.
     *
     * @return varchar
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATE_AT);
    }

    /**
     * Set UpdatedAt.
     *
     * @param string $UpdatedAt
     * @return void
     */
    public function setUpdatedAt($UpdatedAt)
    {
        return $this->setData(self::UPDATE_AT, $UpdatedAt);
    }

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set CreatedAt.
     *
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
