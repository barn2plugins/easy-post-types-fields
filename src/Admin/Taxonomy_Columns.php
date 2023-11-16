<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Admin;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Registerable,
    Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Service,
	  Barn2\Plugin\Easy_Post_Types_Fields\Util;

/**
 * Manage the custom taxonomy columns in different post types
 *
 * @package   Barn2\easy-post-types-fields
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Taxonomy_Columns implements Registerable, Service {


	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_filter( 'manage_edit-product_columns', [ $this, 'manage_product_taxonomies' ] );
    add_action( 'current_screen', [ $this, 'manage_taxonomies_column' ] );
  }


  /**
   * Delete the taxonomies from the product listing page
   */
  public function manage_product_taxonomies( $columns ) {
    $post_type_object = Util::get_post_type_object( 'product' );

		if ( $post_type_object ) {
			$taxonomies = get_post_meta( $post_type_object->ID, '_ept_taxonomies', true );
      
      if( $taxonomies ) {
        foreach( $taxonomies as $taxonomy ) {
          unset( $columns[ 'taxonomy-product_' . $taxonomy['slug'] ] );
        }
      }
    }

    return $columns;
  }

  public function manage_taxonomies_column() {
    $post_types = get_post_types( [
      'public'   => true
    ] );

    if( $post_types ) {
      foreach( $post_types as $post_type ) {
        if( $post_type !== 'product' ) {
          add_action( 'manage_edit-'. $post_type .'_columns', [ $this, 'manage_pt_taxonomies' ] );
        }
      }
    }
  }

  public function manage_pt_taxonomies( $columns ) {
    
    $screen = get_current_screen();
    $post_type_object = Util::get_post_type_object( $screen->post_type );

		if ( $post_type_object ) {
			$taxonomies = get_post_meta( $post_type_object->ID, '_ept_taxonomies', true );
      
      if( is_array( $taxonomies ) && count($taxonomies) > 1 ) {
        array_shift( $taxonomies );
        foreach( $taxonomies as $taxonomy ) {
          unset( $columns[ 'taxonomy-'. $screen->post_type .'_' . $taxonomy['slug'] ] );
        }
      }
    }

    return $columns;
  }
}
