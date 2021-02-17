<?php
/*
 * Plugin Name:     Simple tracker for Profitwell
 * Plugin URI:      https://wordpress.org/plugins/simple-tracker-for-profitwell/
 * Description:     Simple plugin to add a tracking code of Profitwell
 * Version:         0.0.1
 * Author:          DigitalCube Inc. 
 * Author URI:      http://en.digitalcube.jp/
 * License:         GPL-2.0-or-later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     profitwell_extension
*/

class ProfitwellExtension {
    function __construct() {
		add_action( 'admin_init', array( $this, 'init_options' ) );
		add_action( 'wp_footer',  array( $this, 'add_profitwell_script_to_footer' ) );
    }

	public function init_options() {
		register_setting( 'reading', 'profitwell_public_api_token', array(
			'type' => 'string',
			'sanitize_callback' => 'esc_attr'
		) );
		add_settings_section(
			'profitwell_settings',
			__( 'Profiltwell', 'profitwell_extension' ),
			array( $this, 'profitwell_setting_description' ),
			'reading',
		);
		add_settings_field(
			'profitwell_public_api_token',
			__( 'Public api token', 'profitwell_extension' ),
			array( $this, 'profitwell_public_api_token' ),
			'reading',
			'profitwell_settings',
		);
	}
	public function profitwell_setting_description() {
		_e( 'Put the `data-pw-auth` property', 'profitwell_extension' );
		echo "<pre><code>&lt;script id=\"profitwell-js\" data-pw-auth=\"<b>XXXXXXX</b>\"></pre></code>";
	}

	public function profitwell_public_api_token() {
		?>
		<input
			id="profitwell_public_api_token"
			name="profitwell_public_api_token"
			class="regular-text"
			type="text"
			value="<?php form_option('profitwell_public_api_token'); ?>"
		/>
		<?php
	}

	public function add_profitwell_script_to_footer() {

		if ( is_admin() ) {
			return;
		}

		$token = esc_attr( get_option( 'profitwell_public_api_token' ) );

		$start_options = "{}";
		$current_user = wp_get_current_user();
		if ($current_user->exists()) {
			$start_options = "{user_email: '{$current_user->user_email}'}";
		}
		
		echo "
		<script id='profitwell-js' data-pw-auth='$token'>
			(function(i,s,o,g,r,a,m){i[o]=i[o]||function(){(i[o].q=i[o].q||[]).push(arguments)};
			a=s.createElement(g);m=s.getElementsByTagName(g)[0];a.async=1;a.src=r+'?auth='+
			s.getElementById(o+'-js').getAttribute('data-pw-auth');m.parentNode.insertBefore(a,m);
			})(window,document,'profitwell','script','https://public.profitwell.com/js/profitwell.js');

			profitwell('start', $start_options);
		</script>
		";
	}
}

new ProfitwellExtension();