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
 *    'expand'               => 'checkout', //optional - nr of results retrieved
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
		parent::useV1();
		$base = parent::getEndpoint();

		$query_string = NULL;
		if ( $this->getExpand() ) {
			$query_string = '?expand=' . $this->getExpand();
		}

		return $base . '/transactions/' . $this->getTransactionReference() . $query_string;
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
