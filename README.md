# Omnipay: Affirm

**Affirm gateway for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Affirm support for Omnipay.

## Install

##### For Omnipay v3:

Instal the gateway using require. Require the `league/omnipay` base package and this gateway.

``` bash
$ composer require league/omnipay eduardlleshi/omnipay-affirm
```

##### For Omnipay v2:

Instal the gateway using require. Require the `league/omnipay` base package and this gateway.

``` bash
$ composer require league/omnipay:^2.0 eduardlleshi/omnipay-affirm:^2.0
```

## Usage

The following gateways are provided by this package:

 * affirm

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

#### Affirm.js

The Affirm integration is fairly straight forward. Essentially you just pass
a `checkout_token ` field through to Affirm instead of the regular payment data.

Start by following the guide here:
[Affirm Direct API](https://docs.affirm.com/payments/docs/direct-api-overview)

After that you will have a `checkout_token` field which will be submitted to your server.
Simply pass this through to the gateway as `transaction_id`, instead of the usual `options` array:

```php
        $transaction_id = $_POST['checkout_token'];

        $response = $gateway->authorize(
            'transaction_id' => $transaction_id,
        ])->send();
        
        $transaction_reference = $response->getTransactionReference();
        
        // you may use $transaction_reference in the upcoming calls.
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/eduardlleshi/omnipay-affirm/issues),
or better yet, fork the library and submit a pull request.

#### TODO
- [ ] Add unit testing.
