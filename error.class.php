<?php

	/** 
	* @title error
	* @author Ammar Haq
    *
    * This class is used to provide a common interface for providing readable and detailed
    * error across the API throughout requests.
    */

	namespace MCHN\Error; 

	class error {

		// These are tenative as we keep foreignKey to siteID
		// the field there is a problem ( could be URL param or in post)
		var $field;
		// code for error like 400
		var $code;
		// custom description of error,e.g. missing_field, missing_param, invalid
		// already_exists, unprocessable
		var $errorDescription;


		/**
		 * @param mixed[] $options[] Array to hold the variables associated with constructing the object see below:
		 *          $options = [
         * 					code code for error like 400
         *                  field Defines the field there is a problem ( could be URL param or in post)
         *                  errorDescription Defines a custom description of error,e.g. missing_field, missing_param, invalid
         *                  resource: Defines the field there is a problem ( could be URL param or in post)
		 * ]
		 *             
		 * 
		 * @return self
		 * 
		 */  
		 public function __construct($options = array())
		 {

			if (!empty($options['errorDescription'])) {
				$this->errorDescription = $options['errorDescription'];
			}


			if(!empty($options['field'])){
				$this->field = $options['field'];
			}

			if(!empty($options['code'])){
				$this->code = $options['code'];
			}

		}

		/**
		* 		
		* This function will return an array holding information regarding an error
		* @param mixed[] $options[] Array to hold all the elements to construct the object
		*				$options = [
		* ] 
		*	
		*/

		public function getErrorInfo($options = array()){
			
			$errorInfo = array();

			if (!empty($this->field)){
				$errorInfo['field'] = $this->field;
			}

			if(!empty($this->code)){
				$errorInfo['code'] = $this->code;
			}

			if(!empty($this->resource)){
				$errorInfo['resource'] = $this->resource;
			}

			return $errorInfo;
		}
    }
