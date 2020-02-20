<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * integrate elementor into responsder
 *
 */
class ResponderElementorIntegrate{

	private $elementorTabSettings;
	private $arrFields;
	public static $isEditMode = false;
	private static $arrWidgetsSettings;
	

	/**
	 * tells if elmeentor exists
	 */
	public static function isElementorExists(){

		if(defined("ELEMENTOR_VERSION"))
			return(true);

		return(false);
	}
	
	/**
	 * check if elementor pro exists
	 */
	public static function isElementorProExists(){
		
		if(defined("ELEMENTOR_PRO_VERSION"))
			return(true);

		return(false);
	}

	/**
	 * get widget settings
	 */
	public static function getWidgetSettings($formID){

		$arrSettings = UniteFunctionsRes::getVal(self::$arrWidgetsSettings, $formID);

		return($arrSettings);
	}


	/**
	 * add field
	 */
	private function getWidgetSettings_addField($name, $title, $isDate = false){

		$arrField = array();
		$arrField["name"] = $name;
		$arrField["title"] = $title;
		$arrField["isdate"] = $isDate;

		return($arrField);
	}


	/**
	 * init fileds values
	 */
	private function initFieldsValues(){

		$arrFields = array();
		$arrFields[] = $this->getWidgetSettings_addField("field_email", __res("Email",RESPONDER_TEXTDOMAIN));
		$arrFields[] = $this->getWidgetSettings_addField("field_name", __res("Name",RESPONDER_TEXTDOMAIN));
		$arrFields[] = $this->getWidgetSettings_addField("field_phone", __res("Phone",RESPONDER_TEXTDOMAIN));


		$this->arrFields = $arrFields;
	}

	/**
	 * get widget settings
	 */
	private function getElementorTabSettings(){

		$arrSettings = array();

		$arrSettings = HelperResponder::getElementorFormSettings();


		return($arrSettings);
	}


	/**
	 * on native widgets registered
	 * add the widgets
	 */
	public function on_widgets_registered() {

       $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
       $pathWidgets = __DIR__."/widgets/";

		
       //require the widget
       $arrForms = UniteFunctionsRes::getVal($this->elementorTabSettings, "elementor_forms");

       if(empty($arrForms))
       	  return(false);

       require_once $pathWidgets . 'responder_form_widget.class.php';


       foreach($arrForms as $form){

       		$title = UniteFunctionsRes::getVal($form, "title");
       		if(empty($title))
       			$form["title"] = __res("Responder Form", RESPONDER_TEXTDOMAIN);
			
       		$formID = UniteFunctionsRes::getVal($form, "generated_id");
       		$formName = "responder_form_".$formID;

       		$arrFields = $this->arrFields;

       		//save widget settings
       		$arrSettings = $form;
       		$arrSettings["form_name"] = $formName;
       		$arrSettings["fields"] = $arrFields;	//additional fields later

       		self::$arrWidgetsSettings[$formName] = $arrSettings;

       		$className = "ResponderDynamicForm_{$formName}";
       		$code = "class {$className} extends \ResponderDynamicForm{}";
		    eval($code);

		    //dmp("added: $formName");

	        $objWidget = new $className;
	   		$widgets_manager->register_widget_type($objWidget);

       }
		

	}


    /**
     * add elementor category
     */
    public function add_elementor_category(){

       \Elementor\Plugin::instance()->elements_manager->add_category( 'responder', array(
                'title' => __res("Responder", RESPONDER_TEXTDOMAIN),
                'icon'  => 'fa fa-plug',
        ), 1 );

    }



    /**
     * put widgets scripts
     */
    public function onRegisterFrontScripts(){

    	wp_enqueue_script("jquery");
		
	    wp_register_script('elmentor-responder-front', GlobalsResponder::$urlPlugin."js/elementor_front.js");
	    
	    wp_register_style('elmentor-responder-forms', GlobalsResponder::$urlPlugin."css/style_forms.css");

	    
        //datepicker styles and scripts
        
        $urlDatePicker = GlobalsResponder::$urlPlugin."assets/datepicker/";
        
        wp_register_script("responder-picker", $urlDatePicker."picker.js");
        wp_register_script("responder-picker-date",$urlDatePicker."picker.date.js");
        
        wp_register_style("responder-picker-classic",$urlDatePicker."classic.css");
        wp_register_style("responder-picker-date-classic",$urlDatePicker."classic.date.css");
        wp_register_style("responder-picker-rtl",$urlDatePicker."rtl.css");
        
        if(self::$isEditMode == true){

        	wp_enqueue_style("elmentor-responder-forms");
        	wp_enqueue_style("responder-picker-classic");
        	wp_enqueue_style("responder-picker-date-classic");
        	wp_enqueue_style("responder-picker-rtl");
        }
        
                
    }

    /**
     * register backend scripts
     */
    public function onRegisterBackScripts(){

    	wp_enqueue_style('elmentor-responder-forms', GlobalsResponder::$urlPlugin."css/style_forms.css");

    }

    /**
     * register new controls
     */
    public function onRegisterControls(){
       
       	$pathControls = __DIR__."/controls/";
       	require_once $pathControls."heading_control_rtl.class.php";	
       	require_once $pathControls."fields_map.class.php";	
       	require_once $pathControls."select_control_rtl.class.php";	
       	
       	
       	$controlHeadingRtl = new Elementor\Control_HeadingRTL();
       	$controlFieldsMap = new Elementor\Control_ResponderFieldsMap();
       	
       	$controlSelectRtl = new Elementor\Control_Select_Responder_RTL();
       	
       	       	
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		
		$controls_manager->register_control( 'heading_rtl', $controlHeadingRtl);       	
		$controls_manager->register_control( 'select_responder_rtl', $controlSelectRtl);
		$controls_manager->register_control( 'responder_fields_map', $controlFieldsMap);       	
    	
    }
    
    /**
     * tells if forms exists
     */
    public function isFormsExists($arrSettings = null){
       
    	if(empty($arrSettings))
    		$arrSettings = $this->getElementorTabSettings();
    	
    	$elementorForms = UniteFunctionsRes::getVal($arrSettings, "elementor_forms");
    	
    	if(empty($elementorForms))
    		return(false);
    	
    	return(true);
    }
    
	
	/**
	 * init the integration
	 */
	public function init(){
		
		$isExists = self::isElementorExists();
		if($isExists == false)
			return(false);
		
		$arrSettings = $this->getElementorTabSettings();
		
    	//init elementor pro
    	$isProExists = self::isElementorProExists();
    	
    	if($isProExists){
    		$objIntegratePro = new ResponderElementorProIntegrate();
    		$objIntegratePro->init();
    	}
    	
    	$isPreviewOption = UniteFunctionsRes::getGetVar("elementor-preview", "", UniteFunctionsRes::SANITIZE_KEY);
    	if(!empty($isPreviewOption))
    		self::$isEditMode = true;
    	
		$isFormsExists = $this->isFormsExists($arrSettings);

		$isRegisterControls = ($isProExists || $isFormsExists);
		
		if($isRegisterControls == true)
	    	add_action( 'elementor/controls/controls_registered', [ $this, 'onRegisterControls' ] );
		
		
		if($isFormsExists == true){
			
	       	$this->elementorTabSettings = $arrSettings;
			
	       	$this->initFieldsValues();
	
			add_action( 'elementor/init', array( $this, 'add_elementor_category' ) );
			add_action('elementor/widgets/widgets_registered', array($this, 'on_widgets_registered' ) );
	    	add_action('elementor/frontend/after_register_scripts', array($this, 'onRegisterFrontScripts'), 10);
	    	add_action('elementor/backend/after_register_scripts', array($this, 'onRegisterBackScripts'), 10);						
		}
			
    	    	
    	
	}
    
}
