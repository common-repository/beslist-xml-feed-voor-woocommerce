<?php

/*
  Plugin Name: Beslist XML voor WooCommerce
  Plugin URI: http://www.jackjunior.nl
  Description: XML Feed voor beslist.nl | Versie 1.1
  Author: beslist Turtle Productions
  Version: 1.1.3
  Author URI: http://www.jackjunior.nl

 */
global $logger, $beslistxml_plugin_dir, $beslistxml_plugin_url;

$beslistxml_plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));
$beslistxml_plugin_url = plugins_url() . "/beslist-xml/";


function beslist_xml_init() {

    if (isset($_REQUEST['beslistxml'])) {
        $beslistxml = $_REQUEST['beslistxml'];
        switch ($beslistxml) {
            case 'feed':
                $category = $_REQUEST['category'];
                $inc = 'beslist-feed.php';
                include_once $inc;

                beslist_getBeslistFeed($category);
                break;
            default: $inc = '';
            break;
        }

        if ($inc != "") {
            exit();
        }
    }
}

add_action('init', 'beslist_xml_init');


if (is_admin()) {
    add_action('admin_menu', 'setup_beslist_feeds_admin_menu');

    $plugin = plugin_basename(__FILE__);

    add_filter("plugin_action_links_" . $plugin, 'beslist_xml_add_settings_link');

    function beslist_xml_add_settings_link($links) {

        $settings_link = '<a href="options-general.php?page=beslist-feeds-xml">Uw XML feed</a>';

        array_unshift($links, $settings_link);

        return $links;
    }

    function setup_beslist_feeds_admin_menu() {

        add_submenu_page('options-general.php', 'Beslist XML', 'Beslist XML', 'manage_options', 'beslist-feeds-xml', 'beslist_feeds_page_settings');
    }

}

function beslist_feeds_page_settings() {
    global $wpdb;
    echo "<br/>";
    echo "<h1>Beslist XML</h1>";
    echo "<div id='poststuff'><div class='postbox' style='width: 98%;'><br/>";
    echo "<div class='inside export-target'>";
    echo "<a href='".get_site_url()."/?beslistxml=feed' target='_blank'>Beslist XML feed</a><br/>";
    echo "<p>U kunt de Feed aanmelden bij Beslist via deze link: <a href='https://cl.beslist.nl/'>cl.beslist.nl</a>. <br/>Het opnemen van uw producten bij Beslist kan een paar dagen duren.</p>";
    echo "</div></div></div>";

    echo "<div id='poststuff'><div class='postbox' style='width: 98%;'><br/>";
    echo "<div class='inside export-target' style='margin-top:-10px;'>";
    echo "<p>Wij zijn bezig met het ontwikkelen van deze plugin. Werkt iets niet of mist u iets? <a href='mailto:info@jackjunior.nl' target='_blank'>Mail ons dan</a></p>";
    echo "</div></div></div>";

}

?>