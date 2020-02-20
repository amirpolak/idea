<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * responder helper
 *
 */
class HelperResponder{

	/**
	 * validate that all is ready like php curl functions
	 */
	public static function validateResponderReady(){
		
		//check curl
		if(function_exists("curl_init") == false){
			$message = __res("CURL PHP extension not installed. Please enable it in php.ini", RESPONDER_TEXTDOMAIN);			
			
			UniteFunctionsRes::throwError($message);
			
		}
		
	}
	
	/**
	 * create nonce
	 */
	public static function createNonce(){
		
        $nonce = wp_create_nonce("responder_actions");
		
        return($nonce);
	}
	
	/**
	 * get responder plugin version
	 */
	public static function getResponderPluginVersion(){
		
		$arrPlugins = get_plugins();
		
		$arrPlugin = $arrPlugins["responder/responder.php"];
		
		if(empty($arrPlugin))
			return(null);
		
		$version = $arrPlugin["Version"];
		
		return($version);
	}
	
	
    /**
     * get elementor settings
     */
    public static function getElementorFormSettings(){
		
    	$settings = get_option(GlobalsResponder::OPTION_ELEMENTOR_SETTINGS);

    	if(empty($settings))
    		$settings = array();

    	return($settings);
    }

    
    /**
     * get elementor settings
     */
    public static function getAdvancedFormSettings(){
		
    	$settings = get_option(GlobalsResponder::OPTION_ADVANCED_SETTINGS);

    	if(empty($settings))
    		$settings = array();

    	return($settings);
    }
    
    
    /**
     * get form settings by form ID from the repeater.
     * if not found - return null
     */
    public static function getElementorFormRepeaterSettings($formID){
    	
    	$settings = self::getElementorFormSettings();
    	
    	$arrForms = UniteFunctionsRes::getVal($settings, "elementor_forms");
    	
    	if(empty($arrForms))
    		return(null);
    		
    	foreach($arrForms as $formSettings){
    		
    		$formGeneratedID = UniteFunctionsRes::getVal($formSettings, "generated_id");
    		if($formGeneratedID == $formID)
    			return($formSettings);
    	}

    	return(null);
    }
    
	
}