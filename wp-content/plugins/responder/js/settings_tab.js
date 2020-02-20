function ResSettingsTabView(){
	
	var t = this;
	if(!g_resAdmin)
		window.g_resAdmin = new UniteAdminRes();

	var g_objSettings, g_settingsContainer, g_objSettingsAdvanced;
	var g_objButtonSave, g_numFormsBefore = 0;
    var state = "list";
	
	function _______________ELEMENTOR_______________(){}
    

	/**
	 * get form settings values
	 */
	function getSettingsValues(){
		
		var settingsValues = g_objSettings.getSettingsValues();
		
		//get add fields
		var objLabels = jQuery("#n_el_fields label");
		if(objLabels.length == 0)
			return(settingsValues);
		
		var arrFields = [];
		
		jQuery.each(objLabels, function(index, label){
			
			var field = jQuery(label).text();
			
			arrFields.push(field);
			
		});
		
		settingsValues["add_fields"] = arrFields;
		
		return(settingsValues);
	}
	
	/**
	 * get num forms from save data
	 */
	function getNumFormsFromData(data){
		
		if(data.settings.hasOwnProperty("elementor_forms") == false)
			return(0);
		
		var numFormsAfter = data.settings.elementor_forms.length;
		
		return(numFormsAfter);
	}
	
	
	/**
	 * show message instructions
	 */
	function showElementorInstructionsMessage(){
		
		var objMessage = jQuery("#res_addnew_elementor_message");
		
		objMessage.show();
		
		setTimeout(function(){
			objMessage.hide();
		}, 7000);
		
	}
	
	
	/**
	 * on button save click
	 */
	function onSaveButtonClick(){
				
		var settingsValues = getSettingsValues();
				
		var data = {settings:settingsValues};
		
		g_resAdmin.setErrorMessageID("res_message_error");
		g_resAdmin.setSuccessMessageID("res_message_saved");
		g_resAdmin.setAjaxLoaderID("res_saving_text");
		g_resAdmin.setAjaxHideButtonID("res_button_save_elementor_settings");
		
		g_resAdmin.ajaxRequest("save_elementor_settings", data, function(){
			
			var numFormsAfter = getNumFormsFromData(data);
			
			if(numFormsAfter > g_numFormsBefore){
				
				setTimeout(showElementorInstructionsMessage, 500);
			}
			
			g_numFormsBefore = numFormsAfter;
				
			
		});
		
	}

	/**
	 * init forms for elementor view
	 */
	function initFormsElementorView(){
		
		g_settingsContainer = jQuery("#plugin_config-elementor").children(".unite_settings_wrapper");
		
		if(g_settingsContainer.length == 0)
			return(false);
		
		//throw new Error("Settings container not found");
		
		g_objSettings = new UniteSettingsRes();
		g_objSettings.init(g_settingsContainer);
		
		g_objButtonSave = jQuery("#res_button_save_elementor_settings");
		
		g_numFormsBefore = g_objButtonSave.data("numforms");
		if(!g_numFormsBefore)		
			g_numFormsBefore = 0;
		
		g_objButtonSave.click(onSaveButtonClick);
		
	}
	
	function _______________FORMS_______________(){}
	
    //bring regular (non elementor form fields)
	function bringRegularFormFields(suffix){
		
    	validateSuffix(suffix);
		
        bring_fields('#select_form'+suffix, '#n_fields'+suffix, suffix);
	}
	
    
    //bring elementor 
	function bringElementorFormFields(){
		
        bring_fields('#plugin_config-elementor select[name=list_id]', '#n_el_fields');
	}

    //bring fields of a form
    function bring_fields(formSelector, fieldsSelector, suffix) {
    	
        var form_id = jQuery(formSelector).val();
    	var isPojo = false;
    	if(suffix == "_pojo")
    		isPojo = true;
        
        
        if(suffix){
            jQuery('#cc_fields'+suffix).html("טוען...");
            jQuery('#c_fields'+suffix).html("טוען...");
            jQuery('#rqq_fields'+suffix).html("טוען...");
        }
        
        jQuery.ajax({
            url: g_urlAjaxForms,
            type: 'post',
            data: {
                action: 'select_fields',
                id: form_id,
                nonce: g_resNonce
            }
            , success: function (response) {
                var result = response.split(',');
                var build = "";
                var n_build = "";
                var cc_build = "";
                var rq_build = "";
                var hidden_build = "";
                var hidden_value_build = "";
                
                jQuery.each(result, function (i, elem) {
                    var check = elem.split('|');
                    if (typeof check[1] != 'undefined') {
                        build = build + "<li><input type='checkbox' value='" + elem + "' class='cf' cf_id"+suffix+"='cc_" + i + "' ></li>";
                        n_build = n_build + "<li><label>" + check[1] + "</label></li>";
                        rq_build = rq_build + "<li><input type='checkbox' class='rf' rq_id"+suffix+"='cc_" + i + "' ></li>";
						
                        if(isPojo == false){
                            hidden_build = hidden_build + "<li><input type='checkbox' class='rf res-hidden-checkbox"+suffix+" res-hidden' hidden_id"+suffix+"='cc_" + i + "' ></li>";
                                                        
                            selectHiddenType = "<select disabled class='res-hidden-select"+suffix+" res-hidden' hidden_id"+suffix+"='cc_" + i + "'>";
                            selectHiddenType += "	<option value='' selected>בחרו אחת מהאפשרויות</option>";
                            selectHiddenType += "	<option value='val'>הגדרת ערך קבוע לשדה נסתר</option>";
                            selectHiddenType += "	<option value='param'>שאיבת ערך השדה מכתובת הקישור</option>";
                            selectHiddenType += "</select>";
                            
                            var text = "לדוגמה:";
                            text = text+" http://yourpage.com/pagename?"+check[1]+"=value";
                            var exampleText = "<div class='res-text-example res-hidden' hidden_id"+suffix+"='cc_" + i + "' style='display:none'>"+text+"</div>";
                            
                            hidden_value_build = hidden_value_build + "<li style='text-align:right;'>"+selectHiddenType+"<input type='text' disabled class='res-hidden-value res-hidden' placeholder='הזינו ערך ברירת מחדל' hidden_value"+suffix+"='cc_"+i+"' style='display:none'>"+exampleText+"</li>";
                           
                            cc_build = cc_build + "<li><input type='checkbox' class='cc' cc_id"+suffix+"='cc_" + i + "'></li>";
                        }
                        
                        
                    }
                });
                
                jQuery('#c_fields'+suffix).html(build);
                
                jQuery(fieldsSelector).html(n_build);
                
                jQuery('#cc_fields'+suffix).html(cc_build);
                jQuery('#rqq_fields'+suffix).html(rq_build);
                jQuery('#hidden_fields'+suffix).html(hidden_build);
                jQuery('#hidden_fields_value'+suffix).html(hidden_value_build);
                
            }
        });
    }

    
    
    /**
     * on generate form click
     */
    function onGenerateFormClick(suffix){
    	
    	validateSuffix(suffix);
    	
    	var objForm = jQuery('#select_form'+suffix);
    	var isPojo = false;
    	if(suffix == "_pojo")
    		isPojo = true;
    	
    	if(objForm.length == 0)
    		return(false);
        
            var form_id = objForm.val();
                        
            var onExistingAction = jQuery('#select_onexisting'+suffix).val();
            
            var p_fields = [];
            var cc_fields = [];
            var rqq_fields = [];
            var hidden_fields = [];
            var hidden_values = [];
            
			var objCFields = jQuery('#c_fields'+suffix+' input.cf');
            
			objCFields.each(function (i, checkbox) {

                var cf_id = jQuery(checkbox).attr('cf_id'+suffix);
								
            	var isAddField = jQuery(checkbox).is(':checked');
                var isHidden = jQuery('input[hidden_id'+suffix+'="' + cf_id + '"]').is(':checked'); 
				if(isHidden == true)
					isAddField = true;
                
                if(isAddField == true){
                    
                    p_fields.push(jQuery(checkbox).val());
                    
                    cc_fields.push(jQuery('input[cc_id'+suffix+'="' + cf_id + '"]').is(':checked'));
                    rqq_fields.push(jQuery('input[rq_id'+suffix+'="' + cf_id + '"]').is(':checked'));
                    
                    hidden_fields.push(isHidden);
                    
                    var hiddenType = jQuery('select[hidden_id'+suffix+'="' + cf_id + '"]').val();
                    
                    var hiddenValue = jQuery('input[hidden_value'+suffix+'="' + cf_id + '"]').val();
                    
                    if(hiddenType == "param")
                    	hiddenValue = "__param__";
                    
                    hidden_values.push(hiddenValue);
                    
                }
                
            });
            
            var p_values = p_fields.join(",");
            var cc_values = cc_fields.join(",");
            var rqq_values = rqq_fields.join(",");
            var hidden_field_put = hidden_fields.join(",");
            var hidden_values = hidden_values.join(",");
            
			
            if (p_values != '') p_values = "&p_fields=" + p_values;
            if (cc_values != '') cc_values = "&cc_values=" + cc_values;
            if (rqq_values != '') rqq_values = "&rqq_values=" + rqq_values;
            
            if (hidden_field_put != '') hidden_field_put = "&hidden_field_put=" + hidden_field_put;
            
            if (hidden_values != '') hidden_values = "&hidden_values=" + hidden_values;
            
            var form_name = jQuery('input[name=f_name'+suffix+']').val();
            
            var d_email = jQuery('input[name=d_email'+suffix+']').is(':checked');
            var d_name = jQuery('input[name=d_name'+suffix+']').is(':checked');
            var d_phone = jQuery('input[name=d_phone'+suffix+']').is(':checked');
            
            var r_name = jQuery('input[name=r_name'+suffix+']').is(':checked');
            var r_phone = jQuery('input[name=r_phone'+suffix+']').is(':checked');
            var r_email = jQuery('input[name=r_email'+suffix+']').is(':checked');
            
            var form_ty = jQuery('input[name=f_ty'+suffix+']').val();
            var form_send = jQuery('input[name=f_send'+suffix+']').val();
            
            var openNewTab = "";
            var sendParamsToThankyouPage = "";
            
            if(isPojo == false){
            	openNewTab = jQuery('input[name=url_open_newtab'+suffix+']').is(":checked");
                sendParamsToThankyouPage = jQuery('input[name=send_params_to_link'+suffix+']').is(":checked");;
            }
            
            if (form_ty != '') {
                form_ty = "&form_ty=" + form_ty;
            }
            if (form_send != '') {
                form_send = "&form_send=" + form_send;
            }
            var url = "admin.php?page=wpcf7-new";
            if (isPojo == true) 
                url = "post-new.php?post_type=pojo_forms";
            
            url += "&form_name=" + form_name + form_ty + form_send +  "&d_email=" + d_email + "&r_email=" + r_email + "&d_name=" + d_name + "&d_phone=" + d_phone + "&r_name=" + r_name + "&r_phone=" + r_phone + "&form_id=" + form_id + p_values + cc_values+ rqq_values + hidden_field_put + hidden_values + "&onexisting="+onExistingAction+"&urlnewtab="+openNewTab+"&sendparams="+sendParamsToThankyouPage;
           
            window.location = url;
    }
    
	
    /**
     * validate the suffix
     */
    function validateSuffix(suffix){
    	
    	switch(suffix){
	    	case "_cf7":
	    	case "_pojo":
	    	break;
	    	default:
	    		console.trace();
	    		throw new Error("Wrong suffix: "+suffix);
	    	break;
    	}
    	
    }

    /**
     * on hidden select click
     */
    function onHiddenSelectClick(objSelect, suffix){
    	
    	var value = objSelect.val();
		var checkboxID = objSelect.attr("hidden_id"+suffix);
		
		var objInput = jQuery("input[hidden_value"+suffix+"="+checkboxID+"]");
		var objExampleText = jQuery("div.res-text-example[hidden_id"+suffix+"="+checkboxID+"]");
		
		if(value == "param")
			objExampleText.show();
		else
			objExampleText.hide();
		
		
		if(value == "val"){
			objInput.show();
		}else{
			objInput.hide();
		}
		
    }
    
    
    /**
   	* on hidden checkbox click, enable the hidden value input
   	*/
    function onHiddenCheckboxClick(objCheckbox, suffix){
    	
    	validateSuffix(suffix);
    	
		var checkboxID = objCheckbox.attr("hidden_id"+suffix);
		
		g_resAdmin.validateNotEmpty(checkboxID, "checkbox id");
				
		var isChecked = objCheckbox.is(":checked");
		
		var objInput = jQuery("input[hidden_value"+suffix+"="+checkboxID+"]");
		var objInputSelect = jQuery("select[hidden_id"+suffix+"="+checkboxID+"]");
		var objExampleText = jQuery("div.res-text-example[hidden_id"+suffix+"="+checkboxID+"]");
		
		
		if(isChecked == true){
						
			var objInputShow = jQuery("input.cf[cf_id"+suffix+"='"+checkboxID+"']")
			
			objInputShow.attr("checked","checked");
			objInputShow.prop("checked","checked");
			
			objInputSelect.removeClass("res-hidden");
			objInput.removeClass("res-hidden");
			objExampleText.removeClass("res-hidden");
			
			objInputSelect.attr("disabled", false);
			objInput.attr("disabled", false);
		}
		else{
			
			objInputSelect.addClass("res-hidden");
			objInput.addClass("res-hidden");
			objExampleText.addClass("res-hidden");
			
			objInput.attr("disabled", true);
			objInput2.attr("disabled", true);
		}
		
    }

	
    // show warning message
    function onCheckboxEmailClick(suffix){
    	
    	validateSuffix(suffix);
    	
		var objCheckbox1 = jQuery("#checkbox_email_show"+suffix);
		var objCheckbox2 = jQuery("#checkbox_email_must"+suffix);
		
		var isChecked = objCheckbox1.is(":checked");
		var isChecked2 = objCheckbox2.is(":checked");

		var objMessage = jQuery("#res_email_warning"+suffix);

		if(isChecked == true && isChecked2 == true)
			objMessage.hide();
		else
			objMessage.show();
    }
    
    
	/**
	 * init forms view
	 */
	function initFormsView(suffix){
    	
		validateSuffix(suffix);
    	
		var objForm = jQuery('#select_form'+suffix);
		
    	if(objForm.length == 0)
    		return(false);
				
	    
	    jQuery('#generate'+suffix).click(function(){
	    	onGenerateFormClick(suffix);
	    });
	    
        jQuery('#select_form'+suffix).change(function(){
        	bringRegularFormFields(suffix);
        });
        
        bringRegularFormFields(suffix);
		
		//bring elementor fields
		setTimeout(bringElementorFormFields, 500);
		
		jQuery("body").on("click", ".res-hidden-checkbox"+suffix, function(){
			onHiddenCheckboxClick(jQuery(this), suffix);
		});
		
		jQuery("body").on("change", ".res-hidden-select"+suffix, function(){
			onHiddenSelectClick(jQuery(this), suffix);
		});

		jQuery("#checkbox_email_must"+suffix+", #checkbox_email_show"+suffix+"").click(function(){
			onCheckboxEmailClick(suffix);
		});
	    
	}
	
	/**
	 * on advanced settings sve
	 */
	function onAdvancedSettingsSaveClick(){
		
		var settingsValues = g_objSettingsAdvanced.getSettingsValues();
		
		var data = {settings:settingsValues};
		
		g_resAdmin.setErrorMessageID("res_message_error_advanced");
		g_resAdmin.setSuccessMessageID("res_message_saved_advanced");
		g_resAdmin.setAjaxLoaderID("res_saving_text_advanced");
		g_resAdmin.setAjaxHideButtonID("res_button_save_advanced_settings");
						
		
		g_resAdmin.ajaxRequest("save_advanced_settings", data, function(){
			location.reload();
		});
		
	}
	
	
	/**
	 * init advanced tab
	 */
	function initAdvancedTab(){
		
		var objTabWrapper = jQuery("#uc_advanced_settings_wrapper");
		
		if(objTabWrapper.length == 0)
			return(false);
		
		var objAdvacedSettingsContainer = jQuery("#uc_advanced_settings_wrapper");
		
		g_objSettingsAdvanced = new UniteSettingsRes();
		g_objSettingsAdvanced.init(objAdvacedSettingsContainer);
		
		
		var objButton = jQuery("#res_button_save_advanced_settings");
		
		objButton.click(onAdvancedSettingsSaveClick);
		
	}
	
	
	/**
	 * init function
	 */
	this.init = function(){
		
		jQuery(".res-form-select2").select2();
		
		initFormsView("_cf7");
		initFormsView("_pojo");
		
		initFormsElementorView();
		
		initAdvancedTab();
	}
	
}


jQuery(document).ready(function(){
	
	var objView = new ResSettingsTabView();
	objView.init();
	
});