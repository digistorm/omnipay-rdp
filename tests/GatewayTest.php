<?php

namespace Omnipay\Rdp;

use Omnipay\Common\CreditCard;
use Omnipay\Tests\GatewayTestCase;

/**
 * @property Gateway gateway
 */
class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
        $this->gateway->setEndpointBase('https://pay.e-ghl.com/ipgsg/payment.aspx');
        $this->gateway->setMerchantId('TEST123');
        $this->gateway->setPassword('pass654321');
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidCreditCardException
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function testPurchase()
    {
        // TODO this should be fixed
        $request = $this->gateway->purchase([
            'transactionId' => uniqid(),
            'orderId' => uniqid(),
            'amount' => '10.00',
            'currency' => 'MYR',
            'description' => 'Here is a description that is over 40 characters long. It will get truncated to 40 characters.',
            'card' => new CreditCard([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'number' => '4444333322221111',
                'expiryMonth' => '03',
                'expiryYear' => '2030',
                'cvv' => '123',
            ]),
            'customer' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'phone' => '012345678',
                'address' => [
                    'line_1' => 'Line 1',

                ],
                'email' => 'john.doe@example.com',
            ],
        ]);

        $this->assertInstanceOf('Omnipay\Rdp\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());

        $data = $request->getData();

        $expectedData = [
            'apiOperation' => 'PAY',
            'order' => [
                'amount' => '10.00',
                'currency' => 'AUD',
                'reference' => 'Here is a description that is over 40 ch',
            ],
            'sourceOfFunds' => [
                'type' => 'CARD',
                'provided' => [
                    'card' => [
                        'number' => '5999999789012346',
                        'securityCode' => '123',
                        'expiry' => [
                            'month' => '3',
                            'year' => '22',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedData, $data);
    }
}
