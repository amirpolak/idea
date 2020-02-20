<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * 
 * creator helper functions class
 *
 */
	class HelperHtmlRes extends HtmlOutputBaseRes{
				

		
		/**
		 *
		 * get link html
		 */
		public static function getHtmlLink($link,$text,$id="",$class="", $isNewWindow = false){
		
			if(!empty($class))
				$class = " class='$class'";
		
			if(!empty($id))
				$id = " id='$id'";
		
			$htmlAdd = "";
			if($isNewWindow == true)
				$htmlAdd = ' target="_blank"';
		
			$html = "<a href=\"$link\"".$id.$class.$htmlAdd.">$text</a>";
			return($html);
		}

		
		/**
		 *
		 * get select from array
		 */
		public static function getHTMLSelect($arr,$default="",$htmlParams="",$assoc = false, $addData = null, $addDataText = null){
		
			$html = "<select $htmlParams>";
			//add first item
			if($addData == "not_chosen"){
				$selected = "";
				$default = trim($default);
				if(empty($default))
					$selected = " selected ";
					
				$itemText = $addDataText;
				if(empty($itemText))
					$itemText = "[".__("not chosen", RESPONDER_TEXTDOMAIN)."]";
					
				$html .= "<option $selected value=''>{$itemText}</option>";
			}
			
			foreach($arr as $key=>$item){
				$selected = "";
		
				if($assoc == false){
					if($item == $default) 
						$selected = " selected ";
				}
				else{
					if(trim($key) == trim($default))
						$selected = " selected ";
				}
				
				$addHtml = "";
				if(strpos($key, "html_select_sap") !== false)
					$addHtml = " disabled";
				
				if($assoc == true)
					$html .= "<option $selected value='$key' $addHtml>$item</option>";
				else
					$html .= "<option $selected value='$item' $addHtml>$item</option>";
			}
			$html.= "</select>";
			return($html);
		}
		
		/**
		 * get hidden input field
		 */
		public static function getHiddenInputField($name, $value){
			$value = htmlspecialchars($value);
			
			$html = '<input type="hidden" name="'.$name.'" value="'.$value.'">';
			
			return($html);
		}
		
		/**
		 * show error div
		 */
		public static function showErrorDiv($message){
			?>
			<div class="unite_error_message"><?php echo $message?></div>
			<?php 
		}
		
		/**
		 * output exception
		 */
		public static function outputError(Error $e){
						
			$message = $e->getMessage();
			$trace = $e->getTraceAsString();
			$line = $e->getLine();
			$file = $e->getFile();
			
			dmp("PHP Error Occured!!!");
			
			dmp("<b>$message </b>");
			dmp("in file: <b>$file</b> (line: <b>$line</b>)");
			dmp("trace: ");
			dmp($trace);
				
		}
		
		
		/**
		 * output exception
		 */
		public static function outputException($e, $prefix=""){
			
			if(empty($prefix))
				$prefix = "Responder Error: ";
			
			$message = $prefix.$e->getMessage();
			$trace = $e->getTraceAsString();
			
			dmp($message);
			if(GlobalsResponder::SHOW_TRACE == true)
				dmp($trace);
			else
				if($e instanceof Error)
					dmp($trace);
				
		}
		
		
		
	} //end class
	
	
	