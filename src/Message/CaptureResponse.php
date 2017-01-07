<?php
namespace Omnipay\Affirm\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * PayPal REST Authorize Response
 */
class CaptureResponse extends AbstractResponse
{
	public function isSuccessful()
	{
		return (empty( $this->data['error'] ) && $this->getCode() == 201) || $this->data['code'] == 'duplicate-capture';
	}

	public function isRedirect()
	{
		return $this->getRedirectUrl() !== NULL;
	}

	public function getRedirectUrl()
	{
		if ( isset( $this->data['links'] ) && is_array( $this->data['links'] ) ) {
			foreach ( $this->data['links'] as $key => $value ) {
				if ( $value['rel'] == 'approval_url' ) {
					return $value['href'];
				}
			}
		}

		return NULL;
	}


	public function getTransactionId()
	{
		return $this->data['transaction_id'];
	}

	public function getMessage()
	{
		return $this->data['message'];
	}
}
