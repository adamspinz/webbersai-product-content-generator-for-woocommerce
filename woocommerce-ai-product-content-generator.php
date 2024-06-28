<?php
/*
Plugin Name: Woocommerce AI Product Content Generator
Plugin URI: https://spinzsoft.com
Description: This plugin generates content and meta description for WooCommerce products using AI.
Tags: Woocommerce, Product Content Generator, AI, Product Tags, Meta Tags, SEO
Version: 1.0
Author: adam@spinzsoft.com
Author URI: https://spinzsoft.com
License: GPLv2 or later
*/
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/content-generator.php';

define('WACG_PLUGIN_URL', plugin_dir_url(__FILE__));
// Register activation hook
register_activation_hook(__FILE__, 'wacg_activate');

function wacg_activate() {
    // Code to execute on plugin activation
}

// Adding the button under the product title
add_action('edit_form_after_title', 'add_generate_content_button');

function add_generate_content_button($post) {
    if ($post->post_type == 'product') {
	?>
<div id="loading-icon" style="display: none;">
<img src="<?php echo WACG_PLUGIN_URL . 'assets/images/aicontent-loading.gif'; ?>" alt="Loading..." style="height:70px;" >
</div>
<style>
    .error-message{
    color: #fe0303;
    font-weight: bold;
    float: left;
    width: 100%;font-size: 13px;
    }
	  #savemsg {float: left;
    width: 100%;font-size: 14px;font-weight: bold;color: #008a00;
    }
	button#generate-content-button {
    margin-bottom: 20px;
    }#postdivrich.woocommerce-product-description {
    margin-top: 35px;}
    
	.ui-dialog-titlebar-close .ui-button-icon-space {display: none;}
    .ui-dialog-titlebar-close {text-indent: -9999px; }
	.wacg_description {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
   /* max-width: 700px;*/
    margin: 20px auto;
}

.wacg_description h4 {
    color: #ff0000;
    font-size: 16px;
    margin-bottom: 12px;
}

.wacg_description p {
    color: #333;
    font-size: 13px;
    line-height: 1.2;
    margin-bottom: 12px; font-wieght:bold;
}

.wacg_description .highlight {
    font-weight: bold;
    color: #d54e21;
    font-size: 16px;
}

#generate-content-button {
    background-color: #0073aa;
    color: #fff;
    border: none;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 4px;
}

#generate-content-button:hover {
    background-color: #005a87;
}

#msg {
    display: inline-block;
    margin-left: 10px;
    font-size: 13px;
    color:  #008a00;
}
p.error-message {
    line-height: 0.1;
}
</style>
<?php   
        echo '<div class="wacg_description">
        <h4>Enhance Your Product Listings with Our WooCommerce AI Product Content Generator</h4>
        <p>Boost your sales by generating compelling product descriptions, short descriptions, and product tags with just <span class="highlight">one click</span>.</p>
        <p>Our AI also crafts SEO-optimized meta keywords and meta descriptions to enhance your online visibility.</p>
      </div>';
	  echo'<div><button id="generate-content-button" type="button" class="button button-primary">Generate AI Product Content</button></div>';
	  echo' <div id="msg"></div>';
    //    echo '<button id="generate-meta-tags-button" type="button" class="button button-primary" style="margin-right: 20px; margin-top: 12px;">Generate Meta Tags</button><span id="msg"></span>';
		?>
		 <!-- Modal Box -->
        <div id="overwrite-confirmation" title="Overwrite Content" style="display:none;">
            <p>The product description already exists. Do you want to overwrite it?</p>
        </div>
		<?php

    }
}


// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'wacg_admin_scripts');
function wacg_admin_scripts($hook) {
    if ($hook === 'post.php' || $hook === 'post-new.php') {
        // Check if SEO plugins are active
        $yoast_active = is_plugin_active('wordpress-seo/wp-seo.php');
        $all_in_one_seo_active = is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php');
        $other_seo_plugins_active = $yoast_active || $all_in_one_seo_active;

        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('jquery-ui.min.css', plugin_dir_url(__FILE__) . 'assets/css/jquery-ui/jquery-ui.min.css');
        wp_enqueue_script('wacg-admin-script', plugin_dir_url(__FILE__) . 'includes/js/wacg-admin.js', array('jquery', 'jquery-ui-dialog'), '1.0', true);

        // Pass the plugin active status to the script
        wp_localize_script('wacg-admin-script', 'wacgSeoPluginCheck', array(
            'otherSeoPluginDetected' => $other_seo_plugins_active
        ));
    }
}

// Utility function to check if a plugin is active
if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}



// ss_edit
add_action('add_meta_boxes', 'add_generate_meta_metabox');

function add_generate_meta_metabox() {
    add_meta_box(
        'generate_meta_metabox', // ID
        __('Generate Meta Keywords and Description', 'textdomain'), // Title
        'generate_meta_metabox_callback', // Callback
        'product', // Post type
        'normal', // Context
        'high' // Priority
    );
}



function generate_meta_metabox_callback($post) {
    $meta_tags = get_post_meta($post->ID, '_meta_tags', true);
    $meta_description = get_post_meta($post->ID, '_meta_description', true);
    ?>
			<!-- Add this HTML inside your meta box -->
<div id="wacg-confirm-dialog" title="Generate Meta Keywords" style="display:none;">
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
        <?php _e('Save Meta Keywords and Description', 'textdomain'); ?>
    </button>
	
    <?php
 }


     // Handle AJAX request to generate meta keywords
    add_action('wp_ajax_wacg_generate_meta_tags', 'wacg_generate_meta_tags_ajax');
	
	function wacg_generate_meta_tags_ajax() {
    // check_ajax_referer('generate_meta_nonce', 'nonce');

    if (!isset($_POST['post_id']) || !isset($_POST['post_title']) || !isset($_POST['post_content'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field($_POST['post_title']);
    $product_description = sanitize_text_field($_POST['post_content']);

    $meta_tags = wacg_generate_meta_tags($title, $product_description);
    $meta_description = wacg_generate_meta_description($title, $product_description);

    if ($meta_tags && $meta_description) {
        wp_send_json_success(array('meta_tags' => $meta_tags, 'meta_description' => $meta_description));
    } else {
        wp_send_json_error('Failed to generate meta keywords and description.');
    }

    wp_die();
 }

// Handle AJAX request to save meta tags and description
add_action('wp_ajax_save_meta_tags_description', 'save_meta_tags_description_ajax');
	
	function save_meta_tags_description_ajax() {
			if (!isset($_POST['post_id']) || !isset($_POST['meta_tags']) || !isset($_POST['meta_description'])) {
				wp_send_json_error('Invalid data.');
			}

    $post_id = intval($_POST['post_id']);
    $meta_tags = sanitize_text_field($_POST['meta_tags']);
    $meta_description = sanitize_text_field($_POST['meta_description']);

    update_post_meta($post_id, '_meta_tags', $meta_tags);
    update_post_meta($post_id, '_meta_description', $meta_description);

    wp_send_json_success();
}

// Save Meta Fields During WooCommerce Product Update
add_action('save_post_product', 'save_woocommerce_product_meta', 10, 3);
		function save_woocommerce_product_meta($post_id, $post, $update) {
			if ($post->post_type != 'product') {
				return;
			}
			
			if (isset($_POST['_meta_tags'])) {
			$post_id = intval($_POST['post_id']);
			}
		//$meta_tags = sanitize_text_field($_POST['_meta_tags']);
		//$meta_description = sanitize_text_field($_POST['_meta_description']);
		

    if (isset($_POST['_meta_tags'])) {
	    $meta_tags = sanitize_text_field($_POST['_meta_tags']);
        update_post_meta($post_id, '_meta_tags', $meta_tags);
    }

    if (isset($_POST['_meta_description'])) {
		$meta_description = sanitize_text_field($_POST['_meta_description']);
        update_post_meta($post_id, '_meta_description', $meta_description);
    }
	if (isset($_POST['meta-tags-result'])) {
        update_post_meta($post_id, '_meta_tags', sanitize_text_field($_POST['meta-tags-result']));
    }

    if (isset($_POST['meta-description-result'])) {
        update_post_meta($post_id, '_meta_description', sanitize_text_field($_POST['meta-description-result']));
    }
}


// Function to output meta tags and description in the head section
function wacg_output_meta_tags() {
    if (is_product()) {
        global $post;
        $meta_tags = get_post_meta($post->ID, '_meta_tags', true);
        $meta_description = get_post_meta($post->ID, '_meta_description', true);

        if (!empty($meta_tags)) {
            echo '<meta name="keywords" content="' . esc_attr($meta_tags) . '">' . "\n";
        }

        if (!empty($meta_description)) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }
    }
}

// Hook the function to wp_head with a high priority
add_action('wp_head', 'wacg_output_meta_tags', 1);


// Handle AJAX request to generate content
add_action('wp_ajax_generate_product_content', 'generate_product_content_ajax');

function generate_product_content_ajax() {

    if (!isset($_POST['post_id']) || !isset($_POST['title'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field($_POST['title']);

    $generated_content = wacg_generate_content($title);

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
add_action('wp_ajax_generate_short_content', 'generate_short_content_ajax');

function generate_short_content_ajax() {
    if (!isset($_POST['post_id']) || !isset($_POST['title'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field($_POST['title']);

    $generated_content = wacg_generate_short_content($title);

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
add_action('wp_ajax_wacg_generate_product_tags', 'wacg_generate_product_tags_ajax');

function wacg_generate_product_tags_ajax() {
    if (!isset($_POST['post_id']) || !isset($_POST['title'])) {
        wp_send_json_error('Invalid data.');
    }

    $post_id = intval($_POST['post_id']);
    $title = sanitize_text_field($_POST['title']);

    $generated_tags = wacg_generate_product_tags($title);

    if ($generated_tags) {
        wp_send_json_success($generated_tags);
    } else {
        wp_send_json_error('Tag generation failed.');
    }
}
