<?php
/**
 * Direct Test Script for Alt Text Generation
 * Access this file directly to test if the generation function works
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied');
}

// Test with a specific image
$test_image_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Alt Text Generation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .test-box { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; margin: 20px 0; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: #0073aa; }
        pre { background: #fff; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
        input[type="number"] { padding: 5px; width: 100px; }
        button { padding: 8px 15px; background: #0073aa; color: white; border: none; cursor: pointer; }
        button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>Test Alt Text Generation</h1>
    
    <div class="test-box">
        <h2>Configuration Status</h2>
        <?php
        $api_key = get_option('custom_ai_image_description_claude_api_key');
        $model = get_option('custom_ai_image_description_model', 'claude-3-5-sonnet-20241022');
        $debug = get_option('custom_ai_image_description_debug_mode', false);
        
        echo '<p>API Key: ' . ($api_key ? '<span class="success">✓ Configured</span>' : '<span class="error">✗ Not configured</span>') . '</p>';
        echo '<p>Model: <code>' . esc_html($model) . '</code></p>';
        echo '<p>Debug Mode: ' . ($debug ? '<span class="info">Enabled</span>' : 'Disabled') . '</p>';
        ?>
    </div>
    
    <div class="test-box">
        <h2>Test Image Generation</h2>
        <form method="get">
            <label>Enter Image Attachment ID: 
                <input type="number" name="id" value="<?php echo esc_attr($test_image_id); ?>" />
            </label>
            <button type="submit">Test Generation</button>
        </form>
        
        <?php if ($test_image_id > 0): ?>
            <hr>
            <h3>Testing with Attachment ID: <?php echo $test_image_id; ?></h3>
            
            <?php
            // Get image details
            $image_url = wp_get_attachment_url($test_image_id);
            $image_title = get_the_title($test_image_id);
            $current_alt = get_post_meta($test_image_id, '_wp_attachment_image_alt', true);
            
            if (!$image_url) {
                echo '<p class="error">Error: Invalid attachment ID or image not found.</p>';
            } else {
                echo '<p><strong>Image URL:</strong> <a href="' . esc_url($image_url) . '" target="_blank">' . esc_html($image_url) . '</a></p>';
                echo '<p><strong>Image Title:</strong> ' . esc_html($image_title ?: '(no title)') . '</p>';
                echo '<p><strong>Current Alt Text:</strong> ' . esc_html($current_alt ?: '(none)') . '</p>';
                
                // Show the image
                echo '<p><strong>Preview:</strong><br><img src="' . esc_url($image_url) . '" style="max-width: 300px; height: auto; border: 1px solid #ddd;"></p>';
                
                echo '<h3>Attempting Generation...</h3>';
                
                // Test direct function call
                if (function_exists('custom_ai_image_description_generate_with_retry')) {
                    $start_time = microtime(true);
                    
                    echo '<p class="info">Calling generation function...</p>';
                    $result = custom_ai_image_description_generate_with_retry($image_url, $image_title);
                    
                    $end_time = microtime(true);
                    $duration = round($end_time - $start_time, 2);
                    
                    echo '<p>Generation took ' . $duration . ' seconds</p>';
                    
                    if (is_wp_error($result)) {
                        echo '<p class="error">Generation failed!</p>';
                        echo '<p><strong>Error Code:</strong> ' . $result->get_error_code() . '</p>';
                        echo '<p><strong>Error Message:</strong> ' . $result->get_error_message() . '</p>';
                        
                        // Check error log for more details
                        echo '<h4>Debugging Information:</h4>';
                        echo '<pre>';
                        echo 'Function exists: ' . (function_exists('custom_ai_image_description_generate') ? 'Yes' : 'No') . "\n";
                        echo 'API Key present: ' . (!empty($api_key) ? 'Yes' : 'No') . "\n";
                        echo 'Model: ' . $model . "\n";
                        echo '</pre>';
                    } else {
                        echo '<p class="success">✓ Generation successful!</p>';
                        echo '<div style="background: #d4f4dd; padding: 15px; border: 1px solid #46b450; margin: 10px 0;">';
                        echo '<strong>Generated Alt Text:</strong><br>';
                        echo '<em>' . esc_html($result) . '</em>';
                        echo '</div>';
                        
                        // Option to save
                        if (isset($_GET['save']) && $_GET['save'] == '1') {
                            update_post_meta($test_image_id, '_wp_attachment_image_alt', $result);
                            echo '<p class="success">✓ Alt text saved to database!</p>';
                        } else {
                            echo '<p><a href="?id=' . $test_image_id . '&save=1" class="button">Save this alt text</a></p>';
                        }
                    }
                } else {
                    echo '<p class="error">Generation function not found! Plugin may not be properly loaded.</p>';
                }
            }
            ?>
        <?php endif; ?>
    </div>
    
    <div class="test-box">
        <h2>Recent Images in Media Library</h2>
        <?php
        $recent_images = get_posts(array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        if ($recent_images) {
            echo '<p>Click on an ID to test generation for that image:</p>';
            echo '<table style="width: 100%; border-collapse: collapse;">';
            echo '<tr><th style="text-align: left; padding: 5px; border-bottom: 1px solid #ddd;">ID</th>';
            echo '<th style="text-align: left; padding: 5px; border-bottom: 1px solid #ddd;">Title</th>';
            echo '<th style="text-align: left; padding: 5px; border-bottom: 1px solid #ddd;">Current Alt Text</th></tr>';
            
            foreach ($recent_images as $image) {
                $alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
                echo '<tr>';
                echo '<td style="padding: 5px;"><a href="?id=' . $image->ID . '">' . $image->ID . '</a></td>';
                echo '<td style="padding: 5px;">' . esc_html($image->post_title) . '</td>';
                echo '<td style="padding: 5px;">' . ($alt ? esc_html(substr($alt, 0, 50) . '...') : '<em>None</em>') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No images found in media library.</p>';
        }
        ?>
    </div>
    
    <div class="test-box">
        <h2>Test AJAX Endpoint</h2>
        <button onclick="testAjax()">Test AJAX Generation</button>
        <div id="ajax-result"></div>
        
        <script>
        function testAjax() {
            var resultDiv = document.getElementById('ajax-result');
            resultDiv.innerHTML = '<p>Testing AJAX endpoint...</p>';
            
            // Get the first image ID from the table
            var firstLink = document.querySelector('table a[href*="?id="]');
            if (!firstLink) {
                resultDiv.innerHTML = '<p class="error">No images found to test with.</p>';
                return;
            }
            
            var testId = firstLink.href.split('id=')[1];
            
            // Create form data
            var formData = new FormData();
            formData.append('action', 'caidg_generate_alt_text');
            formData.append('attachment_id', testId);
            formData.append('nonce', '<?php echo wp_create_nonce("caidg_ajax_nonce"); ?>');
            
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<p class="success">✓ AJAX endpoint working!</p>' +
                                         '<p>Generated alt text: <em>' + data.data.alt_text + '</em></p>';
                } else {
                    resultDiv.innerHTML = '<p class="error">✗ AJAX error: ' + data.data + '</p>';
                }
            })
            .catch(error => {
                resultDiv.innerHTML = '<p class="error">✗ Network error: ' + error + '</p>';
            });
        }
        </script>
    </div>
</body>
</html>