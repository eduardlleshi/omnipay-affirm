<?php

/**
 * Stripe Gateway.
 */
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

	/**
	 * Capture Request.
	 *
	 * Use this request to capture and process a previously created authorization.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Affirm\Message\CaptureRequest
	 */
	public function capture( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\CaptureRequest', $parameters );
	}



	public function fetch( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Affirm\Message\FetchRequest', $parameters );
	}

	/**
	 * Purchase request.
	 *
	 * To charge a credit card, you create a new charge object. If your API key
	 * is in test mode, the supplied card won't actually be charged, though
	 * everything else will occur as if in live mode. (Stripe assumes that the
	 * charge would have completed successfully).
	 *
	 * Either a customerReference or a card is required.  If a customerReference
	 * is passed in then the cardReference must be the reference of a card
	 * assigned to the customer.  Otherwise, if you do not pass a customer ID,
	 * the card you provide must either be a token, like the ones returned by
	 * Stripe.js, or a dictionary containing a user's credit card details.
	 *
	 * IN OTHER WORDS: You cannot just pass a card reference into this request,
	 * you must also provide a customer reference if you want to use a stored
	 * card.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\PurchaseRequest
	 */
	public function purchase( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\PurchaseRequest', $parameters );
	}

	/**
	 * Refund Request.
	 *
	 * When you create a new refund, you must specify a
	 * charge to create it on.
	 *
	 * Creating a new refund will refund a charge that has
	 * previously been created but not yet refunded. Funds will
	 * be refunded to the credit or debit card that was originally
	 * charged. The fees you were originally charged are also
	 * refunded.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\RefundRequest
	 */
	public function refund( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\RefundRequest', $parameters );
	}

	/**
	 * Fetch Transaction Request.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\VoidRequest
	 */
	public function void( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\VoidRequest', $parameters );
	}

	/**
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchBalanceTransactionRequest
	 */
	public function fetchBalanceTransaction( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchBalanceTransactionRequest', $parameters );
	}

	//
	// Cards
	// @link https://stripe.com/docs/api#cards
	//

	/**
	 * Create Card.
	 *
	 * This call can be used to create a new customer or add a card
	 * to an existing customer.  If a customerReference is passed in then
	 * a card is added to an existing customer.  If there is no
	 * customerReference passed in then a new customer is created.  The
	 * response in that case will then contain both a customer token
	 * and a card token, and is essentially the same as CreateCustomerRequest
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CreateCardRequest
	 */
	public function createCard( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\CreateCardRequest', $parameters );
	}

	/**
	 * Update Card.
	 *
	 * If you need to update only some card details, like the billing
	 * address or expiration date, you can do so without having to re-enter
	 * the full card details. Stripe also works directly with card networks
	 * so that your customers can continue using your service without
	 * interruption.
	 *
	 * When you update a card, Stripe will automatically validate the card.
	 *
	 * This requires both a customerReference and a cardReference.
	 *
	 * @link https://stripe.com/docs/api#update_card
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\UpdateCardRequest
	 */
	public function updateCard( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\UpdateCardRequest', $parameters );
	}

	/**
	 * Delete a card.
	 *
	 * This is normally used to delete a credit card from an existing
	 * customer.
	 *
	 * You can delete cards from a customer or recipient. If you delete a
	 * card that is currently the default card on a customer or recipient,
	 * the most recently added card will be used as the new default. If you
	 * delete the last remaining card on a customer or recipient, the
	 * default_card attribute on the card's owner will become null.
	 *
	 * Note that for cards belonging to customers, you may want to prevent
	 * customers on paid subscriptions from deleting all cards on file so
	 * that there is at least one default card for the next invoice payment
	 * attempt.
	 *
	 * In deference to the previous incarnation of this gateway, where
	 * all CreateCard requests added a new customer and the customer ID
	 * was used as the card ID, if a cardReference is passed in but no
	 * customerReference then we assume that the cardReference is in fact
	 * a customerReference and delete the customer.  This might be
	 * dangerous but it's the best way to ensure backwards compatibility.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\DeleteCardRequest
	 */
	public function deleteCard( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\DeleteCardRequest', $parameters );
	}

	//
	// Customers
	// link: https://stripe.com/docs/api#customers
	//

	/**
	 * Create Customer.
	 *
	 * Customer objects allow you to perform recurring charges and
	 * track multiple charges that are associated with the same customer.
	 * The API allows you to create, delete, and update your customers.
	 * You can retrieve individual customers as well as a list of all of
	 * your customers.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CreateCustomerRequest
	 */
	public function createCustomer( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\CreateCustomerRequest', $parameters );
	}

	/**
	 * Fetch Customer.
	 *
	 * Fetches customer by customer reference.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CreateCustomerRequest
	 */
	public function fetchCustomer( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchCustomerRequest', $parameters );
	}

	/**
	 * Update Customer.
	 *
	 * This request updates the specified customer by setting the values
	 * of the parameters passed. Any parameters not provided will be left
	 * unchanged. For example, if you pass the card parameter, that becomes
	 * the customer's active card to be used for all charges in the future,
	 * and the customer email address is updated to the email address
	 * on the card. When you update a customer to a new valid card: for
	 * each of the customer's current subscriptions, if the subscription
	 * is in the `past_due` state, then the latest unpaid, unclosed
	 * invoice for the subscription will be retried (note that this retry
	 * will not count as an automatic retry, and will not affect the next
	 * regularly scheduled payment for the invoice). (Note also that no
	 * invoices pertaining to subscriptions in the `unpaid` state, or
	 * invoices pertaining to canceled subscriptions, will be retried as
	 * a result of updating the customer's card.)
	 *
	 * This request accepts mostly the same arguments as the customer
	 * creation call.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CreateCustomerRequest
	 */
	public function updateCustomer( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\UpdateCustomerRequest', $parameters );
	}

	/**
	 * Delete a customer.
	 *
	 * Permanently deletes a customer. It cannot be undone. Also immediately
	 * cancels any active subscriptions on the customer.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\DeleteCustomerRequest
	 */
	public function deleteCustomer( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\DeleteCustomerRequest', $parameters );
	}

	//
	// Tokens
	// @link https://stripe.com/docs/api#tokens
	//
	// This gateway does not currently have a CreateToken message.  In
	// any case tokens are probably not what you are looking for because
	// they are single use.  You probably want to create a Customer or
	// Card reference instead.  This function is left here for further
	// expansion.
	//

	/**
	 * Stripe Fetch Token Request.
	 *
	 * Often you want to be able to charge credit cards or send payments
	 * to bank accounts without having to hold sensitive card information
	 * on your own servers. Stripe.js makes this easy in the browser, but
	 * you can use the same technique in other environments with our token API.
	 *
	 * Tokens can be created with your publishable API key, which can safely
	 * be embedded in downloadable applications like iPhone and Android apps.
	 * You can then use a token anywhere in our API that a card or bank account
	 * is accepted. Note that tokens are not meant to be stored or used more
	 * than once—to store these details for use later, you should create
	 * Customer or Recipient objects.
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchTokenRequest
	 */
	public function fetchToken( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchTokenRequest', $parameters );
	}

	/**
	 * Create Plan
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CreatePlanRequest
	 */
	public function createPlan( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\CreatePlanRequest', $parameters );
	}

	/**
	 * Fetch Plan
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchPlanRequest
	 */
	public function fetchPlan( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchPlanRequest', $parameters );
	}

	/**
	 * Delete Plan
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\DeletePlanRequest
	 */
	public function deletePlan( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\DeletePlanRequest', $parameters );
	}

	/**
	 * Create Subscription
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CreateSubscriptionRequest
	 */
	public function createSubscription( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\CreateSubscriptionRequest', $parameters );
	}

	/**
	 * Fetch Subscription
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchSubscriptionRequest
	 */
	public function fetchSubscription( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchSubscriptionRequest', $parameters );
	}

	/**
	 * Update Subscription
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\UpdateSubscriptionRequest
	 */
	public function updateSubscription( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\UpdateSubscriptionRequest', $parameters );
	}

	/**
	 * Cancel Subscription
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CancelSubscriptionRequest
	 */
	public function cancelSubscription( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\CancelSubscriptionRequest', $parameters );
	}

	/**
	 * Fetch Event
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchEventRequest
	 */
	public function fetchEvent( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchEventRequest', $parameters );
	}

	/**
	 * Fetch Invoice Lines
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchInvoiceLinesRequest
	 */
	public function fetchInvoiceLines( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchInvoiceLinesRequest', $parameters );
	}

	/**
	 * Fetch Invoice
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchInvoiceRequest
	 */
	public function fetchInvoice( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchInvoiceRequest', $parameters );
	}

	/**
	 * List Invoices
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\ListInvoicesRequest
	 */
	public function listInvoices( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\ListInvoicesRequest', $parameters );
	}

	/**
	 * Create Invoice Item
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\CreateInvoiceItemRequest
	 */
	public function createInvoiceItem( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\CreateInvoiceItemRequest', $parameters );
	}

	/**
	 * Fetch Invoice Item
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\FetchInvoiceItemRequest
	 */
	public function fetchInvoiceItem( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\FetchInvoiceItemRequest', $parameters );
	}

	/**
	 * Delete Invoice Item
	 *
	 * @param array $parameters
	 *
	 * @return \Omnipay\Stripe\Message\DeleteInvoiceItemRequest
	 */
	public function deleteInvoiceItem( array $parameters = [] )
	{
		return $this->createRequest( '\Omnipay\Stripe\Message\DeleteInvoiceItemRequest', $parameters );
	}
}
