<?php
/*
Plugin Name: Combine Orders for WooCommerce
Description: Simple buttons in order list page to combine given Order IDs. Easily.
Version: 1.0.0
Author: Mr.Ebabi
Author URI: https://github.com/MrEbabi
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: combine-orders-for-woocommerce
WC requires at least: 2.5
WC tested up to:  3.6.4 
*/

if(!defined('ABSPATH'))
{
    die;
}

defined('ABSPATH') or die('You shall not pass!');

if(!function_exists('add_action'))
{
    echo "You shall not pass!";
    exit;
}

//require woocommerce to install global coupons for woocommerce
add_action( 'admin_init', 'combine_orders_require_woocommerce' );

function combine_orders_require_woocommerce() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) 
    {
        add_action( 'admin_notices', 'combine_orders_require_woocommerce_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) 
        {
            unset( $_GET['activate'] );
        }
    }
}

//throw admin notice if woocommerce is not active
function combine_orders_require_woocommerce_notice(){
    ?>
    <div class="error"><p>Sorry, Combine Orders for WooCommerce requires the Woocommerce plugin to be installed and activated.</p></div>
    <?php
    return;
}

if(!class_exists('CombineOrdersForWooCommerce'))
{
    class CombineOrdersForWooCommerce
    {
        function __construct()
        {
            require_once(dirname(__FILE__) . '/src/combine-orders.php');
        }
    }
}

if(class_exists('CombineOrdersForWooCommerce'))
{
    $combineOrdersForWooCommerce = new CombineOrdersForWooCommerce();
}

register_activation_hook( __FILE__, array($combineOrdersForWooCommerce, '__construct'));
