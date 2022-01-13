<?php
/*
	Plugin Name: Elodin Block: Checkerboard
	Plugin URI: https://elod.in
    Description: Just another checkerboard block
	Version: 1.3
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
define ( 'CHECKERBOARD_BLOCK_VERSION', '1.3' );

/////////////////
// INCLUDE ACF //
/////////////////

// Define path and URL to the ACF plugin.
define( 'ELODIN_CHECKERBOARD_BLOCK_ACF_PATH', plugin_dir_path( __FILE__ ) . 'vendor/acf/' );
define( 'ELODIN_CHECKERBOARD_BLOCK_ACF_URL', plugin_dir_url( __FILE__ ) . 'vendor/acf/' );

if( !class_exists('ACF') ) {
    
    // Include the ACF plugin.
    include_once( ELODIN_CHECKERBOARD_BLOCK_ACF_PATH . 'acf.php' );

    // Customize the url setting to fix incorrect asset URLs.
    add_filter('acf/settings/url', 'elodin_checkerboard_block_acf_settings_url');
    
}

function elodin_checkerboard_block_acf_settings_url( $url ) {
    return ELODIN_CHECKERBOARD_BLOCK_ACF_URL;
}

////////////
// FIELDS //
////////////

// Get the fields from php (the json version is there in case we want to edit it further using a UI)
require_once( 'acf-json/fields.php');

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
            'icon'              => 'columns',
            'keywords'          => array( 'checkerboard', 'column', 'image' ),
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
    
    //* Default class
    $className = 'checkerboard';
    
    //* Default ID
    $id = 'checkerboard-' . $block['id'];
    
    //* Get settings
    $alignment = get_field( 'alignment' );
    $background_image = get_field( 'background_image' );
    $background_attachment = get_field( 'background_attachment' );
    $section_background_color = get_field( 'section_background_color' );
    $minimum_height = get_field( 'minimum_height' );
    $minimum_height_mobile = get_field( 'minimum_height_mobile' );
    $add_fade_effect = get_field( 'add_fade_effect' );
    
    // Create id attribute allowing for custom "anchor" value.
    if( !empty($block['anchor']) ) 
        $id = $block['anchor'];

    // Create class attribute allowing for custom "className" and "align" values.
    if( !empty($block['className']) )
        $className .= ' ' . $block['className'];

    if( !empty($block['align']) )
        $className .= ' align' . $block['align'];
    
    // Alignment class (this is a setting, not the default align)
    if ( $alignment == 'imageright' ) {
        $className .= ' ' . 'checkerboard-image-right';
    } else {
        $className .= ' ' . 'checkerboard-image-left';
    }
        
    // Fade class (this is not the only component of this)
    if ( $add_fade_effect == 'fade' )
        $className .= ' ' . 'checkerboard-fade';
        
    // Background attachment
    if ( $background_attachment == 'fixed' )
        $className .= ' ' . 'background-fixed';
            
    //* Render
    printf( '<div id="%s" class="checkerboard-wrap %s" style="background-color:%s">', $id, $className, $section_background_color );
        printf( '<div class="checkerboard-image" style="background-image:url(%s);">', $background_image );
        
            if ( $add_fade_effect == 'fade' )
                printf( '<div class="fade"></div>');
        
        echo '</div>';
        echo '<div class="checkerboard-content">';
            echo '<div class="checkerboard-content-wrap">';
                echo '<InnerBlocks />';
            echo '</div>';
        echo '</div>';
        
        ?>
        <style>
            
            @media( min-width: 960px ) { 
                #checkerboard-<?php echo $block['id']; ?> .checkerboard-image {
                    <?php 
                    if ( isset( $minimum_height ) ) {
                        printf( 'min-height: %spx !important;', $minimum_height );
                    }
                    ?>
                }
            }
            
            @media( min-width: 600px and max-width: 960px ) { 
                #checkerboard-<?php echo $block['id']; ?> .checkerboard-image {
                    <?php 
                    if ( isset( $minimum_height_tablet ) ) {
                        printf( 'min-height: %spx !important;', $minimum_height_tablet );
                    }
                    ?>
                }
            }
            
            @media( max-width: 600px ) { 
                #checkerboard-<?php echo $block['id']; ?> .checkerboard-image {
                    <?php 
                    if ( isset( $minimum_height_mobile ) ) {
                        printf( 'min-height: %spx !important;', $minimum_height_mobile );
                    }
                    ?>
                }
            }
            
            @media( min-width: 600px ) { 
                
                #checkerboard-<?php echo $block['id']; ?> .checkerboard-image .fade {
                    <?php                     
                    if ( isset( $section_background_color ) && isset( $add_fade_effect ) && !isset( $alignment ) ) {
                        printf( 'background: linear-gradient( 90deg, transparent, %s ) !important;', $section_background_color );
                    }
                    
                    if ( isset( $section_background_color ) && isset( $add_fade_effect ) && isset( $alignment ) ) {
                        if ( $alignment == 'imageleft' )
                            printf( 'background: linear-gradient( 90deg, transparent, %s ) !important;', $section_background_color );
                        
                        if ( $alignment == 'imageright' )
                            printf( 'background: linear-gradient( 90deg, %s, transparent ) !important;', $section_background_color );
                    }
                    ?>
                }
            }
            
        </style>
        <?php
    
    echo '</div>';
}

function checkerboard_enqueue() {
    wp_enqueue_style( 'checkerboard-block-style', plugin_dir_url( __FILE__ ) . 'css/checkerboard.css', array(), CHECKERBOARD_BLOCK_VERSION, 'screen' );
}

function checkerboard_get_the_colors() {
	
	// get the colors
    $color_palette = current( (array) get_theme_support( 'editor-color-palette' ) );

	// bail if there aren't any colors found
	if ( !$color_palette )
		return;

	// output begins
	ob_start();

	// output the names in a string
	echo '[';
		foreach ( $color_palette as $color ) {
			echo "'" . $color['color'] . "', ";
		}
	echo ']';
    
    return ob_get_clean();

}

add_action( 'acf/input/admin_footer', 'checkerboard_register_acf_color_palette' );
function checkerboard_register_acf_color_palette() {

    $color_palette = checkerboard_get_the_colors();
    if ( !$color_palette )
        return;
    
    ?>
    <script type="text/javascript">
        (function( $ ) {
            acf.add_filter( 'color_picker_args', function( args, $field ){

                // add the hexadecimal codes here for the colors you want to appear as swatches
                args.palettes = <?php echo $color_palette; ?>

                // return colors
                return args;

            });
        })(jQuery);
    </script>
    <?php

}

// Updater
require 'vendor/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/jonschr/elodin-checkerboard-block',
	__FILE__,
	'elodin-checkerboard-block'
);

// Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');