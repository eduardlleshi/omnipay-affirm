<?php

/**
 * Affirm Fetch Request
 */

namespace Omnipay\Affirm\Message;

use Exception;

/**
 * Class Fetch Request
 *
 * Fetches a single or list of transactions.
 *
 * EXAMPLE
 * <code>
 * $gateway = Omnipay::create( 'Affirm' );
 * $gateway->setPublicKey( 'AFFIRM_PUBLIC_KEY' );
 * $gateway->setPrivateKey( 'AFFIRM_PRIVATE_KEY' );
 * $gateway->setProductKey( 'AFFIRM_PRODUCT_KEY' );
 * $gateway->setTestMode( 'AFFIRM_TEST' );
 *
 * $options            = [
 *    'transactionReference' => $charge_id, //optional - if not filled all transactions will be listed
 *    'limit'                => 10, //optional - nr of results retrieved
 *    'before'               => $before_charge_id, //optional - show transactions that happened before this transaction
 *    'after'                => $afteR_charge_id //optional - show transactions that happened after this transactionw
 * ];
 *
 * $response = $gateway->fetch( $options )->send();
 *
 * if ( $response->isSuccessful() ) {
 *     var_dump( 'success', $response->getData());
 *     var_dump($response->getEntries()); // all entries
 *     var_dump($response->getFirstEntry()); // first entry from the list
 * } else {
 *     var_dump( 'fail', $response->getMessage() );
 * }
 * </code>
 *
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#Read
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states
 */
class FetchRequest extends AbstractRequest
{
	/**
	 * Get transactionReference (optional) parameter
	 *
	 * @return mixed|null
	 */
	public function getTransactionReference()
	{
		return $this->getParameter( 'transactionReference' ) ? $this->getParameter( 'transactionReference' ) : NULL;
	}

	/**
	 * Get limit param
	 *
	 * @return mixed
	 */
	public function getLimit()
	{
		return $this->getParameter( 'limit' );
	}

	/**
	 * Set limit param. Limits the retrieved transactions - can be used in combination with above/after to create a pagination
	 *
	 * @param $value
	 *
	 * @return \Omnipay\Common\Message\AbstractRequest
	 */
	public function setLimit( $value )
	{
		return $this->setParameter( 'limit', $value );
	}

	/**
	 * Get before param (needs to be an existing charge_id) Filters transactions that happened before a specific charge.
	 *
	 * @return mixed
	 */
	public function getBefore()
	{
		return $this->getParameter( 'before' );
	}

	/**
	 * Set before parameter.
	 *
	 * @param $value
	 *
	 * @return \Omnipay\Common\Message\AbstractRequest
	 */
	public function setBefore( $value )
	{
		return $this->setParameter( 'before', $value );
	}

	/**
	 * Get after param (needs to be an existing charge_id) Filters transactions that happened after a specific charge.
	 *
	 * @return mixed
	 */
	public function getAfter()
	{
		return $this->getParameter( 'after' );
	}

	/**
	 * Set after parameter.
	 *
	 * @param $value
	 *
	 * @return \Omnipay\Common\Message\AbstractRequest
	 */
	public function setAfter( $value )
	{
		return $this->setParameter( 'after', $value );
	}


	/**
	 * Prepare the data that the endpoint requests
	 *
	 * @return array
	 */
	public function getData()
	{
		$data = [];

		return $data;
	}

	/**
	 * Make the HTTP request to Affirm endpoints
	 * Need to override the parent function since we are sending a GET request and removing the POST params
	 * @param mixed $data
	 *
	 * @return FetchResponse|FetchResponse
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

		$httpRequest = $this->httpClient->createRequest( $this->getHttpMethod(), $this->getEndpoint(), NULL, NULL );
		$httpRequest->getCurlOptions()->set( CURLOPT_SSLVERSION, 6 ); // CURL_SSLVERSION_TLSv1_2 for libcurl < 7.35
		$httpRequest->getCurlOptions()->set( CURLOPT_USERPWD, $this->getPublicKey() . ':' . $this->getPrivateKey() );
		$httpRequest->getCurlOptions()->set( CURLOPT_RETURNTRANSFER, 1 );

		try {
			$httpResponse        = $httpRequest->setHeader( 'Content-Type', 'application/json' )->send();
			$jsonToArrayResponse = !empty( $httpResponse->getBody( true ) ) ? $httpResponse->json() : [];
		} catch ( Exception $e ) {
			d( $e->getMessage() );

			return $this->createResponse( [], $e->getCode() );
		}

		return $this->createResponse( $jsonToArrayResponse, $httpResponse->getStatusCode() );
	}

	/**
	 * Get HTTP Method.
	 *
	 * @return string
	 */
	public function getHttpMethod()
	{
		return 'GET';
	}

	/**
	 * Get endpoint URI to make the request
	 *
	 * @return string
	 */
	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . $this->getQueryString();
	}

	/**
	 * Build query string used to filter the transaction list
	 *
	 * @return null|string
	 */
	public function getQueryString()
	{
		$query_string = [];
		if ( $this->getLimit() )
			$query_string['limit'] = $this->getLimit();

		if ( $this->getBefore() )
			$query_string['before'] = $this->getBefore();

		if ( $this->getAfter() )
			$query_string['after'] = $this->getAfter();

		if ( count( $query_string ) )
			$query_string_builded = '?' . http_build_query( $query_string, '', '&amp;' );
		else
			$query_string_builded = NULL;


		return $query_string_builded;
	}

	/**
	 * Create the response object with the returned data.
	 *
	 * @param $data
	 * @param $statusCode
	 *
	 * @return FetchResponse
	 */
	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new FetchResponse( $this, $data, $statusCode );
	}
}
