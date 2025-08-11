<?php
/*
Plugin Name: Custom AI Image Description Generator (Claude)
Description: Automatically generates alt text for images using Anthropic's Claude API
Version: 2.0
Author: Your Name
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add settings page
function custom_ai_image_description_settings_page() {
    add_options_page('Custom AI Image Description Settings', 'Custom AI Image Description', 'manage_options', 'custom-ai-image-description-settings', 'custom_ai_image_description_settings_page_html');
}
add_action('admin_menu', 'custom_ai_image_description_settings_page');

// Settings page HTML
function custom_ai_image_description_settings_page_html() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('custom_ai_image_description_options');
            do_settings_sections('custom_ai_image_description_options');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function custom_ai_image_description_register_settings() {
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_claude_api_key');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_model');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_prompt');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_language');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_max_tokens');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_debug_mode');

    add_settings_section('custom_ai_image_description_settings', 'API Settings', 'custom_ai_image_description_settings_section_callback', 'custom_ai_image_description_options');
    
    add_settings_field('custom_ai_image_description_claude_api_key', 'Claude API Key', 'custom_ai_image_description_claude_api_key_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_model', 'Claude Model', 'custom_ai_image_description_model_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_prompt', 'Custom Prompt', 'custom_ai_image_description_prompt_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_language', 'Language', 'custom_ai_image_description_language_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_max_tokens', 'Max Tokens', 'custom_ai_image_description_max_tokens_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_debug_mode', 'Debug Mode', 'custom_ai_image_description_debug_mode_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
}
add_action('admin_init', 'custom_ai_image_description_register_settings');

// Settings section callback
function custom_ai_image_description_settings_section_callback() {
    echo '<p>Enter your Claude API settings below:</p>';
}

// Settings field callbacks
function custom_ai_image_description_claude_api_key_callback() {
    $api_key = get_option('custom_ai_image_description_claude_api_key');
    echo '<input type="password" name="custom_ai_image_description_claude_api_key" value="' . esc_attr($api_key) . '" size="50">';
    echo '<p class="description">Get your API key from <a href="https://console.anthropic.com/" target="_blank">console.anthropic.com</a></p>';
}

function custom_ai_image_description_model_callback() {
    $model = get_option('custom_ai_image_description_model', 'claude-3-5-sonnet-20241022');
    $models = array(
        'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Recommended)',
        'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku (Fast & Economical)',
        'claude-3-7-sonnet-20250219' => 'Claude 3.7 Sonnet (Latest)',
        'claude-opus-4-20250514' => 'Claude Opus 4',
        'claude-opus-4-1-20250805' => 'Claude Opus 4.1 (Most Powerful)',
        'claude-sonnet-4-20250514' => 'Claude Sonnet 4'
    );
    echo '<select name="custom_ai_image_description_model">';
    foreach ($models as $model_id => $model_name) {
        echo '<option value="' . esc_attr($model_id) . '" ' . selected($model, $model_id, false) . '>' . esc_html($model_name) . '</option>';
    }
    echo '</select>';
}

function custom_ai_image_description_prompt_callback() {
    $prompt = get_option('custom_ai_image_description_prompt', 'Generate a brief alt text description for this image:');
    echo '<textarea name="custom_ai_image_description_prompt" rows="3" cols="50">' . esc_textarea($prompt) . '</textarea>';
}

function custom_ai_image_description_language_callback() {
    $language = get_option('custom_ai_image_description_language', 'en');
    echo '<input type="text" name="custom_ai_image_description_language" value="' . esc_attr($language) . '" size="10"> (e.g., en, es, fr)';
}

function custom_ai_image_description_max_tokens_callback() {
    $max_tokens = get_option('custom_ai_image_description_max_tokens', 200);
    echo '<input type="number" name="custom_ai_image_description_max_tokens" value="' . esc_attr($max_tokens) . '" min="50" max="500">';
}

function custom_ai_image_description_debug_mode_callback() {
    $debug_mode = get_option('custom_ai_image_description_debug_mode', false);
    echo '<input type="checkbox" name="custom_ai_image_description_debug_mode" value="1" ' . checked(1, $debug_mode, false) . '>';
    echo '<label for="custom_ai_image_description_debug_mode">Enable debug mode (logs API responses)</label>';
}

// Generate alt text using Claude API
function custom_ai_image_description_generate($image_url, $image_title = '') {
    $api_key = get_option('custom_ai_image_description_claude_api_key');
    $model = get_option('custom_ai_image_description_model', 'claude-3-5-sonnet-20241022');
    $prompt = get_option('custom_ai_image_description_prompt', 'Generate a brief alt text description for this image:');
    $language = get_option('custom_ai_image_description_language', 'en');
    $max_tokens = intval(get_option('custom_ai_image_description_max_tokens', 200));
    $debug_mode = get_option('custom_ai_image_description_debug_mode', false);

    if (empty($api_key)) {
        error_log('Custom AI Image Description Generator Error: Claude API key is missing');
        return new WP_Error('missing_api_key', 'Claude API key is missing');
    }

    // Get image content
    $image_content = file_get_contents($image_url);
    if ($image_content === false) {
        error_log("Custom AI Image Description Generator Error: Failed to fetch image content from URL: $image_url");
        return new WP_Error('image_fetch_error', 'Failed to fetch image content');
    }
    
    // Detect actual image type
    $image_info = getimagesizefromstring($image_content);
    if ($image_info === false) {
        error_log("Custom AI Image Description Generator Error: Invalid image format for URL: $image_url");
        return new WP_Error('invalid_image', 'Invalid image format');
    }
    
    $mime_type = $image_info['mime'];
    $base64_image = base64_encode($image_content);
    
    if ($debug_mode) {
        error_log("Image MIME type detected: " . $mime_type);
        error_log("Image size: " . strlen($image_content) . " bytes");
    }

    // Prepare the message for Claude
    $system_prompt = "You are an AI assistant that generates concise and accurate alt text descriptions for images in $language. Focus on key visual elements and provide descriptions that enhance accessibility. Be specific but concise.";
    
    $user_message = $prompt;
    if (!empty($image_title)) {
        $user_message .= " The image title is: \"$image_title\".";
    }
    $user_message .= " Please provide a clear, concise description suitable for alt text.";
    
    $messages = [
        [
            "role" => "user",
            "content" => [
                ["type" => "text", "text" => $user_message],
                ["type" => "image", "source" => ["type" => "base64", "media_type" => $mime_type, "data" => $base64_image]]
            ]
        ]
    ];

    $request_body = [
        'model' => $model,
        'max_tokens' => $max_tokens,
        'system' => $system_prompt,
        'messages' => $messages,
        'temperature' => 0.3
    ];

    $args = [
        'timeout' => 60,
        'headers' => [
            'Content-Type' => 'application/json',
            'x-api-key' => $api_key,
            'anthropic-version' => '2023-06-01'
        ],
        'body' => json_encode($request_body)
    ];

    $response = wp_remote_post('https://api.anthropic.com/v1/messages', $args);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log("Custom AI Image Description Generator Error: Error connecting to Claude API: $error_message");
        return new WP_Error('api_error', 'Error connecting to Claude API: ' . $error_message);
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $body = json_decode($response_body, true);
    
    if ($debug_mode) {
        error_log('Claude API Response Code: ' . $response_code);
        error_log('Claude API Response: ' . print_r($body, true));
    }
    
    if ($response_code !== 200) {
        $error_message = isset($body['error']['message']) ? $body['error']['message'] : wp_remote_retrieve_response_message($response);
        error_log("Custom AI Image Description Generator Error: Claude API returned status $response_code: $error_message");
        
        if ($debug_mode) {
            error_log("Request body was: " . json_encode($request_body));
        }
        
        return new WP_Error('api_error', "Claude API error: $error_message");
    }

    if (isset($body['content'][0]['text'])) {
        return trim($body['content'][0]['text']);
    }

    error_log('Custom AI Image Description Generator Error: Invalid response structure from Claude API');
    return new WP_Error('invalid_response', 'Invalid response from Claude API');
}

// Generate alt text with retry mechanism
function custom_ai_image_description_generate_with_retry($image_url, $image_title = '', $max_retries = 3) {
    for ($i = 0; $i < $max_retries; $i++) {
        $result = custom_ai_image_description_generate($image_url, $image_title);
        if (!is_wp_error($result)) {
            return $result;
        }
        
        // Don't retry on certain errors
        $error_code = $result->get_error_code();
        if (in_array($error_code, ['missing_api_key', 'invalid_image', 'image_fetch_error'])) {
            return $result;
        }
        
        if ($i < $max_retries - 1) {
            error_log("Retry attempt " . ($i + 1) . " for image: $image_url");
            sleep(2 * ($i + 1)); // Exponential backoff
        }
    }
    return $result;
}

// Add alt text to images when uploaded
function custom_ai_image_description_add_on_upload($metadata, $attachment_id) {
    if (empty($metadata['image_meta'])) {
        return $metadata;
    }

    $image_url = wp_get_attachment_url($attachment_id);
    $image_title = get_the_title($attachment_id);
    $alt_text = custom_ai_image_description_generate_with_retry($image_url, $image_title);

    if (!is_wp_error($alt_text)) {
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
    } else {
        error_log('Custom AI Image Description Generator Error: ' . $alt_text->get_error_message());
    }

    return $metadata;
}
add_filter('wp_generate_attachment_metadata', 'custom_ai_image_description_add_on_upload', 10, 2);

// Add bulk action to generate alt text
function custom_ai_image_description_bulk_action($bulk_actions) {
    $bulk_actions['generate_custom_ai_description'] = 'Generate AI Alt Text';
    return $bulk_actions;
}
add_filter('bulk_actions-upload', 'custom_ai_image_description_bulk_action');

// Handle bulk action
function custom_ai_image_description_handle_bulk_action($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'generate_custom_ai_description') {
        return $redirect_to;
    }

    $success_count = 0;
    $error_count = 0;
    
    foreach ($post_ids as $post_id) {
        $image_url = wp_get_attachment_url($post_id);
        if (!$image_url) {
            $error_count++;
            continue;
        }
        
        $image_title = get_the_title($post_id);
        $alt_text = custom_ai_image_description_generate_with_retry($image_url, $image_title);

        if (!is_wp_error($alt_text)) {
            update_post_meta($post_id, '_wp_attachment_image_alt', $alt_text);
            $success_count++;
        } else {
            error_log('Custom AI Image Description Generator Error for attachment ' . $post_id . ': ' . $alt_text->get_error_message());
            $error_count++;
        }
    }

    $redirect_to = add_query_arg([
        'generated_custom_ai_description' => $success_count,
        'generation_errors' => $error_count
    ], $redirect_to);
    
    return $redirect_to;
}
add_filter('handle_bulk_actions-upload', 'custom_ai_image_description_handle_bulk_action', 10, 3);

// Display admin notice after bulk action
function custom_ai_image_description_bulk_action_admin_notice() {
    if (!empty($_REQUEST['generated_custom_ai_description'])) {
        $count = intval($_REQUEST['generated_custom_ai_description']);
        $errors = intval($_REQUEST['generation_errors'] ?? 0);
        
        $message = sprintf(
            _n(
                'Generated AI alt text for %s image.',
                'Generated AI alt text for %s images.',
                $count,
                'custom-ai-image-description-generator'
            ),
            $count
        );
        
        if ($errors > 0) {
            $message .= sprintf(' %d errors occurred.', $errors);
        }
        
        printf('<div id="message" class="updated notice is-dismissible"><p>%s</p></div>', $message);
    }
}
add_action('admin_notices', 'custom_ai_image_description_bulk_action_admin_notice');

// AJAX handlers
add_action('wp_ajax_caidg_generate_alt_text', 'custom_ai_ajax_generate_alt_text');
function custom_ai_ajax_generate_alt_text() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'caidg_ajax_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    // Check permissions
    if (!current_user_can('upload_files')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    $attachment_id = intval($_POST['attachment_id']);
    if (!$attachment_id) {
        wp_send_json_error('Invalid attachment ID');
        return;
    }
    
    $image_url = wp_get_attachment_url($attachment_id);
    if (!$image_url) {
        wp_send_json_error('Could not retrieve image URL');
        return;
    }
    
    $image_title = get_the_title($attachment_id);
    $alt_text = custom_ai_image_description_generate_with_retry($image_url, $image_title);
    
    if (is_wp_error($alt_text)) {
        wp_send_json_error($alt_text->get_error_message());
        return;
    }
    
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt_text);
    
    wp_send_json_success([
        'alt_text' => $alt_text,
        'attachment_id' => $attachment_id
    ]);
}

// Enqueue admin scripts
add_action('admin_enqueue_scripts', 'custom_ai_enqueue_scripts');
function custom_ai_enqueue_scripts($hook) {
    if ('upload.php' !== $hook) {
        return;
    }
    
    wp_add_inline_script('jquery', "
    jQuery(document).ready(function($) {
        // Localization object
        window.caidg_ajax = {
            ajax_url: '" . admin_url('admin-ajax.php') . "',
            nonce: '" . wp_create_nonce('caidg_ajax_nonce') . "',
            generating: 'Generating...',
            error: 'Error:',
            complete: 'Complete!'
        };
        
        // Handle bulk action with progress
        $('#doaction, #doaction2').on('click', function(e) {
            var action = $(this).prev('select').val();
            
            if (action === 'generate_custom_ai_description') {
                e.preventDefault();
                
                var checkedBoxes = $('#the-list input[type=\"checkbox\"]:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select at least one image');
                    return false;
                }
                
                var attachmentIds = [];
                checkedBoxes.each(function() {
                    var id = $(this).val();
                    if (id !== '0' && id !== 'on') {
                        attachmentIds.push(id);
                    }
                });
                
                if (attachmentIds.length === 0) {
                    alert('No valid images selected');
                    return false;
                }
                
                // Create progress container
                var progressHtml = '<div id=\"caidg-progress\" style=\"margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #c3c4c7; box-shadow: 0 1px 1px rgba(0,0,0,.04);\">' +
                    '<h3>Generating Alt Text</h3>' +
                    '<div style=\"margin: 10px 0;\">Processing <span id=\"caidg-current\">1</span> of <span id=\"caidg-total\">' + attachmentIds.length + '</span> images...</div>' +
                    '<div style=\"background: #f0f0f1; height: 24px; border-radius: 3px; overflow: hidden;\">' +
                    '<div id=\"caidg-progress-bar\" style=\"background: #2271b1; height: 100%; width: 0%; transition: width 0.3s; border-radius: 3px;\"></div>' +
                    '</div>' +
                    '<div id=\"caidg-status\" style=\"margin-top: 10px; color: #50575e;\"></div>' +
                    '</div>';
                
                $('.tablenav.top').after(progressHtml);
                
                // Process images sequentially
                processImages(attachmentIds, 0);
                
                function processImages(ids, index) {
                    if (index >= ids.length) {
                        $('#caidg-status').html('<strong style=\"color: #00a32a;\">✓ All images processed successfully!</strong>');
                        setTimeout(function() {
                            $('#caidg-progress').fadeOut(function() {
                                $(this).remove();
                                location.reload(); // Reload to show updated alt text
                            });
                        }, 2000);
                        return;
                    }
                    
                    var progress = ((index + 1) / ids.length) * 100;
                    $('#caidg-current').text(index + 1);
                    $('#caidg-progress-bar').css('width', progress + '%');
                    $('#caidg-status').text('Generating alt text for image #' + ids[index] + '...');
                    
                    $.ajax({
                        url: caidg_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'caidg_generate_alt_text',
                            attachment_id: ids[index],
                            nonce: caidg_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                console.log('Generated alt text for #' + ids[index]);
                            } else {
                                console.error('Error for #' + ids[index] + ': ' + response.data);
                                $('#caidg-status').append('<div style=\"color: #d63638;\">Error for image #' + ids[index] + ': ' + response.data + '</div>');
                            }
                            // Continue with next image
                            setTimeout(function() {
                                processImages(ids, index + 1);
                            }, 500); // Small delay between requests
                        },
                        error: function(xhr, status, error) {
                            console.error('Network error for #' + ids[index]);
                            $('#caidg-status').append('<div style=\"color: #d63638;\">Network error for image #' + ids[index] + '</div>');
                            // Continue despite error
                            setTimeout(function() {
                                processImages(ids, index + 1);
                            }, 500);
                        }
                    });
                }
                
                return false;
            }
        });
    });
    ");
    
    // Add inline CSS
    wp_add_inline_style('wp-admin', "
    #caidg-progress h3 {
        margin: 0 0 15px 0;
        font-size: 1.3em;
        font-weight: 600;
    }
    ");
}

// Refresh alt text in post content
function custom_ai_image_description_refresh_alt_text($post_id) {
    $post = get_post($post_id);
    if (!$post) {
        return false;
    }

    $content = $post->post_content;
    $updated = false;
    
    // Find all images and update their alt text
    $updated_content = preg_replace_callback('/<img[^>]+>/', function($matches) use (&$updated) {
        $img_tag = $matches[0];
        
        // Look for WordPress image class to get attachment ID
        if (preg_match('/wp-image-(\d+)/', $img_tag, $id_matches)) {
            $attachment_id = $id_matches[1];
            $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
            
            if ($alt_text) {
                // Check if alt attribute exists
                if (preg_match('/alt=["\'](.*?)["\']/', $img_tag, $alt_matches)) {
                    // Replace existing alt text
                    $new_img_tag = str_replace($alt_matches[0], 'alt="' . esc_attr($alt_text) . '"', $img_tag);
                } else {
                    // Add alt attribute if it doesn't exist
                    $new_img_tag = str_replace('<img', '<img alt="' . esc_attr($alt_text) . '"', $img_tag);
                }
                
                if ($new_img_tag !== $img_tag) {
                    $updated = true;
                    return $new_img_tag;
                }
            }
        }
        return $img_tag;
    }, $content);

    // Update post if changes were made
    if ($updated && $content !== $updated_content) {
        wp_update_post([
            'ID' => $post_id,
            'post_content' => $updated_content
        ]);
        return true;
    }
    
    return false;
}

// Add "Refresh Alt Text" action to post/page row actions
function custom_ai_image_description_refresh_alt_text_action($actions, $post) {
    if (current_user_can('edit_post', $post->ID)) {
        $url = wp_nonce_url(
            admin_url('admin-post.php?action=refresh_alt_text&post=' . $post->ID),
            'refresh_alt_text_' . $post->ID
        );
        $actions['refresh_alt_text'] = '<a href="' . esc_url($url) . '">' . __('Refresh Alt Text', 'custom-ai-image-description-generator') . '</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'custom_ai_image_description_refresh_alt_text_action', 10, 2);
add_filter('page_row_actions', 'custom_ai_image_description_refresh_alt_text_action', 10, 2);

// Handle "Refresh Alt Text" action
function custom_ai_image_description_handle_refresh_alt_text() {
    if (!isset($_GET['post'])) {
        wp_die('Invalid request');
    }
    
    $post_id = intval($_GET['post']);
    
    if (!current_user_can('edit_post', $post_id) || 
        !wp_verify_nonce($_GET['_wpnonce'], 'refresh_alt_text_' . $post_id)) {
        wp_die('Security check failed');
    }
    
    $updated = custom_ai_image_description_refresh_alt_text($post_id);
    
    // Redirect back with status
    $redirect_url = admin_url('edit.php');
    if (get_post_type($post_id) === 'page') {
        $redirect_url = admin_url('edit.php?post_type=page');
    }
    
    if ($updated) {
        $redirect_url = add_query_arg('alt_text_refreshed', '1', $redirect_url);
    } else {
        $redirect_url = add_query_arg('alt_text_refresh_failed', '1', $redirect_url);
    }
    
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_post_refresh_alt_text', 'custom_ai_image_description_handle_refresh_alt_text');

// Add bulk action for refreshing alt text
function custom_ai_image_description_bulk_refresh_alt_text($bulk_actions) {
    $bulk_actions['refresh_alt_text'] = __('Refresh Alt Text', 'custom-ai-image-description-generator');
    return $bulk_actions;
}
add_filter('bulk_actions-edit-post', 'custom_ai_image_description_bulk_refresh_alt_text');
add_filter('bulk_actions-edit-page', 'custom_ai_image_description_bulk_refresh_alt_text');

// Handle bulk refresh alt text action
function custom_ai_image_description_handle_bulk_refresh_alt_text($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'refresh_alt_text') {
        return $redirect_to;
    }

    $updated_count = 0;
    foreach ($post_ids as $post_id) {
        if (custom_ai_image_description_refresh_alt_text($post_id)) {
            $updated_count++;
        }
    }

    $redirect_to = add_query_arg('refreshed_alt_text', $updated_count, $redirect_to);
    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-post', 'custom_ai_image_description_handle_bulk_refresh_alt_text', 10, 3);
add_filter('handle_bulk_actions-edit-page', 'custom_ai_image_description_handle_bulk_refresh_alt_text', 10, 3);

// Display admin notice after refresh
function custom_ai_image_description_refresh_admin_notice() {
    if (isset($_GET['alt_text_refreshed'])) {
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             __('Alt text refreshed successfully in post content.', 'custom-ai-image-description-generator') . 
             '</p></div>';
    }
    
    if (isset($_GET['alt_text_refresh_failed'])) {
        echo '<div class="notice notice-warning is-dismissible"><p>' . 
             __('No alt text updates were needed for this post.', 'custom-ai-image-description-generator') . 
             '</p></div>';
    }
    
    if (!empty($_GET['refreshed_alt_text'])) {
        $count = intval($_GET['refreshed_alt_text']);
        printf(
            '<div class="notice notice-success is-dismissible"><p>' .
            _n(
                'Refreshed alt text in %s post/page.',
                'Refreshed alt text in %s posts/pages.',
                $count,
                'custom-ai-image-description-generator'
            ) . '</p></div>',
            $count
        );
    }
}
add_action('admin_notices', 'custom_ai_image_description_refresh_admin_notice');

// Add settings link on plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'custom_ai_add_settings_link');
function custom_ai_add_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=custom-ai-image-description-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}