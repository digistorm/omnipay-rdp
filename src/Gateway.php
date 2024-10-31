<?php

declare(strict_types=1);

namespace Omnipay\Rdp;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Rdp\Message\PurchaseRequest;
use Omnipay\Rdp\Message\TokenizeRequest;

/**
 * Rdp gateway
 *
 * @method RequestInterface authorize(array $options = []) (Optional method)
 * Authorize an amount on the customers card
 * @method RequestInterface completeAuthorize(array $options = []) (Optional method)
 * Handle return from off-site gateways after authorization
 * @method RequestInterface capture(array $options = []) (Optional method)
 * Capture an amount you have previously authorized
 * @method RequestInterface completePurchase(array $options = []) (Optional method)
 * Handle return from off-site gateways after purchase
 * @method RequestInterface refund(array $options = []) (Optional method)
 * Refund an already processed transaction
 * @method RequestInterface void(array $options = []) (Optional method)
 * Generally can only be called up to 24 hours after submitting a transaction
 * @method RequestInterface createCard(array $options = []) (Optional method)
 * The returned response object includes a cardReference, which can be used for future transactions
 * @method RequestInterface updateCard(array $options = []) (Optional method)
 * Update a stored card
 * @method RequestInterface deleteCard(array $options = []) (Optional method)
 * Delete a stored card
 * @method RequestInterface fetchTransaction(array $options = []) (Optional method)
 * Fetch a transaction
 * @method RequestInterface acceptNotification(array $options = []) (Optional method)
 * Accept a notification
 */
class Gateway extends AbstractGateway
{
    public function getName(): string
    {
        return 'rdp';
    }

    public function getDefaultParameters(): array
    {
        return [
            'endpointBase' => 'https://secure-dev.reddotpayment.com/',
            'merchantId' => '',
            'secretKey' => '',
        ];
    }

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

    public function createToken(array $parameters = []): AbstractRequest
    {
        return $this->createRequest(TokenizeRequest::class, $parameters);
    }

    /**
     * @throws InvalidRequestException
     */
    public function purchase(array $options = []): AbstractRequest
    {
        $options['endpointBase'] = $this->getEndpointBase();
        $options['merchantId'] = $this->getMerchantId();
        $options['secretKey'] = $this->getSecretKey();

        $request = $this->createRequest(PurchaseRequest::class, $options);

        // Validate required parameters
        $request->validate('endpointBase', 'merchantId', 'secretKey');

        return $request;
    }
}
