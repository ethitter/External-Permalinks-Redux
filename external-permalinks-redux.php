<?php
/**
 * Plugin Name: External Permalinks Redux
 * Plugin URI: http://www.thinkoomph.com/plugins-modules/external-permalinks-redux/
 * Description: Allows users to point WordPress objects (posts, pages, custom post types) to a URL of your choosing. Inspired by and backwards-compatible with <a href="http://txfx.net/wordpress-plugins/page-links-to/">Page Links To</a> by Mark Jaquith. Written for use on WordPress.com VIP.
 * Version: 1.2
 * Author: Erick Hitter & Oomph, Inc.
 * Author URI: http://www.thinkoomph.com/
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

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load main plugin.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/external-permalinks-redux.php';

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
