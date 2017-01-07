<?php
namespace Omnipay\Affirm\Message;

class CaptureRequest extends AbstractRequest
{
	public function getTransactionReference()
	{
		return $this->getParameter( 'transactionReference' );
	}

	public function getData()
	{
		$this->validate( 'transactionReference' );

		$data['order_id'] = $this->getTransactionReference();

		return $data;
	}

	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/capture';
	}

	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new CaptureResponse( $this, $data, $statusCode );
	}
}
