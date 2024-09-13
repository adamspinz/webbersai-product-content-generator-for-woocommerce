jQuery(document).ready(function($) {
	const nonces = webbersaicgNonceData;
								
    $("#generate-content-button").click(function() {
        var title = $("#title").val().trim();
        if (title === '') {
            alert("Please enter a product title.");
            return;
        }
        var description = getEditorContent("content").trim();
        var shortDescriptionExists = (getEditorContent("excerpt").trim() !== '');
        var metaTagsExists = ($("#meta-tags-result").text().trim() !== '');
        var metaDescriptionExists = ($("#meta-description-result").text().trim() !== '');
        var descriptionExists = (description !== '');
        var descriptionIsShort = (description.length < 100);
        if (descriptionExists || shortDescriptionExists || metaTagsExists || metaDescriptionExists) {
            var message = "Do you want to overwrite existing content?";
            if (descriptionExists && descriptionIsShort) {
                message += "\nAllow AI to regenerate the description based on the existing content?";
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

    function generateAllContent(overwrite, regenerateDescription) {
        $("#loading-icon").show();
        var postId = $("#post_ID").val();
        var title = $("#title").val();
        $("#msg").text("");
        var data = {
            action: "webbersaicg_generate_product_content",
            post_id: postId,
            title: title,
            overwrite: overwrite,
			security: nonces.webbersaicg_generate_product_content_nonce  // Add nonce here
        };
        if (regenerateDescription) {
            data.previous_content = getEditorContent("content").trim();
        }
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                updateTinyMCEContent("content", response.data);
                $("#msg").append("<p id='description-msg'>Product Description generated successfully.</p>");
            } else {
                $("#msg").append("<p class='error-message'>Failed to generate product description.</p>");
            }
        });
        $.post(ajaxurl, {
            action: "webbersaicg_generate_short_content",
            post_id: postId,
            title: title,
			security: nonces.webbersaicg_generate_short_content_nonce
        }, function(response) {
            if (response.success) {
                updateTinyMCEContent("excerpt", response.data);
                $("#msg").append("<p id='short-description-msg'>Product Short Description generated successfully.</p>");
            } else {
                $("#msg").append("<p class='error-message'>Failed to generate short description.</p>");
            }
        });
        $.post(ajaxurl, {
            action: "webbersaicg_generate_product_tags",
            post_id: postId,
            title: title,
			security: nonces.webbersaicg_generate_product_tags_nonce
        }, function(response) {
            if (response.success) {
                var tags = response.data;
                $("#new-tag-product_tag").val(tags);
                $("#msg").append("<p id='tags-msg'>Product Tags generated successfully.</p>");
            } else {
                $("#msg").append("<p class='error-message'>Failed to generate product tags.</p>");
            }
        });
        var content = getEditorContent("content");
        $.post(ajaxurl, {
            action: "webbersaicg_generate_meta_tags",
            post_id: postId,
            post_title: title,
            post_content: content,
            override_existing: overwrite,
			security: nonces.webbersaicg_generate_meta_tags_nonce
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
            }
            arrangeMessages();
        });
    }

    function arrangeMessages() {
        var msgElement = $("#msg");
        var shortDescriptionMsg = $("#short-description-msg").detach();
        var descriptionMsg = $("#description-msg").detach();
        var metaMsg = $("#meta-msg").detach();
        var tagsMsg = $("#tags-msg").detach();
        var imgMsg = $("#img-msg").detach();
        msgElement.append(shortDescriptionMsg);
        msgElement.append(descriptionMsg);
        msgElement.append(metaMsg);
        msgElement.append(tagsMsg);
        msgElement.append(imgMsg);
    }

    function getEditorContent(editorId) {
        if (tinymce.get(editorId) && !tinymce.get(editorId).hidden) {
            return tinymce.get(editorId).getContent();
        } else {
            return $("#" + editorId).val();
        }
    }

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
            action: "webbersaicg_save_meta_tags_description",
            post_id: postId,
            meta_tags: metaTags,
            meta_description: metaDescription,
			security: nonces.webbersaicg_save_meta_tags_description_nonce
        }, function(response) {
            $("#loading-icon").hide();
            if (response.success) {
                $("#savemsg").text("Meta Keywords and Description saved successfully.");
            } else {
                $("#savemsg").text("Failed to save meta tags and description.").addClass("error-message");
            }
        });
    });

    $("#generate-image-button").click(function() {
        var postId = $("#post_ID").val();
        var title = $("#title").val().trim();
        if (title === '') {
            alert("Please enter a product title.");
            return;
        }
        $("#loading-icon").show();
        $("#msg").text("");
        $.post(ajaxurl, {
            action: "webbersaicg_generate_product_image",
            post_id: postId,
            title: title,
			security: nonces.webbersaicg_generate_product_image_nonce
        }, function(response) {
            $("#loading-icon").hide();
            if (response.success) {
                var imageUrl = response.data.image_url;
				
                $("#msg").append("<p id='img-msg'>Image generated and set successfully.</p>");
                if ($('#set-post-thumbnail').length) {
                    $('#set-post-thumbnail').html('<img src="' + imageUrl + '" alt="Product Image" />');
                }
                $("#_thumbnail_id").val(response.data.attachment_id);
            } else {
                $("#msg").append("<p class='error-message'>Image generation failed. Select Models (DALL-E-3 or DALL-E-2).</p>");
            }
        }).fail(function() {
            $("#loading-icon").hide();
            alert('Check Your API Key.');
        });
    });
});
