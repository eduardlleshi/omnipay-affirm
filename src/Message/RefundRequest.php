<?php

/**
 * Affirm Refund Request
 */

namespace Omnipay\Affirm\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class AuthorizeRequest
 *
 * @NOTE there are some issues with the refund producing "refund-exceeded" error on all calls.
 *
 * Makes a partial or full refund request.
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
 *    'amount'               => 10, // optional - the amount (in cents) to be refunded, if this param is not sent a full refund will happen.
 * ];
 *
 * $response = $gateway->refund( $options )->send();
 *
 * if ( $response->isSuccessful() ) {
 *     var_dump( 'success', $response->getData());
 * } else {
 *     var_dump( 'fail', $response->getMessage() );
 * }
 * </code>
 *
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#Refund
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states
 */
class RefundRequest extends AbstractRequest
{
	/**
	 * Prepare the data that the endpoint requests
	 *
	 * @return array
	 * @throws InvalidRequestException
	 */
	public function getData()
	{
		$this->validate( 'transactionReference' );
		if ( $this->getAmount() ) {
			$data['amount'] = (int) $this->getAmount();
		} else {
			$data = [];
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
		return parent::getEndpoint() . '/charges/' . $this->getTransactionReference() . '/refund';
	}

	/**
	 * Create the response object with the returned data.
	 *
	 * @param $data
	 *
	 * @return RefundResponse
	 */
	protected function createResponse( $data )
	{
		return $this->response = new RefundResponse( $this, $data );
	}
}
