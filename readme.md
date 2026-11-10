# WordPress Customizer Range Control

A range control slider for the WordPress Customizer

![Animation. There is a numerical input next to a slider range. As the slider is moved the input number value changes to match.](https://user-images.githubusercontent.com/507025/136671232-6c925852-0fc0-4a71-bfe8-0b41d968cf60.gif)

## Installation

```
composer require helgatheviking/kia-customizer-range-control
```

The class is autoloaded through Composer's classmap, so there is nothing to `require`. If your plugin or theme doesn't load Composer's autoloader, `require` `vendor/helgatheviking/kia-customizer-range-control/class-kia-customizer-range-control.php` inside your `customize_register` callback.

## Adding the control

```php
/**
 * Add the control to the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function kia_customizer( $wp_customize ) {

    // Register the control type that we're using as a JavaScript control.
    $wp_customize->register_control_type( 'KIA_Customizer_Range_Control' );

    $wp_customize->add_setting(
        'my_setting',
        array(
            'default'              => 3,
            'type'                 => 'option',
            'capability'           => 'edit_themes',
            'transport'            => 'postMessage',
            'sanitize_callback'    => 'absint',
            'sanitize_js_callback' => 'absint',
        )
    );

    $wp_customize->add_control(
        new KIA_Customizer_Range_Control(
            $wp_customize,
            'my_setting',
            array(
                'label'       => __( 'Number of columns', 'your-textdomain' ),
                'description' => __( 'How many items should be shown per row?', 'your-textdomain' ),
                'section'     => 'my_section',
                'settings'    => 'my_setting',
                'input_attrs' => array(
                    'min'  => 1,
                    'max'  => 6,
                    'step' => 1,
                ),
            )
        )
    );
}
add_action( 'customize_register', 'kia_customizer' );
```

## Bundling it in a plugin or theme

The control type, script and style handle, CSS scope and JS constructor all come from the PHP class name: `KIA_Customizer_Range_Control` uses the type `kia-customizer-range-control`. If you prefix the class, your copy gets its own of each, so another plugin bundling a different version can't replace yours.

With [Strauss](https://github.com/BrianHenryIE/strauss) and a `classmap_prefix` of `My_Plugin_`, the class becomes `My_Plugin_KIA_Customizer_Range_Control` with the type `my-plugin-kia-customizer-range-control`. Use the prefixed class name in `register_control_type()`, `new …()` and the sanitize callbacks.

The JS and CSS URLs are worked out from the file path, so the package has to live inside the WordPress install (under `ABSPATH`).

## Upgrading from 1.x

* The package is a Composer `library` and no longer needs `composer/installers` or `installer-paths`. Remove the `installer-paths` entry and the `require_once` for the class.
* The control type is now `kia-customizer-range-control` (or your prefixed equivalent) instead of `kia-range`. Drop any `'type' => 'kia-range'` argument: it's ignored. If you styled or scripted the control by its old type, ex: `.customize-control-kia-range`, update those selectors.
* The script and style handle is the control type, and the styles are printed inline.

## Credits

Huge props to Rich Tabor's [Login Designer](https://github.com/thatplugincompany/login-designer) and Per Soderlind's [Customizer Range Value Control](https://github.com/soderlind/class-customizer-range-value-control)
