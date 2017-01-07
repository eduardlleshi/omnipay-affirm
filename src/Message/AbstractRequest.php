<?php

/**
 * Stripe Abstract Request.
 */
namespace Omnipay\Affirm\Message;

/**
 * Stripe Abstract Request.
 *
 * This is the parent class for all Stripe requests.
 *
 * Test modes:
 *
 * Stripe accounts have test-mode API keys as well as live-mode
 * API keys. These keys can be active at the same time. Data
 * created with test-mode credentials will never hit the credit
 * card networks and will never cost anyone money.
 *
 * Unlike some gateways, there is no test mode endpoint separate
 * to the live mode endpoint, the Stripe API endpoint is the same
 * for test and for live.
 *
 * Setting the testMode flag on this gateway has no effect.  To
 * use test mode just use your test mode API key.
 *
 * You can use any of the cards listed at https://stripe.com/docs/testing
 * for testing.
 *
 * @see \Omnipay\Stripe\Gateway
 * @link https://stripe.com/docs/api
 *
 * @method \Omnipay\Stripe\Message\Response send()
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
	/**
	 * Live or Test Endpoint URL.
	 *
	 * @var string URL
	 */
	const API_VERSION = 'v2';
	public $liveEndpoint = 'https://api.affirm.com/api/';
	public $testEndpoint = 'https://sandbox.affirm.com/api/';


	public function getEndpoint()
	{
		$base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;

		return $base . self::API_VERSION;
	}

	public function getCheckoutToken()
	{
		return $this->getParameter( 'checkout_token' );
	}

	public function setCheckoutToken( $value )
	{
		return $this->setParameter( 'checkout_token', $value );
	}

	public function getPublicKey()
	{
		return $this->getParameter( 'public_key' );
	}

	public function setPublicKey( $value )
	{
		return $this->setParameter( 'public_key', $value );
	}

	public function getPrivateKey()
	{
		return $this->getParameter( 'privateKey' );
	}

	public function setPrivateKey( $value )
	{
		return $this->setParameter( 'privateKey', $value );
	}

	public function getProductKey()
	{
		return $this->getParameter( 'productKey' );
	}

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

		$json_data = json_encode( $data );

		$httpRequest = $this->httpClient->createRequest(
			$this->getHttpMethod(),
			$this->getEndpoint(),
			NULL,
			$json_data
		);

		$httpRequest->getCurlOptions()->set( CURLOPT_SSLVERSION, 6 ); // CURL_SSLVERSION_TLSv1_2 for libcurl < 7.35
		$httpRequest->getCurlOptions()->set( CURLOPT_USERPWD, $this->getPublicKey() . ':' . $this->getPrivateKey() );
		$httpRequest->getCurlOptions()->set( CURLOPT_POSTFIELDS, $json_data );

//		dd( $httpRequest->getCurlOptions() );
		$httpResponse = $httpRequest
			->setHeader( 'Content-Type', 'application/json' )
			->setHeader( 'Content-Length', strlen( $json_data ) )
			->send();
		
		$jsonToArrayResponse = !empty( $httpResponse->getBody( true ) ) ? $httpResponse->json() : [];

		return $this->createResponse( $jsonToArrayResponse, $httpResponse->getStatusCode() );
	}


	protected function createResponse( $data, $httpStatusCode )
	{
		return $this->response = new Response( $this, $data, $httpStatusCode );
	}

}
