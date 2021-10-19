<?php

namespace Omnipay\Rdp\Message;

use Omnipay\Common\Message\AbstractResponse as OmnipayResponse;
use Omnipay\Common\Message\RequestInterface;

class AbstractResponse extends OmnipayResponse
{

    const PAYMENT_STATUS_SUCCESS = '0';
    const PAYMENT_STATUS_BANK_REJECTED = '-1';
    const PAYMENT_STATUS_PENDING = '-01';


    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, json_decode($data, true));
    }

    public function isSuccessful()
    {
        if (!isset($this->data['response_code']) || $this->data['response_code'] !== AbstractResponse::PAYMENT_STATUS_SUCCESS) {
            return false;
        }
        return true;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()
    {
        if ($this->isSuccessful()) {
            return null;
        }
        if (isset($this->data['response_code'])) {
            switch ($this->data['response_code']) {
                case '-1':
                    return 'Bank or acquirer rejected the transaction.';
                case '-01':
                    return 'Transaction pending. Please contact us.';
            }
        }
        if (isset($this->data['acquirer_response_msg']) && strlen($this->data['acquirer_response_msg'])) {
            return $this->data['acquirer_response_msg'];
        }
        if (isset($this->data['response_msg'])) {
            return $this->data['response_msg'];
        }
        return 'Unknown Error';
    }

    /**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        if (isset($this->data['transaction_id'])) {
            return $this->data['transaction_id'];
        }
    }
}
