<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor heading control.
 *
 * A base control for creating heading control. Displays a text heading between
 * controls in the panel.
 *
 * @since 1.0.0
 */

class Control_ResponderFieldsMap extends Base_Data_Control {

	/**
	 * Get heading control type.
	 *
	 * Retrieve the control type, in this case `heading`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'responder_fields_map';
	}

	/**
	 * Get heading control default settings.
	 *
	 * Retrieve the default settings of the heading control. Used to return the
	 * default settings while initializing the heading control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
		];
	}

		
	/**
	 * Render heading control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		
		$settings = $this->get_settings();
		$control_uid = $this->get_control_uid();
		
		?>
		
		<div class="elementor-control-field" style="direction:rtl;">
			
			<div class="elementor-control-input-wrapper" style='display:none;'>
				
				<input id="<?php echo $control_uid; ?>" type="text" class="tooltip-target elementor-control-tag-area responder-input-fieldmap" data-setting="{{ data.name }}"  />
			</div>
			
			<div id="div_debug" style="display:none"></div>
			
			<div id="responder_fieldsmap_loader" style="display:none"><?php echo __res("Loading Settings", RESPONDER_TEXTDOMAIN)?>...</div>
			<div id="responder_fieldsmap_error" style="display:none"></div>
			
			<div id="responder_fieldsmap_wrapper" class="responder-fieldsmap-wrapper" style="width:100%;display:none;"></div>
			
			
		</div>
		
		<script>			
			var objScriptsResponder = new ResponderElementorScripts();
			objScriptsResponder.runFieldsMapping();
			
		</script>
		<?php
	}
	
}
