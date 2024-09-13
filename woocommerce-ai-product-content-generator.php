<?php
/*
Plugin name: WebbersAI Product Content Generator For WooCommerce
Version: 1.0
Plugin URI: https://webbersai.com
Author: WebbersAI
Author URI: https://webbersai.com/support/
Description: This plugin generates product content, product images, product tags, meta tags, and meta descriptions for WooCommerce products using AI.
Tags: Product Content Generator, Images, Product Tags, Meta Tags, SEO
License: GPLv2 or later
Requires at least: 6.0
Requires PHP: 7.2
*/
if (!defined('ABSPATH')) {

    exit;

}



// Include necessary files

require_once plugin_dir_path(__FILE__) . 'includes/webbersaicg-admin-page.php';

require_once plugin_dir_path(__FILE__) . 'includes/webbersaicg-content-generator.php';



define('WEBBERSAICG_PLUGIN_URL', esc_url(plugin_dir_url(__FILE__)));



// Register activation hook

register_activation_hook(__FILE__, 'webbersaicg_activate');



function webbersaicg_activate() {

    // Code to execute on plugin activation

}



// Adding the button under the product title

add_action('edit_form_after_title', 'webbersaicg_add_generate_content_button');



function webbersaicg_add_generate_content_button($post) {

    if ($post->post_type == 'product') {

        echo '<div id="loading-icon" style="display: none;">

                <img src="' . esc_url(WEBBERSAICG_PLUGIN_URL . 'assets/images/aicontent-loading.gif') . '" alt="' . esc_attr__('Loading...', 'webbersai-product-content-generator') . '" style="height:70px;">

              </div>';



        

        echo '<div class="webbersaicg_description">

                <h4>Enhance Your Product Listings with Our WebbersAI Product Content Generator For WooCommerce</h4>

                <p>Boost your sales by generating compelling product descriptions, short descriptions, product images, and product tags with just <span class="highlight">one click</span>.</p>

                <p>Our AI also crafts SEO-optimized meta keywords and meta descriptions to enhance your online visibility.</p>

              </div>';



        echo '<div>

                <span><button id="generate-content-button" type="button" class="button button-primary">Generate AI Product Content</button></span>

                <span><button id="generate-image-button" type="button" class="button button-primary">Generate AI Product Image</button></span>

              </div>';



        echo '<div id="msg"></div>';



        // Inline scripts

        $inline_js = '

            jQuery(document).ready(function($) {

                 // Add your inline JS code here

            });

        ';

        wp_add_inline_script('webbersaicg-admin-script', $inline_js);



        echo '<div id="overwrite-confirmation" title="Overwrite Content" style="display:none;">

                <p>The product description already exists. Do you want to overwrite it?</p>

              </div>';

    }

}



// Enqueue admin scripts and styles

add_action('admin_enqueue_scripts', 'webbersaicg_admin_scripts');



function webbersaicg_admin_scripts($hook) {

    if ($hook === 'post.php' || $hook === 'post-new.php') {

        // Check if SEO plugins are active

        $yoast_active = is_plugin_active('wordpress-seo/wp-seo.php');

        $all_in_one_seo_active = is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php');

        $other_seo_plugins_active = $yoast_active || $all_in_one_seo_active;

		

//		// Register and enqueue admin styles
		wp_register_style('webbersaicg-admin-style', plugin_dir_url(__FILE__) . 'assets/css/webbersaicg-admin.css', array(), '1.0.0'); 
		wp_enqueue_style('webbersaicg-admin-style');

		

        wp_enqueue_script('jquery-ui-dialog');

        wp_register_style('jquery-ui-style', plugin_dir_url(__FILE__) . 'assets/css/jquery-ui/jquery-ui.min.css', array(), '1.0.0');

        wp_enqueue_style('jquery-ui-style');

        wp_register_script('webbersaicg-admin-script', plugin_dir_url(__FILE__) . 'includes/js/webbersaicg-admin.js', array('jquery', 'jquery-ui-dialog'), '1.0.0', true);

		wp_script_add_data('webbersaicg-admin-script', array('async', 'defer'), true);

        wp_enqueue_script('webbersaicg-admin-script');


        // Pass the plugin active status and nonces to the script
        wp_localize_script('webbersaicg-admin-script', 'webbersaicgNonceData', array(
            'webbersaicg_generate_meta_tags_nonce' => wp_create_nonce('webbersaicg_generate_meta_tags_nonce'),
            'webbersaicg_save_meta_tags_description_nonce' => wp_create_nonce('webbersaicg_save_meta_tags_description_nonce'),
            'webbersaicg_generate_product_content_nonce' => wp_create_nonce('webbersaicg_generate_product_content_nonce'),
		    'webbersaicg_generate_product_image_nonce' => wp_create_nonce('webbersaicg_generate_product_image_nonce'),
			'webbersaicg_generate_short_content_nonce' => wp_create_nonce('webbersaicg_generate_short_content_nonce'),
		    'webbersaicg_generate_product_tags_nonce' => wp_create_nonce('webbersaicg_generate_product_tags_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
        wp_localize_script('webbersaicg-admin-script', 'webbersaicgSeoPluginCheck', array(

            'otherSeoPluginDetected' => $other_seo_plugins_active

        ));

		

// Inline styles

        $inline_css = '

            .error-message {

                color: #fe0303;

                font-weight: bold;

                float: left;

                width: 100%;

                font-size: 13px;

            }

            #savemsg {

                float: left;

                width: 100%;

                font-size: 14px;

                font-weight: bold;

                color: #008a00;

            }

            button#generate-content-button, button#generate-image-button {

                margin-bottom: 20px;

            }

            #postdivrich.woocommerce-product-description {

                margin-top: 35px;

            }

            .ui-dialog-titlebar-close .ui-button-icon-space {

                display: none;

            }

            .ui-dialog-titlebar-close {

                text-indent: -9999px;

            }

            .webbersaicg_description {

                background-color: #f9f9f9;

                border: 1px solid #ddd;

                padding: 20px;

                border-radius: 8px;

                margin: 20px auto;

            }

            .webbersaicg_description h4 {

                color: #ff0000;

                font-size: 16px;

                margin-bottom: 12px;

            }

            .webbersaicg_description p {

                color: #333;

                font-size: 13px;

                line-height: 1.2;

                margin-bottom: 12px;

                font-weight: bold;

            }

            .webbersaicg_description .highlight {

                font-weight: bold;

                color: #d54e21;

                font-size: 16px;

            }

            #generate-content-button, #generate-image-button {

                background-color: #0073aa;

                color: #fff;

                border: none;

                padding: 10px 20px;

                font-size: 14px;

                cursor: pointer;

                border-radius: 4px;

            }

            #generate-content-button:hover, #generate-image-button:hover {

                background-color: #005a87;

            }

            #msg {

                display: inline-block;

                margin-left: 10px;

                font-size: 13px;

                color: #008a00;

            }

            p.error-message {

                line-height: 0.1;

            }

            #orgid {

                font-weight: 500;

            }

        ';

        wp_add_inline_style('webbersaicg-admin-style', $inline_css);

		

    }





    if ($hook != 'toplevel_page_webbersaicg-settings') {

        return;

    }



    wp_register_script('webbersaicg-model-selection', plugin_dir_url(__FILE__) . 'includes/js/webbersaicg-model-selection.js', array('jquery'), '1.0', true);

    wp_enqueue_script('webbersaicg-model-selection');

}



// Utility function to check if a plugin is active

if (!function_exists('is_plugin_active')) {

    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

}



// ss_edit

add_action('add_meta_boxes', 'webbersaicg_add_generate_meta_metabox');



function webbersaicg_add_generate_meta_metabox() {

    add_meta_box(

        'webbersaicg_generate_meta_metabox', // ID

        __('Generate Meta Keywords and Description', 'webbersai-product-content-generator'), // Title

        'webbersaicg_generate_meta_metabox_callback', // Callback

        'product', // Post type

        'normal', // Context

        'high' // Priority

    );

}

function webbersaicg_generate_meta_metabox_callback($post) {

    $meta_tags = get_post_meta($post->ID, '_meta_tags', true);

    $meta_description = get_post_meta($post->ID, '_meta_description', true);

    ?>

			<!-- Add this HTML inside your meta box -->

<div id="webbersaicg-confirm-dialog" title="Generate Meta Keywords" style="display:none;">

    <p>Another SEO plugin is detected. Do you want to override the existing meta description with custom metas?</p>

</div>



    <div>

	<span id="savemsg"></span>

        <p><strong>Meta Keywords:</strong></p>

        <div id="meta-tags-result" contenteditable="true" style="border: 1px solid #ccc; padding: 5px; min-height: 40px;"><?php echo esc_html($meta_tags); ?></div>

        <input type="hidden" name="_meta_tags" id="hidden-meta-tags" value="<?php echo esc_attr($meta_tags); ?>">

    </div>

    <div>

        <p><strong>Meta Description:</strong></p>

        <div id="meta-description-result" contenteditable="true" style="border: 1px solid #ccc; padding: 5px; min-height: 40px;"><?php echo esc_html($meta_description); ?></div>

        <input type="hidden" name="_meta_description" id="hidden-meta-description" value="<?php echo esc_attr($meta_description); ?>">

    </div>

    <button type="button" id="save-meta-tags-button" class="button button-primary" style="margin-top: 20px;">

    <?php esc_html_e('Save Meta Keywords and Description', 'webbersai-product-content-generator'); ?>

    </button>
	
     <?php

  }
  

function webbersaicg_get_ajax_nonces() {
			return array(
			
				'webbersaicg_generate_meta_tags_nonce' => wp_create_nonce('webbersaicg_generate_meta_tags_nonce'),
				'webbersaicg_save_meta_tags_description_nonce' => wp_create_nonce('webbersaicg_save_meta_tags_description_nonce'),
				'webbersaicg_generate_product_content_nonce' => wp_create_nonce('webbersaicg_generate_product_content_nonce'),
				'webbersaicg_generate_product_image_nonce' => wp_create_nonce('webbersaicg_generate_product_image_nonce'),
				'webbersaicg_generate_short_content_nonce' => wp_create_nonce('webbersaicg_generate_short_content_nonce'),
				'webbersaicg_generate_product_tags_nonce' => wp_create_nonce('webbersaicg_generate_product_tags_nonce')
			);
}
     // Handle AJAX request to generate meta keywords
	add_action('wp_ajax_webbersaicg_generate_meta_tags', 'webbersaicg_generate_meta_tags_ajax');

function webbersaicg_generate_meta_tags_ajax() {
       check_ajax_referer('webbersaicg_generate_meta_tags_nonce', 'security');

    if (!isset($_POST['post_id']) || !isset($_POST['post_title']) || !isset($_POST['post_content'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field(wp_unslash($_POST['post_title'])); // Added wp_unslash
    $product_description = sanitize_text_field(wp_unslash($_POST['post_content'])); // Added wp_unslash

    $meta_tags = webbersaicg_generate_meta_tags($title, $product_description);
    $meta_description = webbersaicg_generate_meta_description($title, $product_description);

    if ($meta_tags && $meta_description) {
        wp_send_json_success(array('meta_tags' => $meta_tags, 'meta_description' => $meta_description));
    } else {
        wp_send_json_error('Failed to generate meta keywords and description.');
    }

    wp_die();
}

// Handle AJAX request to save meta tags and description
add_action('wp_ajax_webbersaicg_save_meta_tags_description', 'webbersaicg_save_meta_tags_description_ajax');

function webbersaicg_save_meta_tags_description_ajax() {
 check_ajax_referer('webbersaicg_save_meta_tags_description_nonce', 'security'); // Add nonce check
 
    if (!isset($_POST['post_id']) || !isset($_POST['meta_tags']) || !isset($_POST['meta_description'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $meta_tags = sanitize_text_field(wp_unslash($_POST['meta_tags'])); // Added wp_unslash
    $meta_description = sanitize_text_field(wp_unslash($_POST['meta_description'])); // Added wp_unslash

    update_post_meta($post_id, '_meta_tags', $meta_tags);
    update_post_meta($post_id, '_meta_description', $meta_description);

    wp_send_json_success();
}

// Save Meta Fields During WooCommerce Product Update
add_action('save_post_product', 'webbersaicg_save_woocommerce_product_meta', 10, 3);

function webbersaicg_save_woocommerce_product_meta($post_id, $post, $update) {
	if (!isset($_POST['webbersaicg_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['webbersaicg_meta_nonce'])), 'webbersaicg_save_meta')) {
        return; // Nonce check failed
    }
    if ($post->post_type != 'product') {
        return;
    }

    if (isset($_POST['_meta_tags'])) {
        $meta_tags = sanitize_text_field(wp_unslash($_POST['_meta_tags'])); // Added wp_unslash
        update_post_meta($post_id, '_meta_tags', $meta_tags);
    }

    if (isset($_POST['_meta_description'])) {
        $meta_description = sanitize_text_field(wp_unslash($_POST['_meta_description'])); // Added wp_unslash
        update_post_meta($post_id, '_meta_description', $meta_description);
    }

    if (isset($_POST['meta-tags-result'])) {
        update_post_meta($post_id, '_meta_tags', sanitize_text_field(wp_unslash($_POST['meta-tags-result']))); // Added wp_unslash
    }

    if (isset($_POST['meta-description-result'])) {
        update_post_meta($post_id, '_meta_description', sanitize_text_field(wp_unslash($_POST['meta-description-result']))); // Added wp_unslash
    }
}

// Handle AJAX request to generate content
add_action('wp_ajax_webbersaicg_generate_product_content', 'webbersaicg_generate_product_content_ajax');

function webbersaicg_generate_product_content_ajax() {
	check_ajax_referer('webbersaicg_generate_product_content_nonce', 'security'); // Add nonce check

    if (!isset($_POST['post_id']) || !isset($_POST['title'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field(wp_unslash($_POST['title'])); // Added wp_unslash

    $generated_content = webbersaicg_generate_content($title);

    if ($generated_content) {
        // Only update the post content if 'overwrite' is set to true
        if (isset($_POST['overwrite']) && $_POST['overwrite'] === 'true') {
            wp_update_post([
                'ID' => $post_id,
                'post_content' => sanitize_text_field($generated_content),
            ]);
        }

        wp_send_json_success($generated_content);
    } else {
        wp_send_json_error('Content generation failed.');
    }
}

// Handle AJAX request to generate short content
add_action('wp_ajax_webbersaicg_generate_short_content', 'webbersaicg_generate_short_content_ajax');

function webbersaicg_generate_short_content_ajax() {
	check_ajax_referer('webbersaicg_generate_short_content_nonce', 'security'); // Add nonce check
	
    if (!isset($_POST['post_id']) || !isset($_POST['title'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field(wp_unslash($_POST['title'])); // Added wp_unslash

    $generated_content = webbersaicg_generate_short_content($title);

    if ($generated_content) {
        wp_update_post([
            'ID' => $post_id,
            'post_excerpt' => sanitize_text_field($generated_content),
        ]);
        wp_send_json_success($generated_content);
    } else {
        wp_send_json_error('Content generation failed.');
    }
}

// Handle AJAX request to generate tags
add_action('wp_ajax_webbersaicg_generate_product_tags', 'webbersaicg_generate_product_tags_ajax');

function webbersaicg_generate_product_tags_ajax() {
	check_ajax_referer('webbersaicg_generate_product_tags_nonce', 'security'); // Add nonce check
	
    if (!isset($_POST['post_id']) || !isset($_POST['title'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field(wp_unslash($_POST['title'])); // Added wp_unslash

    $generated_tags = webbersaicg_generate_product_tags($title);

    if ($generated_tags) {
        wp_send_json_success($generated_tags);
    } else {
        wp_send_json_error('Tag generation failed.');
    }
}
