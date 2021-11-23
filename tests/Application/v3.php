<?php

use Http\Client\Curl\Client;
use Paygreen\Sdk\Core\Environment;
use Paygreen\Sdk\Payment\Model\Order;
use Paygreen\Sdk\Payment\Model\Customer;
use Paygreen\Sdk\Payment\V3\Model\PaymentOrder;
use Paygreen\Sdk\Payment\V3\PaymentClient;

$curl = new Client();

$environment = new Environment(
    getenv('PG_PAYMENT_PUBLIC_KEY'),
    getenv('PG_PAYMENT_PRIVATE_KEY'),
    getenv('PG_PAYMENT_API_SERVER'),
    getenv('PG_PAYMENT_API_VERSION')
);

$client = new PaymentClient($curl, $environment);

$response = $client->authenticate();

$bearer = $response->getData()->data->token;

$client->setBearer($bearer);

$buyer = new Customer();
$buyer->setId(uniqid());
$buyer->setFirstname('John');
$buyer->setLastname('Doe');
$buyer->setEmail('romain@paygreen.fr');
$buyer->setCountryCode('FR');

$response = $client->createBuyer($buyer);
$data = $response->getData();
dump($data);
$buyer->setReference($data->data->id);
$response = $client->getBuyer($buyer);
dump($response->getData());
$buyer->setFirstname('Jerry');
$buyer->setLastname('Cane');
$buyer->setEmail('dev-module@paygreen.fr');
$buyer->setCountryCode('US');
$response = $client->updateBuyer($buyer);
dump($response->getData());

$buyerNoreference = new Customer();
$buyerNoreference->setId(uniqid());
$buyerNoreference->setFirstname('Will');
$buyerNoreference->setLastname('Jackson');
$buyerNoreference->setEmail('romain@paygreen.fr');
$buyerNoreference->setCountryCode('FR');

$order = new Order();
$order->setCustomer($buyerNoreference);
$order->setReference('SDK-ORDER-123');
$order->setAmount(107);
$order->setCurrency('eur');

$paymentOrder = new PaymentOrder();
$paymentOrder->setPaymentMode("instant");
$paymentOrder->setAutoCapture(true);
$paymentOrder->setIntegrationMode("hosted_fields");
$paymentOrder->setOrder($order);

$response = $client->createOrder($paymentOrder);
$data = $response->getData();
dump($data);

$order->setCustomer($buyer);
$response = $client->createOrder($paymentOrder);
$data = $response->getData();
dump($data);

$order->setReference($data->data->id);
$response = $client->getOrder($paymentOrder);
$data = $response->getData();
dump($data);


