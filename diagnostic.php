<?php
/**
 * Diagnostic script for Custom AI Image Description Generator
 * 
 * This script helps diagnose API issues by testing the connection
 * and showing detailed error information.
 * 
 * Usage: Place in plugin folder and access via browser after activating plugin
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied. Admin privileges required.');
}

// Get settings
$api_key = get_option('custom_ai_image_description_claude_api_key');
$model = get_option('custom_ai_image_description_model', 'claude-3-5-sonnet-20241022');
$prompt = get_option('custom_ai_image_description_prompt', 'Generate a brief alt text description for this image:');
$language = get_option('custom_ai_image_description_language', 'en');
$max_tokens = intval(get_option('custom_ai_image_description_max_tokens', 200));

?>
<!DOCTYPE html>
<html>
<head>
    <title>CAIDG Diagnostic Tool</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 10px;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #0073aa;
        }
        .success {
            color: #46b450;
            font-weight: bold;
        }
        .error {
            color: #dc3232;
            font-weight: bold;
        }
        .warning {
            color: #ffb900;
            font-weight: bold;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        pre {
            background: #282c34;
            color: #abb2bf;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .test-button {
            background: #0073aa;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .test-button:hover {
            background: #005a87;
        }
        .test-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .test-result.success {
            background: #d4f4dd;
            border: 1px solid #46b450;
        }
        .test-result.error {
            background: #f8d7da;
            border: 1px solid #dc3232;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Custom AI Image Description Generator - Diagnostic Tool</h1>
        
        <div class="section">
            <h2>📋 Current Configuration</h2>
            <table>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>API Key</td>
                    <td>
                        <?php if ($api_key): ?>
                            <code><?php echo substr($api_key, 0, 15) . '...' . substr($api_key, -4); ?></code>
                        <?php else: ?>
                            <span class="error">Not configured</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($api_key && strpos($api_key, 'sk-ant-') === 0): ?>
                            <span class="success">✅ Valid format</span>
                        <?php elseif ($api_key): ?>
                            <span class="warning">⚠️ Invalid format</span>
                        <?php else: ?>
                            <span class="error">❌ Missing</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Selected Model</td>
                    <td><code><?php echo esc_html($model); ?></code></td>
                    <td>
                        <?php 
                        $valid_models = [
                            'claude-opus-4-1-20250805',
                            'claude-opus-4-20250514',
                            'claude-sonnet-4-20250514',
                            'claude-3-7-sonnet-20250219',
                            'claude-3-5-sonnet-20241022',
                            'claude-3-5-haiku-20241022'
                        ];
                        if (in_array($model, $valid_models)): ?>
                            <span class="success">✅ Valid model</span>
                        <?php else: ?>
                            <span class="error">❌ Invalid model ID</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Language</td>
                    <td><code><?php echo esc_html($language); ?></code></td>
                    <td><span class="success">✅</span></td>
                </tr>
                <tr>
                    <td>Max Tokens</td>
                    <td><code><?php echo esc_html($max_tokens); ?></code></td>
                    <td>
                        <?php if ($max_tokens >= 50 && $max_tokens <= 500): ?>
                            <span class="success">✅ Valid range</span>
                        <?php else: ?>
                            <span class="warning">⚠️ Outside recommended range</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>WordPress Version</td>
                    <td><code><?php echo get_bloginfo('version'); ?></code></td>
                    <td><span class="success">✅</span></td>
                </tr>
                <tr>
                    <td>PHP Version</td>
                    <td><code><?php echo PHP_VERSION; ?></code></td>
                    <td>
                        <?php if (version_compare(PHP_VERSION, '7.4', '>=')): ?>
                            <span class="success">✅ Compatible</span>
                        <?php else: ?>
                            <span class="error">❌ Requires PHP 7.4+</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>SSL/HTTPS</td>
                    <td><code><?php echo is_ssl() ? 'Enabled' : 'Disabled'; ?></code></td>
                    <td>
                        <?php if (is_ssl()): ?>
                            <span class="success">✅</span>
                        <?php else: ?>
                            <span class="warning">⚠️ Recommended</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>🧪 API Connection Test</h2>
            <p>Click the button below to test your API connection with a simple text-only request:</p>
            
            <form method="post">
                <?php wp_nonce_field('caidg_diagnostic_test'); ?>
                <button type="submit" name="test_api" class="test-button">Test API Connection</button>
            </form>
            
            <?php
            if (isset($_POST['test_api']) && wp_verify_nonce($_POST['_wpnonce'], 'caidg_diagnostic_test')) {
                echo '<div class="test-result">';
                echo '<h3>Test Results:</h3>';
                
                // Prepare test request
                $test_body = [
                    'model' => $model,
                    'max_tokens' => 50,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Say "API connection successful" if you receive this message.'
                        ]
                    ]
                ];
                
                echo '<h4>Request Details:</h4>';
                echo '<pre>' . json_encode($test_body, JSON_PRETTY_PRINT) . '</pre>';
                
                $args = [
                    'timeout' => 30,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'x-api-key' => $api_key,
                        'anthropic-version' => '2023-06-01'
                    ],
                    'body' => json_encode($test_body)
                ];
                
                $response = wp_remote_post('https://api.anthropic.com/v1/messages', $args);
                
                if (is_wp_error($response)) {
                    echo '<div class="error">❌ Connection Error: ' . $response->get_error_message() . '</div>';
                } else {
                    $response_code = wp_remote_retrieve_response_code($response);
                    $response_body = json_decode(wp_remote_retrieve_body($response), true);
                    
                    echo '<h4>Response Status: ' . $response_code . '</h4>';
                    
                    if ($response_code == 200) {
                        echo '<div class="success">✅ API connection successful!</div>';
                        if (isset($response_body['content'][0]['text'])) {
                            echo '<p><strong>Response:</strong> ' . esc_html($response_body['content'][0]['text']) . '</p>';
                        }
                    } else {
                        echo '<div class="error">❌ API Error</div>';
                        echo '<h4>Error Response:</h4>';
                        echo '<pre>' . json_encode($response_body, JSON_PRETTY_PRINT) . '</pre>';
                        
                        // Provide specific guidance based on error
                        if ($response_code == 401) {
                            echo '<p class="error">⚠️ Authentication failed. Please check your API key.</p>';
                        } elseif ($response_code == 400) {
                            echo '<p class="error">⚠️ Bad request. The model ID may be incorrect or the request format is invalid.</p>';
                            if (isset($response_body['error']['message'])) {
                                echo '<p><strong>Error message:</strong> ' . esc_html($response_body['error']['message']) . '</p>';
                            }
                        } elseif ($response_code == 429) {
                            echo '<p class="warning">⚠️ Rate limit exceeded. Please wait before trying again.</p>';
                        }
                    }
                }
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="section">
            <h2>📝 Valid Model IDs</h2>
            <p>These are the correct model IDs to use in your configuration:</p>
            <table>
                <tr>
                    <th>Model Name</th>
                    <th>Model ID (use this in settings)</th>
                    <th>Description</th>
                </tr>
                <tr>
                    <td>Claude Opus 4.1</td>
                    <td><code>claude-opus-4-1-20250805</code></td>
                    <td>Most powerful, highest quality</td>
                </tr>
                <tr>
                    <td>Claude Opus 4</td>
                    <td><code>claude-opus-4-20250514</code></td>
                    <td>Very powerful</td>
                </tr>
                <tr>
                    <td>Claude Sonnet 4</td>
                    <td><code>claude-sonnet-4-20250514</code></td>
                    <td>Balanced performance</td>
                </tr>
                <tr>
                    <td>Claude 3.7 Sonnet</td>
                    <td><code>claude-3-7-sonnet-20250219</code></td>
                    <td>Latest 3.x series</td>
                </tr>
                <tr style="background: #e8f5e9;">
                    <td><strong>Claude 3.5 Sonnet</strong></td>
                    <td><code><strong>claude-3-5-sonnet-20241022</strong></code></td>
                    <td><strong>Recommended - Best balance</strong></td>
                </tr>
                <tr>
                    <td>Claude 3.5 Haiku</td>
                    <td><code>claude-3-5-haiku-20241022</code></td>
                    <td>Fast and economical</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>🔧 Troubleshooting Steps</h2>
            <ol>
                <li><strong>Verify API Key:</strong> Ensure it starts with <code>sk-ant-api</code></li>
                <li><strong>Check Model ID:</strong> Use the exact model ID from the table above</li>
                <li><strong>Test Connection:</strong> Use the test button above to verify API access</li>
                <li><strong>Enable Debug Mode:</strong> Turn on debug mode in plugin settings for detailed logs</li>
                <li><strong>Check Error Logs:</strong> Review <code>/wp-content/debug.log</code> for detailed errors</li>
                <li><strong>Verify Credits:</strong> Ensure you have sufficient credits at <a href="https://console.anthropic.com" target="_blank">console.anthropic.com</a></li>
            </ol>
        </div>
        
        <div class="section">
            <h2>💡 Quick Fix</h2>
            <p>If you're seeing model-related errors, go to your plugin settings and:</p>
            <ol>
                <li>Select <strong>"Claude 3.5 Sonnet"</strong> from the model dropdown</li>
                <li>Save the settings</li>
                <li>Try generating alt text again</li>
            </ol>
        </div>
    </div>
</body>
</html>