<?php
// Include the Square Connect API resources
require_once(__DIR__ . '/connect-php-sdk-master/autoload.php');
//Replace your access token and location ID
$accessToken = 'EAAAEFowcf8rZ2a7k19hYhzqUbKwAG5KgYz8Gn7uLjVs0ZttPgsysqj1p0pX4Szi';
$locationId = 'AEDNZHJ9X9W53';

// Create and configure a new API client object
$defaultApiConfig = new \SquareConnect\Configuration();
$defaultApiConfig->setAccessToken($accessToken);
$defaultApiConfig->setHost("https://connect.squareup.com");
$defaultApiClient = new \SquareConnect\ApiClient($defaultApiConfig);
$checkoutClient = new SquareConnect\Api\CheckoutApi($defaultApiClient);

//Puts our line item object in an array called lineItems.
$lineItems = array(
);
foreach($_POST['info'] as $item) {
	//Create a Money object to represent the price of the line item.
	$price = new \SquareConnect\Model\Money;
	$price->setAmount($item['line_price']  * 100);
	$price->setCurrency('USD');

	//Create the line item and set details
	$book = new \SquareConnect\Model\CreateOrderRequestLineItem;
	$book->setName($item['title']);
	$book->setQuantity($item['quantity']);
	$book->setBasePriceMoney($price);
	array_push($lineItems, $book);
}

//Shipping money obj
$price = new \SquareConnect\Model\Money;
$price->setAmount($_POST['shipping'] * 100);
$price->setCurrency('USD');

//Shipping line item
$book = new \SquareConnect\Model\CreateOrderRequestLineItem;
$book->setName('Shipping');
$book->setQuantity('1');
$book->setBasePriceMoney($price);
array_push($lineItems, $book);

// Create an Order object using line items from above
$order = new \SquareConnect\Model\CreateOrderRequest();

$order->setIdempotencyKey(uniqid()); //uniqid() generates a random string.

//sets the lineItems array in the order object
$order->setLineItems($lineItems);
//$order->setDiscounts('100');

//var_dump($order);

//////
///Create Checkout request object.
$checkout = new \SquareConnect\Model\CreateCheckoutRequest();

$checkout->setIdempotencyKey(uniqid()); //uniqid() generates a random string.
$checkout->setOrder($order); //this is the order we created in the previous step.
$checkout->setRedirectUrl('https://radicalrootsherbs.com/pages/thank-you'); //Replace with the URL where you want to redirect your customers after transaction. 

//////
try {
    $result = $checkoutClient->createCheckout(
      $locationId,
      $checkout
    );
    //Save the checkout ID for verifying transactions
    $checkoutId = $result->getCheckout()->getId();
    //Get the checkout URL that opens the checkout page.
    $checkoutUrl = $result->getCheckout()->getCheckoutPageUrl();
    //print_r($checkoutUrl);
    echo json_encode(array('url' => $checkoutUrl));
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->createCheckout: ', $e->getMessage(), PHP_EOL;
}
?>