<?php

use Elementor\Group_Control_Border;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;


if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class ResponderDynamicForm extends Widget_Base {
	
	protected $widgetSettings;
	protected $isHorType = false;
	protected $isShowLabel = false;
	protected $isShowPlaceholder = false;
	protected $activeSettings;
	protected $formGeneratedID;
	protected $isDateExists = false;
	
	
    private function a_______INIT________(){}
	
	
	/**
	 * init widget settings
	 */
	protected function initWidgetSettings(){
        
		if(!empty($this->widgetSettings))
			return(false);
		
		$className = get_class($this);
        $formName = str_replace("ResponderDynamicForm_", "", $className);
        $this->widgetSettings = ResponderElementorIntegrate::getWidgetSettings($formName);
         
        $defaultType = UniteFunctionsRes::getVal($this->widgetSettings, "form_defaults_type");
        
        $this->isHorType = ($defaultType == "hor");
        
	}
	
	
	/**
	 * responder settings
	 */
    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);
		
        $this->initWidgetSettings();
        
    }
	    
    

    /*
     * get widget setting name
     */
    private function getWidgetSetting($key, $default=""){
    	
    	$value = UniteFunctionsRes::getVal($this->widgetSettings, $key, $default);
    	
    	return($value);
    }
    
    /*
     * get widget setting name
     */
    private function printWidgetSettings(){
    	
    	dmp($this->widgetSettings);
    	exit();    	
    }
    
    private function a_______COMMON________(){}
    
    /**
     * get name
     */
    public function get_name(){
        
        $this->initWidgetSettings();
    	$formName = UniteFunctionsRes::getVal($this->widgetSettings, "form_name");
        
    	return($formName);
    }
    
    
    /**
	 * get title
     */
    public function get_title() {
    	
        $this->initWidgetSettings();
    	$formTitle = UniteFunctionsRes::getVal($this->widgetSettings, "title");
        return($formTitle);    	
    }

    
    /**
     * get icon
     */
    public function get_icon() {
        return 'fa fa-wpforms';
    }
	
    /**
     * get categories
     */
    public function get_categories() {
    	
        return array('responder');
    }
	
        
    
    /**
	 * get scripts
     */
    public function get_script_depends() {
    	
    	$arrDepends = array();
    	$arrDepends[] = "elmentor-responder-front";
    	
    	if($this->isDateExists == true){
    		$arrDepends[] = "responder-picker";
    		$arrDepends[] = "responder-picker-date";
    	}

    	
    	return($arrDepends);
    }

    
    /**
	 * get styles
     */
    public function get_style_depends() {
    	
    	$arrDepends = array();
    	$arrDepends[] = "elmentor-responder-forms";
    	
    	if($this->isDateExists == true){
    		$arrDepends[] = "responder-picker-classic";
    		$arrDepends[] = "responder-picker-date-classic";
    		$arrDepends[] = "responder-picker-rtl";
    	}
    	
    	
    	return($arrDepends);
    }
    
    
    private function a_______CONTROLS________(){}
    
    /**
	* register controls
     */
    protected function _register_controls() {
		
		$this->addControls_content();
        
		$this->addControls_styleForm();
		
		$this->addControls_styleFields();
		
		$this->addControls_styleButton();
		
		$this->addControls_styleThanksMessage();
    }

    
    /**
     * get widget fields
     */
    private function getWidgetFields(){
                
        $arrFields = $this->getWidgetSetting("fields");
        
        $arrCustomFields = $this->getWidgetSetting("list_custom_fields");
        
     
        if(empty($arrCustomFields))
            return($arrFields);
        
            foreach($arrCustomFields as $field){
                
                if(empty($field))
                    continue;
                
                $fieldID = UniteFunctionsRes::getVal($field, "ID");
                $fieldName = UniteFunctionsRes::getVal($field, "NAME");
                $fieldName = UniteFunctionsRes::urlToHandle($fieldName);
                
                $type = UniteFunctionsRes::getVal($field, "TYPE");
                
                if(empty($fieldID))
                    continue;
                
                $isDate = false;
                if($type == 1){
                    $isDate = true;
                    $this->isDateExists = true;
                }
                
                $field = array();
                $field["name"] = "field_". $fieldID;
                $field["title"] = $fieldName;
                $field["id"] = $fieldID;
                $field["isdate"] = $isDate;
                
                $arrFields[] = $field;
            }
                      
           return($arrFields);
    }
    
    
    /**
     * add content controls
     */
    private function addControls_content(){
    	
        $formType = $this->getWidgetSetting("form_defaults_type");      //hor,vert
    	$arrFields = $this->getWidgetFields();
    	
    	$listName = $this->getWidgetSetting("list_id_unite_selected_text");
    	
    	$numFields = count($arrFields);
    	
    	$this->start_controls_section(
    	    'general',
    	    [
    	        'label' => __res("General", RESPONDER_TEXTDOMAIN),
    	    ]
    	    );

    	   
    	$listNameText =  __res('This form connected to list', RESPONDER_TEXTDOMAIN) . " :". $listName;
    		
    	$this->add_control(
    	    'list_name',
    	    [
    	        'label' => $listNameText,
    	        'type' => "heading_rtl",    	  
    	    ]
    	    );
 
    	
    	$this->end_controls_section();
    	 	
    	//$this->printWidgetSettings();
    	$isEmailOptional = $this->getWidgetSetting("is_email_optional");
    	    	
    	foreach($arrFields as $index => $field){
    		
    		$name = UniteFunctionsRes::getVal($field, "name");
    		$title = UniteFunctionsRes::getVal($field, "title");

    		$isMustField = false;
    		
    		//set default
    		$default = "";
    		$isCustomField = false;
    		
    		switch($name){
    			case "field_email":
    				$default = "example@email.com";
    				$isMustField = true;
    				
    			break;
    			case "field_name":
    				$default = __res("Israel Israely", RESPONDER_TEXTDOMAIN);
    			break;
    			case "field_phone":
    				    				
    				$default = "054-7773388";
    			break;
    			default:
    				$isCustomField = true;
    			break;
    		}

    	
    	//set condition
    	$conditionShow = [
    		'is_show_'.$name => "yes"
    	];
    	
    	if($isCustomField)
	    	$conditionShow = [
	              'is_hidden_field_'.$name."!" => "yes"
	    	];
    	
    	
        $this->start_controls_section(
            'section_field_'.$name,
            [
                'label' => $title,
            ]
        );
        
        $showDefault = "yes";
        if($isCustomField == true)
        	$showDefault = "no";
        
        $conditionShowField = "";
        
        if($isCustomField == true)
        	$conditionShowField = $conditionShow;
        
       	$this->add_control(
        	'is_show_'.$name,
        	 [
        	    'label' => __res( 'Show field', RESPONDER_TEXTDOMAIN ),
        	    'type' => Controls_Manager::SWITCHER,
        	    'default' => $showDefault,
        	 	'condition'=>$conditionShowField
        	 ]
       	);
       	        
            $defaultRequired = 'yes';
            if($isCustomField == true)
                $defaultRequired = 'no';
            
	        $this->add_control(
	            $name.'_required',
	            [
	                'label' => __res('Required', RESPONDER_TEXTDOMAIN),
	                'type' => Controls_Manager::SWITCHER,
	                'default' => $defaultRequired,
	                'label_off' => __res('No', RESPONDER_TEXTDOMAIN),
	                'label_on' => __res('Yes', RESPONDER_TEXTDOMAIN),
	                'condition'=>$conditionShow            
	            ]
	        );
        	
        		if($isMustField == true){		//if it's must field (email), show message
        			        			
	        	    $this->add_control(
	        	        'is_show_text_required'.$name,
	        	        [
	        	            'type' => "heading_rtl",
	        	            'label' => __res("For register only with phone must be set proper setting in responder site",RESPONDER_TEXTDOMAIN),
	        	        	'message_type' => "alert",
		                	'condition'=>[
		                		$name.'_required' => "",
		                	]
	        	        ]
	        	     );
        		}	
       	
       	
        	if($isCustomField == false){
          	            		
        	     
        		if($isMustField == true){		//if it's must field (email), show message
        			        			
	        	    $this->add_control(
	        	        'is_show_text_'.$name,
	        	        [
	        	            'type' => "heading_rtl",
	        	            'label' => __res("For register only with phone must be set proper setting in responder site",RESPONDER_TEXTDOMAIN),
	        	        	'message_type' => "alert",
		                	'condition'=>[
		                		'is_show_'.$name => "",
		                	]
	        	        ]
	        	     );
        		}	
	        	     
    	    
        	}else{
        	
        	
	       	$this->add_control(
	        	'is_hidden_field_'.$name,
	        	 [
	        	    'label' => __res( 'Is Hidden Field', RESPONDER_TEXTDOMAIN ),
	        	    'type' => Controls_Manager::SWITCHER,
	        	    'default' => 'no'
	        	]
	       	);
        		
        	
            $this->add_control(
                'hidden_field_value_'.$name,
                [
                    'label' => __res( 'Hidden Field Value', RESPONDER_TEXTDOMAIN ),
                    'type' => Controls_Manager::TEXT,
                    'default' => '',
                	'condition'=>[
	              		'is_hidden_field_'.$name => "yes",
	              		'enable_value_from_url_'.$name."!" => "yes"
	    			]
                ]
             );
			
            $valueFromUrlDescription = __res("Enable pass throug values like: ", RESPONDER_TEXTDOMAIN);
            $valueFromUrlDescription .= "http://yourpage.com/pagename?{$title}=value";
            
            
	       	$this->add_control(
	        	'enable_value_from_url_'.$name,
	        	 [
	        	    'label' => __res( 'Enable Value From Url', RESPONDER_TEXTDOMAIN ),
	        	    'type' => Controls_Manager::SWITCHER,
	        	    'default' => 'no',
	        	 	'description'=>$valueFromUrlDescription,
                	'condition'=>[
	              		'is_hidden_field_'.$name => "yes"
	    			]
	        	]
	       	);
            
             
       }
        
       
        
        $this->add_control(
            $name.'_field_label',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __res('Label', RESPONDER_TEXTDOMAIN),
                'default' => $title,
                'condition'=>$conditionShow
            
            ]
        );
        
        $this->add_control(
            $name.'_field_type',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __res('Field Type', RESPONDER_TEXTDOMAIN),
            	'options'=>[
            		"text"=>__res("Text Field", RESPONDER_TEXTDOMAIN),
            		"textarea"=>__res("Text Area", RESPONDER_TEXTDOMAIN),
            	],
                'default' => "text",
                'condition'=>$conditionShow
            ]
        );
        
        
        $this->add_control(
            $name.'_field_placeholder',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __res('Placeholder', RESPONDER_TEXTDOMAIN),
                'default' => $default,
            	'description'=>__res("The option to show the placeholder or not is located in style tab",RESPONDER_TEXTDOMAIN),
                'condition'=>$conditionShow            
            ]
        );
        
            
        

        if($this->isHorType == false){
        	
        $defaultWidth = "100";
        
        $this->add_control(
            $name.'_field_width',
            [
                'label' => __res( 'Width', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '100' => '100%',
                    '80' => '80%',
                    '75' => '75%',
                    '66.66' => '66%',
                    '60' => '60%',
                    '50' => '50%',
                    '40' => '40%',
                    '33.33' => '33%',
                    '25' => '25%',
                    '20' => '20%',
                ],
                'default' => $defaultWidth,
                'selectors' => [
                    '{{WRAPPER}} .res-form-field-input.'.$name => 'width: {{SIZE}}%',
                ],
                'condition'=>$conditionShow            
            ]
          );
        	
        }
       
        $orderOptions = array();
       	for($i=0; $i<$numFields; $i++){
       		$position = $i+1;
       		$orderOptions[$position] = $position;
       	}
		
       	$lastPost = $numFields;
       	
       	//$defaultOrder = $lastPost;
       	$defaultOrder = $index+1;
       	
       	//dmp($name);
       	
        $this->add_control(
            $name.'_order',
            [
                'label' => __res( 'Position', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => $defaultOrder,
                'options' => $orderOptions,
                'condition'=>$conditionShow            
            ]
            
        );

        $this->end_controls_section();
    	
    	}
    	
    	//submit section
        $this->start_controls_section(
            'section_submit',
            [
                'label' => __res('Submit', RESPONDER_TEXTDOMAIN),
            ]
        );

        $this->add_control(
            'submit_label',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __res('Text On Button', RESPONDER_TEXTDOMAIN),
                'default' => __res('Subscribe',RESPONDER_TEXTDOMAIN),
            ]
        );
        
        $this->add_control(
            'submit_label_divider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );
        
        
        $this->add_control(
            'action_after_submit',
            [
                'label' => __res( 'After Submit Action', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'show_confirm_message',
                'options' => [
                                        "show_confirm_message"=>__res("Show Confirm Message", RESPONDER_TEXTDOMAIN),
                                        "redirect_to_thankyou_page"=>__res("Redirect To Thank You Page",RESPONDER_TEXTDOMAIN) 
                                     ],
            ]
            );
        
        $this->add_control(
            'url_thankyou_page_str',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __res('Thank You Page Link', RESPONDER_TEXTDOMAIN),
                'url' => 'http://www.yourlink.co.il',
            	'placeholder'=>"http://www.yourlink.co.il",
            	'condition'=>[
            		"action_after_submit"=>"redirect_to_thankyou_page"
            	]
            ]
            );
        
        $this->add_control(
            'url_thankyou_add_params',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __res('Add Parameters To Url', RESPONDER_TEXTDOMAIN),
            	'default' => 'no',
            	'condition'=>[
            		"action_after_submit"=>"redirect_to_thankyou_page"
            	]
            ]
            );
            
        $this->add_control(
            'url_thankyou_open_new_page',
            [
                'type' => Controls_Manager::SWITCHER,
                'label' => __res('Open In New Page', RESPONDER_TEXTDOMAIN),
            	'default' => 'no',
            	'condition'=>[
            		"action_after_submit"=>"redirect_to_thankyou_page"
            	]
            ]
            );
            
        $this->add_control(
            'submitted_text_devider',
            [
                'type' => Controls_Manager::DIVIDER,
            ]
        );
        
        $this->add_control(
            'submitted_text',
            [
                'type' => Controls_Manager::TEXTAREA,
                'label' => __res('After Submitted Text', RESPONDER_TEXTDOMAIN),
                'default' => __res('The form was successfully submitted',RESPONDER_TEXTDOMAIN)
            ]
        );
        
        $this->add_control(
            'submitting_text',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __res('Submitting Text', RESPONDER_TEXTDOMAIN),
                'default' => __res('Submitting...',RESPONDER_TEXTDOMAIN),
            ]
        );
        
        
        $this->end_controls_section();
    }
    
    
    /**
     * add style form controls
     */
    private function addControls_styleForm(){
    	
        $this->start_controls_section(
            'style_section_form',
            [
                'label' => __res( 'Form', RESPONDER_TEXTDOMAIN ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );
		
        $labelHeading = __res('The Form is Vertical', RESPONDER_TEXTDOMAIN);
        if($this->isHorType == true)
        	$labelHeading = __res('The Form is Horizontal', RESPONDER_TEXTDOMAIN);
        
        
        $this->add_control(
            'style_heading',
            [
                'label' => $labelHeading,
                'type' => Controls_Manager::HEADING,
            ]
        );
                
        
        $this->add_control(
            'direction',
            [
                'label' => __res( 'Direction', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'rtl',
                'options' => [
                    'rtl' => __res( 'RTL', RESPONDER_TEXTDOMAIN ),
                    'ltr' => __res( 'LTR', RESPONDER_TEXTDOMAIN ),
            ],
            ]
        );

        $this->add_control(
            'label_type',
            [
                'label' => __res( 'Label Type', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'label_only',
                'options' => [
                    'label_only' => __res('Label Only Visible',RESPONDER_TEXTDOMAIN),
                    'placeholder_only' => __res('Placeholder Only Visible',RESPONDER_TEXTDOMAIN),
                    'label_and_placeholder' => __res('Label And Placeholder',RESPONDER_TEXTDOMAIN)
                ],
            ]
        );
        
        
        if($this->isHorType == false){
        	
	        $this->add_control(
	            'row_gap',
	            [
	                'label' => __res( 'Rows Gap', RESPONDER_TEXTDOMAIN ),
	                'type' => Controls_Manager::SLIDER,
	                'default' => [
	                    'size' => 10,
	                ],
	                'range' => [
	                    'px' => [
	                        'min' => 0,
	                        'max' => 60,
	                    ],
	                ],
	                'selectors' => [
	                    '{{WRAPPER}} .res-form-field-input' => 'margin-bottom: {{SIZE}}{{UNIT}};'
	                ],
	            ]
	        );
	        
        	        
	        
        }else{		//horiaontal type
    	
        $this->add_control(
            'column_gap',
            [
                'label' => __res( 'Columns Gap', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .res-form-field' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
                    '{{WRAPPER}} .fields-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
                ],
            ]
        );
        	
        }
        
        $this->add_control(
            'text_gap',
            [
                'label' => __res( 'Text Gap', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .res-form-field label' => 'margin-bottom:{{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        if($this->isHorType == false){
        	
	        $this->add_control(
	            'button_gap',
	            [
	                'label' => __res( 'Special Button Gap', RESPONDER_TEXTDOMAIN ),
	                'type' => Controls_Manager::SLIDER,
	                'default' => [
	                    'size' => 5,
	                ],
	                'range' => [
	                    'px' => [
	                        'min' => 0,
	                        'max' => 50,
	                    ],
	                ],
	                'selectors' => [
	                    '{{WRAPPER}} .res-form-field-submit' => 'margin-top: {{SIZE}}{{UNIT}};'
	                ],
	            ]
	        );
	        
        }
        

        $this->end_controls_section();
    	
    }
    
    /**
     * fields style controls
     */
    private function addControls_styleFields(){
    	
    	
        $this->start_controls_section(
            'style_section_fields',
            [
                'label' => __res( 'Fields', RESPONDER_TEXTDOMAIN ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'field_text_color',
            [
                'label' => __res( 'Text Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .res-form-field label' => 'color: {{VALUE}};',
                ],
                'default'=>"#383838"
            ]
        );
        
        $this->add_control(
            'field_text_inside_color',
            [
                'label' => __res( 'Text Color Inside', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .res-form-field-input input[type="text"]' => 'color: {{VALUE}};',
                ],
                'default'=>"#383838"
            ]
            );
        
        
        $this->add_control(
            'field_placeholder_color',
            [
                'label' => __res( 'Placeholder Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .res-form-field input::placeholder, {{WRAPPER}} .res-form-field input::-moz-placeholder' => 'color: {{VALUE}};',
                ],
                'default' => '#E7E7E7'
            ]
        );

        $this->add_control(
            'field_padding_vertical',
            [
                'label' => __res( 'Padding Vertical', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],                
                'selectors' => [
                    '{{WRAPPER}} .res-form-field input[type="text"]' => 'padding-top: {{SIZE}}{{UNIT}};padding-bottom:{{SIZE}}{{UNIT}};',
                ],
            ]
            );
          
        $this->add_control(
            'field_padding_horizontal',
            [
                'label' => __res( 'Padding Horizontal', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                    ],
                ],                
                'selectors' => [
                    '{{WRAPPER}} .res-form-field input' => 'padding-left: {{SIZE}}{{UNIT}};padding-right:{{SIZE}}{{UNIT}};',
                ],
            ]
        );
            
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'field_typography',
                'selector' => '{{WRAPPER}} .res-form-field label, {{WRAPPER}} .res-form-field input[type="text"], {{WRAPPER}} .res-form-field label',
                'scheme' => Scheme_Typography::TYPOGRAPHY_3,
            ]
        );
		

        $this->add_control(
            'field_background_color',
            [
                'label' => __res( 'Background Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .res-form-field input[type="text"]' => 'background-color: {{VALUE}};'
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'field_border_color',
            [
                'label' => __res( 'Border Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .res-form-field-input input' => 'border-color: {{VALUE}};',

                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'field_border_width',
            [
                'label' => __res( 'Border Width', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::DIMENSIONS,
                'placeholder' => '1',
                'size_units' => [ 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .res-form-field-input input' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'field_border_radius',
            [
                'label' => __res( 'Border Radius', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .res-form-field-input input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]   
        );

        $this->end_controls_section();
    	
    }
    
    /**
     * add border controls
     */
    private function addBorderControls($options){
    	
    	$this->add_control($options["name"]."_border",[
			'label' => __res( 'Border Type', RESPONDER_TEXTDOMAIN ),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'' => __res( 'None', RESPONDER_TEXTDOMAIN ),
				'solid' => __res( 'Solid', RESPONDER_TEXTDOMAIN ),
				'double' => __res( 'Double', RESPONDER_TEXTDOMAIN),
				'dotted' => __res( 'Dotted', RESPONDER_TEXTDOMAIN ),
				'dashed' => __res( 'Dashed', RESPONDER_TEXTDOMAIN),
				'groove' => __res( 'Groove', RESPONDER_TEXTDOMAIN),
			],
            'selectors' => [
                 $options["selector"] => 'border-style: {{VALUE}};',
            ],
		]);
		
    	$this->add_control($options["name"]."_border_width",[
			'label' => __res( 'Border Width',RESPONDER_TEXTDOMAIN),
			'type' => Controls_Manager::DIMENSIONS,
			'selectors' => [
				$options["selector"] => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'default'=>[
				'top'=>1,
				'bottom'=>1,
				'left'=>1,
				'right'=>1,
				'isLinked'=>1,
				'unit'=>'px',
			],
			'condition' => [
				$options["name"]."_border!" => '',
			],
		]);
		
    	$this->add_control($options["name"]."_border_color",[
    		
			'label' => __res( 'Border Color', RESPONDER_TEXTDOMAIN),
			'type' => Controls_Manager::COLOR,
			'default' => '#34A844',
			'selectors' => [
				$options["selector"] => 'border-color: {{VALUE}};',
			],
			'condition' => [
				$options["name"]."_border!" => '',
			],
		]);
    	
		
    }
    
    
    /**
     * add button style controls
     */
    private function addControls_styleButton(){
    	
        $this->start_controls_section(
            'section_button_style',
            [
                'label' => __res( 'Button', RESPONDER_TEXTDOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style' );
		
        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __res( 'Normal', RESPONDER_TEXTDOMAIN ),
            ]
        );
		
        $this->add_control(
            'button_background_color',
            [
                'label' => __res( 'Background Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
            	'default'=>"#61ce70",
                'selectors' => [
                    '{{WRAPPER}} .res-button-submit' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __res( 'Text Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .res-button-submit' => 'color: {{VALUE}};',
                ],
            ]
        );
		        
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'scheme' => Scheme_Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} .res-button-submit',
            ]
        );
		
        $this->addBorderControls(
            [	'name' => 'button_border',
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .res-button-submit',
        ]);
		
        
        $this->add_control(
            'button_border_radius',
            [
                'label' => __res( 'Border Radius', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .res-button-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'button_text_padding',
            [
                'label' => __res( 'Text Padding', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .res-button-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		
        $defaultButtonWidth = "25";
        if($this->isHorType == true)
        	$defaultButtonWidth = "100";
        
        
        $this->add_control(
            'button_width',
            [
                'label' => __res( 'Width', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '100' => '100%',
                    '80' => '80%',
                    '75' => '75%',
                    '66.66' => '66%',
                    '60' => '60%',
                    '50' => '50%',
                    '40' => '40%',
                    '33.33' => '33%',
                    '25' => '25%',
                    '20' => '20%',
                ],
                'default' => $defaultButtonWidth,
                'selectors' => [
                    '{{WRAPPER}} .res-form-field-submit .res-button-submit' => 'width: {{SIZE}}%',
                ]
            ]
        );

        $this->add_control(
            'button_alignment',
            [
                'label' => __res( 'Alignment', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __res( 'Left', RESPONDER_TEXTDOMAIN ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __res( 'Center', RESPONDER_TEXTDOMAIN ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __res( 'Right', RESPONDER_TEXTDOMAIN ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .res-form-field-submit' => 'text-align: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __res( 'Hover', RESPONDER_TEXTDOMAIN ),
            ]
        );

        $this->add_control(
            'button_background_hover_color',
            [
                'label' => __res( 'Background Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
            	'default'=>"#3ABC4C",
                'selectors' => [
                    '{{WRAPPER}} .res-button-submit:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __res( 'Text Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .res-button-submit:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_hover_border_color',
            [
                'label' => __res( 'Border Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .res-button-submit:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'button_border_border!' => '',
                ],
            ]
        );

        $this->add_control(
            'button_hover_animation',
            [
                'label' => __res( 'Animation', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::HOVER_ANIMATION
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    	
    }

    /**
     * add button style controls
     */
    private function addControls_styleThanksMessage(){
    	
        $this->start_controls_section(
            'section_thankyou_style',
            [
                'label' => __res( 'Thanks You Message', RESPONDER_TEXTDOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
                
        $this->add_control(
            'thankyou_message_text_color',
            [
                'label' => __res( 'Text Color', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '#006400',
                'selectors' => [
                    '{{WRAPPER}} .responder-message-sent' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'thankyou_message_align',
            [
                'label' => __res( 'Text Align', RESPONDER_TEXTDOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'center' => __res('Center',RESPONDER_TEXTDOMAIN),
                    'left' => __res('Left',RESPONDER_TEXTDOMAIN),
                    'right' => __res('Right', RESPONDER_TEXTDOMAIN)
                ],
                'default' => "center",
                'selectors' => [
                    '{{WRAPPER}} .responder-message-sent' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'thankyou_typography',
                'scheme' => Scheme_Typography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .responder-message-sent',
            ]
        );
        
        
        $this->end_controls_section();
    	
    }
    
    private function a_______RENDER________(){}
    
    
    /**
     * put some test styles if needed
     */
    private function putTestStyle(){
    	
    	return(false);
    	?>
    	<style>
    	    		
    	</style>
    	<?php 		
    }
    
    /**
     * get fields class
     */
    protected function getFieldsClass($settings){
		
    	if($this->isHorType == false)
    		return("");
    	
    	$numFields = 1;		//submit always there
    	
    	$arrFields = $this->getWidgetFields();
    	foreach($arrFields as $field){
    		
    		$fieldName = $field["name"];
    		$isShow = UniteFunctionsRes::getVal($settings, "is_show_".$fieldName);
    		
    		if($isShow == "yes")
    			$numFields++;
    	}
    	
    	$class = "res-field-width-".$numFields;
    	    	
    	return($class);
    }
    
    
    /**
     * render sent message
     */
    protected function renderSentMessage($settings){
    	
    	$text = $settings["submitted_text"];
    	if(empty($text))
    		$text = __res("$text", RESPONDER_TEXTDOMAIN);
    	
    	$text = htmlspecialchars($text);
    	
    	$direction = $settings['direction'];
    	
    	$html = "";
    	
    	$html .= "<div class='responder-message-sent' style='display:none;direction:$direction'>$text</div>";
    	
    	return($html);
    }   
    
    
    /**
     * render error message
     */
    protected function renderErrorMessage($settings){
    	    	
    	$direction = $settings['direction'];
    	
    	$text = __res("Error Message Example", RESPONDER_TEXTDOMAIN);
    	
    	$html = "";
    	
    	$html .= "<div class='responder-message-error' style='display:none;direction:$direction'>$text</div>";
    	$html .= "<div class='responder-message-debug' style='display:none;direction:$direction'></div>";
    	
    	return($html);
    }
    
    
    /**
     * sort render fields
     */
    public function sortRenderFields($field1, $field2){
    	
    	$settings = $this->activeSettings;
    	
    	$name1 = $field1["name"];
    	
    	$name2 = $field2["name"];
    	
    	$order1 = UniteFunctionsRes::getVal($settings, "{$name1}_order", 0);
    	$order2 = UniteFunctionsRes::getVal($settings, "{$name2}_order", 0);
		
    	if($order1 == $order2)
    		return(0);
    		
    	if($order1 > $order2)
    		return(1);
    	
    	return(-1);
    	    	
    }
        
    /**
     * order fields
     */
    protected function orderFields($arrFields){
    	
    	usort($arrFields, array($this,"sortRenderFields"));
    	
    	return($arrFields);
    }
    
    /**
     * render hidden field
     */
    protected function render_hidden_field($name, $value, $fieldID, $title){
        
        $value = htmlspecialchars($value);
        $title = htmlspecialchars($title);
        
        $html = "<input type='hidden' name='$name' value='$value' data-id='$fieldID' data-label='$title'>"; 
        
        return($html);
    }
    
    
    /**
     * get hidden field value
     */
    private function getHiddenFieldValue($settings, $name, $title){
    	
       $value = UniteFunctionsRes::getVal($settings, "hidden_field_value_".$name);
    	
       $enableValueFromUrl = UniteFunctionsRes::getVal($settings, "enable_value_from_url_".$name);
       
       if($enableValueFromUrl != "yes")
       		return($value);

       //get the field from url
       		
       $value = UniteFunctionsRes::getGetVar($title, "", UniteFunctionsRes::SANITIZE_TEXT_FIELD);
       $value = strip_tags($value);
       
       return($value);
    }
    
    
    /**
     * render fields
     */
    protected function render_fields($settings){
    	
        $fieldsAddClass = $this->getFieldsClass($settings);
        
    	$wrapperAddClass = "";
    	if($this->isHorType)
    		$wrapperAddClass = " res-form-hor";
        
        $fields = "<div class='fields-wrapper{$wrapperAddClass}' style='display: flex; display: -webkit-flex; flex-flow: row wrap; -webkit-flex-flow: row wrap; justify-content: space-between; -webkit-justify-content: space-between;'>";
		
        //$this->printWidgetSettings($key, $default);
        $arrFields = $this->getWidgetFields();
        $arrFields = $this->orderFields($arrFields, $settings);
        
        
        if(empty($arrFields))
        	$arrFields = array();
        	
        foreach ($arrFields as $field) {
        	
        	$name = UniteFunctionsRes::getVal($field, "name");
        	
        	$title = UniteFunctionsRes::getVal($field, "title");
        	$id = UniteFunctionsRes::getVal($field, "id");
        	$isdate = UniteFunctionsRes::getVal($field, "isdate");
        	
        	$required = UniteFunctionsRes::getVal($settings, $name.'_required', 'yes');
        	$fieldType = UniteFunctionsRes::getVal($settings, $name.'_field_type', 'text'); 
        	        	
        	$showField = UniteFunctionsRes::getVal($settings, 'is_show_'.$name);
        	        	
        	$isHiddenField = UniteFunctionsRes::getVal($settings, 'is_hidden_field_'.$name);
        	
        	
        	//draw hidden field
        	if($isHiddenField == "yes"){
        		        	        
        	        $value = $this->getHiddenFieldValue($settings, $name, $title);
        	        
        	        $fields .= $this->render_hidden_field($name, $value, $id, $title);
        	        
        	}	//draw regular field
        	else{
        		
	        	if($showField != "yes")
	        		continue;
        		
        	       $type = $fieldType;
        	       if($isdate == true)
        	           $type = "date";
        	        
        	        $fields .= $this->render_field(
        	            $type,
        	            $settings[$name.'_field_label'],
        	            $settings[$name.'_field_placeholder'],
        	            $required,
        	            $name,
        	            $fieldsAddClass,
        	            $id
        	            );
        		
        	}
        	
           
        }
        
        
        if($this->isHorType == true)
        	$fields .= $this->render_submit($settings, $fieldsAddClass);
        
        $fields .= "</div>";
		
        return $fields;
    }
	
    
    /**
     * render field
     */
    protected function render_field($type, $label, $placeholder, $required, $name, $fieldsAddClass,$id=null){
    	
    	if($required == "yes")
    		$fieldsAddClass .= " elementor-mark-required";
    	
    		$inputClass = "";
    		
    		if($type == "date"){
    	       $fieldsAddClass .= " res-date-field-wrapper";
    		   $inputClass = " class='res-date-input'";
    		}
    	 
    	$htmlPlaceholder = "";
    	if($this->isShowPlaceholder == true)
    		$htmlPlaceholder = "placeholder=\"$placeholder\"";
    	
    	$label = htmlspecialchars($label);
    	
    	$htmlID = "";
    	if(!empty($id))
    	    $htmlID = "data-id=\"$id\"";   	
    	    
    	$fieldID = $this->formGeneratedID."_".$name;

    	if(empty($type))
    		$type = "text";
    	
    	$tag = "input";
    	$showCloseTag = false;
    	$closeTag = "";
    	
    	switch($type){
    		case "textarea":
    			$tag = "textarea";
    			$showCloseTag = true;
    		break;
    	}
    	
    	if($showCloseTag == true)
    		$closeTag = "</$tag>";
    		
        ob_start();
        ?>
        <div class="res-form-field res-form-field-input <?php echo $name." ".$fieldsAddClass?>">
            
            <?php if($this->isShowLabel):?>
            <label class="elementor-field-label" for="<?php echo $fieldID?>"><?php echo $label; ?></label>
            <?php endif?>
            
            <<?php echo $tag?> id="<?php echo $fieldID?>"  type="text" <?php echo $inputClass?>
                    name="<?php echo $name; ?>"
                    <?php echo $htmlID?>
                    <?php echo $htmlPlaceholder?>
                <?php echo ($required == 'yes') ? 'required="required"' : ''; ?> data-label='<?php echo $label?>'><?php echo $closeTag?>
            
        </div>
        <?php
        return ob_get_clean();
    } 

    /**
     * get error texts
     */
    private function getErrorTexts(){
    	
    	$arrErrorTexts = array();
    	$arrErrorTexts["please_fill"] = __res("Please fill field:", RESPONDER_TEXTDOMAIN);
    	$arrErrorTexts["invalid_email"] = __res("Invalid Email", RESPONDER_TEXTDOMAIN);
    	$arrErrorTexts["invalid_phone"] = __res("Invalid Phone", RESPONDER_TEXTDOMAIN);
    	
    	return($arrErrorTexts);
    }
    
    
    /**
     * submit button label
     */
    protected function render_submit($settings, $fieldsAddClass=""){
    	
    	$label = $settings['submit_label'];
    	$label = htmlspecialchars($label);
    	
    	$submittingText = $settings['submitting_text'];
    	$submittingText = htmlspecialchars($submittingText);
    	
    	$hoverAnimation = UniteFunctionsRes::getVal($settings, "button_hover_animation");
    	
    	$classAdd = "";
    	if(!empty($hoverAnimation))
    		$classAdd = " elementor-animation-".$hoverAnimation;
    	
    	$arrErrorTexts = $this->getErrorTexts();
    	$strErrorTexts = UniteFunctionsRes::jsonEncodeForHtmlData($arrErrorTexts);
    		
        ob_start();
        ?>
        <div class="res-form-field res-form-field-submit <?php echo $fieldsAddClass?>" >
        	
        	<?php if($this->isShowLabel && $this->isHorType == true):?>
        	<label>&nbsp;</label>
        	<?php endif?>
            <input type="button" class="res-button-submit<?php echo $classAdd?>" type="submit" value="<?php echo $label ?>" data-origtext="<?php echo $label?>" data-submittingtext="<?php echo $submittingText?>" data-errors="<?php echo $strErrorTexts?>">
        </div>
        <?php
        return ob_get_clean();
    }
	
    
    /**
	 * render the html
     */
    protected function render() {
    	
    	//$this->putTestStyle();
    	
        $this->formGeneratedID = "responder_form_".UniteFunctionsRes::getRandomString(5);
        
        $settings = $this->get_active_settings();
        $this->activeSettings = $settings;
                
        
        //init some fields
        $labelType = UniteFunctionsRes::getVal($settings, "label_type");
        switch($labelType){
        	default:
        	case "label_only":
        		$this->isShowLabel = true;
        		$this->isShowPlaceholder = false;
        	break;
        	case "placeholder_only":
        		$this->isShowLabel = false;
        		$this->isShowPlaceholder = true;
        	break;
        	case "label_and_placeholder":
        		$this->isShowLabel = true;
        		$this->isShowPlaceholder = true;
        	break;
        }
        
        $listID = $this->getWidgetSetting("list_id");
        $formID = $this->getWidgetSetting("generated_id");
        
		$urlAjax = GlobalsResponder::$urlAjaxFront;
		
		$direction = $settings['direction'];
		
		$urlThankyouPage = "";
		$actionAfterSumit = UniteFunctionsRes::getVal($settings, "action_after_submit");
		
		if($actionAfterSumit == "redirect_to_thankyou_page"){
			
			$urlThankyouPage = UniteFunctionsRes::getVal($settings, "url_thankyou_page_str");
			$urlThankyouOpenInNew = UniteFunctionsRes::getVal($settings, "url_thankyou_open_new_page");
			
			$isAddParams = UniteFunctionsRes::getVal($settings, "url_thankyou_add_params");
		}
		
		 $addHtml = "";
		 if(!empty($urlThankyouPage)){
		      $urlLower = strtolower($urlThankyouPage);
		     
		     if(strpos($urlLower, "http://") === false && strpos($urlLower, "https://") === false)
		         $urlThankyouPage = "http://".$urlThankyouPage;
		         
		     $urlThankyouPage = htmlspecialchars($urlThankyouPage);
		     $addHtml = " data-urlthankyou='$urlThankyouPage' data-urlthankyou-opennew='$urlThankyouOpenInNew' data-urlthankyou-addparams='$isAddParams'";
		     
		 }
		   
		$classAdd = "responder-form-vert";
		if($this->isHorType == true)
			$classAdd = "responder-form-hor";
		
		if($direction == "rtl")
			$classAdd .= " responder-rtl";
			
		$output = "<div class='responder-form-main-wrapper $classAdd'>";
        $output .= "<form class='responder-form-wrapper' method='post' data-urlajax='$urlAjax' data-formid='{$formID}' data-listid='$listID' dir='{$direction}' {$addHtml}>";
        $output .= $this->render_fields($settings);
        $output .= "</form>";
        
        if($this->isHorType == false)
        	$output .= $this->render_submit($settings);
        
        $output .= $this->renderSentMessage($settings);
        $output .= $this->renderErrorMessage($settings);
        
        $output .= "</div>";

		echo $output;
    }
    
    
}    
