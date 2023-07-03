<?php
namespace LR\ProductDataApi\Api\Data;

interface ProductDataApiInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    public const ENTITY_ID = 'entity_id';
    public const SMCODE = 'SMCode';
    public const PRODUCT_REFERANCE_ID = 'ProductReferenceID';
    public const PRODUCT_NAME = 'ProductName';
    public const MAIN_COLORS = 'MainColours';
    public const ACTUAL_COLORS = 'ActualColours';
    public const PRICE_RANGE_ID = 'PriceRangeID';
    public const PRICE_RANGE = 'PriceRange';
    public const LEAD_TIME = 'LeadTime';
    public const PRINT_AREA = 'PrintArea';
    public const PRINT_SIZE = 'PrintSize';
    public const PRINT_METHOD = 'PrintMethod';
    public const PRODUCT_DESCRIPTION = 'ProductDescription';
    public const ADDITIONAL_INFORMATION = 'AdditionalInformation';
    public const UNIT = 'Unit';
    public const IS_PLAIN = 'IsPlain';
    public const IS_COLOUR1 = 'IsColour1';
    public const IS_COLOUR2 = 'IsColour2';
    public const IS_COLOUR3 = 'IsColour3';
    public const IS_COLOUR4 = 'IsColour4';
    public const IS_EEE = 'IsEEE';
    public const EEE_HEADING = 'EEEHeading';
    public const QUANTITY_BREAK = 'QuantityBreak';
    public const PLAIN = 'Plain';
    public const ONE_COLOUR = 'OneColour';
    public const TWO_COLOUR = 'TwoColour';
    public const THREE_COLOUR = 'ThreeColour';
    public const FULL_COLOUR = 'FullColour';
    public const EEE = 'EEE';
    public const IMAGE_URL = 'ImageURL';
    public const STATUS = 'status';
    public const UPDATE_AT = 'updated_at';
    public const CREATED_AT = 'created_at';

    /**
     * Get EntityId.
     *
     * @return int
     */
    public function getEntityId();

     /**
      * Set EntityId
      *
      * @param int $entityId
      * @return void
      */
    public function setEntityId($entityId);

    /**
     * Get SMCode.
     *
     * @return string
     */
    public function getSMCode();

    /**
     * Set SMCode.
     *
     * @param string $SMCode
     * @return void
     */
    public function setSMCode($SMCode);

    /**
     * Get ProductReferenceID
     *
     * @return string
     */
    public function getProductReferenceID();

    /**
     * Set ProductReferenceID
     *
     * @param string $ProductReferenceID
     * @return void
     */
    public function setProductReferenceID($ProductReferenceID);

    /**
     * Get ProductName.
     *
     * @return string
     */
    public function getProductName();

    /**
     * Set ProductName
     *
     * @param string $ProductName
     * @return void
     */
    public function setProductName($ProductName);

    /**
     * Get MainColours
     *
     * @return string
     */
    public function getMainColours();

    /**
     * Set MainColours
     *
     * @param string $MainColours
     * @return void
     */
    public function setMainColours($MainColours);

    /**
     * Get ActualColours
     *
     * @return string
     */
    public function getActualColours();

    /**
     * Set ActualColours
     *
     * @param string $ActualColours
     * @return void
     */
    public function setActualColours($ActualColours);

    /**
     * Get PriceRangeID
     *
     * @return string
     */
    public function getPriceRangeID();

    /**
     * Set PriceRangeID
     *
     * @param string $PriceRangeID
     * @return void
     */
    public function setPriceRangeID($PriceRangeID);

    /**
     * Get PriceRange
     *
     * @return string
     */
    public function getPriceRange();

    /**
     * Set PriceRange
     *
     * @param string $PriceRange
     * @return void
     */
    public function setPriceRange($PriceRange);

    /**
     * Get LeadTime
     *
     * @return string
     */
    public function getLeadTime();

    /**
     * Set LeadTime
     *
     * @param string $LeadTime
     * @return void
     */
    public function setLeadTime($LeadTime);

    /**
     * Get PrintArea
     *
     * @return string
     */
    public function getPrintArea();

    /**
     * Set PrintArea
     *
     * @param string $PrintArea
     * @return void
     */
    public function setPrintArea($PrintArea);

    /**
     * Get PrintSize
     *
     * @return string
     */
    public function getPrintSize();

    /**
     * Set PrintSize
     *
     * @param string $PrintSize
     * @return void
     */
    public function setPrintSize($PrintSize);

    /**
     * Get PrintMethod
     *
     * @return string
     */
    public function getPrintMethod();

    /**
     * Set PrintMethod
     *
     * @param string $PrintMethod
     * @return void
     */
    public function setPrintMethod($PrintMethod);

    /**
     * Get ProductDescription
     *
     * @return varchar
     */
    public function getProductDescription();

    /**
     * Set ProductDescription
     *
     * @param varchar $ProductDescription
     * @return void
     */
    public function setProductDescription($ProductDescription);

    /**
     * Get AdditionalInformation
     *
     * @return string
     */
    public function getAdditionalInformation();

    /**
     * Set AdditionalInformation
     *
     * @param string $AdditionalInformation
     * @return void
     */
    public function setAdditionalInformation($AdditionalInformation);

    /**
     * Get Unit
     *
     * @return string
     */
    public function getUnit();

    /**
     * Set Unit
     *
     * @param string $Unit
     * @return void
     */
    public function setUnit($Unit);

    /**
     * Get IsPlain
     *
     * @return string
     */
    public function getIsPlain();

    /**
     * Set IsPlain
     *
     * @param string $IsPlain
     * @return void
     */
    public function setIsPlain($IsPlain);

    /**
     * Get IsColour1
     *
     * @return string
     */
    public function getIsColour1();

    /**
     * Set IsColour1
     *
     * @param string $IsColour1
     * @return void
     */
    public function setIsColour1($IsColour1);

    /**
     * Get IsColour2
     *
     * @return string
     */
    public function getIsColour2();

    /**
     * Set IsColour2
     *
     * @param string $IsColour2
     * @return void
     */
    public function setIsColour2($IsColour2);

    /**
     * Get IsColour3
     *
     * @return string
     */
    public function getIsColour3();

    /**
     * Set IsColour3
     *
     * @param string $IsColour3
     * @return void
     */
    public function setIsColour3($IsColour3);

    /**
     * Get IsColour4
     *
     * @return string
     */
    public function getIsColour4();

    /**
     * Set IsColour4
     *
     * @param string $IsColour4
     * @return void
     */
    public function setIsColour4($IsColour4);

    /**
     * Get IsEEE
     *
     * @return string
     */
    public function getIsEEE();

    /**
     * Set IsEEE
     *
     * @param string $IsEEE
     * @return void
     */
    public function setIsEEE($IsEEE);

    /**
     * Get EEEHeading
     *
     * @return string
     */
    public function getEEEHeading();

    /**
     * Set EEEHeading
     *
     * @param string $EEEHeading
     * @return void
     */
    public function setEEEHeading($EEEHeading);

    /**
     * Get QuantityBreak
     *
     * @return varchar
     */
    public function getQuantityBreak();

    /**
     * Set QuantityBreak
     *
     * @param varchar $QuantityBreak
     * @return void
     */
    public function setQuantityBreak($QuantityBreak);

    /**
     * Get Plain
     *
     * @return varchar
     */
    public function getPlain();

    /**
     * Set Plain
     *
     * @param varchar $Plain
     * @return void
     */
    public function setPlain($Plain);

    /**
     * Get OneColour
     *
     * @return varchar
     */
    public function getOneColour();

    /**
     * Set OneColour
     *
     * @param varchar $OneColour
     * @return void
     */
    public function setOneColour($OneColour);

    /**
     * Get TwoColour
     *
     * @return varchar
     */
    public function getTwoColour();

    /**
     * Set TwoColour
     *
     * @param varchar $TwoColour
     * @return void
     */
    public function setTwoColour($TwoColour);

    /**
     * Get ThreeColour
     *
     * @return varchar
     */
    public function getThreeColour();

    /**
     * Set ThreeColour
     *
     * @param varchar $ThreeColour
     * @return void
     */
    public function setThreeColour($ThreeColour);

    /**
     * Get FullColour
     *
     * @return varchar
     */
    public function getFullColour();

    /**
     * Set FullColour
     *
     * @param varchar $FullColour
     * @return void
     */
    public function setFullColour($FullColour);

    /**
     * Get EEE
     *
     * @return varchar
     */
    public function getEEE();

    /**
     * Set EEE
     *
     * @param varchar $EEE
     * @return void
     */
    public function setEEE($EEE);

    /**
     * Get ImageURL
     *
     * @return varchar
     */
    public function getImageURL();

    /**
     * Set ImageURL
     *
     * @param varchar $ImageURL
     * @return void
     */
    public function setImageURL($ImageURL);

    /**
     * Get Status.
     *
     * @return varchar
     */
    public function getStatus();

    /**
     * Set status.
     *
     * @param varchar $Status
     * @return void
     */
    public function setStatus($Status);

    /**
     * Get UpdatedAt.
     *
     * @return varchar
     */
    public function getUpdatedAt();

    /**
     * Set UpdatedAt.
     *
     * @param varchar $UpdatedAt
     * @return void
     */
    public function setUpdatedAt($UpdatedAt);

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt.
     *
     * @param varchar $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);
}
