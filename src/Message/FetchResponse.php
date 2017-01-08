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
		return empty( $this->data['message'] );
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
	 * Get all listd entries
	 *
	 * @return array
	 */
	public function getEntries()
	{
		if ( !isset( $this->data['entries'] ) ) {
			$data['entries'][] = $this->data;
		} else {
			$data = $this->data;
		}

		return $data;
	}


	/**
	 * Get single (first) entry
	 *
	 * @return array|null
	 */
	public function getSingleEntry()
	{
		if ( isset( $this->getEntries()['entries'][0] ) )
			return $this->getEntries()['entries'][0];

		return NULL;
	}
}
