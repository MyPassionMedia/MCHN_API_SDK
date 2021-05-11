<?php

namespace MCHN\API;

use \MCHN\PHPHash;

/**
 * API Provider
 *
 *
 * An API provider is a function that accepts a type, service plus version and returns an array of API data
 * on sucess or NULL if no API data created for passed data.
 *
 *
 *
 * To make calls to the API provider
 *
*/

class ApiProvider
{


    /**
	 * API version the API provider will send request too.
	 *
	 * @var int
    */

    private $version;

    /**
	 * API provider name of where requests sent from
	 *
	 * @var string
    */
    private $providerName;

    /**
	 * API shared key used to authenticate request.
	 *
	 * @var string
    */
    private $sharedKey;

    /**
	 * API private key used to authetnicate request.
	 *
	 * @var string
    */

    private $privateKey;

    /**
	 * API response data retrieved from making an API response
	 *
	 * @var array
    */
    public $responseData;

    /**
     * API response code retrieved from making an API response
     *
     * @var integer
    */
    public $responseCode;

    /**
	 * Boolean value determining whether the API response has another page in response
	 *
	 * @var bool
    */
    public $hasNextPage;

    /**
	 * String endpoint to get data for API response next page.
	 *
	 * @var array
    */
    public $nextPage;


    /**
     * String endpoint of the current request if exists
     *
     * @var string
    */
    public $endpoint;

    /**
	 * Boolean value determining whether the API response has a previous page in response
	 * @var bool
    */

    // private $previousPage;


    /**
    * @param mixed[] $options[] Array to hold the variables associated with constructing the object see below:
    *          $options = [
    *           sharedKey string the shared key for the api key
    *           version int the version of the API to make a request too default 1
    *           name string the name of the provider of the API, default MCHN
    *           sharedKey string the sharedKey of the API Key
    *           privateKey string the privateKey of the API Key
    *
    * ]
    *
    *
    * @return self
    *
    */

    public function __construct($options = array())
    {
        // Save the version provided
        if(!empty($options['version'])){
            // Match version number with path
            $this->version = "v" . $options['version'];
        } else {
            $this->version = 'v1';
        }


        // Save the provider name
        if(!empty($options['name'])){
            $this->providerName = $options['name'];
        } else {
            // Default provider name is MCHN
            $this->providerName = "MCHN";
        }

        if(!empty($options['sharedKey'])){
            $this->sharedKey = $options['sharedKey'];
        }

        if(!empty($options['privateKey'])){
            $this->privateKey = $options['privateKey'];
        }

        // Initalize pagination and response variables
        $this->hasNextPage = false;
        $this->nextPage = null;
        $this->responseData = null;
    }

    /**
    * This function is used to prevent sensitive data being printed out from the object.
    */
    public function __debugInfo() {
        return [
            'version' => $this->version,
            'providerName' => $this->providerName,
            'responseData' => $this->responseData,
            'responseCode' => $this->responseCode,
            'hasNextPage' => $this->hasNextPage,
            'nextPage' => $this->nextPage,
            'endpoint' => $this->endpoint
        ];
    }

    /**
    *
    * This function will return the response data from the API Provider
    *
    *
    * @return array responseData holding the response information
    */
    public function getResponseData(){
        return $this->responseData;
    }


    /**
    *
    * This function will return the response code from the API Provider
    *
    *
    * @return integer responseCode detailing the response information
    */
    public function getResponseCode(){
        return $this->responseCode;
    }

    /**
    *
    * This function will return and build the hash data for authenticating a request
    *   $options = [
    *       hashAlgorithm string name of algorithm to hash data with
    *       input array holding body of response or null
    *       requestURI string holding the requestURI of the URL
    *
    *   ]
    * @return hash the request hashed
    */

    private function getHashData($options){

        if(empty($options['algorithm'])){
            $hashBuilder = new PHPHash\PHPHash(array(
                "algorithm" => "SHA256"
            ));
        } else {
            $hashBuilder = new PHPHash\PHPHash(array(
                "algorithm" => $options['hashAlgorithm']
            ));
        }

        // Build request in appropriate format
        $requestData = array(
            // Input refers to body input of the JSON request
            "input" => !empty($options['input']) ? $options['input'] : null,
            "requestURI" => $options['requestURI']
        );

        // Store data in format for hashing.
        $hashData = array(
            // Input refers to body input
            "data" => $requestData,
            "sharedKey" => $this->sharedKey
        );

        // Build hash with privateKey and data.
        $hashOptions = array(
            "data" => $hashData,
            "privateKey" => $this->privateKey,
        );

        return $hashBuilder->getHash($hashOptions);
    }

    /**
    *
    * This function will execture a request to grab the next page of results
    * if they exists, otherwise it will return.
    *
    * @return void
    */
    public function getNextPage($options = array()){

        if(!empty($this->nextPage)){
            $this->execute(array(
                "curlURL" => $this->nextPage,
                "requestType" => "GET"
            ));
        } else {
            return;
        }
    }

    /**
    *
    * This function will execture a request to grab the previous page of results
    * if they exist, otherwise it will return.
    *
    * @return void
    */
    public function getPreviousPage($options = array()){

        if(!empty($this->previousPage)){
            $this->execute(array(
                "curlURL" => $this->previousPage
            ));
        } else {
            // No previous page exists, return null
            return null;
        }
    }

    /**
    * This function will execute a request based on the provided options.
    *          $options = [
    *               hashAlgorithm   string name of the algorithm to hash with e.g. md5, sha1)
    *               input       array the data passed to the request in the body
    *               data        array the data we hash which is composed of the URI, sharedKey and request data
    *               requestType string 'GET', 'POST' represents request type to execture
    * ]
    *
    */
    public function execute($options){

        // Reset pagination tools prior to getting a new result.
        $this->nextPage = null;
        $this->hasNextPage = false;

        // If hashing method not passed, default SHA256
        if(empty($options['algorithm'])){
            $options['algorithm'] = "SHA256";
        }

        if(empty($options['input'])){
            $options['input'] = null;
        }
        $requestType = "";

        if(!empty($options['requestType'])){
            $requestType = $options['requestType'];
        } else {
            // Error
        }

        // Make request to appropriate version
        if(!empty($options['data']['requestURI'])){
            $options['data']['requestURI'] = "/". $this->version . "/" . $options['data']['requestURI'];
        }

        $curl = curl_init();

        // If a curlURL overriding is specified use that.
        if(empty($options['curlURL'])){
            $curlURL = "https://api.mchn.io" . $options['data']['requestURI'];
        } else {
            $curlURL = $options['curlURL'];
            // Remove the https://api.mchn.io from curlURL if passed to get requestURI
            $options['data']['requestURI'] = substr($curlURL, 19);
        }

        $requestHash = $this->getHashData(array(
            "hashAlgorithm" => $options['algorithm'],
            "input" => $options['input'],
            "requestURI" => str_replace(' ', '%20', $options['data']['requestURI'])
        ));

        // Fix white spaces issue with curl.
        $curlURL = str_replace ( ' ', '%20', $curlURL);

        // Set the endpoint based on the request being executed 
        $this->endpoint = $curlURL;

        $curlOptions = array(
            CURLOPT_URL => $curlURL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_HTTPHEADER => array(
                "Cookie: PHPSESSID=c7bpjshgpp3oaaurpekqiitr83",
                "x-api-key: " . $this->sharedKey,
                "hash: " . $requestHash
            ),
        );

        if(!empty($options['input'])){
            // $postOption = [  CURLOPT_POSTFIELDS => json_encode($options['data'])];

            $curlOptions[CURLOPT_POSTFIELDS] = json_encode($options['input']);
            // array_push($curlOptions, $postOption);
        }
        
        curl_setopt_array($curl, $curlOptions);
        
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if(!empty($httpcode)){
            $this->responseCode = $httpcode;
        }

        curl_close($curl);

        $raw = $response;

        $response = json_decode($response, true);

        $invalidResponse = false;

        switch(json_last_error()) {
            case JSON_ERROR_DEPTH:
                echo 'API RESPONSE ERROR - Maximum stack depth exceeded invalid response on endpoint' . $curlURL . "\n";
                $invalidResponse = true;
            break;
            case JSON_ERROR_CTRL_CHAR:
                echo 'API RESPONSE ERROR - Unexpected control character found on endpoint ' . $curlURL . "\n";
                $invalidResponse = true;
            break;
            case JSON_ERROR_SYNTAX:

                var_dump($raw);
                // var_dump($response);

                echo 'API RESPONSE ERROR - Syntax error, malformed JSON on endpoint ' . $curlURL . "\n";

                $invalidResponse = true;
            break;
            case JSON_ERROR_NONE:
                // Save pagination data for iteration purposes
                // If we have no message, then we have metadata

                if(!isset($response['message']) && isset($response['metadata']['pagination'])){
                    if(!empty($response['metadata']['pagination']['nextPage'])){

                        $this->nextPage = $response['metadata']['pagination']['nextPage'];
                        $this->hasNextPage = true;
                    } else if (array_key_exists('nextPage', $response['metadata']) 
                                && $response['metadata']['pagination']['nextPage'] == null
                                || !array_key_exists('nextPage', $response['metadata'])){
                        $this->hasNextPage = false;
                        $this->nextPage = null;
                    }
                }

                $this->responseData = $response;

                return $response;
            break;
        }

        
        // If we find an invalid response, ensure the next pages are false / set to null
        // to avoid infinite scrolling.
        if($invalidResponse) {
            $this->hasNextPage = false;
            $this->nextPage = null;
        }

    }


    /**
    * TODO: Use GuzzleHTTP to get a async response for the API request
    * look into payment processing method
    */
    public function executeAsync($options){
    }

    /**
    * This function will return whether the Api Provider's response has another page
    *
    * @return bool true/false if there is a next page in the query results
    */
    public function hasNextPage(){
        return $this->hasNextPage;
    }

}

?>