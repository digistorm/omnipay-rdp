<?php

namespace Omnipay\Rdp\Message;

use Mockery;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\Rdp\Message\AbstractRequest')->makePartial();
        $this->request->initialize();
    }

    public function testEndpointBase()
    {
        $this->assertSame($this->request, $this->request->setEndpointBase('https://secure-dev.reddotpayment.com/'));
        $this->assertSame('https://secure-dev.reddotpayment.com/', $this->request->getEndpointBase());
    }

    public function testSecretKey()
    {
        $this->assertSame($this->request, $this->request->setSecretKey('abc123'));
        $this->assertSame('abc123', $this->request->getSecretKey());
    }

    public function testMerchantId()
    {
        $this->assertSame($this->request, $this->request->setMerchantId('abc123'));
        $this->assertSame('abc123', $this->request->getMerchantId());
    }
}
