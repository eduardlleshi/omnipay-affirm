<?php

/**
 * Affirm Refund Request
 */

namespace Omnipay\Affirm\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Affirm Refund Response
 * Prepares and outputs the data from RefundRequest
 */
class RefundResponse extends AbstractResponse
{
	/**
	 * Check if the transaction was successful.
	 *
	 * @return bool
	 */
	public function isSuccessful()
	{
		return empty( $this->data['error'] ) && $this->getCode() == 201 && $this->data['type'] != 'invalid_request';;
	}

	/**
	 * Check if the transaction needs to redirect to another page
	 *
	 * @return bool
	 */
	public function isRedirect()
	{
		return $this->getRedirectUrl() !== NULL;
	}

	/**
	 * Get the redirect URL
	 *
	 * @return string|null
	 */
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

	/**
	 * Get message param from the response
	 *
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->data['message'];
	}
}
