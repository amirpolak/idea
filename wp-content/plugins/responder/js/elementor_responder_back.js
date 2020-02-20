
function ResponderElementorScripts(){
	
	if(!g_resAdmin)
		window.g_resAdmin = new UniteAdminRes();
	
	var g_elementorForms;
	
	/**
	 * trace some string
	 */
	function trace(str){
		console.log(str);
	}
	
	/**
	 * get all list fields 
	 */
	function getObjFields(){
		
		var objFieldsWrapper = jQuery("#responder_list_fields_wrapper");
		var objFields = objFieldsWrapper.find(".responder-connect-field");
		
		return(objFields);
	}
	
	
	/**
	 * get all active options from fields
	 */
	function getFieldsActiveValues(){
		
		var objValues = getFieldsValues();
		
		var objValuesAssoc = {};
		jQuery.each(objValues, function(index, value){
			objValuesAssoc[value] = true;
		});
		
		
		return(objValuesAssoc);
	}
	
	/**
	 * update field options visibility
	 */
	function updateFieldOptionsVisibility(objField, objActiveValues){
		
		var fieldValue = objField.val();
		
		var objOptions = objField.find("option");
				
		jQuery.each(objOptions, function(index, option){
			
			var optionValue = option.value;
			var objOption = jQuery(option);
						
			if(!optionValue)
				return(true);
			
			if(optionValue == fieldValue)
				return(true);
			
			if(objActiveValues.hasOwnProperty(optionValue) == true)
				objOption.hide();
			else
				objOption.show();
			
		})
				
	}
	
	
	/**
	 * update fields visibility
	 */
	function updateFieldsVisibility(){
		
		var objActiveValues = getFieldsActiveValues();
		
		var objFields = getObjFields();
		
		jQuery.each(objFields, function(index, field){
			
			var objField = jQuery(field);
			
			updateFieldOptionsVisibility(objField, objActiveValues);
			
		});
				
	}
	
	/**
	 * 
	 * on fields change, update field values
	 */
	function onFieldsChange(event){
				
		updateFieldsVisibility();
				
		updateDataFields();
	}
	
	/**
	 * init fields
	 */
	function initListFields(){
		
		updateFieldsVisibility();		
		
		var objFields = getObjFields();
				
		objFields.keyup(onFieldsChange);
		objFields.change(onFieldsChange);
		
	}
	
	function ___________ELEMENTOR_FIELDS___________(){}
	
	/**
	 * get simple form fields from elementor form fields object
	 */
	function mapElementorForms_getFormFields(objFields){
		
		var models = g_resAdmin.getVal(objFields,"models");
		if(!models || models.length == 0)
			return({});
		
		var fieldsData = {};
		var counter = 0;
		jQuery.each(models, function(index, model){
			
			counter++;
			var attributes = model.attributes;
			var fieldID = g_resAdmin.getVal(attributes, "_id");
			var fieldCustomID = g_resAdmin.getVal(attributes, "custom_id");
			
			if(fieldCustomID)
				fieldID = fieldCustomID;
			 
			var fieldLabel = g_resAdmin.getVal(attributes, "field_label");
			
			if(!fieldID)
				return(true);
			
			if(!fieldLabel){
				fieldLabel = "Item #"+counter;
			}
			
			fieldsData[fieldID] = fieldLabel;
		});
		
		
		return(fieldsData);
	}
	
	
	/**
	 * check widget, add forms to array if available
	 */
	function mapElementorForms_collectWidget(objWidget){
		
		var type = g_resAdmin.getVal(objWidget, "widgetType");
		
		if(type != "form")
			return(false);
		
		var widgetID = objWidget.id;
		var settings = objWidget.settings;
			var formFields = g_resAdmin.getVal(settings, "form_fields");
		var responderFieldsMap = g_resAdmin.getVal(settings, "responder_fields_map");
		var objFieldsMap = jQuery.parseJSON(responderFieldsMap);
		
		var objForm = {};
		objForm["id"] = widgetID;
		objForm["form_fields"] = mapElementorForms_getFormFields(formFields);
		objForm["responder_fields_map"] = objFieldsMap;
		
		g_elementorForms[widgetID] = objForm;
	}
	
	
	/**
	 * map forms
	 */
	function mapElementorForms(data){
		
		var isArray = jQuery.isArray(data);
				
		if(isArray == false)
			return(false);
				
		jQuery.each(data, function(index, item){
						
			var elType = g_resAdmin.getVal(item, "elType");
			
			var elements = g_resAdmin.getVal(item, "elements");
						
			if(elType != "widget" && jQuery.isArray(elements) && elements.length > 0){
				mapElementorForms(elements);
				return(true);
			}
			
			if(elType == "widget")
				mapElementorForms_collectWidget(item);
			
		});
		
			
	}
	
	
	/**
	 * get forms from elementor
	 */
	function getEelementor_forms(data){
		
		g_elementorForms = {};
		
		mapElementorForms(data);
		
		return(g_elementorForms);
	}
	
	
	/**
	 * get right elementor form
	 */
	function getCurrentElementorForm(objElementorForms){
		
		var currentID = window.lastElementorModelID;
		
		var objForm = g_resAdmin.getVal(objElementorForms, currentID);
		
		if(!objForm)
			return(null);
		
		return(objForm);
	}
	
	
	/**
	 * get fields from saved modal
	 */
	function getElementorFormDataFromModel(modal){
		
		var settings = modal.settings;
		var attributes = settings.attributes;
		var formFields = g_resAdmin.getVal(attributes, "form_fields");
		
		var widgetID = g_resAdmin.getVal(modal, "id");
		
		var objForm = {};
		objForm["id"] = modal.id;
		objForm["form_fields"] = mapElementorForms_getFormFields(formFields);
				
		return(objForm);
	}
	
	
	/**
	 * get fields from elementor
	 */
	function getFieldsFromElementor(){
		
		var data = elementor.config.data;
		
		var objElementorForms = getEelementor_forms(data);
				
		var objElementorForm = getCurrentElementorForm(objElementorForms);
		var formFields = g_resAdmin.getVal(objElementorForm, "form_fields");
				
		
		//try to get from model
		if(jQuery.isEmptyObject(formFields) == true){
						
			if(window.lastElementorModel)
				var objElementorForm = getElementorFormDataFromModel(window.lastElementorModel);
			
		}
		
		if(!objElementorForm)
			return(null);
		
		var formFields = g_resAdmin.getVal(objElementorForm, "form_fields");
		
		
		return(formFields);
	}
	
	
	function ___________OTHERS___________(){}
	
	
	/**
	 * on change - pull the fields of the selected list
	 */
	function onSelectListsChange(){
		
		var objSelectLists = jQuery("#responder_select_lists");
		var listID = objSelectLists.val();
		
		//update list and random number
		updateDataListID(listID);
		
		var objFieldsWrapper = jQuery("#responder_list_fields_wrapper");
		
		objFieldsWrapper.html("").hide();
		
		if(!listID){
			return(false);
		}
		
		g_resAdmin.setAjaxLoaderID("responder_list_fields_wrapper_loader");
		g_resAdmin.setErrorMessageID("responder_fieldsmap_error");
		
		var objFieldmapData = getFieldmapData();		
		
		var fields = g_resAdmin.getVal(objFieldmapData, "fields");
					
		var elementorFields = getFieldsFromElementor();
				
		var data = {
				list_id: listID,
				fields: fields,
				elementor_fields: elementorFields
		};
		
		g_resAdmin.ajaxRequest("get_elementor_list_fields_html", data, function(response){
			
			objFieldsWrapper.show();
			objFieldsWrapper.html(response.html);
			
			initListFields();
			
		});
		
	}
	

	
	/**
	 * update list ID
	 */
	function updateDataListID(listID){
		
		var objData = getFieldmapData();
		if(!objData)
			objData = {};
		
		objData["list_id"] = listID;
		
		updateData(objData);
	}
	
	
	/**
	 * get fields values
	 */
	function getFieldsValues(){
		
		var objFields = getObjFields();
		if(objFields.length == 0)
			return({});
		
		var objInputValues = {};
		
		jQuery.each(objFields, function(index, input){
			
			var objInput = jQuery(input);
			
			var value = objInput.val();
			var name = objInput.data("name");
			
			if(value == "")
				return(true);
			
			objInputValues[name] = value;
			
		});
		
		
		return(objInputValues);
	}
	
	
	/**
	 * update data
	 */
	function updateDataFields(){
		
		var objData = getFieldmapData();
		if(!objData)
			objData = {};
		
		var objValues = getFieldsValues();
		objData.fields = objValues;
		
		updateData(objData);
	}
	
	
	/**
	 * update data
	 */
	function updateData(objData){
				
		var objInput = jQuery(".responder-input-fieldmap");
		
		var strData = JSON.stringify(objData);
		
		objInput.val(strData);
		
		objInput.trigger("input");
	}
	
	
	/**
	 * get fieldmap data
	 */
	function getFieldmapData(){
		
		var objInput = jQuery(".responder-input-fieldmap");
		
		var strData = objInput.val();
		
		try{
			var objData = jQuery.parseJSON(strData);
			
			if(!objData)
				objData = {};
		
		}catch(error){
			objData = {};
		}
		
		return(objData);
	}
	
	
	/**
	 * init the mapping fields after ajax call
	 */
	function initMappingFields(){
				
		var objSelectLists = jQuery("#responder_select_lists");
		
		if(objSelectLists.hasClass("select2"))
			objSelectLists.select2({dir:"rtl"});
		
		objSelectLists.change(onSelectListsChange);
		
		var objData = getFieldmapData();
		
		var listID = g_resAdmin.getVal(objData, "list_id");
		
		if(listID){
			objSelectLists.val(listID);
			objSelectLists.trigger("change");
		}else{
			
			var selectListID = objSelectLists.val();
			if(selectListID)
				objSelectLists.trigger("change");
			
		}
		
	}
	

	
	/**
	 * run fields mapping
	 */
	this.runFieldsMapping = function(){
		
		var data = {};
				
		var objWrapper = jQuery("#responder_fieldsmap_wrapper");
		g_resAdmin.validateDomElement(objWrapper, "fields wrapper");
		
		objWrapper.hide();
		
		g_resAdmin.setAjaxLoaderID("responder_fieldsmap_loader");
		g_resAdmin.setErrorMessageID("responder_fieldsmap_error");
		
		g_resAdmin.ajaxRequest("get_elementor_fieldmap_html", data, function(response){
			
			var html = response.html;
			
			objWrapper.show();
			objWrapper.html(html);
			
			initMappingFields();
		});
				
	}
	
	
	/**
	 * on elementor panel change, save last opened model ID
	 */
	function onElementorPanelChange(event, model, third){
		
		var lastModelID = model.id;
		
		window.lastElementorModelID = lastModelID;
		window.lastElementorModel = model.attributes;
		
	}
	
	
	/**
	 * init global editor
	 */
	this.initGlobal = function(){
		
		elementor.hooks.addAction("panel/open_editor/widget", onElementorPanelChange);
				
	}
	
}


jQuery(document).ready(function(){
	
	var objScripts = new ResponderElementorScripts();
	objScripts.initGlobal();
	
});
