<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class GlobalsResponder{
	
	const SHOW_TRACE = false;
	
	const INTEGRATE_ELEMENTOR_PRO = true;
	
	const OPTION_ELEMENTOR_SETTINGS = "responder_settings_elementor";
	const OPTION_ADVANCED_SETTINGS = "responder_settings_advanced";
	const POST_TYPE_DEBUG_SUBMISSION = "responder_debug";
	const DEBUG_MANY_LISTS = false;		//for test purposes only, add fake lists
	
	
	public static $isDebug = false;
	public static $urlAjaxFront;
	public static $urlAjaxAdmin;
	
	public static $urlAdminSettings;
	public static $urlAdminSettingsHiddenTab;
	public static $urlAdminSettingsDebugConnection;
	
	public static $urlPlugin;
	public static $pathPlugin;
	public static $isTranslateToHebrew = true;
	public static $clientKey = '6FF503AEA0B1E24E72583C99B0DD471A';
	public static $clientSecret = '72818D716ABC9AF0A959696576F418B0';
	public static $userKey;
	public static $userSecret;
	
	
	/**
	 * init globals
	 */
	public static function initGlobals(){
		
		self::$urlAjaxFront = admin_url("admin-ajax.php")."?action=responder_ajax_action_front";
		self::$urlAjaxAdmin = admin_url("admin-ajax.php")."?action=responder_ajax_action";
		
		self::$urlAdminSettings = admin_url("admin.php?page=Responder_PluginSettings");
		
        self::$userKey = get_option('Responder_Plugin_EnterUsername');
        self::$userSecret = get_option('Responder_Plugin_EnterPassword');
		
        self::$pathPlugin = realpath(dirname(__FILE__)."/../")."/";
        
        self::$urlPlugin = plugins_url("", self::$pathPlugin."responder.php")."/";
        
        self::$urlAdminSettingsHiddenTab = self::$urlAdminSettings."&showadvanced=true";
        self::$urlAdminSettingsDebugConnection = self::$urlAdminSettings."&showadvanced=true&debugconnection=true";
        
        //enable debug
        $advancedSettings = HelperResponder::getAdvancedFormSettings();
        $isEnableDebug = UniteFunctionsRes::getVal($advancedSettings, "enable_debug");
        $isEnableDebug = UniteFunctionsRes::strToBool($isEnableDebug);
        
        if($isEnableDebug == true)
        	self::$isDebug = true;
                
	}
	
}



GlobalsResponder::initGlobals();
