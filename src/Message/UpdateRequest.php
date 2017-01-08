<?php
namespace Omnipay\Affirm\Message;

class UpdateRequest extends AbstractRequest
{
	public function getTransactionReference()
	{
		return $this->getParameter( 'transactionReference' );
	}

	public function setShippingCarrier( $value )
	{
		return $this->setParameter( 'shipping_carrier', $value );
	}

	public function getShippingCarrier()
	{
		return $this->getParameter( 'shipping_carrier' );
	}

	public function setTrackingNumber( $value )
	{
		return $this->setParameter( 'tracking_number', $value );
	}

	public function getTrackingNumber()
	{
		return $this->getParameter( 'tracking_number' );
	}


	public function getData()
	{
		$this->validate( 'transactionReference' );

		$data = [];

		if ( $this->getCard() ) {
			if ( $this->getCard()->getFirstName() && $this->getCard()->getLastName() )
				$data['shipping']['name']['full'] = $this->getCard()->getFirstName() . ' ' . $this->getCard()->getLastName();

			if ( $this->getCard()->getShippingCity() )
				$data['shipping']['address']['city'] = $this->getCard()->getShippingCity();

			if ( $this->getCard()->getShippingState() )
				$data['shipping']['address']['state'] = $this->getCard()->getShippingState();

			if ( $this->getCard()->getShippingPostcode() )
				$data['shipping']['address']['zipcode'] = (string) $this->getCard()->getShippingPostcode();

			if ( $this->getCard()->getShippingAddress1() )
				$data['shipping']['address']['line1'] = $this->getCard()->getShippingAddress1();

			if ( $this->getCard()->getShippingCountry() )
				$data['shipping']['address']['country'] = $this->getCard()->getShippingCountry();
		}

		if ( $this->getOrderId() )
			$data['order_id'] = $this->getOrderId();

		if ( $this->getShippingCarrier() )
			$data['shipping_carrier'] = $this->getShippingCarrier();

		if ( $this->getTrackingNumber() )
			$data['shipping_confirmation'] = $this->getTrackingNumber();

		return $data;
	}

	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/update';
	}

	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new UpdateResponse( $this, $data, $statusCode );
	}
}