<?php

/**
 * Affirm Capture Request
 */

namespace Omnipay\Affirm\Message;

/**
 * Affirm Capture Request
 *
 * Capture a charge. order_id is required to match the charge.
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
 *    'checkout_token'       => $checkout_token,
 *    'order_id'             => $order_id, //optional - internal order_id
 *    'shipping_carrier'     => 'USP', //optional
 *    'tracking_number'      => '123456789' //optional
 * ];
 *
 * $response = $gateway->capture( $options )->send();
 *
 * if ( $response->isSuccessful() ) {
 *     var_dump( 'success', $response->getData());
 * } else {
 *     var_dump( 'fail', $response->getMessage() );
 * }
 * </code>
 *
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#Capture
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states
 */
class CaptureRequest extends AbstractRequest
{

	/**
	 * Get shipping_carrier param
	 *
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
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/capture';
	}

	/**
	 * Create the response object with the returned data.
	 *
	 * @param $data
	 * @param $statusCode
	 *
	 * @return CaptureResponse
	 */
	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new CaptureResponse( $this, $data, $statusCode );
	}
}
