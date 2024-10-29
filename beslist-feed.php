<?php
/**
 * Function to get xml file content of  categories Products feeds
 */
function beslist_getBeslistFeed($category_id) {
header ("Content-Type:text/xml");
    global $wpdb;
    $posts_table = $wpdb->prefix . 'posts';
    $sql = "
            SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_content, $wpdb->posts.post_name
            FROM $wpdb->posts
            LEFT JOIN $wpdb->term_relationships ON
            ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
            LEFT JOIN $wpdb->term_taxonomy ON
            ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
            WHERE $wpdb->posts.post_status = 'publish'
            AND $wpdb->posts.post_type = 'product'
            AND $wpdb->term_taxonomy.taxonomy = 'product_cat'
            ORDER BY post_date DESC
            ";
    $products = $wpdb->get_results($sql);
    $xml = '';
    $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
    xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
//echo '<sitemap>'.PHP_EOL;
    foreach ($products as $prod) {
        $product = get_product($prod->ID);

        $size = sizeof( get_the_terms( $prod->ID, 'product_cat' ) );

        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                       '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                       '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );

        $omschrijving = preg_replace($search, '', $prod->post_content);
        $omschrijving = strip_tags($omschrijving);

        $xml .= '<item>'. PHP_EOL;
        $xml .= '<prijs>' .$product->price. '</prijs>' . PHP_EOL;
        $xml .= '<title>' .get_the_title($prod->ID). '</title>' . PHP_EOL;

        $xml .= '<sku>' . $product->sku . '</sku>' . PHP_EOL;
        $xml .= '<url>'. get_permalink($prod->ID) .'</url>'. PHP_EOL;

        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($prod->ID), 'small-feature');
        $imgurl = $thumb['0'];

        $xml .= '<url_productplaatje>'.$imgurl.'</url_productplaatje>'. PHP_EOL;
        $xml .= '<beschrijving><![CDATA[' .htmlspecialchars($omschrijving). ']]></beschrijving>';


        $xml .= '</item>'. PHP_EOL;
    }
// echo '</sitemap>'.PHP_EOL;
//echo '</sitemap>'.PHP_EOL;
    $xml .= '</urlset>';

//    $xml = utf8_encode( beslist_fix_utf8($xml) );
    $xml = beslist_fix_utf8($xml);
    if ( defined('DB_CHARSET') && 'utf8' != DB_CHARSET || 'UTF-8' != mb_detect_encoding($xml) ) {
        $xml = utf8_encode( $xml );
    }


    echo $xml;
}

function beslist_fix_utf8($str)
{
//    $str = preg_replace_callback('#[\\xA1-\\xFF](?![\\x80-\\xBF]{2,})#', 'beslist_utf8_encode_callback', $str);
    $str = preg_replace("{(<br[\\s]*(>|\/>)\s*){2,}}i", "<br /><br />", $str);
    $str = preg_replace("{(<br[\\s]*(>|\/>)\s*)}i", "<br />", $str);

    return $str;
}

function beslist_utf8_encode_callback($m)
{
    return utf8_encode($m[0]);
}
?>