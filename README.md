# MCHN SDK

## About

The **MCHN SDK for PHP** is a PHP library used to make connecting to and accessing the MCHN API's functions via a user's API Keys simple.

The MCHN SDK supports PHP version 5.5 and above.

Jump To:
* [Getting Started](#Getting-Started)
* [Quick Examples](#Quick-Examples)
* [Features](#Features)
* [More Resources](#Resources)


## Getting Started

1. **Sign up for an account on your MCHN Site** - Login to your MCHN account and go to the API Keys settings under site Settings.
2. **Generate a pair of API Keys on your MCHN Site** - Generate a pair of API Keys with the desired API Key Permissions.
3. **Install the SDK** - Installing the SDK via Composer is the preferred method. If Composer is installed run the following query in the base directory of your project to install the package. Please ensure your **PHP verison >= 5.5**.

11/23/2020 Currently Composer is not enabled.

Alternatively, another method for installing the SDK by including the includes.php file located in the src directory. 

For instance, after cloning the SDK repository, use the following code will include the SDK.

```php
<?php
    $srcDir = "/MCHN_API_SDK";
    require_once $srcDir . "/" . "includes.php";

```

If using composer, run the following command to install the SDK

```
composer require mchn/mchn-sdk-php
```
4. **Use the SDK** - For best results on working with the SDK, read the User and Getting Started Guides and API Docs. 

The API documenation details the endpoints and parameters neccessary to work with the SDK.

For instance, in the following code snippet, we pass the *limit* and *offset* parameters to get 10 products starting from the second product in the MCHN. 


```php
<?php
    $getProductsResponse = $testCommerceClient->getProducts([
        "limit" => 10,
        "offset" => 2
    ]);


```

## Quick Examples

### Create an MCHN Commerce client

```php
<?php
// Require the SDK manually.
$srcDir = "./MCHN_API_SDK";
require_once $srcDir . "/" . "includes.php";

use MCHN\Commerce\CommerceClient;

// Instantiate an MCHN Commerce client.
$testCommerceClient = new CommerceClient([
    "sharedKey" => "{insert key here}",
    "privateKey"  => "{insert key here}"
]);

```

### Get Order #14's information from the MCHN

```php
<?php
// Require the Composer autoloader.
require "vendor/autoload.php";

use MCHN\Commerce\CommerceClient;

// Instantiate an MCHN Commerce client.
$testCommerceClient = new CommerceClient([
    "sharedKey" => "{insert key here}",
    "privateKey"  => "{insert key here}"
]);

$orderInfo = $testCommerceClient->getOrder(14);

```

### Get all prices from product #13

```php
<?php
// Require the Composer autoloader.
require "vendor/autoload.php";

use MCHN\Commerce\CommerceClient;

// Instantiate an MCHN Commerce client.
$testCommerceClient = new CommerceClient([
    "sharedKey" => "{insert key here}",
    "privateKey"  => "{insert key here}"
]);

$productPrices = $testCommerceClient->getProductPrices(13);

```

### Find the average order price for the first 50 orders for the MCHN site

```php

<?php
    $orders = $testCommerceClient->getOrders([
        "offset" => 0,
        "limit" => 50
    ]);

    $count = 0;
    $totalPrice = 0;
    // Loop through each orders item to access its data.
    foreach($orders["data"] as $order){

        // Get more information from a order via it's ID
        $currentOrderData = $testCommerceClient->getOrder($order["ID"]);

        // View order information
        $totalPrice += $currentOrderData["data"]["price"];
        $count++;
        
    }

    echo ("\nAverage price is " . ($totalPrice / $count) . "\n");

?>
```
### Print the product IDs of all products on the MCHN site

```php
<?php

    $getProductsResponse = $testCommerceClient->getProducts([
        "limit" => 10
    ]);

    $count = 0;
    do {
        // Iterate through all data returned in the API"s response
        foreach($getProductsResponse->responseData["data"] as $product){
            $count++;
            echo ("Product's ID is " . $product["ID"] . "\n");
        }
        $timePriorToNextPage = microtime(true);
        
        // Get the next page of the response
        $getProductsResponse->getNextPage();

    } while($getProductsResponse->hasNextPage());


?>
```

### Print the endpoint, status code of the request for debugging

```php
<?php

    $getProductsResponse = $testCommerceClient->getProducts([
        "limit" => 10
    ]);

    // If you would like to debug the response from the API SDK, simply request the appropriate variable

    $responseCode = $getProductsResponse->responseCode;

    // The API Endpoint where the call is being made.
    $apiEndpoint = $getProductsResponse->endpoint;


    // The next page for the API request if it exists or else it's null
    $nextPage = $getProductsResponse->nextPage;

?>
```


### Features


* Provides easy-to-use HTTP clients for all supported MCHN services and authentication protocalls.
* Provides convenience features including easy result pagination via $client->getNextPage(), $client->hasNextPage() and simple result objects.
* Provides accessible and pertinent error information.

### More Resources

* [API Docs](https://api.mchn.io/docs) – For details about operations, parameters, and responses
* [API SDK Functions](https://sdk.mchn.io/docs) - For support tickets with the MCHN and MCHN issues.
* [API Issues & Suggestions](https://github.com/MyPassionMedia/MCHN_PHP_API_SDK/issues) - For issues with the API or API SDK, please create a Github Issue.
* [Contact](mailto:ammarh@mpmbrands.com) - For questions, suggestions, critical issues and more.
