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

class Control_HeadingRTL extends Base_UI_Control {

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
		return 'heading_rtl';
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
		
		?>
		<style>
			.res-message-type-alert{
				color:red;
			}
		</style>		
		
		<div class="elementor-control-field res-message-type-{{ data.message_type }}">
			<h3 class="elementor-control-title" style='direction:rtl;'>{{ data.label }}</h3>
		</div>
		<?php
	}
}
