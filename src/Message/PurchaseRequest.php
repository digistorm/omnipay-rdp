<?php

namespace Omnipay\Rdp\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class PurchaseRequest extends AbstractRequest
{

    const MODE_CARD = 'card';
    const MODE_WALLET = 'wallet';
    const MODE_TOKEN = 'token';

    public function getData()
    {
        if (!$this->getParameter('card')) {
            throw new InvalidRequestException('You must pass a "card" parameter.');
        }

        /* @var $card \OmniPay\Common\CreditCard */
        $card = $this->getParameter('card');
        $card->validate();
        $charge = $this->getParameter('amount');
        $customer = $this->getCustomer();

        $data = [

            // Generic Details
            'mid' => $this->getMerchantId(),
            'payment_type' => 'S',
            'api_mode' => 'direct_n3d',
            
            // Token Details
            // TODO alternatively support without tokenization
            'payer_id' => '',
            
            // Transaction Details
            'order_id' => $this->getTransactionId(),
            'amount' => number_format($charge->getAmount() / 100, 2),
            'ccy' => $charge->getCurrency()->getCode(),
            'payer_email' => $customer['email'],
            'cvv2' => $card->getCvv(),
        ];

        // Generate Signature
        $data['signature'] = $this->generateSignature($data);

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        // https://secure-dev.reddotpayment.com/service/payment-api
        return parent::getEndpointBase() . 'service/payment-api';
    }

        /**
     * Hashing function for rdp
     */
    public function generateSignature($mode, $params)
    {
        $dataToSign = '';
        $dataToSign .= $params['mid'];
        $dataToSign .= $params['order_id'];
        $dataToSign .= $params['payment_type'];
        $dataToSign .= $params['amount'];
        $dataToSign .= $params['ccy'];
        switch ($mode) {
            case static::MODE_CARD:
                $cardNo = substr($params['card_no'], 0, 6) . substr($params['card_no'], -4);
                $dataToSign .= $cardNo;
                $dataToSign .= $params['exp_date'];
                $dataToSign .= substr($params['cvv2'], -1);
                break;
            case static::MODE_TOKEN:
                if (isset($params['payer_id'])) {
                    $dataToSign .= $params['payer_id'];
                } else {
                    $dataToSign .= substr($params['token_id'], 0, 6) . substr($params['token_id'], -4);
                }
                break;
            default:
                throw new \Exception('Unsupported mode');
        }
        $dataToSign .= $this->getParameter('secret_key');
        return hash('sha512', $dataToSign);
    }

    /**
     * @param $data
     *
     * @return \Omnipay\Rdp\Message\PurchaseResponse
     */
    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
