<?php

/**
 * Affirm Fetch Request
 */

namespace Omnipay\Affirm\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Affirm Fetch Response
 * Prepares and outputs the data from FetchRequest
 */
class FetchResponse extends AbstractResponse
{
	/**
	 * Check if the transaction was successful.
	 *
	 * @return bool
	 */
	public function isSuccessful()
	{
		return empty( $this->data['status'] );
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
		return NULL;
	}

	/**
	 * Get all listd entries
	 *
	 * @return array
	 */
	public function getCheckout()
	{
		return $this->data['checkout'] ?? [];
	}

	/**
	 * Get provider
	 *
	 * @return string|null
	 */
	public function getProvider()
	{
		$provider = NULL;
		if ( isset( $this->data['provider_id'] ) ) {

			switch ( $this->data['provider_id'] ) {
				case 1:
					$provider = 'Affirm';
					break;
				case 2:
					$provider = 'Zibby';
					break;

				default:
					$provider = NULL;
			}
		}

		return $provider;
	}
}
