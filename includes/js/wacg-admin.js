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
jQuery(document).ready(function($) {
    $("#generate-content-button").click(function() {
        var title = $("#title").val().trim();
        if (title === '') {
            alert("Please enter a product title.");
            return;
        }

        // Check if product description, short description, and meta tags are already present
        var description = $("#postdivrich .wp-editor-area").val().trim();
        var shortDescriptionExists = ($("#postexcerpt .wp-editor-area").val().trim() !== '');
        var metaTagsExists = ($("#meta-tags-result").text().trim() !== '');
        var metaDescriptionExists = ($("#meta-description-result").text().trim() !== '');

        var descriptionExists = (description !== '');
        var descriptionIsShort = (description.length < 100);

        // Show overwrite confirmation dialog if any content exists
        if (descriptionExists || shortDescriptionExists || metaTagsExists || metaDescriptionExists) {
           // var message = "Existing content found. Do you want to Overwrite?";
			 var message = "Do you want to Overwrite Existing content?";
            if (descriptionExists && descriptionIsShort) {
               // message += "\nThe product description is less than 100 characters. Allow AI to regenerate the description based on the existing content?";
			   message += "\n Allow AI to regenerate the description based on the existing content?";
            }

            $("#overwrite-confirmation").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Yes": function() {
                        $(this).dialog("close");
                        generateAllContent(true, descriptionExists && descriptionIsShort);
                    },
                    "No": function() {
                        $(this).dialog("close");
                    }
                },
                open: function() {
                    $(".ui-dialog-title").text("Confirmation");
                    $(".ui-dialog-content").text(message);
                }
            });
        } else {
            generateAllContent(false, false);
        }
    });
//main 
    function generateAllContent(overwrite, regenerateDescription) {
        // Show loading icon
        $("#loading-icon").show();

        var postId = $("#post_ID").val();
        var title = $("#title").val();

        // Clear previous messages
        $("#msg").text("");

        // Generate Product Content
        var data = {
            action: "generate_product_content",
            post_id: postId,
            title: title,
            overwrite: overwrite
        };
        if (regenerateDescription) {
            data.previous_content = $("#postdivrich .wp-editor-area").val().trim();
        }

        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                updateTinyMCEContent("content", response.data);
                $("#msg").append("<p id='description-msg'>Product Description generated successfully.</p>");
            } else {
                $("#msg").append("<p class='error-message'>Failed to generate product description.</p>");
            }
        });

        // Generate Short Description
        $.post(ajaxurl, {
            action: "generate_short_content",
            post_id: postId,
            title: title
        }, function(response) {
            if (response.success) {
                $("#postexcerpt .wp-editor-area").val(response.data);
                $("#msg").append("<p id='short-description-msg'>Product Short Description generated successfully.</p>");
            } else {
                $("#msg").append("<p class='error-message'>Failed to generate short description.</p>");
            }
        });
		
		 // Generate Tags
        $.post(ajaxurl, {
            action: "wacg_generate_product_tags",
            post_id: postId,
            title: title
        }, function(response) {
            if (response.success) {
                var tags = response.data;
                $("#new-tag-product_tag").val(tags);
                $("#msg").append("<p id='tags-msg'>Product Tags generated successfully.</p>");
            } else {
                $("#msg").append("<p class='error-message'>Failed to generate product tags.</p>");
            }
        });
		
        // Generate Meta Tags and Description
        var content = $("#content").val();
        $.post(ajaxurl, {
            action: "wacg_generate_meta_tags",
            post_id: postId,
            post_title: title,
            post_content: content,
            override_existing: overwrite
        }, function(response) {
            $("#loading-icon").hide();
            if (response.success) {
                var metaTags = response.data.meta_tags;
                var metaDescription = response.data.meta_description;
                $("#meta-tags-result").text(metaTags);
                $("#meta-description-result").text(metaDescription);
                $("#hidden-meta-tags").val(metaTags);
                $("#hidden-meta-description").val(metaDescription);
                $("#msg").append("<p id='meta-msg'>Product Meta Keywords and Description generated successfully.</p>");
            } else {
                $("#msg").append("<p class='error-message'>Failed to generate meta tags and description.</p>");
                //$("#meta-tags-result").text("Failed to generate meta tags.");
                //$("#meta-description-result").text("Failed to generate meta description.");
            }

            // Arrange messages in the desired order
            arrangeMessages();
        });
    }

    function arrangeMessages() {
        var msgElement = $("#msg");
        var shortDescriptionMsg = $("#short-description-msg").detach();
        var descriptionMsg = $("#description-msg").detach();
        var metaMsg = $("#meta-msg").detach();
        var tagsMsg = $("#tags-msg").detach();

        msgElement.append(shortDescriptionMsg);
        msgElement.append(descriptionMsg);
        msgElement.append(metaMsg);
        msgElement.append(tagsMsg);
    }

    // Ensure TinyMCE content updates in both visual and text modes
    function updateTinyMCEContent(editorId, content) {
        if (tinymce.get(editorId) && !tinymce.get(editorId).hidden) {
            tinymce.get(editorId).setContent(content);
        } else {
            $("#" + editorId).val(content);
        }
    }

    $("#save-meta-tags-button").click(function() {
        $("#loading-icon").show();

        var postId = $("#post_ID").val();
        var metaTags = $("#meta-tags-result").text();
        var metaDescription = $("#meta-description-result").text();

        $.post(ajaxurl, {
            action: "save_meta_tags_description",
            post_id: postId,
            meta_tags: metaTags,
            meta_description: metaDescription
        }, function(response) {
            $("#loading-icon").hide();
            if (response.success) {
                $("#savemsg").text("Meta Keywords and Description saved successfully.");
            } else {
                $("#savemsg").text("Failed to save meta tags and description.").addClass("error-message");
            }
        });
    });
});
