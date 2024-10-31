<?php

declare(strict_types=1);

namespace Omnipay\Rdp\Message;

use Omnipay\Common\Message\AbstractResponse as OmnipayResponse;
use Omnipay\Common\Message\RequestInterface;

class AbstractResponse extends OmnipayResponse
{
    public const PAYMENT_STATUS_SUCCESS = '0';
    public const PAYMENT_STATUS_BANK_REJECTED = '-1';
    public const PAYMENT_STATUS_PENDING = '-01';

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, json_decode((string) $data, true));
    }

    public function isSuccessful(): bool
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
     */
    public function getMessage(): ?string
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
        if (isset($this->data['acquirer_response_msg']) && strlen((string) $this->data['acquirer_response_msg'])) {
            return $this->data['acquirer_response_msg'];
        }

        return $this->data['response_msg'] ?? 'Unknown Error';
    }

    /**
     * Gateway Reference
     *
     * Returns a reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference(): ?string
    {
        return $this->data['transaction_id'] ?? null;
    }
}
