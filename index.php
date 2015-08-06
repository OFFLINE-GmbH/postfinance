<?php
require 'vendor/autoload.php';

$shaInSignature = 'Configuration -> Technical information -> Data and origin verification -> SHA-IN pass phrase';
$params = [
    'PSPID'        => 'your-postfinance-username',
    'ORDERID'      => '1234',
    'AMOUNT'       => '200' * 100,
    'CURRENCY'     => 'CHF',
    'LANGUAGE'     => 'de_CH',
    'CN'           => 'Bud Spencer',
    'EMAIL'        => 'bud@offlinegmbh.ch',
    'OWNERZIP'     => '6006',
    'OWNERADDRESS' => 'Maihofstrasse 95c',
    'OWNERCTY'     => 'Schweiz',
    'OWNERTOWN'    => 'Luzern',
    'TITLE'        => 'OFFLINE GmbH'
];

$postfinance = new Offline\PaymentGateways\PostFinance($shaInSignature);
$postfinance->setParamList($params);

?><!DOCTYPE html>
<html lang="de">
<head>
      <meta charset="UTF-8">
      <title>Simple PostFinance Example</title>
</head>
<body>
      <h1>Postfinance Form Example (view source)</h1>
      <form action="https://e-payment.postfinance.ch/ncol/test/orderstandard.asp" method="post">

            <?= $postfinance->getFormFields(); ?>

            <input type="submit" value="Submit Example">
      </form>
</body>
</html>
