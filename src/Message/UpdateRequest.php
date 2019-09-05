<?php

/**
 * Affirm Update Request
 */

namespace Omnipay\Affirm\Message;

/**
 * Affirm update Request
 * Updates the shipping information of an order.
 *
 * EXAMPLE
 * <code>
 * $gateway = Omnipay::create( 'Affirm' );
 * $gateway->setPublicKey( 'AFFIRM_PUBLIC_KEY' );
 * $gateway->setPrivateKey( 'AFFIRM_PRIVATE_KEY' );
 * $gateway->setProductKey( 'AFFIRM_PRODUCT_KEY' );
 * $gateway->setTestMode( 'AFFIRM_TEST' );
 *
 * // create a card to send the shipping info - all @params are optional
 * $card = new CreditCard( [
 *      'firstName'        => 'Eduard',
 *      'lastName'         => 'Lleshi',
 *      'shippingAddress1' => '1 Main St',
 *      'shippingAddress2' => 'Line 2',
 *      'shippingCity'     => 'San Jose',
 *      'shippingPostcode' => 95131,
 *      'shippingState'    => 'CA',
 *      'shippingCountry'  => 'USA'
 * ] );
 *
 * $options         = [
 *     'transactionReference' => $charge_id,
 *     'card'                 => $card, //optional - shipping info
 *     'order_id'             => $order_id, //optional - sets an internal order_id to the charge
 *     'shipping_carrier'     => 'USP', //optional
 *     'tracking_number'      => '123456789' //optional
 * ];
 *
 * $response = $gateway->update( $options )->send();
 *
 * if ( $response->isSuccessful() ) {
 *     var_dump( 'success', $response->getData());
 * } else {
 *     var_dump( 'fail', $response->getMessage() );
 * }
 * </code>
 *
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#Update
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states
 */
class UpdateRequest extends AbstractRequest
{

	/**
	 * Get shipping_carrier param
	 * @return mixed
	 */
	public function getShippingCarrier()
	{
		return $this->getParameter( 'shipping_carrier' );
	}

	/**
	 * Set shipping_carrier param
	 *
	 * @param $value
	 *
	 * @return \Omnipay\Common\Message\AbstractRequest
	 */
	public function setShippingCarrier( $value )
	{
		return $this->setParameter( 'shipping_carrier', $value );
	}

	/**
	 * Get tracking_number param
	 *
	 * @return mixed
	 */
	public function getTrackingNumber()
	{
		return $this->getParameter( 'tracking_number' );
	}

	/**
	 * Set tracking_number param
	 *
	 * @param $value
	 *
	 * @return \Omnipay\Common\Message\AbstractRequest
	 */
	public function setTrackingNumber( $value )
	{
		return $this->setParameter( 'tracking_number', $value );
	}


	/**
	 * Prepare the data that the endpoint requests
	 *
	 * @return array
	 */
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

			if ( $this->getCard()->getShippingAddress2() )
				$data['shipping']['address']['line2'] = $this->getCard()->getShippingAddress2();

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

	/**
	 * Get endpoint URI to make the request
	 *
	 * @return string
	 */
	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/update';
	}

	/**
	 * Create the response object with the returned data.
	 *
	 * @param $data
	 * @param $statusCode
	 *
	 * @return UpdateResponse
	 */
	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new UpdateResponse( $this, $data, $statusCode );
	}
}