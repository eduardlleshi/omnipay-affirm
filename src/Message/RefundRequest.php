<?php
namespace Omnipay\Affirm\Message;

class RefundRequest extends AbstractRequest
{
	public function getTransactionReference()
	{
		return $this->getParameter( 'transactionReference' );
	}

	public function getData()
	{
		$this->validate( 'transactionReference' );
		if ( $this->getAmount() ) {
			$data['amount'] = number_format( $this->getAmount(), 2 );
		} else {
			$data = [];
		}

		return $data;
	}

	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/refund';
	}

	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new RefundResponse( $this, $data, $statusCode );
	}
}