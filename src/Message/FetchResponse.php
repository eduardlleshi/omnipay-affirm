<?php
namespace Omnipay\Affirm\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * PayPal REST Authorize Response
 */
class FetchResponse extends AbstractResponse
{
	public function isSuccessful()
	{
		return empty( $this->data['error'] ) && $this->getCode() == 201 && $this->data['type'] != 'invalid_request';;
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

	public function getEntries()
	{
		if ( !isset( $this->data['entries'] ) ) {
			$data['entries'][] = $this->data;
		} else {
			$data = $this->data;
		}

		return $data;
	}

	public function getSingleEntry()
	{
		if ( isset( $this->getEntries()['entries'][0] ) )
			return $this->getEntries()['entries'][0];

		return NULL;
	}
}
