<?php

namespace Omnipay\Rdp\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class TokenizeRequest extends AbstractRequest
{

    public function getData()
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

    public function generateSignature($params) {
        unset($params['signature']);
        ksort($params);
        $dataToSign = '';
        foreach ($params as $v) {
           $dataToSign .= $v;
        } 
        $dataToSign .= $this->getSecretKey();
        return hash('sha512', $dataToSign);
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        // https://secure-dev.reddotpayment.com/service/token-api
        return trim(parent::getEndpointBase(), '/') . '/service/token-api';
    }
    /**
     * @param $data
     *
     * @return \Omnipay\Rdp\Message\TokenizeResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new TokenizeResponse($this, $data);
    }
}
