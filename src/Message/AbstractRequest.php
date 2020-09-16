<?php

/**
 * Affirm Abstract Request
 */

namespace Omnipay\Affirm\Message;

use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Affirm Abstract Request
 *
 * This class forms the base class for all request sent to Affirm endpoints
 *
 * If running on Sandbox mode there will be an additional debuggign array in every API response.
 *
 * @see https://docs.affirm.com/affirm-developers/reference#introduction (Direct API)
 * @see https://docs.affirm.com/affirm-developers/reference#transactions-api
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
	/**
	 * API V1 (Transaction API)
	 */
	const API_V1 = 'v1';

	/**
	 * API V2 (Direct API)
	 */
	const API_V2 = 'v2';
	/**
	 * Endpoint to make live/production calls
	 * @var string
	 */
	public $liveEndpoint = 'https://api.affirm.com/api/';
	/**
	 * Endpoint to make sandbox/test calls
	 * @var string
	 */
	public $testEndpoint = 'https://sandbox.affirm.com/api/';

	/**
	 * Set default version to be v2
	 * @var string
	 */
	public $api_version = self::API_V2;

	/**
	 * Get endpoint to make calls
	 * @return string
	 */
	public function getEndpoint()
	{
		$base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;

		return $base . $this->api_version;
	}

	/**
	 * Update the version on the fly
	 * @return $this
	 */
	public function useV1()
	{
		$this->api_version = self::API_V1;

		return $this;
	}

	/**
	 * Get checkout_token param
	 *
	 * @return mixed
	 */
	public function getCheckoutToken()
	{
		return $this->getParameter( 'checkout_token' );
	}

	/**
	 * Set checkout_token param
	 *
	 * @param $value
	 *
	 * @return BaseAbstractRequest
	 */
	public function setCheckoutToken( $value )
	{
		return $this->setParameter( 'checkout_token', $value );
	}

	/**
	 * Get transaction_id param
	 *
	 * @return mixed
	 */
	public function getTransactionId()
	{
		return $this->getParameter( 'transaction_id' );
	}

	/**
	 * Set transaction_id param
	 *
	 * @param $value
	 *
	 * @return BaseAbstractRequest
	 */
	public function setTransactionId( $value )
	{
		return $this->setParameter( 'transaction_id', $value );
	}

	/**
	 * Get transaction_id param
	 *
	 * @return mixed
	 */
	public function getExpand()
	{
		return $this->getParameter( 'expand' );
	}

	/**
	 * Set transaction_id param
	 *
	 * @param $value
	 *
	 * @return BaseAbstractRequest
	 */
	public function setExpand( $value )
	{
		return $this->setParameter( 'expand', $value );
	}

	/**
	 * Get order_id param
	 *
	 * @return mixed
	 */
	public function getOrderId()
	{
		return $this->getParameter( 'order_id' );
	}

	/**
	 * Set order_id param
	 *
	 * @param $value
	 *
	 * @return BaseAbstractRequest
	 */
	public function setOrderId( $value )
	{
		return $this->setParameter( 'order_id', $value );
	}

	/**
	 * Get public_ket param
	 *
	 * @return mixed
	 */
	public function getPublicKey()
	{
		return $this->getParameter( 'public_key' );
	}

	/**
	 * Set public_key param
	 *
	 * @param $value
	 *
	 * @return BaseAbstractRequest
	 */
	public function setPublicKey( $value )
	{
		return $this->setParameter( 'public_key', $value );
	}

	/**
	 * Get privateKey param
	 *
	 * @return mixed
	 */
	public function getPrivateKey()
	{
		return $this->getParameter( 'privateKey' );
	}

	/**
	 * Set privateKey param
	 *
	 * @param $value
	 *
	 * @return BaseAbstractRequest
	 */
	public function setPrivateKey( $value )
	{
		return $this->setParameter( 'privateKey', $value );
	}

	/**
	 * Get productKey param
	 *
	 * @return mixed
	 */
	public function getProductKey()
	{
		return $this->getParameter( 'productKey' );
	}

	/**
	 * Set productKey param
	 *
	 * @param $value
	 *
	 * @return BaseAbstractRequest
	 */
	public function setProductKey( $value )
	{
		return $this->setParameter( 'productKey', $value );
	}

	/**
	 * Get HTTP Method.
	 *
	 * This is nearly always POST but can be over-ridden in sub classes.
	 *
	 * @return string
	 */
	public function getHttpMethod()
	{
		return 'POST';
	}

	/**
	 * Make the HTTP request to Affirm endpoints
	 *
	 * @param mixed $data - parameters sent to Affirm endpoints
	 *
	 * @return AuthorizeResponse
	 */
	public function sendData( $data )
	{
		// if there are no data to be sent, send null value
		$json_body = NULL;
		if ( !empty( $data ) && count( $data ) ) {
			$json_body = json_encode( $data );
		}

		$headers = [
			'Authorization'  => 'Basic ' . base64_encode( $this->getPublicKey() . ':' . $this->getPrivateKey() ),
			'Accept'         => 'application/json',
			'Content-Type'   => 'application/json',
			'Content-Length' => strlen( $json_body )
		];

		$response = $this->httpClient->request( $this->getHttpMethod(), $this->getEndpoint(), $headers, $json_body );

		$data = json_decode( $response->getBody(), true );

		if ( $this->getTestMode() ) {
			$debug_log = [
				'sandbox_logs' => [
					'headers'  => $headers,
					'method'   => $this->getHttpMethod(),
					'endpoint' => $this->getEndpoint(),
					'body'     => $json_body,
					'response' => $data
				]
			];

			$data = array_merge( $data, $debug_log );
		}

		return $this->createResponse( $data );
	}

	/**
	 * Generate the Response class with the returning data from sendData
	 *
	 * @param $data
	 *
	 * @return AuthorizeResponse
	 */
	protected function createResponse( $data )
	{
		return $this->response = new AuthorizeResponse( $this, $data );
	}
}
