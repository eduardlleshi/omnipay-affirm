<?php
namespace Omnipay\Affirm\Message;

class AuthorizeRequest extends AbstractRequest
{
	/**
	 * @return mixed
	 */
	public function getCheckoutToken()
	{
		return $this->getParameter( 'checkout_token' );
	}


	public function getData()
	{
		$this->validate( 'checkout_token' );

		$data = [];

		$data['checkout_token'] = $this->getCheckoutToken();

		return $data;
	}

	public function getEndpoint()
	{
		return parent::getEndpoint() . '/charges';
	}

	protected function createResponse( $data, $statusCode )
	{
		return $this->response = new AuthorizeResponse( $this, $data, $statusCode );
	}
}
