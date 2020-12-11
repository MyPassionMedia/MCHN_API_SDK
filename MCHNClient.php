<?php

use MCHN\API\APIProvider;

namespace MCHN;

/**
* Default MCHN Client implementation.
*/

class MCHNClient 
{

    // String equal to the request URI
    private $endpoint;

    // API Provider used to provide API functions
    private $api;

    // string equal to shared API key
    private $sharedKey;

    // array holding body of input if it exists
    private $requestData;

    // String equal to GET, POST, PUT etc
    private $requestMethod;

    /**
    * @param mixed[] $options[] Array to hold the variables associated with constructing the object see below:
    *          $options = [
    * ]
    *             
    * 
    * @return self
    * 
    */  

    public function __construct($options = array()){

        $this->sharedKey = $options['sharedKey'];
        $this->api = new \MCHN\API\APIProvider($options);

        $this->requestData = $options['requestData'];

        $this->requestMethod = $options['requestMethod'];

    }

    /**
    * 
    * Get the api and return it.
    * @return object api
    */
    public function getAPI(){
        return $this->api;
    }

    /**
    * 
    * Get the endpoint and return it.
    * @return string endpoint
    */
    public function getEndpoint(){
        return $this->endpoint;
    }

    /**
    * 
    * Get the sharedKey and return it.
    * @return string shared key
    */
    public function getSharedKey(){
        return $this->sharedKey;
    }


}

?>