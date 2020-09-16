<?php

/**
 * Affirm Fetch Request
 */

namespace Omnipay\Affirm\Message;

use Exception;

/**
 * Class Fetch Request
 *
 * Fetches a single or list of transactions.
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
 *    'transactionReference' => $charge_id, //optional - if not filled all transactions will be listed
 *    'expand'               => 'checkout', //optional - nr of results retrieved
 * ];
 *
 * $response = $gateway->fetch( $options )->send();
 *
 * if ( $response->isSuccessful() ) {
 *     var_dump( 'success', $response->getData());
 *     var_dump($response->getEntries()); // all entries
 *     var_dump($response->getFirstEntry()); // first entry from the list
 * } else {
 *     var_dump( 'fail', $response->getMessage() );
 * }
 * </code>
 *
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#Read
 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states
 */
class FetchRequest extends AbstractRequest
{

	/**
	 * Prepare the data that the endpoint requests
	 *
	 * @return array
	 */
	public function getData()
	{
		return [];
	}

	/**
	 * Get HTTP Method.
	 *
	 * @return string
	 */
	public function getHttpMethod()
	{
		return 'GET';
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

		$query_string = NULL;
		if ( $this->getExpand() ) {
			$query_string = '?expand=' . $this->getExpand();
		}

		return $base . '/transactions/' . $this->getTransactionReference() . $query_string;
	}

	/**
	 * Create the response object with the returned data.
	 *
	 * @param $data
	 *
	 * @return FetchResponse
	 */
	protected function createResponse( $data )
	{
		return $this->response = new FetchResponse( $this, $data );
	}
}
