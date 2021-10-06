# Omnipay: RDP (Red Dot Payment)

**RDP (Red Dot Payment) driver for the Omnipay PHP payment processing library**

Currently only supports purchases with tokenized card. Available methods:

- createToken()
- purchase()

## Usage

```php
<?php
use Omnipay\Omnipay;
use Omnipay\Common\CreditCard;
use Money\Currency;
use Money\Money;

// Create a gateway for the Rdp Gateway
// (routes to GatewayFactory::create)
/* @var \Omnipay\Rdp\Gateway $gateway */
$gateway = Omnipay::create('Rdp');

$gateway->setTestMode(true);
$gateway->setEndpointBase('https://secure-dev.reddotpayment.com/');
$gateway->setMerchantId('merchantIdValue');
$gateway->setSecretKey('secretKeyValue');


// Charge using a card
/* @var \Omnipay\Rdp\Message\TokeneResponse $response */
$response = $gateway->createToken([
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
])->send();

$response = $gateway->createToken([
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
])->send();

$response = $gateway->purchase([
    'card' => new CreditCard([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'expiryMonth' => '09',
        'expiryYear' => '2029',
        'number' => '4444333322221111',
        'cvv' => '123',
    ]),
    'payer_id' => $response->payer_id,
    'payer_name' => 'John Doe',
    'payer_email' => 'john.doe@example.com',
    'orderId' => 'abc',
    'money' => new Money(100, new Currency('SGD')),
])->send();
```
