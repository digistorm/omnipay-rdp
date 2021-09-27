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

        /* @var $card \OmniPay\Common\CreditCard */
        $card = $this->getParameter('card');
        $card->validate();
        // $charge = $this->getParameter('amount');
        // $customer = $this->getCustomer();
        // $metadata = $this->getMetadata();

        $data = [

            // Generic Details
            'mid' => $this->getMerchantId(),
            'order_id' => 'ds-placeholder', // $this->getTransactionId(),
            'api_mode' => 'direct_token_api',
            'transaction_type' => 'C',
            'payer_name' => $card->getName(),
            'payer_email' => 'placeholder@email.com', // $customer['email'],
            'ccy' => 'SGD', // TODO where does this come from? $charge->getCurrency()->getCode(),
            'card_no' => $card->getNumber(),
            'exp_date' => $card->getExpiryDate('mY'),
            'cvv2' => $card->getCvv(),

            // Transaction Details
            // 'PaymentDesc' => 'Payment for entry ' . $metadata['entry_uuid'],
            // 'OrderNumber' => $this->getOrderId(),
            // 'Amount' => number_format($charge->getAmount() / 100, 2),

            // Card Details
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
