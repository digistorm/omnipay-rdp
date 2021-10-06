<?php

namespace Omnipay\Rdp;

use Money\Currency;
use Money\Money;
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
        $this->gateway->setEndpointBase('https://secure-dev.reddotpayment.com/');
        $this->gateway->setMerchantId('TEST123');
        $this->gateway->setSecretKey('pass654321');
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase([
            'card' => new CreditCard([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'expiryMonth' => '09',
                'expiryYear' => '2029',
                'number' => '4444333322221111',
                'cvv' => '123',
            ]),
            'payer_id' => 'abc',
            'payer_name' => 'John Doe',
            'payer_email' => 'john.doe@example.com',
            'orderId' => 'abc',
            'money' => new Money(100, new Currency('SGD')),
        ]);

        $this->assertInstanceOf('Omnipay\Rdp\Message\PurchaseRequest', $request);

        $data = $request->getData();

        $expectedData = [
            'api_mode' => 'direct_n3d',
            'payment_type' => 'S',
            'mid' => 'TEST123',
            'order_id' => 'abc',
            'amount' => '1.00',
            'ccy' => 'SGD',
            'payer_id' => 'abc',
            'payer_name' => 'John Doe',
            'payer_email' => 'john.doe@example.com',
            'cvv2' => '123',
            'signature' => 'd7ad968031f84df2fe7404df230ca55fc9d77ce2fe1eeaebb2a3c07e360aae36b4b1f173dc72e844110f8ecd540a0439ed8448610b2061b136006cde0f3455a8',
        ];

        $this->assertEquals($expectedData, $data);
    }

    public function testCreateToken()
    {
        $request = $this->gateway->createToken([
            'card' => new CreditCard([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'expiryMonth' => '09',
                'expiryYear' => '2029',
                'number' => '4444333322221111',
                'cvv' => '123',
            ]),
            'email' => 'john.doe@example.com',
            'order_id' => 'cba',
        ]);

        $this->assertInstanceOf('Omnipay\Rdp\Message\TokenizeRequest', $request);

        $data = $request->getData();

        $expectedData = [
            'api_mode' => 'direct_token_api',
            'transaction_type' => 'C',
            'mid' => 'TEST123',
            'order_id' => 'cba',
            'payer_name' => 'John Doe',
            'payer_email' => 'john.doe@example.com',
            'cvv2' => '123',
            'card_no' => '4444333322221111',
            'exp_date' => '092029',
            'signature' => '4e7c3bec6b7582d8889c21fcb2666e8be9e6f6016db654194eb098d45957ac88f366c2a12dbc5d7c656736a2e786f24ad260bd816d68eb4d502c81bff07b2507',
        ];

        $this->assertEquals($expectedData, $data);
    }


}
