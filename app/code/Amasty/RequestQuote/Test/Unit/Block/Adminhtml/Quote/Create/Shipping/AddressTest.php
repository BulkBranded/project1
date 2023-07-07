<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Request a Quote Base for Magento 2
 */


namespace Amasty\RequestQuote\Test\Unit\Block\Adminhtml\Quote\Create\Shipping;

use Amasty\RequestQuote\Block\Adminhtml\Quote\Create\Shipping\Address;
use Amasty\RequestQuote\Test\Unit\Traits\ObjectManagerTrait;
use Amasty\RequestQuote\Test\Unit\Traits\ReflectionTrait;
use Magento\Framework\App\Request\Http;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AddressTest
 *
 * @see Address
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class AddressTest extends \PHPUnit\Framework\TestCase
{
    use ObjectManagerTrait;
    use ReflectionTrait;

    /**
     * @covers Address::getDontSaveInAddressBook
     *
     * @dataProvider getDontSaveInAddressBookDataProvider
     *
     * @throws \ReflectionException
     */
    public function testGetDontSaveInAddressBook($isShipping, $isAsBilling, $params, $expected)
    {
        /** @var Address|MockObject $block */
        $block = $this->createPartialMock(Address::class, [
            'getIsAsBilling',
            'getIsShipping',
            'getRequest'
        ]);
        $block->expects($this->any())->method('getIsAsBilling')->willReturn($isAsBilling);
        $block->expects($this->any())->method('getIsShipping')->willReturn($isShipping);
        $request = $this->createMock(Http::class);
        $request->expects($this->any())->method('getParams')->willReturn($params);
        $block->expects($this->any())->method('getRequest')->willReturn($request);

        $actual = $block->getDontSaveInAddressBook();

        $this->assertEquals($expected, $actual);
    }

    /**
     * Data provider for getDontSaveInAddressBook test
     * @return array
     */
    public function getDontSaveInAddressBookDataProvider()
    {
        return [
            [
                true,
                true,
                [
                    'quote' => [
                        'billing_address' => [
                            'save_in_address_book' => true
                        ]
                    ]
                ],
                false
            ],
            [
                true,
                true,
                [
                    'quote' => [
                        'billing_address' => [
                            'xxx' => true
                        ]
                    ]
                ],
                true
            ],
            [
                true,
                true,
                [],
                false
            ],
            [
                true,
                false,
                [],
                false
            ],
            [
                false,
                true,
                [],
                true
            ]
        ];
    }
}
