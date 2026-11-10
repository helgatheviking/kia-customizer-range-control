<?php
/**
 * Customizer Range Control.
 *
 * @version 2.0.0
 * @package Kia_Customizer_Range_Control
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if WP_Customize_Control does not exsist.
if ( ! class_exists( 'WP_Customize_Control' ) ) {
	return null;
}

/**
 * KIA_Customizer_Range_Control class.
 */
class KIA_Customizer_Range_Control extends \WP_Customize_Control {

	/**
	 * Control version.
	 *
	 * @var string
	 */
	const VERSION = '2.0.0';

	/**
	 * Control type. Always set from the class name, see get_control_type().
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * Constructor.
	 *
	 * The control type comes from the class name, so a copy prefixed by a tool like Strauss
	 * gets its own control type, script, styles and JS constructor.
	 *
	 * @param \WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string                $id      Control ID.
	 * @param array                 $args    Optional. Arguments to override class property defaults.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		$this->type = static::get_control_type();
	}

	/**
	 * Get the control type from the class name.
	 *
	 * @return string ex: kia-customizer-range-control
	 */
	public static function get_control_type() {
		return strtolower( str_replace( array( '\\', '_' ), '-', static::class ) );
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * Registered once per control type. The script stores its constructor on
	 * wp.customize.kiaRangeControl and the inline script after it registers that constructor
	 * for this type before another copy of the control can replace it.
	 */
	public function enqueue() {
		$handle = $this->type;

		if ( ! wp_script_is( $handle, 'registered' ) ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( $handle, $this->abs_path_to_url( __DIR__ . '/js/script' . $suffix . '.js' ), array( 'jquery', 'customize-controls' ), self::VERSION, true );
			wp_add_inline_script( $handle, sprintf( 'wp.customize.controlConstructor[ %s ] = wp.customize.kiaRangeControl;', wp_json_encode( $handle ) ) );

			wp_register_style( $handle, false, array(), self::VERSION );
			wp_add_inline_style( $handle, $this->get_styles() );
		}

		wp_enqueue_script( $handle );
		wp_enqueue_style( $handle );
	}

	/**
	 * Get the control's styles, scoped to this control type.
	 *
	 * @return string
	 */
	protected function get_styles() {
		$css = file_get_contents( __DIR__ . '/css/style.css' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file.
		$css = preg_replace( '/^\xEF\xBB\xBF/', '', (string) $css ); // Strip a byte order mark.
		return $css ? str_replace( 'customize-control-kia-customizer-range-control', 'customize-control-' . $this->type, $css ) : '';
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 */
	public function to_json() {
		parent::to_json();

		// The setting value.
		$this->json['id']                  = $this->id;
		$this->json['value']               = $this->value();
		$this->json['link']                = $this->get_link();
		$this->json['defaultValue']        = $this->setting->default;
		$this->json['input_attrs']['min']  = ( isset( $this->input_attrs['min'] ) ) ? $this->input_attrs['min'] : '0';
		$this->json['input_attrs']['max']  = ( isset( $this->input_attrs['max'] ) ) ? $this->input_attrs['max'] : '100';
		$this->json['input_attrs']['step'] = ( isset( $this->input_attrs['step'] ) ) ? $this->input_attrs['step'] : '1';
	}

	/**
	 * Don't render the content via PHP.  This control is handled with a JS template.
	 */
	public function render_content() {}

	/**
	 * An Underscore (JS) template for this control's content.
	 *
	 * Class variables for this control class are available in the `data` JS object;
	 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
	 *
	 * @see    WP_Customize_Control::print_template()
	 */
	protected function content_template() {
		?>
		<label class="customize-control-kia-range">

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<p class="customize-control-description">{{ data.description }}</p>
		<# } #>

		<div class="kia-range">
			<input id="range-{{ data.id }}" type="number" class="kia-range__number-input" value="{{ data.value }}" data-default-value="{{ data.defaultValue }}" min="{{ data.input_attrs['min'] }}" max="{{ data.input_attrs['max'] }}" step="{{ data.input_attrs['step'] }}" {{{ data.link }}} <# if ( data.value ) { #> checked="checked" <# } #> />
			<input type="range" data-input-type="range" class="kia-range__track" value="{{ data.value }}" data-default-value="{{ data.defaultValue }}"  min="{{ data.input_attrs['min'] }}" max="{{ data.input_attrs['max'] }}" step="{{ data.input_attrs['step'] }}" {{{ data.link }}} />
			<a type="button" value="reset" class="kia-range__reset"></a>
		</div>

		</label>
		<?php
	}

	/**
	 * Plugin / theme agnostic path to URL.
	 *
	 * @see https://wordpress.stackexchange.com/a/264870/14546
	 * @param string $path File path.
	 * @return string      URL.
	 */
	private function abs_path_to_url( $path = '' ) {
		$url = str_replace(
			wp_normalize_path( untrailingslashit( ABSPATH ) ),
			site_url(),
			wp_normalize_path( $path )
		);
		return esc_url_raw( $url );
	}
}
