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
  if ( $value instanceof WP_Taxonomy ) {
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

$post_type = "akillisaat";
function my_rest_prepare_post($data, $post, $request) {
    $_data = $data->data;
    $fields = get_fields($post->ID);
    foreach ($fields as $key => $value) {
        //$_data[$key] = get_field($key, $post->ID);
        $_data["acf"][$key]=get_field_object($key,$post->ID);
        if($_data["acf"][$key]["type"]=="select"){
          if(is_array($_data["acf"][$key]["value"])){
            if(count($_data["acf"][$key]["value"])>0){
              $_data["acf"][$key]["show"]=true;
            }
          }else{
            $_data["acf"][$key]["show"]=false;
          }
        }else if ($_data["acf"][$key]["type"]=="text"){
          if($_data["acf"][$key]["value"]=='0'){
            $_data["acf"][$key]["show"]=false;
          }else if(strlen($_data["acf"][$key]["value"])==0){
            $_data["acf"][$key]["show"]=false;
          }else{
            $_data["acf"][$key]["show"]=true;
          }
          
        }else{
          $_data["acf"][$key]["show"]=true;
        }
    }
    $field_groups = [];
    foreach ( acf_get_field_groups() as $group ) {
      // DO NOT USE here: $fields = acf_get_fields($group['key']);
      // because it causes repeater field bugs and returns "trashed" fields
      
      $fields = get_posts(array(
        'posts_per_page'   => -1,
        'post_type'        => 'acf-field',
        'orderby'          => 'menu_order',
        'order'            => 'ASC',
        'suppress_filters' => true, // DO NOT allow WPML to modify the query
        'post_parent'      => $group['ID'],
        'post_status'      => 'any',
        'update_post_meta_cache' => false
      ));
      foreach ( $fields as $field ) {
        $group["fields"][]=$field->post_excerpt;
      }
      $field_groups[]=$group;
    }
    $_data["acf"]["group"]=$field_groups;
    $desc = RankMath\Post::get_meta( 'description', $post->ID );
    $title = RankMath\Post::get_meta( 'title', $post->ID );
    $_data["acf"]["seo"]=array("title"=>$title,"description"=>$desc);
    $data->data = $_data;
    return $data;
}
add_filter("rest_prepare_akillisaat", 'my_rest_prepare_post', 10, 99);

add_action( 'rest_api_init', 'slug_register_meta' );
function slug_register_meta() {
    register_rest_field( 'category',
        'meta', 
        array(
            'get_callback'    => 'slug_get_meta',
            'update_callback' => 'slug_update_meta',
            'schema'          => null,
        )
    );
    register_rest_field( 'post',
        'meta', 
        array(
            'get_callback'    => 'slug_get_meta',
            'update_callback' => 'slug_update_meta',
            'schema'          => null,
        )
    );
    register_rest_field( 'akillisaat',
        'meta', 
        array(
            'get_callback'    => 'slug_get_post_meta',
            'update_callback' => 'slug_update_meta',
            'schema'          => null,
        )
    );
    register_rest_field( 'tag',
        'meta', 
        array(
            'get_callback'    => 'slug_get_meta',
            'update_callback' => 'slug_update_meta',
            'schema'          => null,
        )
    );
}
function slug_get_meta( $object, $field_name, $request ) {
    
    return get_term_meta( $object[ 'id' ] );
}
function slug_update_meta($value, $object, $field_name){
    // please note: make sure that $object is indeed and object or array
    return update_post_meta($object['id'], $field_name, $value);
}

function slug_get_post_meta( $object, $field_name, $request ) {
    
    return get_post_meta( $object[ 'id' ] );
}