<?php
/**
 * File CommerceClient.php 
 * @package 	MCHN PHP SDK
 * @author      Ammar Haq
 * @version     v 0.1
 * @copyright   Copyright (c) 2020, MPM Brands
 */

 namespace MCHN\Commerce;

 use MCHN;
 use MCHN\Error;
 use MCHN\API\ApiProvider;

/**
 * Class CommerceClient | src/Commerce/CommerceClient.php
 *
 * The Commerce Client is used to retrieve, delete and create data in a MCHN
 * easily via the MCHN API.
 *
 * Methods to build, retrieve and delete data correspond with API endpoints e.g. use the getOrder 
 * function to get data from the orders API endpoint.
 *
 * To see a list of supported URL parameters for each function inside of the Commerce Client
 * please visit api.mchn.io/docs.
 *
 * To specify a URL parameter, simply pass an associate array with the paramter name and value
 * for example, $commerceClient->getOrders(['limit' => 20]); will set the limit parameter as 20.
 * 
 * For more examples please visit the following page https://github.com/MyPassionMedia/MCHN_PHP_API_SDK
 *
 *
 */

class CommerceClient 
{

	/** @var string|null The endpoint equals the request URI of the API request */
	private $endpoint;

	/** @var object The API Provider object used to conduct API requests */
	public $api;

	/** @var string Public API Key used for Commerce Client */
	private $sharedKey;

	/** @var array|null Request Body data if applicable */
	private $requestData;

	/** @var string Represents request type e.g. "GET" "PUT" */
	private $requestMethod;
	
	/** @var array Array holding currently supported Commerce Endpoints */
	private $supportedCommerceTypes = ['order', 'orderPayment', 'orderStatus', 'inventory' ,'shipment', 'price', 'product', 'payment', 'shippingPrice', 'productCategory', 'articleCategory'];
	
	/** @var array Array holding currently supported Commerce Endpoints mapped to types */
	private $supportedTypeEndpoints = array(
		'articleCategory' => 'articleCategories',
		'order' => 'orders',
		'orderPayment' => 'orderPayments',
		'orderStatus' => 'orderStatuses',
		'inventory' => 'inventories',
		'shipment' => 'shipments',
		'price' => 'prices',
		'product'=> 'products',
		'payment' => 'payments',
		'productCategory' => 'productCategories',
		'shippingPrice' => 'shippingPrices'
	
	);

	/** @var array Array holding all Errors associated with Commerce Client */
	private $errors;

	/**
	*
	* This function will instantiate a new Commerce Client object
	*
	* @param array $options {
	*
	*		@type string $sharedKey The Shared API Key used to send requests in the API Provider.
	*		@type string $privateKey The Private API Key used to send requests in the API Provider.
	*		@type int $version The API version used.
	* }
	*
	* @return self
	*
	*/
	public function __construct($options){

		// Build the api provider
		$apiInformation = array();

		$this->errors = array();

		if(!empty($options['sharedKey'])){
			$apiInformation['sharedKey'] = $options['sharedKey'];
		}

		if(!empty($options['privateKey'])){
			$apiInformation['privateKey'] = $options['privateKey'];
		}

		if(!empty($options['version'])){
			$apiInformation['version'] = $options['version'];
		}

		// Or could be more specific i.e. orders 
		$apiInformation['name'] = 'Commerce Client';

		$this->api = new ApiProvider($apiInformation);

	}

	/**
	*
	* This function will create a order in the MCHN via the passed parameters
	*
	* @param array $options {
	*
	*		@type int $price The price for the order
	*		@type int $productID The productID for the order to 
	*		@type int $accountID The accountID associated with the order
	*		@type int $currencyCountryCode The 2 digit country code of the order e.g. "CA"
	*		@type int $cartID The cart ID for the order if applicable
	*		@type string $purchaseURL The URL of the product's purchase
	*		@type bool $isCompleted Whether the order completed successfully
	*		@type bool $async Bool to make request asynchronous
	* }
	*
	* @return array POST response from API
	*
	*/
	public function buildOrder($options){

		$buildOptions = array(
			"data" => $options,
			"type" => 'order'
		);

		return $this->buildObject($buildOptions);
	}

	/**
	*
	* This function will create a object in the MCHN via the passed parameters
	*
	* @param array $options {
	*
	*		@type array $data An array holding the information to build the object with.
	*		@type string $type Name of the object to build e.g. order
	*		@type bool $async Switch to make request asynchronous
	* }
	*
	* @return array POST response from API
	*
	*/
	private function buildObject($options){
		
		// Data to build the object
		$buildOptions = array();

		// Build API request information
		$requestURI = "";

		if(
			!empty($options['data'])
			&& is_array($options['data'])
		){
			$buildOptions = $options['data'];
		} else{
			// throw error
			$this->addError(array(
				"code" => "400",
				"field" => "data",
				"errorDescription" => "Missing or Invalid 'data' in commerceClient::buildObject().",
			));
		}

		if(
			!empty($options['type'])
			&& in_array($options['type'],$this->supportedCommerceTypes)
		){
			$buildOptions['type'] = $options['type'];
			$requestURI .= $this->supportedTypeEndpoints[$buildOptions['type']];
		} else {
			// throw error
			$this->addError(array(
				"code" => "400",
				"field" => "type",
				"errorDescription" => "Missing or Invalid 'type' in commerceClient::buildObject().",
			));
		}

		$requestURI .= "/";

		$buildOptions['data']['requestURI'] = $requestURI;

		// We are using a 
		$buildOptions['requestType'] = "POST";
		// Execute request 
		if(empty($this->errors)){
			if(empty($options['async'])){
				$this->api->execute($buildOptions);
			} else{
				$this->api->executeAsync($buildOptions);
			}    
		} else {
			return $this->getErrors();
		}

		// Return response data, if DNE it returns NULL.
		return $this->api;
	}
	/**
	*
	* This function will grab a article category from the MCHN based on the ID provided
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the article category to get 
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getArticleCategory($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'articleCategory';

		return $this->getObject($options);
	}

	/**
	*
	* This function will grab article categories from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getArticleCategories($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'articleCategories';

		return $this->getObjects($options);
	}

	/**
	*
	* This function grab a order from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the order to grab
	*		@type int $version The API version used.
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getOrder($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'order';

		return $this->getObject($options);
	}

	/**
	*
	* This function grab a order payment from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the order payment to grab
	*		@type int $version The API version used.
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getOrderPayment($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'orderPayment';
		
		return $this->getObject($options);
	}

	/**
	*
	* This function will grab orders from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	*		@type int $sinceID The last record to get orders from with cursor pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getOrders($options = []){

		$options['type'] = 'orders';

		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab inventories from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getInventories($options = []){

		$options['type'] = 'inventories';

		return $this->getObjects($options);
	}

	
	/**
	*
	* This function grab an inventory record from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the inventory to grab
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getInventory($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'inventory';

		return $this->getObject($options);
	}
	
	/**
	*
	* This function grab an inventory record from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the inventory to grab
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getOrderStatus($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'orderStatus';

		return $this->getObject($options);
	}


	/**
	*
	* This function will grab an orders payments from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID the ID of the order who's payments are to be returned.
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getOrderPaymentsByOrderID($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'orders';
		// We want to get payments from orders.
		$options['getType'] = 'payments';
		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab orderPayments from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getOrderPayments($options = []){

		$options['type'] = 'orderPayments';

		return $this->getObjects($options);
	}


	/**
	*
	* This function will grab an orders shipments from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID the ID of the order who's shipments are to be returned.
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getOrderShipments($options = []){
 
		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'orders';
		// We want to get shipments from orders.
		$options['getType'] = 'shipments';
		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab an orders statuses from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID the ID of the order who's statuses are to be returned.
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getOrderStatusesByOrderID($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'orders';
		// We want to get shipments from orders.
		$options['getType'] = 'statuses';
		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab orderStatuses from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getOrderStatuses($options = []){

		$options['type'] = 'orderStatuses';

		return $this->getObjects($options);
	}

	/**
	*
	* This function grab a payment from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the payment to grab
	*		@type int $version The API version used.
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getPayment($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}
		
		$options['type'] = 'payment';

		return $this->getObject($options);
	}

	/**
	*
	* This function will grab payments from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getPayments($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'payments';

		return $this->getObjects($options);
	}
	/**
	*
	* This function will grab all voiding payments from a payment from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID the ID of the payment who's voiding payments are to be returned.
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getPaymentsVoided($options = []){

		$options['type'] = 'payments';

		$options['getType'] = "voidedPayments";

		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab the payment record(s) associated with the order payment
	*
	* @param array $options {
	*
	*		@type int $ID the ID of the order payment who's payment to return
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getOrderPaymentsPayments($options = []){

		$options['type'] = 'orderPayments';

		$options['getType'] = "payments";

		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab a product from the MCHN depending on the passed ID 
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the product to get 
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getProduct($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'product';

		return $this->getObject($options);
	}

	/**
	*
	* This function will grab the count of all products
	*
	* @return array response from API Provider
	*
	*/
	public function getProductsCount(){

		$options['type'] = 'products';

		$options['auxiliaryType'] ='count';

		return $this->getObjects($options);
	}


	/**
	*
	* This function will grab a product category from the MCHN based on the ID provided
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the product category to get 
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getProductCategory($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'productCategory';

		return $this->getObject($options);
	}

	/**
	*
	* This function will grab product categories from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getProductCategories($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'productCategories';

		return $this->getObjects($options);
	}


	/**
	*
	* This function will grab product categories from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the product to get the categories from 
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	*		@type int $sinceID The last record to get orders from with cursor pagination
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getProductCategoriesByProductID($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'products';
		// We want to get prices from products.
		$options['getType'] = 'productCategories';
		return $this->getObjects($options);
	}

	
	/**
	*
	* This function will grab product prices from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the product to get the prices from 
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getProductPrices($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'products';
		// We want to get prices from products.
		$options['getType'] = 'prices';
		return $this->getObjects($options);
	}
	
	/**
	*
	* This function will grab product price for a country from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the product to get the price from 
	*		@type string $secondaryID The name of the country e.g. 'CA' to get the price from.
	* }
	*
	* @return array response from API Provider
	*
	*/

	public function getProductPriceCountry($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'products';
		// We want to get prices from products.
		$options['getType'] = 'prices';
		if(!empty($options['country'])){
			$options['secondaryID'] = $options['country'];
			unset($options['country']);	
		}
		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab product shipping prices from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the product to get the shipping prices from 
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getProductShippingPrices($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'products';
		// We want to get shipping prices from products.
		$options['getType'] = 'shippingPrices';
		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab product shipping price for a country from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the product to get the shipping price from 
	*		@type string $country The name of the country e.g. 'CA' to get the shipping price from.
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getProductShippingPriceCountry($options = []){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		// We're starting with 
		$options['type'] = 'products';
		// We want to get shipping prices from products.
		$options['getType'] = 'shippingPrices';
		if(!empty($options['country'])){
			$options['secondaryID'] = $options['country'];
			unset($options['country']);
		}
	
		return $this->getObjects($options);
	}

	/**
	*
	* This function will grab an products from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getProducts($options = []){

		$options['type'] = 'products';

		return $this->getObjects($options);
	}

	/**
	*
	* This function grab a price from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the price to grab
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getPrice($options ){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'price';

		return $this->getObject($options);
	}

	/**
	*
	* This function will grab prices from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getPrices($options = []){

		$options['type'] = 'prices';

		return $this->getObjects($options);
	}

	/**
	*
	* This function grab a shipping price from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the shipping price to grab
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getShippingPrice($options){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'shippingPrice';

		return $this->getObject($options);
	}

	/**
	*
	* This function will grab shipping prices from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getShippingPrices($options = []){

		$options['type'] = 'shippingPrices';

		return $this->getObjects($options);
	}

	/**
	*
	* This function grab a shipment from the MCHN depending on the passed options
	*
	* @param array $options {
	*
	*		@type int $ID The ID of the shipment to grab
	*		@type int $version The API version used.
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getShipment($options ){

		// If options passed in as number or ID only, convert it.
		if(!is_array($options)){
			$id = $options;
			$options = array();
			$options['id'] = $id;
		}

		$options['type'] = 'shipment';

		return $this->getObject($options);
	}

	/**
	*
	* This function will grab shipments from the MCHN depending on the passed options 
	*
	* @param array $options {
	*
	*		@type int $limit The number of records to return 
	*		@type int $offset Where to get records from with offset pagination
	*		@type string $sentSinceDate define date to get shipments from
	*		@type string $sentSinceDate define date to get shipments until
	*		@type string $statusCodes define comma delimited list of shipments to get with status codes e.g. '3,4,5'
	* }
	*
	* @return array response from API Provider
	*
	*/
	public function getShipments($options = []){

		$options['type'] = 'shipments';

		return $this->getObjects($options);
	}
	
	
	/**
	*
	* This function will grab a Commerce Client object via the passed parameters
	*
	* @param array $options {
	*
	*		@type int $id The ID of the object to get 
	*		@type string The name of the object to get e.g. order
	* }
	*
	* @return array GET response from API
	*
	*/
	public function getObject($options){
		
		// Grab getObject request info
		$getOptions = array();

		if(
			!empty($options['id'])
			&& intval($options['id'])
		){
			$getOptions['id'] = $options['id'];
		} else{
			// throw error
			$this->addError(array(
				"code" => "400",
				"field" => "id",
				"errorDescription" => "Missing or Invalid 'id' in commerceClient::getObject().",
			));
		}

		if(
			!empty($options['type'])
			&& in_array($options['type'],$this->supportedCommerceTypes)
		){
			$getOptions['type'] = $options['type'];
		} else {
			// throw error
			$this->addError(array(
				"code" => "400",
				"field" => "type",
				"errorDescription" => "Missing or Invalid 'type' in commerceClient::getObject().",
			));
		}

		// Build API request information

		$requestURI = "";

		// Append appropriate request URI based on type.
		switch($options['type']){
			case "articleCategory":
				$requestURI .= "articleCategories";
				break; 
			case "order":
				$requestURI .= "orders";
				break;
			case "shipment":
				$requestURI .= "shipments";
				break;
			case "product":
				$requestURI .= "products";
				break;
			case "payment":
				$requestURI .= 'payments';
				break;    
			case "price":
				$requestURI .= 'prices';
				break;
			case "shippingPrice":
				$requestURI .= 'shippingPrices';
				break;
			case "orderPayment":
				$requestURI .= 'orderPayments';
				break;
			case "inventory":
				$requestURI .= "inventories";
				break;
			case "orderStatus":
				$requestURI .= "orderStatuses";
				break;
			case "productCategory":
				$requestURI .= "productCategories";
				break;
		}

		// /orders/
		$requestURI .= "/" . $options['id'];

		$getOptions['data']['requestURI'] = $requestURI;

		// We are providing a GET request
		$getOptions['requestType'] = "GET";
		// Execute request 
		if(empty($options['async'])){
			$this->api->execute($getOptions);
		} else{
			$this->api->executeAsync($getOptions);
		}    
		// Return response data, if DNE it returns NULL.
		return $this->api;
	}

	/**
	*
	* This function will grab the Commerce Client's objects based on the passed parameters.
	*
	* @param array $options {
	*
	*		@type string $ids The IDs of the objects to include
	*		@type string $exids The IDs of the objects to exclude
	*		@type string $type The type of get ( 0 for singular order, 1 for plural orders)
	*		@type int $offset the offset of or start of records to get
	*		@type string $auxiliaryType the auxiliary endpoint e.g. /products/cpimt
	*		@type int $limit The number of records to return
	*		@type string $sinceDate The start date for records to get 
	*		@type string $untilDate The end date for records to get
	*		@type string $getType The name of nested endpoint to get from e.g. /orders/{id}/shipments <--
	*		@type int $ID ID of the endpoint to get objects from e.g. /orders/{id}
	*		@type int $secondaryID the id of nested endpoint to get from e.g. /products/{id}/prices/{$secondaryID}
	* }
	*
	* @return array response data from get Objects API request.
	*
	*/
	private function getObjects($options){
		
		// Grab getObjects request info
		$getOptions = array();

		// A type like 'order' or 'orders' must be provided to get objects
		if(
			isset($options['type'])
			&& in_array(substr($options['type'],0,-1),$this->supportedCommerceTypes)
		){
			$getOptions['type'] = $options['type'];
		}

		// If nested request e.g. /orders/{id}/shipments grab the parent 

		if(!empty($options['getType']) || !empty($options['id'])){

			// If only one of getType or id is provided, add an error, not possible.
			if(empty($options['getType'])){
				$this->addError(array(
					"code" => "400",
					"field" => "getType",
					"errorDescription" => "Missing or Invalid 'getType' in commerceClient::getObjects().",
				));
			} else if(empty($options['id'])){
				$this->addError(array(
					"code" => "400",
					"field" => "id",
					"errorDescription" => "Missing or Invalid 'id' in commerceClient::getObjects().",
				));
			}

			// Validate the from Type
			// A type like 'order' or 'orders' must be provided to get objects
			if(
				!empty($options['getType'])
			){
				$getOptions['getType'] = $options['getType'];

			} else {

				// throw error
				$this->addError(array(
					"code" => "400",
					"field" => "getType",
					"errorDescription" => "Invalid 'getType' in commerceClient::getObjects().",
				));
			}
		}
		// Build API request information
		$requestURI = "";

		// Check if URL has query string 
		$query = parse_url($requestURI, PHP_URL_QUERY);

		$requestURI .= $options['type'];

		// Add getType and id if they exist.

		if(!empty($options['id']) && !empty($options['getType'])){
			$requestURI .= "/" . $options['id'] . "/" . $options['getType'];
			if(!empty($options['secondaryID'])){
				$requestURI .= "/" . $options['secondaryID'];
			}
		}

		
		if(!empty($options['auxiliaryType'])){
			$requestURI .= "/" . $options['auxiliaryType'];
		}
		
		// Remove all extra parameters, so only URL parameters left in options.
		unset($options['type']);
		unset($options['getType']);
		unset($options['id']);

		if(!empty($options['auxiliaryType'])){	unset($options['auxiliaryType']);	}

		if(!empty($options['secondaryID'])){	unset($options['secondaryID']);	}

		// Convert all options if they passed as an array to a string
		foreach($options as $optionName => $optionValue){
			if(is_array($optionValue)){
				$options[$optionName] = implode(",", $optionValue);
			}
		}

		// Check for ID or exIDs being ann array
		// Add each objects query parameter if it exists in the right format. 
		foreach($options as $urlParam => $value){
			if(is_bool($value)){
				$value = var_export($value,true);
			}
			$query = parse_url($requestURI, PHP_URL_QUERY);
			if(!$query){
				$requestURI .= "?". $urlParam. "=" . $value;
			} else {
				$requestURI .= "&". $urlParam. "=" . $value;
			}
		}

		$getOptions['data']['requestURI'] = $requestURI;

		$getOptions['requestType'] = 'GET';
		
		if(empty($options['async'])){
			$this->api->execute($getOptions);
		} else{
			$this->api->executeAsync($getOptions);
		}    
		return $this->api;
	}


	/**
	* This function will return an array of all errors associated with this object
	* in the proper format for the clients
	* @return array an array holding all errors neatly formatted
	*
	*/
	public function getErrors() {

		$errorsOutput = array();

		// If a general / header error message is set e.g. Body not a JSON object, set it
		if(!empty($this->errorsHeader)){
			$errorsOutput['message'] = $this->errorsHeader;
		}

		// If we have specific errors
		if(!empty($this->errors)) {
			
			if(empty($this->errorsHeader)){
				// If there is no header error message ( for multiple errors ) use first error
				$errorsOutput['message'] = $this->errors[0]->errorDescription;
			} 

			$errorsObject = array();

			// Get all errors on object and save them 
			foreach($this->errors as $error){
				array_push($errorsObject, $error);
			}

			$errorsOutput['errors'] = $errorsObject;
		}

		return $errorsOutput;
	}

	/**
	*
	* This function will add an error to the Commerce Client.
	*
	* @param array $information {
	*
	*		@type int $code Error Code e.g. 400.
	*		@type string $field The name of the field or parameter with the issue
	*		@type string $errorDescription Defines the error e.g. missing field, missing parameter, invalid input
	* }
	*
	* @return void
	*
	*/
	public function addError($information) {

		// check if not invalid
		$error = new Error\error(array(
			"code" => $information['code'],
			"field" => $information['field'],
			"errorDescription" => $information['errorDescription']
		));
		// Add error to our list of errors.
		array_push($this->errors, $error);
	}

	/**
	* 
	* Get the api and return it.
	*
	* @return object api
	*/
	public function getAPI(){
		return $this->api;
	}
	
	/**
	* 
	* Get the endpoint and return it.
	*
	* @return string endpoint
	*/
	public function getEndpoint(){
		return $this->endpoint;
	}

	/**
	* 
	* Get the sharedKey and return it.
	*
	* @return string Shared Key
	*/
	public function getSharedKey(){
		return $this->sharedKey;
	}
}
?>

