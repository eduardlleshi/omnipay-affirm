<?php
namespace Omnipay\Affirm\Message;

class FetchRequest extends AbstractRequest
{
	public function getTransactionReference()
	{
		return $this->getParameter( 'transactionReference' ) ? $this->getParameter( 'transactionReference' ) : NULL;
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
			$httpResponse        = $httpRequest->setHeader( 'Content-Type', 'application/json' )->send();
			$jsonToArrayResponse = !empty( $httpResponse->getBody() ) ? $httpResponse->json() : [];
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
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference();
	}

	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new FetchResponse( $this, $data, $statusCode );
	}
}
