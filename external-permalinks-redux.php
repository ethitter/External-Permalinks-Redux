<?php
/**
 * Plugin Name: External Permalinks Redux
 * Plugin URI: http://www.thinkoomph.com/plugins-modules/external-permalinks-redux/
 * Description: Allows users to point WordPress objects (posts, pages, custom post types) to a URL of your choosing. Inspired by and backwards-compatible with <a href="http://txfx.net/wordpress-plugins/page-links-to/">Page Links To</a> by Mark Jaquith. Written for use on WordPress.com VIP.
 * Version: 1.3.2
 * Author: Erick Hitter & Oomph, Inc.
 * Author URI: http://www.thinkoomph.com/
 * Text Domain: external-permalinks-redux
 * Domain Path: /languages/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package External_Permalinks_Redux
 */

// Include block-editor class.
require_once dirname( __FILE__ ) . '/inc/class-external-permalinks-redux-block-editor.php';

/**
 * Class external_permalinks_redux.
 */
// phpcs:ignore PEAR.NamingConventions.ValidClassName, Squiz.Commenting.ClassComment.Missing
class external_permalinks_redux {
	/**
	 * Singleton!
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Redirect URL meta key.
	 *
	 * @var string
	 */
	public $meta_key_target = '_links_to';

	/**
	 * Redirect type meta key.
	 *
	 * @var string
	 */
	public $meta_key_type = '_links_to_type';

	/**
	 * Supported redirect codes.
	 *
	 * @var array
	 */
	public $status_codes;

	/**
	 * Supported post types.
	 *
	 * Cannot be used before `admin_init` hook.
	 *
	 * @var array
	 */
	private $post_types;

	/**
	 * Instantiate class as a singleton.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->_setup();
		}

		return self::$instance;
	}

	/**
	 * Unused constructor.
	 */
	final private function __construct() {}

	/**
	 * Allow access to certain private properties.
	 *
	 * @param string $name Property name.
	 * @return array|null
	 */
	public function __get( $name ) {
		if ( 'post_types' === $name ) {
			if ( ! did_action( 'admin_init' ) ) {
				_doing_it_wrong(
					__METHOD__,
					'Cannot be used before `admin_init` hook.',
					'1.0'
				);
			}

			return $this->post_types;
		}

		return null;
	}

	/**
	 * Disallow setting private properties except via filters.
	 *
	 * @param string $name  Property name.
	 * @param mixed  $value Property value.
	 * @return false
	 */
	public function __set( $name, $value ) {
		return false;
	}

	/**
	 * Check if certain private properties are set.
	 *
	 * @param string $name Property name.
	 * @return bool
	 */
	public function __isset( $name ) {
		if ( 'post_types' === $name ) {
			if ( ! did_action( 'admin_init' ) ) {
				return false;
			}

			return is_array( $this->post_types )
				&& ! empty( $this->post_types );
		}

		return false;
	}

	/**
	 * Register actions and filters.
	 */
	protected function _setup() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'action_init' ), 0 ); // Other init actions may rely on permalinks so filter early.
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		add_action( 'save_post', array( $this, 'action_save_post' ) );

		add_filter( 'post_link', array( $this, 'filter_post_permalink' ), 1, 2 );
		add_filter( 'post_type_link', array( $this, 'filter_post_permalink' ), 1, 2 );
		add_filter( 'page_link', array( $this, 'filter_page_link' ), 1, 2 );
		add_action( 'wp', array( $this, 'action_wp' ) );
	}

	/**
	 * Load plugin translations.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'external-permalinks-redux',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
	}

	/**
	 * Register plugin keys and status codes.
	 *
	 * @action init
	 */
	public function action_init() {
		$this->meta_key_target = apply_filters( 'epr_meta_key_target', $this->meta_key_target );
		$this->meta_key_type   = apply_filters( 'epr_meta_key_type', $this->meta_key_type );

		$status_codes       = array(
			302 => __( 'Temporary (302)', 'external-permalinks-redux' ),
			301 => __( 'Permanent (301)', 'external-permalinks-redux' ),
		);
		$this->status_codes = apply_filters( 'epr_status_codes', $status_codes );
	}

	/**
	 * Add meta box.
	 */
	public function action_admin_init() {
		$this->post_types = apply_filters( 'epr_post_types', array( 'post', 'page' ) );

		if ( ! is_array( $this->post_types ) ) {
			return;
		}

		$this->post_types = array_flip( $this->post_types );

		foreach ( array_keys( $this->post_types ) as $post_type ) {
			$title = apply_filters( 'epr_metabox_title', '', $post_type );

			if ( empty( $title ) ) {
				$title = __( 'External Permalinks Redux', 'external-permalinks-redux' );
			}

			$this->post_types[ $post_type ] = $title;

			if (
				function_exists( 'use_block_editor_for_post_type' )
				&& use_block_editor_for_post_type( $post_type )
			) {
				continue;
			}

			add_meta_box( 'external-permalinks-redux', $title, array( $this, 'meta_box' ), $post_type, 'normal' );

			unset( $title );
		}
	}


	/**
	 * Render meta box.
	 *
	 * @param object $post Post object.
	 */
	public function meta_box( $post ) {
		$type = get_post_meta( $post->ID, $this->meta_key_type, true );
		?>
		<p class="epr-destination">
			<label for="epr-url"><?php esc_html_e( 'Destination Address:', 'external-permalinks-redux' ); ?></label><br />
			<input name="<?php echo esc_attr( $this->meta_key_target ); ?>_url" class="large-text code" id="epr-url" type="text" value="<?php echo esc_url( get_post_meta( $post->ID, $this->meta_key_target, true ) ); ?>" />
		</p>

		<p class="description"><?php esc_html_e( 'To restore the original permalink, remove the link entered above.', 'external-permalinks-redux' ); ?></p>

		<p class="epr-separator">&nbsp;</p>

		<p class="epr-redirect-type">
			<label for="epr-type"><?php esc_html_e( 'Redirect Type:', 'external-permalinks-redux' ); ?></label>
			<select name="<?php echo esc_attr( $this->meta_key_target ); ?>_type" id="epr-type">
				<option value=""><?php esc_html_e( '-- Select --', 'external-permalinks-redux' ); ?></option>
				<?php
				foreach ( $this->status_codes as $status_code => $explanation ) {
					echo '<option value="' . esc_attr( $status_code ) . '"';
					selected( $status_code, (int) $type );
					echo '>' . esc_html( $explanation ) . '</option>';
				}
				?>
			</select>
		</p>

		<?php
		wp_nonce_field( 'external-permalinks-redux', $this->meta_key_target . '_nonce', false );
	}

	/**
	 * Save meta box input.
	 *
	 * @param int $post_id Post ID.
	 */
	public function action_save_post( $post_id ) {
		if (
			function_exists( 'use_block_editor_for_post' )
			&& use_block_editor_for_post( $post_id )
		) {
			return;
		}

		if ( isset( $_POST[ $this->meta_key_target . '_nonce' ] ) && wp_verify_nonce( sanitize_text_field( $_POST[ $this->meta_key_target . '_nonce' ] ), 'external-permalinks-redux' ) ) {
			// Target.
			$url = isset( $_POST[ $this->meta_key_target . '_url' ] ) ? esc_url_raw( $_POST[ $this->meta_key_target . '_url' ] ) : '';

			if ( ! empty( $url ) ) {
				update_post_meta( $post_id, $this->meta_key_target, $url );
			} else {
				delete_post_meta( $post_id, $this->meta_key_target, $url );
			}

			// Redirect type.
			$type = isset( $_POST[ $this->meta_key_target . '_type' ] ) ? (int) $_POST[ $this->meta_key_target . '_type' ] : '';

			if ( ! empty( $url ) && array_key_exists( $type, $this->status_codes ) ) {
				update_post_meta( $post_id, $this->meta_key_type, $type );
			} else {
				delete_post_meta( $post_id, $this->meta_key_type );
			}
		}
	}

	/**
	 * Filter post and custom post type permalinks.
	 *
	 * @param string $permalink Post permalinks.
	 * @param object $post Post object.
	 * @return string
	 */
	public function filter_post_permalink( $permalink, $post ) {
		$external_link = get_post_meta( $post->ID, $this->meta_key_target, true );

		if ( ! empty( $external_link ) ) {
			$permalink = $external_link;
		}

		return $permalink;
	}

	/**
	 * Filter page permalinks.
	 *
	 * @param string $link Page permalink.
	 * @param int    $id Post ID.
	 * @return string
	 */
	public function filter_page_link( $link, $id ) {
		$external_link = get_post_meta( $id, $this->meta_key_target, true );

		if ( ! empty( $external_link ) ) {
			$link = $external_link;
		}

		return $link;
	}

	/**
	 * Redirect to external link if object requested directly.
	 */
	public function action_wp() {
		global $post;

		if ( ! is_singular() ) {
			return;
		}

		$redirect = $this->get_redirect_data( $post->ID );

		if ( false === $redirect ) {
			return;
		}

		// Unreasonable to validate redirect destination.
		// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		wp_redirect( $redirect['link'], $redirect['type'] );
		exit;
	}

	/**
	 * Retrieve redirect data for a given post ID.
	 *
	 * @param int $post_id Post ID.
	 * @return array|bool
	 */
	public function get_redirect_data( $post_id ) {
		if ( ! is_numeric( $post_id ) ) {
			return false;
		}

		$link = get_post_meta( $post_id, $this->meta_key_target, true );

		if ( empty( $link ) ) {
			return false;
		}

		$type = (int) get_post_meta( $post_id, $this->meta_key_type, true );
		$type = apply_filters( 'epr_status_code', $type, $link, get_post( $post_id ) );

		if ( ! $type ) {
			$type = 302;
		}

		return compact( 'link', 'type' );
	}
}

// Initialize the plugin if it hasn't already.
external_permalinks_redux::get_instance();
External_Permalinks_Redux_Block_Editor::get_instance();

/**
 * Wrapper for meta box function.
 *
 * Can be used as an alternative to the `epr_post_types` filter
 * found in the plugin class's `action_admin_init` function.
 *
 * @param object $post Post object.
 */
function external_permalinks_redux_meta_box( $post ) {
	external_permalinks_redux::get_instance()->meta_box( $post );
}
