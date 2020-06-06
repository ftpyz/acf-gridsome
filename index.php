<?php
/**
 * Plugin Name: ACF For Gridsome
 * Description: Use gridsome with acf plugin
 * Author: Fuat POYRAZ
 * Author URI: https://gurmewoo.com
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

add_filter( 'acf/format_value/type=select', function ( $value ) {
  if(count($value)==0){
    return false;
  }

  return $value;
}, 100 );