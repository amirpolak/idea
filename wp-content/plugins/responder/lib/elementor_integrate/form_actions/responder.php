<?php 

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Modules\Forms\Classes\Form_Record;
use ElementorPro\Modules\Forms\Classes\Integration_Base;
use ElementorPro\Modules\Forms\Controls\Fields_Map;
use ElementorPro\Modules\Forms\Classes\Mailchimp_Handler;
use Elementor\Settings;


class Responder_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
		
	
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'responder';
	}

	
	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'Responder', RESPONDER_TEXTDOMAIN );
	}
	
	
	/**
	 * throw debug error
	 */
	private function throwDebugError($array1, $array2 = null){
		
		$message = print_r($array1, true);
				
		$message = "<pre>$message</pre>";
		
		if(!empty($array2))
			$message .= "<pre>".print_r($array2, true)."</pre>";
		
		UniteFunctionsRes::throwError($message);
	}
	
	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		
		try{
			
			$arrFormSettings = $record->get("form_settings");
			$arrFields = $record->get('fields');
			
			//check if debug
			$settingsValuesAdvanced = HelperResponder::getAdvancedFormSettings();
			
			$optionEnableDebug = UniteFunctionsRes::getVal($settingsValuesAdvanced, "enable_elementor_pro_debug");
			$optionEnableDebug = UniteFunctionsRes::strToBool($optionEnableDebug);
						
			
			$settingFieldMap = UniteFunctionsRes::getVal($arrFormSettings, "responder_fields_map");
			
			if($optionEnableDebug === true){
				$this->throwDebugError($settingFieldMap, $arrFields);
			}
			
			
			$actionOnExisting = UniteFunctionsRes::getVal($arrFormSettings, "responder_onexisting");
			
			$isResubscribe = false;
			if($actionOnExisting == "resubscribe")
				$isResubscribe = true;
			
				
			$arrFieldMap = UniteFunctionsRes::jsonDecode($settingFieldMap);
			$arrFieldMapFields = UniteFunctionsRes::getVal($arrFieldMap, "fields");
			
			if(empty($arrFieldMapFields))
				UniteFunctionsRes::throwError("No linking fields");
			
			$arrFieldMapFields = array_flip($arrFieldMapFields);
			
			$listID = UniteFunctionsRes::getVal($arrFieldMap, "list_id");
			
			if(empty($listID))
				UniteFunctionsRes::throwError("List not found");
			
			if(empty($arrFields))
				UniteFunctionsRes::throwError("nothing to send");
			
			$arrSubscriber = array();
			
			//get subscriber array
			foreach($arrFields as $fieldName => $arrField){
				
				$responderField = UniteFunctionsRes::getVal($arrFieldMapFields, $fieldName);
				
				if(empty($responderField))
					continue;
				
				$responderField = str_replace("responder_field_", "", $responderField);
				
				if(empty($responderField))
					continue;
				
				$fieldValue = UniteFunctionsRes::getVal($arrField, "value");
				
				$arrSubscriber[$responderField] = $fieldValue;
			}
			
			$arrAPISubscriber = array();
			$arrCustomFields = array();
			
			foreach($arrSubscriber as $name => $value){
				
				switch($name){
					case "name":
						$arrAPISubscriber["NAME"] = $value;
					break;
					case "email":
						$arrAPISubscriber["EMAIL"] = $value;
					break;
					case "phone":
						$arrAPISubscriber["PHONE"] = $value;
					break;
				}
				
				if(is_numeric($name))
					$arrCustomFields[$name] = $value;
			}
			
			if(!empty($arrCustomFields))
			    $arrAPISubscriber["PERSONAL_FIELDS"] = $arrCustomFields;
				    
	    	$api = new ResponderAPIRes();		//update on found, resubscribe
			$api->insertSubscriberArray($listID, $arrAPISubscriber, true, $isResubscribe);
			
		}catch(Exception $e){
			
			$message = $e->getMessage();
			$ajax_handler->add_error_message($message);
			
		}
		
	}
	

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		
		$widget->start_controls_section(
			'section_responder',
			[
				'label' => __( 'Responder', 'text-domain' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
		
        $widget->add_control(
            'responder_onexisting',
            [
                'label' => __res( 'Action on Existing Subscriber', RESPONDER_TEXTDOMAIN ),
                'type' => "select_responder_rtl",
                'default' => "update",
                'options' => array("update"=>__res( 'Update Subscriber', RESPONDER_TEXTDOMAIN ),
                				   "resubscribe"=>__res( 'Resubscribe', RESPONDER_TEXTDOMAIN )
            				 )
            ]
            
        );
		
        
        
        $widget->add_control(
            'responder_divider1',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
            
        );
        
		
		$widget->add_control(
			'responder_fields_map',
			[
				'label' => __( 'Field Mapping', 'elementor-pro' ),
				'type' => "responder_fields_map"
			]
		);
		
		
		$widget->end_controls_section();

	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */
	public function on_export( $element ) {
		unset(
			$element['responder_fields_map']
		);
	}
}