<?php

/**
 * Affirm Void Request
 */

namespace Omnipay\Affirm\Message;

/**
 * Affirm Void Request
 *
 * Voids a given charge by using transactionReference param.
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
 *    'transactionReference' => $charge_id,
 * ];
 *
 * $response = $gateway->void( $options )->send();
 *
 * if ( $response->isSuccessful() ) {
 *     var_dump( 'success', $response->getData());
 * } else {
 *     var_dump( 'fail', $response->getMessage() );
 * }
 * </code>
 *
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#Void
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states
 */
class VoidRequest extends AbstractRequest
{

	/**
	 * Prepare the data that the endpoint requests
	 *
	 * @return array
	 */
	public function getData()
	{
		$this->validate( 'transactionReference' );
		$data = [];

		return $data;
	}

	/**
	 * Get endpoint URI to make the request
	 *
	 * @return string
	 */
	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/void';
	}

	/**
	 * Generate the Response class with the returning data from sendData
	 *
	 * @param $data
	 * @param $statusCode
	 *
	 * @return VoidResponse
	 */
	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new VoidResponse( $this, $data, $statusCode );
	}
}