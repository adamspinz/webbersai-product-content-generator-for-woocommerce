<?php
add_action('admin_menu', 'webbersaicg_add_admin_menu');

function webbersaicg_add_admin_menu() {
    add_menu_page(
        esc_html__('Webbersai Product Content Generator Settings For Woocommerce', 'webbersai-product-content-generator'),
        esc_html__('Webbersai Product Content Generator For Woocommerce', 'webbersai-product-content-generator'),
        'manage_options',
        'webbersaicg-settings',
        'webbersaicg_settings_page'
    );
}

// Register settings
add_action('admin_init', 'webbersaicg_settings_init');

function webbersaicg_settings_init() {
    register_setting('webbersaicg_options_group', 'webbersaicg_options', 'webbersaicg_options_sanitize');

    add_settings_section(
        'webbersaicg_section',
        esc_html__('Settings', 'webbersai-product-content-generator'),
        null,
        'webbersaicg-settings'
    );

    add_settings_field(
        'webbersaicg_endpoint_url',
        esc_html__('Type of AI', 'webbersai-product-content-generator'),
        'webbersaicg_endpoint_url_render',
        'webbersaicg-settings',
        'webbersaicg_section'
    );

    add_settings_field(
        'webbersaicg_api_key',
        esc_html__('API Key', 'webbersai-product-content-generator'),
        'webbersaicg_api_key_render',
        'webbersaicg-settings',
        'webbersaicg_section'
    );

    add_settings_field(
        'webbersaicg_organization_id',
        '',
        'webbersaicg_organization_id_render',
        'webbersaicg-settings',
        'webbersaicg_section'
    );

    add_settings_field(
        'webbersaicg_ai_model',
        esc_html__('AI Model', 'webbersai-product-content-generator'),
        'webbersaicg_ai_model_render',
        'webbersaicg-settings',
        'webbersaicg_section'
    );

    add_settings_field(
        'webbersaicg_language',
        esc_html__('Language', 'webbersai-product-content-generator'),
        'webbersaicg_language_render',
        'webbersaicg-settings',
        'webbersaicg_section'
    );
}

// Sanitize callback function
function webbersaicg_options_sanitize($options) {
    if (empty($options['webbersaicg_api_key'])) {
        add_settings_error(
            'webbersaicg_options',
            'webbersaicg_api_key_error',
            esc_html__('API Key is required', 'webbersai-product-content-generator'),
            'error'
        );
        $options['webbersaicg_api_key'] = '';
    }
    return $options;
}

// Render AI model field
function webbersaicg_ai_model_render() {
    $options = get_option('webbersaicg_options', []); // Default to empty array if option is false
    $endpoint = isset($options['webbersaicg_endpoint_url']) ? $options['webbersaicg_endpoint_url'] : '';
    $openai_models = [
        'gpt-4o' => 'gpt-4o',
        'gpt-4-turbo' => 'gpt-4-turbo',
        'gpt-3.5-turbo' => 'gpt-3.5-turbo',
        'dall-e-3' => 'Image - DALL E 3',
        'dall-e-2' => 'Image - DALL E 2'
    ];

    $openrouter_models = [
        'openrouter/auto' => 'Auto (best for prompt)',
        'nousresearch/nous-capybara-7b:free' => 'Nous: Capybara 7B (free)',
        'openchat/openchat-7b:free' => 'OpenChat 3.5 (free)',
        'gryphe/mythomist-7b:free' => 'MythoMist 7B (free)',
        'undi95/toppy-m-7b:free' => 'Toppy M 7B (free)',
        'openrouter/cinematika-7b:free' => 'Cinematika 7B (alpha) (free)',
        'google/gemma-7b-it:free' => 'Google: Gemma 7B (free)',
        'meta-llama/llama-3-8b-instruct:free' => 'Meta: Llama 3 8B Instruct (free)',
        'microsoft/phi-3-medium-128k-instruct:free' => 'Phi-3 Medium Instruct (free)',
        'mistralai/mistral-7b-instruct:free' => 'Mistral AI Model'
    ];
    ?>
    <select name="webbersaicg_options[webbersaicg_ai_model]" id="webbersaicg_ai_model">
        <?php
		if ($endpoint === 'https://api.openai.com/v1/chat/completions') {
            foreach ($openai_models as $value => $label) {
                echo '<option value="' . esc_attr($value) . '" ' . (isset($options['webbersaicg_ai_model']) ? selected($options['webbersaicg_ai_model'], $value, false) : '') . '>' . esc_html($label) . '</option>';
            }
        } else {
            foreach ($openrouter_models as $value => $label) {
                echo '<option value="' . esc_attr($value) . '" ' . (isset($options['webbersaicg_ai_model']) ? selected($options['webbersaicg_ai_model'], $value, false) : '') . '>' . esc_html($label) . '</option>';
            }
        }
        ?>
    </select>
    <?php
}

// Render endpoint URL field
function webbersaicg_endpoint_url_render() {
    $options = get_option('webbersaicg_options', []); // Default to empty array if the option does not exist
    $endpoint_urls = [
        'https://api.openai.com/v1/chat/completions' => 'OpenAI',
        'https://openrouter.ai/api/v1/chat/completions' => 'OpenRouter AI'
    ];
    
    // Check if the key exists, and set a default value if not
    $selected_endpoint = isset($options['webbersaicg_endpoint_url']) ? $options['webbersaicg_endpoint_url'] : '';
    ?>
    <select name="webbersaicg_options[webbersaicg_endpoint_url]" id="webbersaicg_endpoint_url">
        <?php foreach ($endpoint_urls as $url => $label) : ?>
            <option value="<?php echo esc_attr($url); ?>" <?php selected($selected_endpoint, $url); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

// Render API key field
function webbersaicg_api_key_render() {
    $options = get_option('webbersaicg_options', []); // Default to empty array
    $api_key = isset($options['webbersaicg_api_key']) ? $options['webbersaicg_api_key'] : '';
    ?>
    <input type="text" name="webbersaicg_options[webbersaicg_api_key]" value="<?php echo esc_attr($api_key); ?>" required />
    <p>
        <?php esc_html_e('Create your API key here:', 'webbersai-product-content-generator'); ?> 
        <a href="https://openrouter.ai/">OpenRouter.ai</a>, 
        <a href="https://platform.openai.com/signup">OpenAI</a>
    </p>
    <?php
}

// Render Organization ID field
function webbersaicg_organization_id_render() {
    $options = get_option('webbersaicg_options', []); // Default to empty array
    $organization_id = isset($options['webbersaicg_organization_id']) ? $options['webbersaicg_organization_id'] : '';
    ?>
    <div id="organization_id_field" style="display: none;">
        <div style="font-weight:500"><label for="webbersaicg_organization_id"><?php esc_html_e('Organization ID', 'webbersai-product-content-generator'); ?></label></div>
        <input type="text" name="webbersaicg_options[webbersaicg_organization_id]" value="<?php echo esc_attr($organization_id); ?>" />
        <p>
            <?php esc_html_e('Create your Organization ID here:', 'webbersai-product-content-generator'); ?> 
            <a href="https://platform.openai.com/signup">OpenAI</a>
        </p>
    </div>
    <?php
}

// Render language field
function webbersaicg_language_render() {
    $options = get_option('webbersaicg_options', []); // Default to empty array
    $language = isset($options['webbersaicg_language']) ? $options['webbersaicg_language'] : 'en'; // Default to 'en'
    ?>
    <select name="webbersaicg_options[webbersaicg_language]" id="webbersaicg_language">
        <option value="en" <?php selected($language, 'en'); ?>>English</option>
        <option value="es" <?php selected($language, 'es'); ?>>Spanish</option>
        <option value="fr" <?php selected($language, 'fr'); ?>>French</option>
        <option value="de" <?php selected($language, 'de'); ?>>German</option>
        <!-- Add more languages if needed -->
    </select>
    <?php
}

// Admin settings page content
function webbersaicg_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Webbersai Product Content Generator Settings', 'webbersai-product-content-generator'); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('webbersaicg_options_group');
            do_settings_sections('webbersaicg-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}