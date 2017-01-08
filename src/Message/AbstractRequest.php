<?php

/**
 * Affirm Abstract Request
 */

namespace Omnipay\Affirm\Message;

/**
 * Affirm Abstract Request
 *
 * This class forms the base class for all request sent to Affirm endpoints
 *
 * @link https://docs.affirm.com/Integrate_Affirm/Direct_API
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
	/**
	 * Define the current API version of Affirm
	 */
	const API_VERSION = 'v2';
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
	 * Get endpoint to make calls
	 * @return string
	 */
	public function getEndpoint()
	{
		$base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;

		return $base . self::API_VERSION;
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
	 * @return \Omnipay\Common\Message\AbstractRequest
	 */
	public function setCheckoutToken( $value )
	{
		return $this->setParameter( 'checkout_token', $value );
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
	 * @return \Omnipay\Common\Message\AbstractRequest
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
	 * @return \Omnipay\Common\Message\AbstractRequest
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
	 * @return \Omnipay\Common\Message\AbstractRequest
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
	 * @return \Omnipay\Common\Message\AbstractRequest
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

		// don't throw exceptions for 4xx errors
		$this->httpClient->getEventDispatcher()->addListener(
			'request.error',
			function ( $event ) {
				if ( $event['response']->isClientError() ) {
					$event->stopPropagation();
				}
			}
		);

		// if there are no data to be sent, send null value
		if ( !empty( $data ) && count( $data ) )
			$json_data = json_encode( $data );
		else
			$json_data = NULL;

		$httpRequest = $this->httpClient->createRequest( $this->getHttpMethod(), $this->getEndpoint(), NULL, $json_data );
		$httpRequest->getCurlOptions()->set( CURLOPT_SSLVERSION, 6 );
		$httpRequest->getCurlOptions()->set( CURLOPT_USERPWD, $this->getPublicKey() . ':' . $this->getPrivateKey() );
		$httpRequest->getCurlOptions()->set( CURLOPT_POSTFIELDS, $json_data );

		$httpResponse        = $httpRequest->setHeader( 'Content-Type', 'application/json' )->setHeader( 'Content-Length', strlen( $json_data ) )->send();
		$jsonToArrayResponse = !empty( $httpResponse->getBody( true ) ) ? $httpResponse->json() : [];

		return $this->createResponse( $jsonToArrayResponse, $httpResponse->getStatusCode() );
	}

	/**
	 * Generate the Response class with the returning data from sendData
	 *
	 * @param $data
	 * @param $httpStatusCode
	 *
	 * @return AuthorizeResponse
	 */
	protected function createResponse( $data, $httpStatusCode )
	{
		return $this->response = new AuthorizeResponse( $this, $data, $httpStatusCode );
	}
}
