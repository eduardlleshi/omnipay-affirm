<?php

/**
 * Affirm Abstract Request
 */

namespace Omnipay\Affirm\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Affirm Authorize Response
 * Prepares and outputs the data from AuthorizeRequest
 */
class AuthorizeResponse extends AbstractResponse
{
	/**
	 * Check if the transaction was successful.
	 *
	 * @return bool
	 */
	public function isSuccessful()
	{
		return empty( $this->data['message'] ) && $this->getCode() == 200 && $this->data['type'] != 'invalid_request' || ( isset( $this->data['status'] ) && $this->data['status'] == 'authorized' );
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
	 * Get charge_id param from the response
	 *
	 * @return mixed
	 */
	public function getTransactionReference()
	{
		if ( $this->isSuccessful() ) {
			$charge_id = isset( $this->data['id'] ) ? $this->data['id'] : NULL;
		} else {
			$charge_id = isset( $this->data['charge_id'] ) ? $this->data['charge_id'] : NULL;
		}

		return $charge_id;
	}

	/**
	 * Get amount param from the response
	 *
	 * @return mixed
	 */
	public function getAmount()
	{
		return $this->data['amount'];
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
