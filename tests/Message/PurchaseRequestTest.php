<?php

namespace Omnipay\Rdp\Message;

use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            [
                'endpointBase' => 'https://pay.e-ghl.com/ipgsg/payment.aspx',
                'merchantId' => 'SIT',
                'password' => 'sit12345',
                'transactionId' => uniqid(),
                'amount' => '10.00',
                'currency' => 'MYR',
                'card' => $this->getValidCard(),
            ]
        );
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getMessage());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('[INVALID_REQUEST] Value \'ABCD1234\' is invalid. No valid Merchant Acquirer Relationship available', $response->getMessage());
    }
}
