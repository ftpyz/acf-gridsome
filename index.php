<?php
/**
 * Plugin Name: ACF For Gridsome
 * Description: Use gridsome with acf plugin
 * Author: Fuat POYRAZ
 * Author URI: https://github.com/ftpyz/acf-gridsome
 * Version: 0.5
 * Plugin URI: https://github.com/ftpyz/acf-gridsome
 */
add_filter( 'acf/format_value', function ( $value ) {
  if ( $value instanceof WP_Post ) {
    return [
      'post_type' => $value->post_type,
      'id'        => $value->ID,
    ];
  }

  return $value;
}, 100 );