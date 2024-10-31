<?php

declare(strict_types=1);

namespace Omnipay\Rdp\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class TokenizeRequest extends AbstractRequest
{
    /**
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        if (!$this->getParameter('card')) {
            throw new InvalidRequestException('You must pass a "card" parameter.');
        }
        $card = $this->getParameter('card');
        $card->validate();

        $data = [
            'api_mode' => 'direct_token_api',
            'transaction_type' => 'C',
            'mid' => $this->getMerchantId(),
            'order_id' => $this->getOrderId(),
            'payer_name' => $card->getName(),
            'payer_email' => $this->getEmail(),
            'card_no' => $card->getNumber(),
            'exp_date' => $card->getExpiryDate('mY'),
            'cvv2' => $card->getCvv(),
        ];

        // Generate Signature
        $data['signature'] = $this->generateSignature($data);

        return $data;
    }

    public function generateSignature(array $params): string
    {
        unset($params['signature']);
        ksort($params);
        $dataToSign = implode('', $params);
        $dataToSign .= $this->getSecretKey();

        return hash('sha512', $dataToSign);
    }

    public function getEndpoint(): string
    {
        // Endpoint is https://secure-dev.reddotpayment.com/service/token-api
        return trim((string) parent::getEndpointBase(), '/') . '/service/token-api';
    }

    protected function createResponse(mixed $data): TokenizeResponse
    {
        return $this->response = new TokenizeResponse($this, $data);
    }
}
