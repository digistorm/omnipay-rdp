<?php

declare(strict_types=1);

namespace Omnipay\Rdp\Message;

use Money\Currency;
use Money\Money;
use Money\Number;
use Money\Parser\DecimalMoneyParser;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Rdp Abstract Request.
 *
 * This is the parent class for all Rdp requests.
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * Live or Test Endpoint URL.
     */
    public function getEndpointBase(): ?string
    {
        return $this->getParameter('endpointBase');
    }

    public function setEndpointBase(string $value): self
    {
        return $this->setParameter('endpointBase', $value);
    }

    public function getMerchantId(): ?string
    {
        return $this->getParameter('merchantId');
    }

    public function setMerchantId(string $value): self
    {
        return $this->setParameter('merchantId', $value);
    }

    public function getSecretKey(): ?string
    {
        return $this->getParameter('secretKey');
    }

    public function setSecretKey(string $value): self
    {
        return $this->setParameter('secretKey', $value);
    }

    public function getPayerId(): ?string
    {
        return $this->getParameter('payerId');
    }

    public function setPayerId(string $value): self
    {
        return $this->setParameter('payerId', $value);
    }

    public function getPayerName(): ?string
    {
        return $this->getParameter('payerName');
    }

    public function setPayerName(string $value): self
    {
        return $this->setParameter('payerName', $value);
    }

    public function getPayerEmail(): ?string
    {
        return $this->getParameter('payerEmail');
    }

    public function setPayerEmail(string $value): self
    {
        return $this->setParameter('payerEmail', $value);
    }

    public function setCustomer(string $value): self
    {
        return $this->setParameter('customer', $value);
    }

    public function getCustomer(): ?string
    {
        return $this->getParameter('customer');
    }

    public function setEmail(string $value): self
    {
        return $this->setParameter('email', $value);
    }

    public function getEmail(): ?string
    {
        return $this->getParameter('email');
    }

    public function setOrderId(string $value): self
    {
        return $this->setParameter('orderId', $value);
    }

    public function getOrderId(): ?string
    {
        return $this->getParameter('orderId');
    }

    abstract protected function createResponse(string $data): AbstractResponse;

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in subclasses.
     *
     * @return string
     */
    public function getHttpMethod(): string
    {
        return 'POST';
    }

    public function getHeaders(): array
    {
        return ['Content-Type' => 'application/json'];
    }

    /**
     * {@inheritdoc}
     */
    public function sendData(mixed $data): AbstractResponse
    {
        $response = $this->httpClient->request(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            $this->getHeaders(),
            json_encode($data) ?: null,
        );

        return $this->createResponse($response->getBody()->getContents());
    }

    public function getEndpoint(): string
    {
        return $this->getEndpointBase();
    }

    /**
     * @throws InvalidRequestException
     */
    public function getMoney(string $amount = 'amount'): ?Money
    {
        $amount = $this->getParameter($amount);

        if ($amount instanceof Money) {
            return $amount;
        }

        if ($amount !== null) {
            $moneyParser = new DecimalMoneyParser($this->getCurrencies());
            $currencyCode = $this->getCurrency() ?: 'MYR';
            $currency = new Currency($currencyCode);

            $number = Number::fromString($amount);

            // Check for rounding that may occur if too many significant decimal digits are supplied.
            $decimal_count = strlen($number->getFractionalPart());
            $subunit = $this->getCurrencies()->subunitFor($currency);
            if ($decimal_count > $subunit) {
                throw new InvalidRequestException('Amount precision is too high for currency.');
            }

            $money = $moneyParser->parse((string) $number, $currency->getCode());

            // Check for a negative amount.
            if (!$this->negativeAmountAllowed && $money->isNegative()) {
                throw new InvalidRequestException('A negative amount is not allowed.');
            }

            // Check for a zero amount.
            if (!$this->zeroAmountAllowed && $money->isZero()) {
                throw new InvalidRequestException('A zero amount is not allowed.');
            }

            return $money;
        }

        return null;
    }
}
