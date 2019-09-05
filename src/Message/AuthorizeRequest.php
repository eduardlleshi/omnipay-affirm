<?php

/**
 * Affirm Authorize Request
 */

namespace Omnipay\Affirm\Message;

/**
 * Affirm Authorize Request
 *
 * Authorizes and retrives the transactionReference charge.
 * checkout_token is required to identify an authorization.
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
 *    'transaction_id' => $transaction_id,
 *    'order_id'       => $order_id //optional - sets an internal order_id to the charge
 * ];
 *
 * $response = $gateway->authorize( $options )->send();
 *
 * if ( $response->isSuccessful() ) {
 *     var_dump( 'success', $response->getData());
 * } else {
 *     var_dump( 'fail', $response->getMessage() );
 * }
 * </code>
 *
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#Authorize_a_charge
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states
 */
class AuthorizeRequest extends AbstractRequest
{
	/**
	 * Prepare the data that the endpoint requests
	 *
	 * @return array
	 */
	public function getData()
	{
		$this->validate( 'transaction_id' );

		$data = [];

		$data['transaction_id'] = $this->getTransactionId();
		if ( $this->getOrderId() ) {
			$data['order_id'] = $this->getOrderId();
		}

		return $data;
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

		return $base . '/transactions';
	}

	/**
	 * Create the response object with the returned data.
	 *
	 * @param $data
	 * @param $statusCode
	 *
	 * @return AuthorizeResponse
	 */
	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new AuthorizeResponse( $this, $data, $statusCode );
	}
}
