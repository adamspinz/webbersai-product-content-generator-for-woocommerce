<?php
/*
Plugin Name: Woocommerce AI Product Content Generator
Plugin URI: https://webbersai.com
Description: This plugin generates content and meta description for WooCommerce products using AI.
Tags: Woocommerce, Product Content Generator, AI, Product Tags, Meta Tags, SEO
Version: 1.0
Author: adam@spinzsoft.com
Author URI: https://webbersai.com
License: GPLv2 or later
*/
if (!defined('ABSPATH')) {

    exit;

}

function wacg_generate_content($title) {

	$options = get_option('wacg_options');
    $api_url = $options['wacg_endpoint_url'];
    $api_key = $options['wacg_api_key'];
    $ai_model = $options['wacg_ai_model'];
    $ai_language = $options['wacg_language'];

    $data = [
	    'model' => $ai_model,
        'title' => $title,
		 'prompt' => 'Write product based description in "' .$ai_language. '" for "'.$title.'" ',
        'max_tokens' => 300, // Adjust as needed
    ];
    $payload = json_encode($data);
	
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key

          ];

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return null;
    }

    $result = json_decode($response, true);
    return $result['choices'][0]['text'] ?? null;
}



add_action('wp_ajax_wacg_generate_content', 'wacg_generate_content_ajax');

//to generate short content

function wacg_generate_short_content($title) {

	$options = get_option('wacg_options');
    $api_url = $options['wacg_endpoint_url'];
    $api_key = $options['wacg_api_key'];
    $ai_model = $options['wacg_ai_model'];
    $ai_language = $options['wacg_language'];
	
    $data = [
	    'model' => $ai_model,
        'title' => $title,
		'prompt' => 'Write a short description based on product "'.$title.'" in "' .$ai_language. '" ',
        'max_tokens' => 150, // Adjust as needed
    ];
    $payload = json_encode($data);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key

          ];

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return null;
    }

    $result = json_decode($response, true);
    return $result['choices'][0]['text'] ?? null;
}



add_action('wp_ajax_wacg_generate_short_content', 'wacg_generate_short_content_ajax');

//generate product tags
function wacg_generate_product_tags($title) {

	$options = get_option('wacg_options');
    $api_url = $options['wacg_endpoint_url'];
    $api_key = $options['wacg_api_key'];
    $ai_model = $options['wacg_ai_model'];
    $ai_language = $options['wacg_language'];
	
    $data = [
	    'model' =>  $ai_model,
        'prompt' => 'Write a suitable tags for the product "'.$title.'",with comma separated values in "' .$ai_language. '" ', 
        'max_tokens' => 45, // Adjust as needed
    ];
    $payload = json_encode($data);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key

          ];

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key,
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return null;
    }

    $result = json_decode($response, true);
    return $result['choices'][0]['text'];
	

}

add_action('wp_ajax_wacg_generate_product_tags', 'wacg_generate_product_tags_ajax');



//add meta tags

function wacg_generate_meta_tags($title, $product_description) {

    $options = get_option('wacg_options');
    $api_url = $options['wacg_endpoint_url'];
    $api_key = $options['wacg_api_key'];
    $ai_model = $options['wacg_ai_model'];
    $ai_language = $options['wacg_language'];
	
	
    $data = [
        'model' => $ai_model,
        'prompt' => 'Write a keywords for the product "'.$title.'",with comma separated values in "' .$ai_language. '" ', 
		//'prompt' => 'Generate comma seperated keywords for "'.$title.'" in "' .$ai_language. '" ' ,
        'max_tokens' => 50,
    ];
    $payload = json_encode($data);

    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['choices'][0]['text'] ?? null;
}

function wacg_generate_meta_description($title, $product_description) {

    $options = get_option('wacg_options');
    $api_url = $options['wacg_endpoint_url'];
    $api_key = $options['wacg_api_key'];
    $ai_model = $options['wacg_ai_model'];
    $ai_language = $options['wacg_language'];
	
    $data = [
        'model' => $ai_model,
         'prompt' => 'Generate meta description for the product "'.$title.'" in "'.$ai_language.'" ',
	    // 'prompt' => 'Generate meta description for the product "'.$title.'" in "'.$ai_language.'" ',
         'max_tokens' => 50,
    ];
    $payload = json_encode($data);

    $ch = curl_init($api_url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['choices'][0]['text'] ?? null;
}





