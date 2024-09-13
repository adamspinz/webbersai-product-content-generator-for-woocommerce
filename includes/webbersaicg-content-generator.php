<?php
if (!defined('ABSPATH')) {
    exit;
}

function webbersaicg_generate_content($title) {
    $options = get_option('webbersaicg_options');
    $api_url = $options['webbersaicg_endpoint_url'];
    $api_key = $options['webbersaicg_api_key'];
    $org_id = $options['webbersaicg_organization_id'];
    $ai_model = $options['webbersaicg_ai_model'];
    $ai_language = $options['webbersaicg_language'];
	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){
	$data = [
			'model' => $ai_model,
			'title' => $title,
			 'prompt' => 'Write product based description in "' .$ai_language. '" for "'.$title.'" ',
			'max_tokens' => 300, // Adjust as needed
		];
	}else {
    $data = [

       		'model' => $ai_model,
	        'messages' => [
            ['role' => 'user', 'content' => 'Write a product description in "' .$ai_language. '" for "' .$title. '"']
        ],
        'max_tokens' => 300, // Adjust as needed
    ];
	}
    $payload = wp_json_encode($data);
   //replaced instead curl
   $response = wp_remote_post($api_url, [

        'headers' => [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key

        ],
        'body' => $payload,

    ]);


    if (is_wp_error($response)) {

        return null;
    }


    $result = json_decode(wp_remote_retrieve_body($response), true);
    return isset($result['choices'][0]['message']['content']) ? sanitize_textarea_field($result['choices'][0]['message']['content']) : null;


	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){

	return $result['choices'][0]['text'] ?? null;

	}else{

    return $result['choices'][0]['message']['content'] ?? null;

	}

}

add_action('wp_ajax_webbersaicg_generate_content', 'webbersaicg_generate_content_ajax');
// Generate short content

function webbersaicg_generate_short_content($title) {

    $options = get_option('webbersaicg_options');
    $api_url = $options['webbersaicg_endpoint_url'];
    $api_key = $options['webbersaicg_api_key'];
    $org_id = $options['webbersaicg_organization_id'];
    $ai_model = $options['webbersaicg_ai_model'];
    $ai_language = $options['webbersaicg_language'];
	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){

	$data = [
	    'model' => $ai_model,
        'title' => $title,
		'prompt' => 'Write a short description based on product "'.$title.'" in "' .$ai_language. '" ',
        'max_tokens' => 150, // Adjust as needed
    ];
	}else {
    $data = [
        'model' => $ai_model,
        'messages' => [

            ['role' => 'user', 'content' => 'Write a short description based on product "' .$title. '" in "' .$ai_language. '"']
        ],

        'max_tokens' => 200, // Adjust as needed

    ];


	}

    $payload = wp_json_encode($data);

	//replaced instead curl
  $response = wp_remote_post($api_url, [
        'headers' => [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key
        ],

        'body' => $payload,
    ]);

    if (is_wp_error($response)) {

        return null;

    }

    $result = json_decode(wp_remote_retrieve_body($response), true);

    return isset($result['choices'][0]['message']['content']) ? sanitize_textarea_field($result['choices'][0]['message']['content']) : null;


    if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){
	return $result['choices'][0]['text'] ?? null;

	}else{
    return $result['choices'][0]['message']['content'] ?? null;

	}
}


add_action('wp_ajax_webbersaicg_generate_short_content', 'webbersaicg_generate_short_content_ajax');


// Generate product tags


function webbersaicg_generate_product_tags($title) {
    $options = get_option('webbersaicg_options');
    $api_url = $options['webbersaicg_endpoint_url'];
    $api_key = $options['webbersaicg_api_key'];
    $org_id = $options['webbersaicg_organization_id'];
   $ai_model = $options['webbersaicg_ai_model'];
   $ai_language = $options['webbersaicg_language'];

	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){

	 $data = [

	    'model' =>  $ai_model,
        'prompt' => 'Write a suitable tags for the product "'.$title.'",with comma separated values in "' .$ai_language. '" ', 
        'max_tokens' => 45, // Adjust as needed

    ];

	}else {
  $data = [
        'model' => $ai_model,

        'messages' => [

            ['role' => 'user', 'content' => 'Write suitable tags for the product "' .$title. '" with comma-separated values in "' .$ai_language. '"']

        ],

        'max_tokens' => 45, // Adjust as needed
    ];
	}


    $payload = wp_json_encode($data);

   //replaced instead curl

   $response = wp_remote_post($api_url, [
        'headers' => [
            'Content-Type' => 'application/json',

            'Authorization' => 'Bearer ' . $api_key

        ],
        'body' => $payload,

    ]);
    if (is_wp_error($response)) {


        return null;

    }

    $result = json_decode(wp_remote_retrieve_body($response), true);

    return isset($result['choices'][0]['message']['content']) ? sanitize_textarea_field($result['choices'][0]['message']['content']) : null;

	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){

	return $result['choices'][0]['text'] ?? null;


	}else{

    return $result['choices'][0]['message']['content'] ?? null;

	}

}

add_action('wp_ajax_webbersaicg_generate_product_tags', 'webbersaicg_generate_product_tags_ajax');

// Generate meta tags


function webbersaicg_generate_meta_tags($title, $product_description) {

    $options = get_option('webbersaicg_options');
    $api_url = $options['webbersaicg_endpoint_url'];
    $api_key = $options['webbersaicg_api_key'];
    $org_id = $options['webbersaicg_organization_id'];
    $ai_model = $options['webbersaicg_ai_model'];
    $ai_language = $options['webbersaicg_language'];
	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){
	$data = [
        'model' => $ai_model,
        'prompt' => 'Write a keywords for the product "'.$title.'",with comma separated values in "' .$ai_language. '" ', 
        'max_tokens' => 50,
    ];
	}else {

    $data = [

        'model' => $ai_model,

        'messages' => [
            ['role' => 'user', 'content' => 'Write keywords for the product "' .$title. '" with comma-separated values in "' .$ai_language. '"']
        ],
        'max_tokens' => 50,
    ];
	}
    $payload = wp_json_encode($data);

   //replaced instead curl


   $response = wp_remote_post($api_url, [
        'headers' => [

            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $api_key


        ],

        'body' => $payload,

    ]);

    if (is_wp_error($response)) {

        return null;


    }


    $result = json_decode(wp_remote_retrieve_body($response), true);


    return isset($result['choices'][0]['message']['content']) ? sanitize_textarea_field($result['choices'][0]['message']['content']) : null;


	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){

	return $result['choices'][0]['text'] ?? null;
	}else{



    return $result['choices'][0]['message']['content'] ?? null;

	}


}


// Generate meta description


function webbersaicg_generate_meta_description($title, $product_description) {

    $options = get_option('webbersaicg_options');


    $api_url = $options['webbersaicg_endpoint_url'];


    $api_key = $options['webbersaicg_api_key'];

    $org_id = $options['webbersaicg_organization_id'];


    $ai_model = $options['webbersaicg_ai_model'];


    $ai_language = $options['webbersaicg_language'];


	if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){



	$data = [


        'model' => $ai_model,


         'prompt' => 'Generate meta description for the product "'.$title.'" in "'.$ai_language.'" ',

         'max_tokens' => 50,



    ];

	}else {

    $data = [

        'model' => $ai_model,

        'messages' => [


            ['role' => 'user', 'content' => 'Generate a meta description for the product "' .$title. '" in "' .$ai_language. '"']

        ],

      'max_tokens' => 50,

    ];


	}

    $payload = wp_json_encode($data);


   //replaced instead curl

   $response = wp_remote_post($api_url, [

        'headers' => [


            'Content-Type' => 'application/json',

            'Authorization' => 'Bearer ' . $api_key


        ],


        'body' => $payload,

    ]);



    if (is_wp_error($response)) {

        return null;


    }




    $result = json_decode(wp_remote_retrieve_body($response), true);


    return isset($result['choices'][0]['message']['content']) ? sanitize_textarea_field($result['choices'][0]['message']['content']) : null;


    if($endpoint == "https://openrouter.ai/api/v1/chat/completions"){


	return $result['choices'][0]['text'] ?? null;




	}else{


    return $result['choices'][0]['message']['content'] ?? null;


	}



}


// 5.7 spinz

// Handle AJAX request to generate images


add_action('wp_ajax_webbersaicg_generate_product_image', 'webbersaicg_generate_product_image_ajax');



function webbersaicg_generate_product_image_ajax() {

 check_ajax_referer('webbersaicg_generate_product_image_nonce', 'security');

    if (!isset($_POST['post_id']) || !isset($_POST['title'])) {

        wp_send_json_error('Invalid data.');

    }


    $post_id = intval($_POST['post_id']);

    $title = sanitize_text_field(wp_unslash($_POST['title']));

    $result = webbersaicg_generate_image_from_openai($title, $post_id);


    if ($result) {


        wp_send_json_success(array('image_url' => $result));

         
    } else {

        wp_send_json_error('Image generation failed. Select Models (DALL-E-3 or DALL-E-2)');


  }


}





// Function to call OpenAI API and generate the image


function webbersaicg_generate_image_from_openai($title, $post_id) {



    $options = get_option('webbersaicg_options');


    $api_key = $options['webbersaicg_api_key']; // Replace with your OpenAI API key


    $endpoint = 'https://api.openai.com/v1/images/generations';



    $ai_model = $options['webbersaicg_ai_model']; // Replace with the model you want to use



    $data = array(

        'prompt' => $title,

        'model' => $ai_model,


        'n' => 1,

        'size' => '1024x1024'


    );


    $headers = array(


        'Content-Type' => 'application/json',


        'Authorization' => 'Bearer ' . $api_key


    );


    $response = wp_remote_post($endpoint, array(


        'headers' => $headers,


        'body' => wp_json_encode($data),


		'timeout' => 90


    ));


    if (is_wp_error($response)) {


        return false;


    }


    $response_body = wp_remote_retrieve_body($response);


	//echo"responsedata" .$response_body;

    $response_data = json_decode($response_body, true);

	//echo"responsedata" .$response_data;


    if (isset($response_data['data'][0]['url'])) {


        $image_url = $response_data['data'][0]['url'];

		//echo "test1" .$image_url;

        $attachment_url = webbersaicg_download_and_save_image($image_url, $title, $post_id);

       // echo "test2" .$attachment_url;

        return $attachment_url;


    } else {

        return false;


    }


}


// Function to download and save the image, and attach it to the WooCommerce product


function webbersaicg_download_and_save_image($image_url, $title, $post_id) {


    global $wp_filesystem;


    // Initialize the WP Filesystem API


    if (empty($wp_filesystem)) {


        require_once ABSPATH . 'wp-admin/includes/file.php';


        WP_Filesystem();


    }

    // Get the file name and path


    $upload_dir = wp_upload_dir();

	$filename = sanitize_file_name($title) . '.jpg';


    if (wp_mkdir_p($upload_dir['path'])) {

        $file = trailingslashit($upload_dir['path']) . $filename;
        } else {
       $file = trailingslashit($upload_dir['basedir']) . $filename;


    }

 // Get the image data


    $response = wp_remote_get($image_url, array('timeout' => 90));

    if (is_wp_error($response)) {


        return false;


    }


    $image_data = wp_remote_retrieve_body($response);



    // Save the image file



    $wp_filesystem->put_contents($file, $image_data);


    // Check the file type


    $wp_filetype = wp_check_filetype($filename, null);


    // Create an attachment


    $attachment = array(

        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_text_field($title),
        'post_content' => '',
        'post_status' => 'inherit'
    );



    // Insert the attachment into the WordPress media library

    $attach_id = wp_insert_attachment($attachment, $file, $post_id);



    // Include the image handling library

 require_once ABSPATH . 'wp-admin/includes/image.php';


    // Generate the attachment metadata


    $attach_data = wp_generate_attachment_metadata($attach_id, $file);



    // Update the attachment metadata


    wp_update_attachment_metadata($attach_id, $attach_data);


    // Set the product image

    set_post_thumbnail($post_id, $attach_id);

   // Force WooCommerce to update the thumbnail meta
   update_post_meta($post_id, '_thumbnail_id', $attach_id);

    return wp_get_attachment_url($attach_id);


}