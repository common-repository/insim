<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ardary-insim.com
 * @since      1.0.0
 *
 * @package    Sim_to_shop
 * @subpackage Sim_to_shop/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Sim_to_shop
 * @subpackage Sim_to_shop/public
 * @author     2WS Technologies <contact@ardary-sms.com>
 */
class Sim_To_Shop_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $sim_to_shop    The ID of this plugin.
	 */
	private $sim_to_shop;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $sim_to_shop       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $sim_to_shop, $version ) {

		$this->sim_to_shop = $sim_to_shop;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Octopush_Sms_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Octopush_Sms_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->sim_to_shop, plugin_dir_url( __FILE__ ) . 'css/sim-to-shop-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Octopush_Sms_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Octopush_Sms_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->sim_to_shop, plugin_dir_url( __FILE__ ) . 'js/css/sim-to-shop-public.js', array( 'jquery' ), $this->version, false );

	}

}
