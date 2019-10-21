<?php

// Include the Square Connect API resources
require_once(__DIR__ . '/connect-php-sdk-master/autoload.php');
//Replace your access token and location ID
$accessToken = 'EAAAEEm-YY1h36x93pFrgvlZkTxg1SJgFNML5uXfr_HEXjQpVKvDynSKm60gVz5r';
$locationId = 'M4HM2XA033Z9A';

// Create and configure a new API client object
$defaultApiConfig = new \SquareConnect\Configuration();
$defaultApiConfig->setAccessToken($accessToken);
$defaultApiConfig->setHost("https://connect.squareupsandbox.com");
$defaultApiClient = new \SquareConnect\ApiClient($defaultApiConfig);
$checkoutClient = new SquareConnect\Api\CheckoutApi($defaultApiClient);

var_dump($_POST['data']);
//Create a Money object to represent the price of the line item.
$price = new \SquareConnect\Model\Money;
$price->setAmount(2);
$price->setCurrency('USD');

//Create the line item and set details
$book = new \SquareConnect\Model\CreateOrderRequestLineItem;
$book->setName('The Shining');
$book->setQuantity('2');
$book->setBasePriceMoney($price);

//Puts our line item object in an array called lineItems.
$lineItems = array(
);
array_push($lineItems, $book);


// Create an Order object using line items from above
$order = new \SquareConnect\Model\CreateOrderRequest();

$order->setIdempotencyKey(uniqid()); //uniqid() generates a random string.

//sets the lineItems array in the order object
$order->setLineItems($lineItems);

//////
///Create Checkout request object.
$checkout = new \SquareConnect\Model\CreateCheckoutRequest();

$checkout->setIdempotencyKey(uniqid()); //uniqid() generates a random string.
$checkout->setOrder($order); //this is the order we created in the previous step.
$checkout->setRedirectUrl('http://www.google.com'); //Replace with the URL where you want to redirect your customers after transaction. 

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
    print_r('Complete your transaction: <a href="'. $checkoutUrl .'">' . $checkoutUrl . '</a>');
} catch (Exception $e) {
    echo 'Exception when calling CheckoutApi->createCheckout: ', $e->getMessage(), PHP_EOL;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Square</title>
</head>
<body>
<div>content</div>
</body>
</html>