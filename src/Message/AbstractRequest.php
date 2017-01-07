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
	public $liveEndpoint = 'api.affirm.com/api/';
	public $testEndpoint = 'sandbox.affirm.com/api/';


	public function getEndpoint()
	{
		$base = $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;

		return $base . '/' . self::API_VERSION;
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
		// Stripe only accepts TLS >= v1.2, so make sure Curl is told
		$config                          = $this->httpClient->getConfig();
		$curlOptions                     = $config->get( 'curl.options' );
		$curlOptions[CURLOPT_SSLVERSION] = 6;
		$config->set( 'curl.options', $curlOptions );
		$this->httpClient->setConfig( $config );

		// don't throw exceptions for 4xx errors
		$this->httpClient->getEventDispatcher()->addListener(
			'request.error',
			function ( $event ) {
				if ( $event['response']->isClientError() ) {
					$event->stopPropagation();
				}
			}
		);

		$httpRequest  = $this->httpClient->createRequest(
			$this->getHttpMethod(),
			$this->getEndpoint(),
			NULL,
			$data
		);
		$httpResponse = $httpRequest
			->setHeader( 'Authorization', 'Basic ' . base64_encode( $this->getApiKey() . ':' ) )
			->send();

		$this->response = new Response( $this, $httpResponse->json() );

		if ( $httpResponse->hasHeader( 'Request-Id' ) ) {
			$this->response->setRequestId( (string) $httpResponse->getHeader( 'Request-Id' ) );
		}

		return $this->response;
	}

	/**
	 * @return mixed
	 */
	public function getSource()
	{
		return $this->getParameter( 'source' );
	}

	/**
	 * @param $value
	 *
	 * @return AbstractRequest provides a fluent interface.
	 */
	public function setSource( $value )
	{
		return $this->setParameter( 'source', $value );
	}


	/**
	 * Get the card data.
	 *
	 * Because the stripe gateway uses a common format for passing
	 * card data to the API, this function can be called to get the
	 * data from the associated card object in the format that the
	 * API requires.
	 *
	 * @return array
	 */
	protected function getCardData()
	{
		$card = $this->getCard();
		$card->validate();

		$data              = [];
		$data['object']    = 'card';
		$data['number']    = $card->getNumber();
		$data['exp_month'] = $card->getExpiryMonth();
		$data['exp_year']  = $card->getExpiryYear();
		if ( $card->getCvv() ) {
			$data['cvc'] = $card->getCvv();
		}
		$data['name']            = $card->getName();
		$data['address_line1']   = $card->getAddress1();
		$data['address_line2']   = $card->getAddress2();
		$data['address_city']    = $card->getCity();
		$data['address_zip']     = $card->getPostcode();
		$data['address_state']   = $card->getState();
		$data['address_country'] = $card->getCountry();
		$data['email']           = $card->getEmail();

		return $data;
	}
}
