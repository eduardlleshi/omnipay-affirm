<?php
namespace Omnipay\Affirm\Message;

class VoidRequest extends AbstractRequest
{
	public function getTransactionReference()
	{
		return $this->getParameter( 'transactionReference' );
	}

	public function getData()
	{
		$this->validate( 'transactionReference' );
		$data = [];

		return $data;
	}

	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/void';
	}

	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new VoidResponse( $this, $data, $statusCode );
	}
}