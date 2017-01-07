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
		return $this->response = new CaptureResponse( $this, $data, $statusCode );
	}
}
