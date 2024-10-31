<?php

declare(strict_types=1);

namespace Omnipay\Rdp\Message;

use Money\Currency;
use Money\Money;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    public $request;

    public function setUp(): void
    {
        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            [
                'endpointBase' => 'https://secure-dev.reddotpayment.com/',
                'merchantId' => 'TEST123',
                'secretKey' => 'pass654321',
                'payer_id' => 'abc',
                'payer_name' => 'John Doe',
                'payer_email' => 'john.doe@example.com',
                'orderId' => 'abc',
                'money' => new Money(100, new Currency('SGD')),
                'card' => $this->getValidCard(),
            ]
        );
    }

    public function testSendSuccess(): void
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNull($response->getMessage());
    }

    public function testSendError(): void
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Bank or acquirer rejected the transaction.', $response->getMessage());
    }
}
