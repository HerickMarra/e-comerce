<?php
/*
  Plugin Name:       Woocommerce EnviaMais
  Plugin URI:        https://enviamais.com.br/
  Description:       Plugin para integração do EnviaMais com o Woocommerce
  Version:           1.1.5
  Requires at least: 5.2
  Requires PHP:      7.2
  Author:            Enviamais
  Author URI:        https://app.enviamais.com.br/
  License:           GPL v2 or later
  License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) {
    exit;
}

class WooComerceEnviaMais
{

    function install()
    {
        global $wp_version;

        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            deactivate_plugins(plugin_basename(__FILE__)); /* Deactivate plugin */
            wp_die('You must run WooCommerce 3.x to install WooCommerce EnviaMais plugin');
            return;
        }

        if (!is_plugin_active('woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php')) {
            deactivate_plugins(plugin_basename(__FILE__)); /* Deactivate plugin */
            wp_die('You must run Brazilian Market on WooCommerce 3.7.x to install WooCommerce EnviaMais plugin');
            return;
        }

        if ((float)$wp_version < 3.5) {
            deactivate_plugins(plugin_basename(__FILE__)); /* Deactivate plugin */
            wp_die('You must run at least WordPress version 3.5 to install WooCommerce EnviaMais plugin');
            return;
        }

        // include_once('controllers/admin/enviamais-install-table.php');
    }

    public function deactivate()
    {
        // include_once('controllers/admin/enviamais-install-table.php');
    }

    public function create_settings_link($links_array)
    {
        array_unshift($links_array, '<a href="admin.php?page=enviamais-partner-api">Configurações</a>');
        return $links_array;
    }

    public function main()
    {
        include_once('classes/Utils.php');
        include_once('classes/Api.php');
        include_once('config/config-page.php');
        include_once('classes/OrderHelper.php');
        include_once('classes/Recipient.php');
        include_once('shipment/methods/enviamais-shipment-method.php');
        include_once('shipment/methods/enviamais-order-completed.php');
        include_once('shipment/methods/enviamais-custom-order-fields.php');
        include_once('utils/Versioning.php');
    }
}

$module = new WooComerceEnviaMais();

/* Execute Hooks */
register_activation_hook(__FILE__, array($module, 'install'));
register_deactivation_hook(__FILE__, array($module, 'deactivate'));
add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($module, 'create_settings_link'));

/* Exec */
$module->main();
