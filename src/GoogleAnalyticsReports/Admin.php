<?php

namespace GoogleAnalyticsReports;

/**
 * Customize the list table on the admin screen.
 *
 * @package GoogleAnalyticsReports
 */
final class Admin
{
	private $prefix;
	private $options;

	public function __construct() {
		$this->prefix = \GoogleAnalyticsReports::get_instance()->get_prefix();
		$this->options = get_option( $this->prefix );
	}

	public static function get_instance() {
		static $instance;
		if ( ! $instance ) {
			$instance = new Admin();
		}
		return $instance;
	}

	public function register() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	public function admin_menu() {
		add_options_page(
			__( 'Google Analytics Reports', $this->prefix ),
			__( 'Google Analytics Reports', $this->prefix ),
			'manage_options',
			$this->prefix,
			array( $this, 'display' )
		);
	}

	public function admin_init() {
		register_setting( $this->prefix . '-settings', $this->prefix );

		add_settings_section(
			'api_settings',
			__( 'API settings', $this->prefix ),
			null,
			$this->prefix
		);

		add_settings_field(
			'secret_key',
			__( 'Secret Key', $this->prefix ),
			array( $this, 'secret_key_callback' ),
			$this->prefix,
			'api_settings'
		);

		add_settings_field(
			'view_id',
			__( 'View ID', $this->prefix ),
			array( $this, 'view_id_callback' ),
			$this->prefix,
			'api_settings'
		);
	}

	public function secret_key_callback() {
		$secret_key = isset( $this->options['secret_key'] ) ? $this->options['secret_key'] : '';
		?>
		<textarea name="<?php echo $this->prefix;?>[secret_key]" id="secret_key" rows="10" cols="50" class="large-text"><?php echo $secret_key;?></textarea>
		<?php
	}

	public function view_id_callback() {
		$view_id = isset( $this->options['view_id'] ) ? $this->options['view_id'] : '';
		?>
		<input name="<?php echo $this->prefix;?>[view_id]" type="text" id="view_id" value="<?php echo $view_id;?>" class="regular-text">
		<?php
	}

	public function display() {
		$action = untrailingslashit( admin_url() ) . '/options.php';
?>
		<div class="wrap GoogleAnalyticsReports-settings">
			<h1 class="wp-heading-inline"><?php _e( 'Google Analytics Settings', 'GoogleAnalyticsReports' ); ?></h1>
			<form action="<?php echo esc_url( $action ); ?>" method="post">
<?php
			settings_fields( $this->prefix . '-settings' );
			do_settings_sections( $this->prefix );
			submit_button();
?>
			</form>
		</div>
<?php
	}
}