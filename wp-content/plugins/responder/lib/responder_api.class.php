<?php
/**
 * custom responder API
 *
 */
class ResponderAPIRes{
	
	const RESPONSE_EMAILS_EXISTING = "EMAILS_EXISTING";
	
	
	private static $isInited = false;
	private static $responder;
	private static $cacheCustomFields = array();
	private static $cacheLists = array();
	
	private $userKey;
	private $userSectret;
	private $lastError = "";
	
	/**
	 * the constructor
	 */
	public function __construct(){
		
		$this->init();
	}
	
	
	/**
	 * init the API
	 */
	private function init(){
		
		if(self::$isInited == true)
			return(false);
		
        $clientKey = GlobalsResponder::$clientKey;
        $clientSecret = GlobalsResponder::$clientSecret;
        
        $userKey = get_option('Responder_Plugin_EnterUsername');
        $userSecret = get_option('Responder_Plugin_EnterPassword');
        
        try{
        
	        if(empty($userKey))
	        	UniteFunctionsRes::throwError("User key missing");
	       
	        if(empty($userSecret))
	        	UniteFunctionsRes::throwError("User secret missing");
        
        	self::$responder = new ResponderOAuth($clientKey, $clientSecret, $userKey, $userSecret);
        	
        	
        }catch (Exception $e){

        	$message = "api error: ".$e->getMessage();
        	
        	//dmp($message);exit();
        	
        	UniteFunctionsRes::throwError(__res("no connection to responder",RESPONDER_TEXTDOMAIN));
        }
		
		
		self::$isInited = true;
	}
	
	
	/**
	 * make request 
	 */
	private function request($path, $method='get', $post=array()){
	    
		$this->lastError = "";
	    
	    $response = self::$responder->http_request($path, $method, $post);
	    	    	   
	    $arrResponse = UniteFunctionsRes::jsonDecode($response);
	   	
	    if(getType($arrResponse) == "string")
	    	$this->lastError = $arrResponse;
	    
	    return($arrResponse);    
	}
	
	/**
	 * get last error
	 */
	public function getLastError(){
		
		return($this->lastError);
	}
	
	/**
	 * validate phone response
	 */
	private function validateSubscriberUpdateResponseCommon($arrResponse){
		
		//check invalid emails
		
	      $arrInvalidEmails = UniteFunctionsRes::getVal($arrResponse, "EMAILS_INVALID");
	      if(!empty($arrInvalidEmails))
	          UniteFunctionsRes::throwError(__res("Invalid Email", RESPONDER_TEXTDOMAIN));
	  
	   //check phone
	   
	      $arrInvalidPhones = UniteFunctionsRes::getVal($arrResponse, "PHONES_INVALID");
	          if(!empty($arrInvalidPhones)){
	              //$phone = $arrInvalidPhones[0];
	              $message = __res("Invalid Phone", RESPONDER_TEXTDOMAIN);
	              //$message .= ": <b>". $phone."</b>";
	              UniteFunctionsRes::throwError($message);
	          }
	          
	      UniteFunctionsRes::throwError(__res("Subscription Failed", RESPONDER_TEXTDOMAIN));
	          
	       // by default - throw non success message
		
	}

	
	/**
	 * validate subscribe response
	 */
	private function validateSubscribeResponse($arrResponse){
	    
		
	    //check for created new subscriber
	    	    
	    $arrCreated = UniteFunctionsRes::getVal($arrResponse, "SUBSCRIBERS_CREATED");

	    $isCreated = !empty($arrCreated);
	    
	    if($isCreated == true)
	         return(true);
	    
	     $arrEmailsExisting = UniteFunctionsRes::getVal($arrResponse, "EMAILS_EXISTING");
	     if(!empty($arrEmailsExisting))
	         return(true);
	     
	     $this->validateSubscriberUpdateResponseCommon($arrResponse);
	         
	}
	
	
	/**
	 * validate update response
	 */
	public function validateUpdateResponse($arrResponse){
		
	    $arrUpdated = UniteFunctionsRes::getVal($arrResponse, "SUBSCRIBERS_UPDATED");

	    $isUpdated = !empty($arrUpdated);
	    
	    if($isUpdated == true)
	         return(true);
		
	    $this->validateSubscriberUpdateResponseCommon($arrResponse);
	    
	    
	}
	
	
	/**
	 * insert fields to list
	 */
	public function updateSubscriberInList($listID, $arrSubscriber){

		$action = "lists/{$listID}/subscribers";

		$email = UniteFunctionsRes::getVal($arrSubscriber, "EMAIL");
		
		$arrSubscriber["IDENTIFIER"] = $email;
		
		$arrSubscriber = $this->addSubscriberMustFields($arrSubscriber);
		
		$arrSubscribers = array($arrSubscriber);
		
		$postData = array();
		$postData["subscribers"] = json_encode($arrSubscribers);
		
		
		//http request and validation
		$arrResponse = $this->request($action, "put", $postData);
		
		do_action("responder_api_after__update_submission", $listID, $arrSubscribers, $arrResponse);
		
		
		$this->validateUpdateResponse($arrResponse);
		
	}
	
	/**
	 * get subscrber by email and list ID
	 * identifier can be user ID or email
	 */
	public function getSubscriber($listID, $identifier){

		UniteFunctionsRes::validateNotEmpty($identifier,"Subscriber ID");
		UniteFunctionsRes::validateNotEmpty($listID,"List ID");

		
		$action = "lists/{$listID}/subscribers";
		
		$subscriber = array();
		$subscriber["IDENTIFIER"] = $identifier;
		
		$params = array();
		$params["subscriber_id"] = $identifier;
		
		$arrResponse = $this->request($action,"get", $params);
		
		if(empty($arrResponse))
			return(null);
		
		$arrSubscriber = $arrResponse[0];
		
		return($arrSubscriber);
	}
	
	/**
	 * add subscriber must fields to post array
	 */
	private function addSubscriberMustFields($arrSubscriber){
		
		$arrSubscriber["NOTIFY"] = 2;
		$arrSubscriber["STATUS"] = 1;
		$arrSubscriber["ACCOUNT_STATUS"] = 1;
		
		return($arrSubscriber);
	}
	
	/**
	 * get subscriber array for insert
	 */
	public function insertSubscriberArray($listID, $subscriber, $isUpdateOnFound = false, $isResubscribe=false){
		
		$subscriber = $this->addSubscriberMustFields($subscriber);
		
		$subscribers = array($subscriber);
				
		$action = "lists/{$listID}/subscribers";

		$postData = array(
			'subscribers' => json_encode($subscribers)
		);
		
		//http request and validation
		
		$response = self::$responder->http_request($action, 'post', $postData);
				
		$arrResponse = UniteFunctionsRes::jsonDecode($response);

		
		$isError = false;
		if(empty($arrResponse)){
			$isError = true;
			$arrResponse = $response;
		}
			
		do_action("responder_api_after__add_submission", $listID, $subscribers, $arrResponse);

		if($isError == true)
			UniteFunctionsRes::throwError(__res("Responder Error Occured!", RESPONDER_TEXTDOMAIN));
		
			
		$this->validateSubscribeResponse($arrResponse);
	    
		$arrEmailsExisting = UniteFunctionsRes::getVal($arrResponse, "EMAILS_EXISTING");
		if(empty($arrEmailsExisting))
			return(false);
		
		//the client exists
		
		if($isUpdateOnFound == false)
			return(true);
		
		//check and update on existing email
		//zero all not filled fields
		
		if($isResubscribe == true){
			
			$subscriber["DAY"] = 0;
			
			//init other variables
			if(isset($subscriber["PHONE"]) == false){
				$subscriber["PHONE"] = null;
				$subscriber["PHONE_IGNORE"] = true;
			}
			
			if(isset($subscriber["NAME"]) == false)
				$subscriber["NAME"] = null;
			
			if(isset($subscriber["EMAIL"]) == false)
				$subscriber["EMAIL"] = null;
			
			//empty custom fields
			
			$arrCustomFields = $this->getListCustomFieldsShort($listID);

		    $personalFields = UniteFunctionsRes::getVal($subscriber, "PERSONAL_FIELDS");
			if(empty($personalFields))
				$personalFields = array();
				
			foreach($arrCustomFields as $fieldID => $fieldName){
				if(isset($personalFields[$fieldID]) == false)
					$personalFields[$fieldID] = "";
			}
		    
			if(!empty($personalFields))
				$subscriber["PERSONAL_FIELDS"] = $personalFields;
			
		}
		
		$this->updateSubscriberInList($listID, $subscriber);
		
		return(true);
	}
	
	
	/**
	 * insert fields to list
	 */
	public function insertSubscriberToList($listID, $arrFields, $isUpdateOnFound = false, $isResubscribe=false){
		
		$arrEmail = UniteFunctionsRes::getVal($arrFields, "email");
		$email = UniteFunctionsRes::getVal($arrEmail, "value");
		
		$arrPhone = UniteFunctionsRes::getVal($arrFields, "phone");
		$phone = UniteFunctionsRes::getVal($arrPhone, "value");
		
		$arrName = UniteFunctionsRes::getVal($arrFields, "name");
		$name = UniteFunctionsRes::getVal($arrName, "value");
		
		$arrPersonalFields = array();
		foreach($arrFields as $key=>$field){
			
			switch($key){
				case "name":
				case "phone":
				case "email":
					continue(2);
				break;
			}
			
			//insert personal field
			$value = UniteFunctionsRes::getVal($field, "value");
			$fieldID = UniteFunctionsRes::getVal($field, "field_id");
						
			if(empty($fieldID))
			    continue;
			
			$arrPersonalFields[$fieldID] = $value;
		}
				
		$subscriber = array(
				'EMAIL' => $email,
				'NAME' => $name
		);
		
		if(!empty($phone))
			$subscriber['PHONE'] = $phone;
		
					
		if(!empty($arrPersonalFields)){
		    
		    $subscriber["PERSONAL_FIELDS"] = $arrPersonalFields;
		 
		   // $subscriber = array_merge($subscriber,$arrPersonalFields);   
		}
		
		$isExists = $this->insertSubscriberArray($listID, $subscriber, $isUpdateOnFound, $isResubscribe);
		
		return($isExists);
	}
	
	/**
	* add some fake lists
	 */
	private function addFakeListsForTest($arrLists){
		
		if(empty($arrLists))
			return($arrLists);

		$numLists = 20;
		
		for($i=0;$i<$numLists;$i++){
			
			$serial = $i+1;
			
			$firstList = $arrLists[0];
			$firstList["ID"] = UniteFunctionsRes::getRandomString(5, true);
			$firstList["DESCRIPTION"] .= " $serial";
		
			$arrLists[] = $firstList;
			
		}
						
		return($arrLists);
	}
	
	/**
	 * get lists array
	 * make solution with more then 500 lists as well
	 */
	public function getLists(){
		
		$limit = 500;
		
	    if(!empty(self::$cacheLists))
	        return(self::$cacheLists);
	     
	     $params = array();
	     
	     $arrResponse = $this->request("lists");
	     
	     if(empty($arrResponse))
	     	return(array());
	  	  
	     $arrLists = UniteFunctionsRes::getVal($arrResponse, "LISTS");
	     
	     if(empty($arrLists))
	     	return(array());
	     
	     $numLists = count($arrLists);
	     
	     //take other lists till the end
	     
	     if($numLists == $limit){
	     	
	     	$offset = 0;
	     	
	     	do{
	     		
	     	   //get the other lists
	     	   
	     	   $offset += 500;
	     	   
		       $params["limit"] = $limit;
		       $params["offset"] = $offset;
		       
	     	   $arrResponseAdd = $this->request("lists", "get", $params);
	     	   $arrListsAdd = UniteFunctionsRes::getVal($arrResponseAdd, "LISTS");
	     	   
	     	   if(empty($arrListsAdd))
	     	   		$arrListsAdd = array();
	     	   		
	     	   	$numListsAdd = count($arrListsAdd);
	     	    	
	     	   	$arrLists = array_merge($arrLists, $arrListsAdd);
	     	   	
	     	   	
	     	}while($numListsAdd == $limit);
	     }
	     
	     
	     if(GlobalsResponder::DEBUG_MANY_LISTS == true)
	     	$arrLists = $this->addFakeListsForTest($arrLists);
	     
	     $arrLists = UniteFunctionsRes::arrayToAssoc($arrLists,"ID");
	     
	     ksort($arrLists);
	     	     
	     self::$cacheLists = $arrLists;
	    
	     return(self::$cacheLists);
	}
	
	
	/**
	 * get lists short
	 */
	public function getListsShort(){
		
		$arrLists = $this->getLists();
		
		$arrShort = array();
		
		foreach($arrLists as $key=>$list){
			
			$name = UniteFunctionsRes::getVal($list, "DESCRIPTION");
			$arrShort[$key] = $name;
		}
		
		return($arrShort);
	}
	
	
	/**
	 * get list data by id
	 */
	public function getListByID($listID){
        
	    $arrLists = $this->getLists();
	    
	    $arrList = UniteFunctionsRes::getVal($arrLists, $listID);
	    
	    return($arrList);	    
	}
	
	
	/**
	 * get list title
	 */
	public function getListTitle($listID){
	    
	    $arrList = $this->getListByID($listID);
	    
	    $title = UniteFunctionsRes::getVal($arrList, "DESCRIPTION");
	    
	    return($title);
	    	    
	}
	
		
	
	/**
	 * get list custom fields
	 */
	public function getListCustomFields($listID){
		
		if(empty($listID))
			return(null);
		
		if(isset(self::$cacheCustomFields[$listID])){
			
			$arrResponse = self::$cacheCustomFields[$listID];
			
		}else{
			$arrResponse = $this->request("lists/{$listID}/personal_fields");
	    	
			self::$cacheCustomFields[$listID] = $arrResponse;
		}
		
		$arrFields = UniteFunctionsRes::getVal($arrResponse, "PERSONAL_FIELDS");
		if(empty($arrFields))
			$arrFields = array();
					
		return($arrFields);
	}
	
	
	/**
	 * get list custom fields short
	 */
	public function getListCustomFieldsShort($listID){
	    
	        $arrFields = $this->getListCustomFields($listID);
	        $arrShort = array();
	        
	        foreach($arrFields as $field){
	            
	            $fieldID = $field["ID"];
	            
	            $arrShort[$fieldID] = $field["NAME"];
	        }
	        
	       return($arrShort);
	       
	}
	
}