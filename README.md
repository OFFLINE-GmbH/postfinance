# PostFinance e-payment helper class

This class helps to implement basic e-commerce features from PostFinance Switzerland.
The implementation follows the [official documentation](https://e-payment.postfinance.ch/Ncol/PostFinance_e-Com-BAS_DE.pdf).

## Install it

    composer require 'offline/postfinance'

## Use it

See `index.php` for an example.

Define your parameters as an array. Get your SHA-IN signature at hand. Initialize a class instance and pass your parameter array to `setParamList`.

```php
$shaInSignature = 'Configuration -> Technical information -> Data and origni verification -> SHA-IN pass phrase';
$params = [
    'PSPID'        => 'your-postfinance-username',
    'ORDERID'      => '1234',
    'AMOUNT'       => '200' * 100,
    'CURRENCY'     => 'CHF',
    'LANGUAGE'     => 'de_CH',
];

$postfinance = new Offline\PaymentGateways\PostFinance($shaInSignature);
$postfinance->setParamList($params);
```

In your view file call `getFormFields` wherever you want to output the hidden input fields.

```php
<form action="https://e-payment.postfinance.ch/ncol/test/orderstandard.asp" method="post">
    
    <?= $postfinance->getFormFields(); ?>
    
    <input type="submit" value="Submit Example">
</form>
```

### Using different algorithms

To use SHA256 or SHA512 algorithms, simply pass the php MHASH constant as second parameter:

```php
$postfinance256 = new Offline\PaymentGateways\PostFinance($shaInSignature, MHASH_SHA256);
$postfinance512 = new Offline\PaymentGateways\PostFinance($shaInSignature, MHASH_SHA512);
```

### Validating SHA-OUT signatures   

Make sure you have the option `I would like to receive transaction feedback parameters on the redirection URLs.` under `Transaktion feedback` enabled.

```php
$shaOutSignature = 'Configuration -> Transaction feedback -> SHA-OUT pass phrase';
$shaSign = isset($_GET['SHASIGN']) ? $_GET['SHASIGN'] : '';

$postfinance = new PostFinance($shaOutSignature);
$postfinance->setParamList($_GET);

$isValid = $postfinance->getDigest() === $shaSign;

var_dump($isValid);
```