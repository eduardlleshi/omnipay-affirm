<?php
namespace Omnipay\Affirm\Message;

class FetchRequest extends AbstractRequest
{
	public function getTransactionReference()
	{
		return $this->getParameter( 'transactionReference' ) ? $this->getParameter( 'transactionReference' ) : NULL;
	}

	public function getLimit()
	{
		return $this->getParameter( 'limit' );
	}

	public function setLimit( $value )
	{
		return $this->setParameter( 'limit', $value );
	}

	public function getBefore()
	{
		return $this->getParameter( 'before' );
	}

	public function setBefore( $value )
	{
		return $this->setParameter( 'before', $value );
	}

	public function getAfter()
	{
		return $this->getParameter( 'after' );
	}

	public function setAfter( $value )
	{
		return $this->setParameter( 'after', $value );
	}


	public function getData()
	{
		$data = [];

		return $data;
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

		$httpRequest = $this->httpClient->createRequest( $this->getHttpMethod(), $this->getEndpoint(), NULL, NULL );
		$httpRequest->getCurlOptions()->set( CURLOPT_SSLVERSION, 6 ); // CURL_SSLVERSION_TLSv1_2 for libcurl < 7.35
		$httpRequest->getCurlOptions()->set( CURLOPT_USERPWD, $this->getPublicKey() . ':' . $this->getPrivateKey() );
		$httpRequest->getCurlOptions()->set( CURLOPT_RETURNTRANSFER, 1 );

		try {
			$httpResponse = $httpRequest->setHeader( 'Content-Type', 'application/json' )->send();
			dd( $this->getEndpoint(), $httpResponse->getBody( true ) );

			$jsonToArrayResponse = !empty( $httpResponse->getBody( true ) ) ? $httpResponse->json() : [];
		} catch ( \Exception $e ) {
			d( $e->getMessage() );

			return $this->createResponse( [], $e->getCode() );
		}

		return $this->createResponse( $jsonToArrayResponse, $httpResponse->getStatusCode() );
	}

	public function getHttpMethod()
	{
		return 'GET';
	}

	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . $this->getQueryString();
	}

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

	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new FetchResponse( $this, $data, $statusCode );
	}
}
