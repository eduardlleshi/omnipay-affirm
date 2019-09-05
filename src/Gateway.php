<?php

/**
 * Affirm Gateway
 */

namespace Omnipay\Affirm;

use Omnipay\Affirm\Message\AuthorizeRequest;
use Omnipay\Affirm\Message\CaptureRequest;
use Omnipay\Affirm\Message\FetchRequest;
use Omnipay\Affirm\Message\RefundRequest;
use Omnipay\Affirm\Message\UpdateRequest;
use Omnipay\Affirm\Message\VoidRequest;
use Omnipay\Common\AbstractGateway;

//TODO: add sample code

class Gateway extends AbstractGateway
{

	public function getName()
	{
		return 'Affirm';
	}

	/**
	 * Get the gateway parameters.
	 *
	 * @return array
	 */
	public function getDefaultParameters()
	{
		return [
			'publicKey'  => '',
			'privateKey' => '',
			'productKey' => '',
			'testMode'   => false,
		];
	}

	/**
	 * Get the Public API Key.
	 *
	 * Authentication to gateway is done by pairing Public and Private keys
	 *
	 * @return string
	 */
	public function getPublicKey()
	{
		return $this->getParameter( 'publicKey' );
	}

	/**
	 * Set the Public API Key.
	 *
	 * @param string $value
	 *
	 * @return Gateway provides a fluent interface
	 */
	public function setPublicKey( $value )
	{
		return $this->setParameter( 'publicKey', $value );
	}

	/**
	 * Get the Private API Key.
	 *
	 * Authentication to gateway is done by pairing Public and Private keys
	 *
	 * @return string
	 */
	public function getPrivateKey()
	{
		return $this->getParameter( 'privateKey' );
	}

	/**
	 * Set the Private API Key.
	 *
	 * @param string $value
	 *
	 * @return Gateway provides a fluent interface
	 */
	public function setPrivateKey( $value )
	{
		return $this->setParameter( 'privateKey', $value );
	}

	/**
	 * Get the Product Key.
	 *
	 * @return string
	 */
	public function getProductKey()
	{
		return $this->getParameter( 'productKey' );
	}

	/**
	 * Set the Product API Key.
	 *
	 * @param string $value
	 *
	 * @return Gateway provides a fluent interface
	 */
	public function setProductKey( $value )
	{
		return $this->setParameter( 'productKey', $value );
	}

	/**
	 * Authorize Request.
	 *
	 * An Authorize request is similar to a purchase request but the
	 * charge issues an authorization (or pre-authorization), and no money
	 * is transferred.  The transaction will need to be captured later
	 * in order to effect payment. Uncaptured charges expire in 30 days.
	 *
	 * checkout_token is required to identify and authorize a charge
	 *
	 * @param array $parameters
	 *
	 * @return AuthorizeRequest
	 */
	public function authorize( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\AuthorizeRequest', $parameters );
	}

	/**
	 * Capture Request.
	 *
	 * Use this request to capture and process a previously created authorization.
	 *
	 * transactionReferece is required in order to capture the request
	 *
	 * @param array $parameters
	 *
	 * @return CaptureRequest
	 */
	public function capture( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\CaptureRequest', $parameters );
	}

	/**
	 * Fetch a single or list of transactions.
	 *
	 * @param array $parameters
	 *
	 * @return FetchRequest
	 */
	public function fetch( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\FetchRequest', $parameters );
	}

	/**
	 * Void a transaction
	 *
	 * NOTE: transaction cannot be charged if it has been voided.
	 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states for a list of statuses where this action can be applied.
	 *
	 * @param array $parameters
	 *
	 * @return VoidRequest
	 */
	public function void( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\VoidRequest', $parameters );
	}

	/**
	 * Refund Request
	 *
	 * TODO: Currently not working as intended.
	 *
	 * You may do a partial or full refund. To do a partial refund you can include the amount field.
	 *
	 * You can apply any amount of refunds on a charge so long as there is a positive balance on the loan.
	 * Once a loan is fully refunded it cannot be reinstated.
	 * Refunds can only be applied within 120 days of capturing the charge.
	 * The amount you refund is based upon the original purchase price, just as if it was a normal credit card transaction.
	 * Interest and fees corresponding to the refunded amount are all automatically calculated on Affirm's end.
	 *
	 * @see https://docs.affirm.com/Integrate_Affirm/Direct_API#charge_states for a list of statuses where this action can be applied.
	 *
	 * @param array $parameters
	 *
	 * @return RefundRequest
	 */
	public function refund( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\RefundRequest', $parameters );
	}

	/**
	 * Update Request
	 *
	 * Updates customer shipping (address, tracking info, carrier) and internal order_id. All params are optional
	 *
	 * @param array $parameters
	 *
	 * @return UpdateRequest
	 */
	public function update( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\UpdateRequest', $parameters );
	}
}