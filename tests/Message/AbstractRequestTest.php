<?php

declare(strict_types=1);

namespace Omnipay\Rdp\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public $request;

    public function setUp(): void
    {
        $this->request = Mockery::mock(AbstractRequest::class)->makePartial();
        $this->request->initialize();
    }

    public function testEndpointBase(): void
    {
        $this->assertSame($this->request, $this->request->setEndpointBase('https://secure-dev.reddotpayment.com/'));
        $this->assertSame('https://secure-dev.reddotpayment.com/', $this->request->getEndpointBase());
    }

    public function testSecretKey(): void
    {
        $this->assertSame($this->request, $this->request->setSecretKey('abc123'));
        $this->assertSame('abc123', $this->request->getSecretKey());
    }

    public function testMerchantId(): void
    {
        $this->assertSame($this->request, $this->request->setMerchantId('abc123'));
        $this->assertSame('abc123', $this->request->getMerchantId());
    }
}
