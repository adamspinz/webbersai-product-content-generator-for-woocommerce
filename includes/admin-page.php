<?php
/*
Plugin Name: Woocommerce AI Product Content Generator
Plugin URI: https://webberssai.com
Description: This plugin generates content and meta description for WooCommerce products using AI.
Tags: Woocommerce, Product Content Generator, AI, Product Tags, Meta Tags, SEO
Version: 1.0
Author: adam@spinzsoft.com
Author URI: https://webberssai.com
License: GPLv2 or later
*/



// Add the admin settings page

add_action('admin_menu', 'wacg_add_admin_menu');

function wacg_add_admin_menu() {

    add_menu_page(

        __('Woocommerce AI Product Content Generator Settings', 'textdomain'),

        __('Woocommerce AI Product Content Generator', 'textdomain'),

        'manage_options',

        'wacg-settings',

        'wacg_settings_page'

    );

}



// Register settings

add_action('admin_init', 'wacg_settings_init');

function wacg_settings_init() {

    register_setting('wacg_options_group', 'wacg_options');



    add_settings_section(

        'wacg_section',

        __('Settings', 'textdomain'),

        null,

        'wacg-settings'

    );



    add_settings_field(

        'wacg_endpoint_url',

        __('Type of AI', 'textdomain'),

        'wacg_endpoint_url_render',

        'wacg-settings',

        'wacg_section'

    );

	    

	add_settings_field(

        'wacg_api_key',

        __('API Key', 'textdomain'),

        'wacg_api_key_render',

        'wacg-settings',

        'wacg_section'

    );



	add_settings_field(

        'wacg_ai_model',

        __('AI Model', 'textdomain'),

        'wacg_ai_model_render',

        'wacg-settings',

        'wacg_section'

    );





    add_settings_field(

        'wacg_language',

        __('Language', 'textdomain'),

        'wacg_language_render',

        'wacg-settings',

        'wacg_section'

    );



}



// Sanitize callback function

function wacg_options_sanitize($options) {

    if (empty($options['wacg_api_key'])) {

        add_settings_error(

            'wacg_options',

            'wacg_api_key_error',

            __('API Key is required', 'textdomain'),

            'error'

        );

        // Remove the invalid API key from the options

        $options['wacg_api_key'] = '';

    }

    return $options;

}

// Render AI model field

function wacg_ai_model_render() {

    $options = get_option('wacg_options');

    ?>

    <select name="wacg_options[wacg_ai_model]">

        <option value="openrouter/auto" <?php selected($options['wacg_ai_model'], 'Auto (best for prompt)'); ?>>Auto (best for prompt)</option>

        <option value="nousresearch/nous-capybara-7b:free" <?php selected($options['wacg_ai_model'], 'nousresearch/nous-capybara-7b:free'); ?>>Nous: Capybara 7B (free)</option>

        <option value="openchat/openchat-7b:free" <?php selected($options['wacg_ai_model'], 'openchat/openchat-7b:free'); ?>>OpenChat 3.5 (free)</option>

        <option value="gryphe/mythomist-7b:free" <?php selected($options['wacg_ai_model'], 'gryphe/mythomist-7b:free'); ?>>MythoMist 7B (free)</option>

        <option value="undi95/toppy-m-7b:free" <?php selected($options['wacg_ai_model'], 'undi95/toppy-m-7b:free'); ?>>Toppy M 7B (free)</option>

        <option value="openrouter/cinematika-7b:free" <?php selected($options['wacg_ai_model'], 'openrouter/cinematika-7b:free'); ?>>Cinematika 7B (alpha) (free)</option>

        <option value="google/gemma-7b-it:free" <?php selected($options['wacg_ai_model'], 'google/gemma-7b-it:free'); ?>>Google: Gemma 7B (free)</option>

        <option value="meta-llama/llama-3-8b-instruct:free" <?php selected($options['wacg_ai_model'], 'meta-llama/llama-3-8b-instruct:free'); ?>>Meta: Llama 3 8B Instruct (free)</option>

        <option value="microsoft/phi-3-medium-128k-instruct:free" <?php selected($options['wacg_ai_model'], 'microsoft/phi-3-medium-128k-instruct:free'); ?>>Phi-3 Medium Instruct (free)</option>

        <option value="mistralai/mistral-7b-instruct:free" <?php selected($options['wacg_ai_model'], 'mistralai/mistral-7b-instruct:free'); ?>>Mistral AI Model</option>

      

    </select>

    <?php

}



// Render endpoint URL field

function wacg_endpoint_url_render() {

    $options = get_option('wacg_options');

    $endpoint_urls = [

        'https://openrouter.ai/api/v1/chat/completions' => 'OpenRouter AI',

        //'https://api.openai.com/v1/chat/completions' => 'OpenAI'

    ];

    ?>

    <select name="wacg_options[wacg_endpoint_url]">

        <?php foreach ($endpoint_urls as $url => $label) : ?>

            <option value="<?php echo esc_attr($url); ?>" <?php selected($options['wacg_endpoint_url'], $url); ?>>

                <?php echo esc_html($label); ?>

            </option>

        <?php endforeach; ?>

    </select>

    <?php

}



// Render language field

function wacg_language_render() {

    $options = get_option('wacg_options');

    $languages = [

        'en' => 'English',

        'es' => 'Spanish',

        'fr' => 'French',

        'de' => 'German',

		'ab' => 'Arabic',

        'kr' => 'Korean',

        'po' => 'Portuguese',

        'ro' => 'Romanian',

        'ru' => 'Russian',

        'hi' => 'Hindi',

        'ta' => 'Tamil'

        



		

    ];

    ?>

    <select name="wacg_options[wacg_language]">

        <?php foreach ($languages as $code => $language) : ?>

            <option value="<?php echo esc_attr($code); ?>" <?php selected($options['wacg_language'], $code); ?>>

                <?php echo esc_html($language); ?>

            </option>

        <?php endforeach; ?>

    </select>

    <?php

}



// Render API key field

function wacg_api_key_render() {

    $options = get_option('wacg_options');

    ?>

    <input type="text" name="wacg_options[wacg_api_key]" value="<?php echo esc_attr($options['wacg_api_key']); ?>" required />

	   <p>

        <?php _e('Create your API key here:', 'textdomain'); ?> 

        <a href="https://openrouter.ai/">OpenRouter.ai</a>, 

       <!-- <a href="https://platform.openai.com/signup">OpenAI</a>-->

    </p>

    <?php

}



// Display the settings page

function wacg_settings_page() {

    ?>

    <form action="options.php" method="post">

        <h1><?php _e('Woocommerce AI Product Content Generator Settings', 'textdomain'); ?></h1>

        <?php

        settings_fields('wacg_options_group');

        do_settings_sections('wacg-settings');

        submit_button();

        ?>

    </form>

	<?php settings_errors(); // Display settings errors or success messages ?>

    <?php

}

?>

