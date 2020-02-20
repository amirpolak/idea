<?php
 
/**
 * responder settings elementor view
 *
 */
class ResponderSettingsElementorView{

	private $settings;
	private $initErrorMessage;
	private $listsResponse;
	private $arrListsShort;
	private $numForms;
	

	/**
	 * constructor
	 */
	public function __construct(){
		
	}

	/**
	 * draw fields list setting
	 */
	public function drawFieldsListSetting($setting){

		unset($setting["callback"]);

?>
		<div class="right_field">

                <table class='uc-table-custom-setting'>
                    <tr>
                        <th style="text-align:right;">
                        	<b>
                        	שם השדה
                        	</b>
                        </th>
                    </tr>
                    <tr>

                        <td>
                            <div style="margin-left: 20px;">
                                <ul class="n_right">
                                    <li>
                                        <!-- <label>מייל</label> -->
                                    </li>
                                    <li>
                                        <label>שם</label>
                                    </li>
                                    <li>
                                        <label>טלפון</label>
                                    </li>
                                </ul>
	                            <ul class="n_right uc-add-fields"> </ul>
                            </div>
                        </td>

                </table>

		</div>

<?php

	}

	
	/**
	 * get repeater item settings
	 */
	private function getRepeaterItemSettings(){

		$settings = new UniteSettingsRes();

		$settings->addTextBox("title",
									 "טופס ראשון",
									 "שם הטופס (השם מוסתר למבקרים באתר)");

		$params = array("hidden"=>true);
		$settings->addTextBox("generated_id", "", "Generated ID", $params);


		//add list chooser
		$optionLists = array_flip($this->arrListsShort);
		

		$firstListID = UniteFunctionsRes::getArrFirstValue($optionLists);

		$settings->addSelect("list_id", $optionLists, "הרשימה ברב מסר שאליה יוכנסו פרטי הנרשמים", $firstListID);
	
		//default type (hor, vert)

		$optionLists = array();
		
		$textVert = __res("Vertical Form", RESPONDER_TEXTDOMAIN);
		$textHor = __res("Horizontal Form", RESPONDER_TEXTDOMAIN);
		
		$optionLists[$textVert] = "vert";
		$optionLists[$textHor] = "hor";

		$settings->addSelect("form_defaults_type", $optionLists, "סוג הטופס", "vert");
		
		//on esiting what to do
		
		$optionsExsting = array();
		//$optionsExsting["show_success"] = __res("Show Success Message", RESPONDER_TEXTDOMAIN);
		//$optionsExsting["show_error"] = __res("Show Error Message", RESPONDER_TEXTDOMAIN);
		$optionsExsting["update"] = __res("Update Subscriber", RESPONDER_TEXTDOMAIN);
		$optionsExsting["update_resubscribe"] = __res("Resubscribe", RESPONDER_TEXTDOMAIN);
		
		$optionsExsting = array_flip($optionsExsting);
		
		$settings->addSelect("action_on_existing", $optionsExsting, __res("Action on Existing Subscriber", RESPONDER_TEXTDOMAIN) ,"update");

		return($settings);
	}

	/**
	 * modify values
	 */
	private function modifySettingValues($arrSettingValues){
		
		$arrForms = UniteFunctionsRes::getVal($arrSettingValues, "elementor_forms");
		
		if(empty($arrForms))
			return($arrSettingValues);
				
		foreach($arrForms as $index=>$form){
			
			$title = UniteFunctionsRes::getVal($form, "title");
			$newTitle = trim($title);
						
			$numCharsMax = 70;
			$numChars = strlen($newTitle);
			
			if($numChars > $numCharsMax){
				$newTitle = substr($title, 0,$numCharsMax);
				$newTitle = trim($newTitle)." - $numChars chars originally";
			}
			
			if(empty($newTitle))
				$newTitle = "form".UniteFunctionsRes::getRandomString();
			
			if($title !== $newTitle)
				$arrForms[$index]["title"] = $newTitle;
			
		}
		
		$arrSettingValues["elementor_forms"] = $arrForms;
				
		return($arrSettingValues);
	}
	

	/**
	 * init the settings
	 */
	private function initSettings(){


		$this->settings = new UniteSettingsRes();

		$optionsBoolean = array("כן"=>"true","לא"=>"false");
		
		//add repeater
		$settingsItem = $this->getRepeaterItemSettings();
		$options = array();
		$options["add_button_text"] = __res("Add New Form",RESPONDER_TEXTDOMAIN);
		$options["empty_text"] = "לא נמצאו טפסים";
		$options["item_title"] = "טופס";
		$options["delete_button_text"] = "מחק טופס";

		$this->settings->addRepeater("elementor_forms", $settingsItem, null, UniteSettingsRes::PARAM_NOTEXT, $options);
		
		//get values
		$arrSettingValues = HelperResponder::getElementorFormSettings();
		
		$arrSettingValues = $this->modifySettingValues($arrSettingValues);
		
		
		$this->settings->setStoredValues($arrSettingValues);
		
		//set num forms
		$this->numForms = 0;
		
		$arrForms = UniteFunctionsRes::getVal($arrSettingValues, "elementor_forms");
		if(!empty($arrForms))
			$this->numForms = count($arrForms);	
		
	}

	/**
	 * mutiply lists
	 */
	private function temp_multiplyLists($arrLists){
		
		$counter = 0;
		for($i=0;$i<100;$i++){
			
			$counter++;
			$key1 = "temp_first".$counter;
			
			$value1 = "temp first ".$counter;
			
			$counter++;
			$key2 = "temp_second".$counter;
			$value2 = "temp second ".$counter;
			
			$arrLists[$key1] = $value1;
			$arrLists[$key2] = $value2;
			
		}
		
		return($arrLists);		
	}
	

	/**
	 * get lists short
	 */
	private function initListsShort(){
		
		$api = new ResponderAPIRes();
		
		$arrLists = $api->getLists();
		
		
		$errorMessage = "lists_not_found";
		if(empty($arrLists))
			UniteFunctionsRes::throwError($errorMessage);

		$arrShort = array();
				
		foreach($arrLists as $list){

			$listID = $list["ID"];
			$description = $list["DESCRIPTION"];
	
			$arrShort[$listID] = $description;
		}
				
		//remove me
		//$arrShort = $this->temp_multiplyLists($arrShort);
		
		$this->arrListsShort = $arrShort;

		if(empty($this->arrListsShort))
			UniteFunctionsRes::throwError($errorMessage);
		
	}

	/**
	 * init the view
	 */
	public function init($status){
		
				
		try{

			$isElementorExists = ResponderElementorIntegrate::isElementorExists();
			if($isElementorExists == false){

				UniteFunctionsRes::throwError("elementor_not_found");
			}

			if($status != "on")
				UniteFunctionsRes::throwError("responder_connection_error");

			$this->initListsShort();

			$this->initSettings();

		}catch(Exception $e){

			$this->initErrorMessage = $e->getMessage();

		}

	}


	/**
	 * output html title
	 */
	public function outputHtmlTitle(){
		?>
		<h1>טפסים לאלמנטור</h1>
		
		<div class="res-elementor-tab-subtitle">
			<?php echo __res("Its posible to create forms for free version of elementor",RESPONDER_TEXTDOMAIN)?>
		</div>
		
		<h2>הטפסים שלי</h2>
		
		<?php
	}

	/**
	 * output error message
	 */
	public function outputErrorMessage($message){


		?>
		<div class="unite_error_message">
			<?php
		switch($message){
			case "elementor_not_found":
				?>

תוסף אלמנטור לא מותקן או לא מופעל באתר זה.

<br>

הלשונית הזה תהיה פעילה ברגע שתוסף האלמנטור יהיה פעיל גם.
				<?php
			break;
			case "responder_connection_error":
				?>
				אין גישה לרב מסר
				<?php
			break;
			case "lists_not_found":
				?>
				לא נמצאו רשימות, אנה בדוק גישה לרב מסר או לאינטרנט
				<?php
			break;
			default:
				echo $message;
			break;
		}

			?>
		</div>
		<?php
	}



	/**
	 * output settings view
	 */
	public function outputSettingsView(){

		$objOutput = new UniteSettingsOutputWideRes();
		$objOutput->init($this->settings);

		?>

		<?php $objOutput->draw("settings_elementor_form")?>

		<div class="vert_sap30"></div>
		
		<div class="elementor-settings-button-wrapper">
		
			<a id="res_button_save_elementor_settings" class="res-button-primary" data-numforms="<?php echo $this->numForms?>" >שמירת הטפסים</a>
			<div id="res_saving_text" class="loader_text_rtl" style="display:none">שומר...</div>
			<div id="res_message_saved" class="unite-color-green" style="display:none">ההגדרות נשמרו בהצלחה</div>
			<div id="res_message_error" class="unite_error_message" style="display:none">ההגדרות נשמרו בהצלחה</div>
	
			 <br>
			 
			 <div id="res_addnew_elementor_message" class="res-addnew-elementor-message" style="display:none">
			 	הטופס מוכן לשימוש ונמצא בתפריט האלמנטים באלמנטור
			 </div>
			 
			 <div class="res-text-comment">
			 
<p>* אם בעתיד תכניסו שינויים ברשימות שלכם ברב מסר או תוסיפו שדות מותאמים אישית,</p>
<p style="padding-right:8px;">תוכלו לעדכן את זה בקלות גם בתוסף:</p>
<p style="padding-right:8px;">כל מה שצריך לעשות זה ללחוץ שוב על כפתור השמירה. הטפסים באלמנטור יתרעננו ויתעדכנו.</p>
			 
			 </div>
		 
		</div>		 
		
		<?php

	}


	/**
	 * output html
	 */
	public function outputHtml(){

		$this->outputHtmlTitle();
		if(!empty($this->initErrorMessage))
			$this->outputErrorMessage($this->initErrorMessage);
		else
			$this->outputSettingsView();


	}

}





