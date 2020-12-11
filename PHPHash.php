<?php
/**
 * Class PHPHash | src/PHPHash.php
 *
 * @author      Ammar Haq <ammarh@mpmbrands.com>
 * @version     v.1.0 11/23/2020
 */


namespace MCHN\PHPHash; 

/**
 * @title PHPHash
 * @author Ammar Haq
 *
 * This class is used to generate a unique hash based on a secret key and a hashing algorithm like MD5.
 *  
*/

class PHPHash 
{

    /**
    *
    * @param string algorithm the name of the hashing algorithm used
    * @param object the hashing object built via a secret key and a hashing algorithm
    *
    */

    /**
    * @param mixed[] $options[] Array to hold the variables associated with constructing the object see below:
    *          $options = [
    *               algorithm   string name of the algorithm to hash with e.g. md5, sha1, sha256)
    * ]
    *             
    * 
    * @return self
    * 
    */  
    public function __construct($options = array()){


        // Save algorithm, if none provided user MD5 default
        if(!empty($options['algorithm'])){
            $this->algorithm = $options['algorithm'];
        } else {
            $this->algorithm = "sha256";
        }
    }

    /**
    * @param mixed[] $options[] Array to hold the variables associated with getting the hash:
    *          $options = [
    *               privateKey   string name of privateKey to build hash
    *               data         array holding the data to be hashed
    * ]
    *
    * This function will build a hash based on the privateKey passed and algorithm method created.
    * It will then return the data hashed.
    * @return string hash
    */

    public function getHash($options){

        $hashResult = null;
        // Validate options
        if(!empty($options)){
            $privateKey = isset($options['privateKey']) ? $options['privateKey'] : null;
                
            // We must hash with unescaped slashes to avoid there being any characeters extra
            $jsonEncodedURL = json_encode($options['data'], JSON_UNESCAPED_SLASHES);

            // Build an json encoded 
            $hashResult = base64_encode(hash_hmac($this->algorithm,$jsonEncodedURL, $privateKey, true));
        }
        return $hashResult;
    }
}