<?php
 
/**
 * responder settings advanced view
 *
 */
class ResponderSettingsAdvancedView{

	private $settings;
	
	
	/**
	 * get settings
	 */
	private function initSettings(){
		
		$settings = new UniteSettingsRes();
		
		$optionsBoolean = array("כן"=>"true","לא"=>"false");
		$settings->addRadio("enable_debug", $optionsBoolean, "לאפשר DEBUG", "false");
		
		//elementor pro debug
		/*
		$optionsBoolean = array("כן"=>"true","לא"=>"false");
		$params = array();
		$params["description"] = "במקרה של כן הטופס לא ישלח אלה יציג ערכים";
		
		$settings->addRadio("enable_elementor_pro_debug", $optionsBoolean, "לאפשר DEBUG לאלמנטור פרו", "false", $params);
		*/
		
		//set values
		$settingsValues = HelperResponder::getAdvancedFormSettings();
		
		$settings->setStoredValues($settingsValues);
		
		
		$this->settings = $settings;
		
		return($settings);
	}
	
	/**
	 * output settings view
	 */
	public function outputSettingsView(){

		$objOutput = new UniteSettingsOutputWideRes();
		$objOutput->init($this->settings);

		?>
		
		<?php $objOutput->draw("settings_advanced_form")?>
		
		<div class="vert_sap30"></div>
		
		<div class="elementor-settings-button-wrapper">
			
			<a id="res_button_save_advanced_settings" class="res-button-primary" >שמירת הגדרות מתקדמות</a>
			<div id="res_saving_text_advanced" class="loader_text_rtl" style="display:none">שומר...</div>
			<div id="res_message_saved_advanced" class="unite-color-green" style="display:none">ההגדרות נשמרו בהצלחה</div>
			<div id="res_message_error_advanced" class="unite_error_message" style="display:none">ההגדרות נשמרו בהצלחה</div>
	
			<br>
			 
		</div>
		 
		<?php

	}
	
	/**
	 * put current path html
	 */
	private function putHtmlCurrentPath(){
		
		//dmp(GlobalsResponder::$pathPlugin);

		$pathPlugin = GlobalsResponder::$pathPlugin;
		?>
		<br>
		מיקום הפלאגין: 
		<pre style='display:inline;direction:ltr;'>
		<?php echo $pathPlugin?>
		</pre>
		</br>
		<?php 
	}
	
	/**
	 * put debug connection url
	 */
	private function putHtmlDebugConnection(){
		
		$urlDebug = GlobalsResponder::$urlAdminSettingsDebugConnection;
		
		
	}
	
	/**
	 * put html
	 */
	public function outputHtml(){
		
		?>
		<div id="uc_advanced_settings_wrapper">
			
			<h1>הגדרות מתקדמות</h1>
			
			<div class="res-elementor-tab-subtitle">
				כאן תוכלו להגדיר הגדרות מתקדמות
			</div>
			
			<?php $this->putHtmlCurrentPath()?>
			<?php $this->putHtmlDebugConnection()?>
			<?php
			$this->outputSettingsView();
			?>
		
		</div>
		<?php 
	}

	/**
	 * init
	 */
	public function init(){
		
		$this->initSettings();
		
	}
	
	
}





