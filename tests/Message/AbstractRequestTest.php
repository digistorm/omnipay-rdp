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
        $this->assertSame($this->request, $this->request->setEndpointBase('https://pay.e-ghl.com/ipgsg/payment.aspx'));
        $this->assertSame('https://pay.e-ghl.com/ipgsg/payment.aspx', $this->request->getEndpointBase());
    }

    public function testPassword()
    {
        $this->assertSame($this->request, $this->request->setPassword('abc123'));
        $this->assertSame('abc123', $this->request->getPassword());
    }

    public function testMerchantId()
    {
        $this->assertSame($this->request, $this->request->setMerchantId('abc123'));
        $this->assertSame('abc123', $this->request->getMerchantId());
    }
}
