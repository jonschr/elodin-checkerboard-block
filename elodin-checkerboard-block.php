<?php
/*
	Plugin Name: Checkerboard Block
	Plugin URI: https://elod.in
    Description: Just another checkerboard block
	Version: 0.1
    Author: Jon Schroeder
    Author URI: https://elod.in

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.
*/


/* Prevent direct access to the plugin */
if ( !defined( 'ABSPATH' ) ) {
    die( "Sorry, you are not allowed to access this page directly." );
}

// Plugin directory
define( 'CHECKERBOARD_BLOCK', dirname( __FILE__ ) );

// Define the version of the plugin
define ( 'CHECKERBOARD_BLOCK_VERSION', '0.1' );


add_action('acf/init', 'checkerboard_register_block');
function checkerboard_register_block() {

    // Check function exists.
    if( function_exists( 'acf_register_block_type') ) {

        // register a testimonial block.
        acf_register_block_type(array(
            'name'              => 'checkerboard',
            'title'             => __('Checkerboard'),
            'description'       => __('A checkerboard block'),
            'render_callback'   => 'checkerboard_render',
            'enqueue_assets'    => 'checkerboard_enqueue',
            'category'          => 'formatting',
            'icon'              => 'admin-comments',
            'keywords'          => array( 'testimonial', 'quote' ),
            'mode'              => 'preview',
            'align'              => 'full',
            'supports'          => array(
                'align' => array( 'full', 'wide', 'normal' ),
                'mode' => false,
                'jsx' => true
            ),
        ));
    }
}

function checkerboard_render( $block, $content = '', $is_preview = false, $post_id = 0 ) {
    
    //* Get settings
    // Create id attribute allowing for custom "anchor" value.
    $id = 'testimonial-' . $block['id'];
    if( !empty($block['anchor']) ) {
        $id = $block['anchor'];
    }

    // Create class attribute allowing for custom "className" and "align" values.
    $className = 'testimonial';
    if( !empty($block['className']) ) {
        $className .= ' ' . $block['className'];
    }
    if( !empty($block['align']) ) {
        $className .= ' align' . $block['align'];
    }

    // Load values and assing defaults.
    $text = get_field('testimonial') ?: 'Your testimonial here...';
    $author = get_field('author') ?: 'Author name';
    $role = get_field('role') ?: 'Author role';
    $image = get_field('image') ?: 295;
    $background_color = get_field('background_color');
    $text_color = get_field('text_color');
    
    //* Render
    printf( '<div id="%s" class="checkerboard-wrap %s">', $id, $className );
        echo '<div class="checkerboard-image">';
        echo '</div>';
        echo '<div class="checkerboard-content">';
            echo '<div class="checkerboard-content-wrap">';
                echo '<InnerBlocks />';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}

function checkerboard_enqueue() {
    wp_enqueue_style( 'checkerboard-block-style', plugin_dir_url( __FILE__ ) . 'css/checkerboard.css', array(), CHECKERBOARD_BLOCK_VERSION, 'screen' );
}
