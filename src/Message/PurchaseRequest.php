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
        // This is currently required to fetch the cvv2 value
        if (!$this->getParameter('card')) {
            throw new InvalidRequestException('You must pass a "card" parameter.');
        }
        $card = $this->getParameter('card');
        $charge = $this->getParameter('amount');
        $data = [
            'api_mode' => 'direct_n3d',
            'payment_type' => 'S',
            'mid' => $this->getMerchantId(),
            'order_id' => $this->getOrderId(),
            'amount' => number_format($charge->getAmount() / 100, 2, '.', ''),
            'ccy' => $charge->getCurrency()->getCode(),
            'payer_id' => $this->getPayerId(),
            'payer_name' => $this->getPayerName(),
            'payer_email' => $this->getPayerEmail(),
            'cvv2' => $card->getCvv(),
        ];

        // Generate Signature
        $data['signature'] = $this->generateSignature(static::MODE_TOKEN, $data);

        return $data;
    }

    /**
     * @return string
     */
    public function getEndpoint()
    {
        // https://secure-dev.reddotpayment.com/service/payment-api
        return trim(parent::getEndpointBase(), '/') . '/service/payment-api';
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
        if (isset($params['cvv2'])) {
            $dataToSign .= substr($params['cvv2'], -1);
        }
        $dataToSign .= $this->getSecretKey();
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
