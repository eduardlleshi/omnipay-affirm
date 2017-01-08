<?php
namespace Omnipay\Affirm;

use Omnipay\Common\AbstractGateway;

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

	public function getPublicKey()
	{
		return $this->getParameter( 'publicKey' );
	}

	public function setPublicKey( $value )
	{
		return $this->setParameter( 'publicKey', $value );
	}

	public function getPrivateKey()
	{
		return $this->getParameter( 'privateKey' );
	}

	public function setPrivateKey( $value )
	{
		return $this->setParameter( 'privateKey', $value );
	}

	public function getProductKey()
	{
		return $this->getParameter( 'productKey' );
	}

	public function setProductKey( $value )
	{
		return $this->setParameter( 'productKey', $value );
	}


	public function authorize( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\AuthorizeRequest', $parameters );
	}

	public function capture( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\CaptureRequest', $parameters );
	}

	public function fetch( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\FetchRequest', $parameters );
	}

	public function void( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\VoidRequest', $parameters );
	}

	public function refund( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\RefundRequest', $parameters );
	}

	public function update( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\UpdateRequest', $parameters );
	}
}
