<?php

namespace MCHN\APIKey;

/**
 * @title APIKey
 * @author Ammar Haq
 * API Key, this class is used to store the private and shared keys of an API
 * it is abstracted away to prevent the API Keys from being accessed easily
 *
*/

class APIKey {


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
    * @param mixed[] $options[] Array to hold the variables associated with constructing the object see below:
    *          $options = [
    *           sharedKey string the shared key for the API key
    *           privateKey string the privateKey of the API Key
    *
    * ]
    *
    *
    * @return self
    *
    */
    public function __construct($options = array()){

        !empty($options['privateKey']) ? $this->privateKey = $options['privateKey'] : $this->privateKey = "";
        !empty($options['sharedKey']) ? $this->sharedKey = $options['sharedKey'] : $this->privateKey = "";
    }

    /**
    * This function will return the private key of the API Key object
    *
    * @return string private key.
    */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
    * This function will return the shared key of the API Key object
    *
    * @return string shared key.
    */
    public function getSharedKey()
    {
        return $this->sharedKey;
    }
    

}
?>