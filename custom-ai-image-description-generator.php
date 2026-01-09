<?php
/*
Plugin Name: Custom AI Image Description Generator
Description: Automatically generates alt text for images using Claude API, OpenAI API, or OpenRouter (90+ vision models with automatic discovery)
Version: 2.7
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
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_api_provider');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_claude_api_key');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_openrouter_api_key');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_openai_api_key');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_model');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_prompt');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_language');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_max_tokens');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_debug_mode');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_compress_images');
    register_setting('custom_ai_image_description_options', 'custom_ai_image_description_skip_existing');

    add_settings_section('custom_ai_image_description_settings', 'API Settings', 'custom_ai_image_description_settings_section_callback', 'custom_ai_image_description_options');
    
    add_settings_field('custom_ai_image_description_api_provider', 'API Provider', 'custom_ai_image_description_api_provider_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_claude_api_key', 'Claude API Key', 'custom_ai_image_description_claude_api_key_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_openrouter_api_key', 'OpenRouter API Key', 'custom_ai_image_description_openrouter_api_key_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_openai_api_key', 'OpenAI API Key', 'custom_ai_image_description_openai_api_key_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_model', 'AI Model', 'custom_ai_image_description_model_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_prompt', 'Custom Prompt', 'custom_ai_image_description_prompt_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_language', 'Language', 'custom_ai_image_description_language_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_max_tokens', 'Max Tokens', 'custom_ai_image_description_max_tokens_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_debug_mode', 'Debug Mode', 'custom_ai_image_description_debug_mode_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_compress_images', 'Image Compression', 'custom_ai_image_description_compress_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
    add_settings_field('custom_ai_image_description_skip_existing', 'Skip Existing Alt Text', 'custom_ai_image_description_skip_existing_callback', 'custom_ai_image_description_options', 'custom_ai_image_description_settings');
}
add_action('admin_init', 'custom_ai_image_description_register_settings');

// Settings section callback
function custom_ai_image_description_settings_section_callback() {
    echo '<p>Configure your AI API settings below. You can use Claude API directly, OpenAI API directly, or OpenRouter for access to multiple models.</p>';
}

// Settings field callbacks
function custom_ai_image_description_api_provider_callback() {
    $provider = get_option('custom_ai_image_description_api_provider', 'claude');
    ?>
    <select name="custom_ai_image_description_api_provider" id="api_provider_select">
        <option value="claude" <?php selected($provider, 'claude'); ?>>Claude (Anthropic)</option>
        <option value="openai" <?php selected($provider, 'openai'); ?>>OpenAI</option>
        <option value="openrouter" <?php selected($provider, 'openrouter'); ?>>OpenRouter</option>
    </select>
    <p class="description">Choose your API provider. OpenAI provides GPT-4 vision models, OpenRouter provides access to multiple AI models including Claude, GPT-4, and more.</p>
    <?php
}

function custom_ai_image_description_claude_api_key_callback() {
    $api_key = get_option('custom_ai_image_description_claude_api_key');
    $provider = get_option('custom_ai_image_description_api_provider', 'claude');
    $style = ($provider !== 'claude') ? 'display:none;' : '';
    echo '<div class="api-key-field" data-provider="claude" style="' . $style . '">';
    echo '<input type="password" name="custom_ai_image_description_claude_api_key" value="' . esc_attr($api_key) . '" size="50">';
    echo '<p class="description">Get your API key from <a href="https://console.anthropic.com/" target="_blank">console.anthropic.com</a></p>';
    echo '</div>';
}

function custom_ai_image_description_openrouter_api_key_callback() {
    $api_key = get_option('custom_ai_image_description_openrouter_api_key');
    $provider = get_option('custom_ai_image_description_api_provider', 'claude');
    $style = ($provider !== 'openrouter') ? 'display:none;' : '';
    echo '<div class="api-key-field" data-provider="openrouter" style="' . $style . '">';
    echo '<input type="password" name="custom_ai_image_description_openrouter_api_key" value="' . esc_attr($api_key) . '" size="50">';
    echo '<p class="description">Get your API key from <a href="https://openrouter.ai/keys" target="_blank">openrouter.ai/keys</a></p>';
    echo '</div>';
}

function custom_ai_image_description_openai_api_key_callback() {
    $api_key = get_option('custom_ai_image_description_openai_api_key');
    $provider = get_option('custom_ai_image_description_api_provider', 'claude');
    $style = ($provider !== 'openai') ? 'display:none;' : '';
    echo '<div class="api-key-field" data-provider="openai" style="' . $style . '">';
    echo '<input type="password" name="custom_ai_image_description_openai_api_key" value="' . esc_attr($api_key) . '" size="50">';
    echo '<p class="description">Get your API key from <a href="https://platform.openai.com/api-keys" target="_blank">platform.openai.com/api-keys</a></p>';
    echo '</div>';
}

// Fetch vision-capable models from OpenRouter API
function custom_ai_image_description_fetch_openrouter_models() {
    // Check cache first (valid for 24 hours)
    $cached_models = get_transient('custom_ai_openrouter_vision_models');
    if ($cached_models !== false) {
        return $cached_models;
    }
    
    // Fetch from OpenRouter API
    $response = wp_remote_get('https://openrouter.ai/api/v1/models', array(
        'timeout' => 30
    ));
    
    if (is_wp_error($response)) {
        // Return fallback models if API fails
        return custom_ai_image_description_get_fallback_openrouter_models();
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!isset($data['data']) || !is_array($data['data'])) {
        return custom_ai_image_description_get_fallback_openrouter_models();
    }
    
    $vision_models = array();
    
    foreach ($data['data'] as $model) {
        // Check if model supports image input
        $has_vision = false;
        
        // Check architecture.input_modalities array
        if (isset($model['architecture']['input_modalities']) && 
            is_array($model['architecture']['input_modalities']) &&
            in_array('image', $model['architecture']['input_modalities'])) {
            $has_vision = true;
        }
        
        // Check architecture.modality string
        if (!$has_vision && isset($model['architecture']['modality']) && 
            strpos($model['architecture']['modality'], 'image') !== false) {
            $has_vision = true;
        }
        
        if ($has_vision) {
            $id = $model['id'];
            $name = $model['name'] ?? $id;
            
            // Add pricing info to name
            if (isset($model['pricing'])) {
                $prompt_cost = floatval($model['pricing']['prompt'] ?? 0) * 1000000;
                if ($prompt_cost == 0) {
                    $name .= ' (Free)';
                } else if ($prompt_cost < 0.5) {
                    $name .= ' (💰 Cheap)';
                } else if ($prompt_cost > 5) {
                    $name .= ' (💎 Premium)';
                }
            }
            
            // Add context length if significant
            if (isset($model['context_length']) && $model['context_length'] > 100000) {
                $name .= ' [' . round($model['context_length'] / 1000) . 'K]';
            }
            
            $vision_models[$id] = $name;
        }
    }
    
    // Sort models by provider and name
    uksort($vision_models, function($a, $b) {
        // Prioritize certain providers
        $priority_providers = ['anthropic/', 'openai/', 'google/', 'meta-llama/'];
        foreach ($priority_providers as $provider) {
            if (strpos($a, $provider) === 0 && strpos($b, $provider) !== 0) return -1;
            if (strpos($b, $provider) === 0 && strpos($a, $provider) !== 0) return 1;
        }
        return strcasecmp($a, $b);
    });
    
    // Cache for 24 hours
    set_transient('custom_ai_openrouter_vision_models', $vision_models, DAY_IN_SECONDS);
    
    return $vision_models;
}

// Fallback models if API is unavailable
function custom_ai_image_description_get_fallback_openrouter_models() {
    return array(
        'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet',
        'anthropic/claude-3-opus' => 'Claude 3 Opus',
        'anthropic/claude-3-haiku' => 'Claude 3 Haiku',
        'openai/gpt-4o' => 'GPT-4o',
        'openai/gpt-4o-mini' => 'GPT-4o Mini',
        'openai/gpt-4-turbo' => 'GPT-4 Turbo',
        'google/gemini-pro-1.5' => 'Gemini Pro 1.5',
        'google/gemini-flash-1.5' => 'Gemini Flash 1.5',
        'meta-llama/llama-3.2-90b-vision-instruct' => 'Llama 3.2 90B Vision',
        'meta-llama/llama-3.2-11b-vision-instruct' => 'Llama 3.2 11B Vision'
    );
}

// Fetch models from Anthropic API
// API Reference: https://platform.claude.com/docs/en/about-claude/pricing#model-pricing
// List models: curl https://api.anthropic.com/v1/models -H 'anthropic-version: 2023-06-01' -H "X-Api-Key: $ANTHROPIC_API_KEY"
function custom_ai_image_description_fetch_claude_models() {
    // Check cache first (valid for 24 hours)
    $cached_models = get_transient('custom_ai_claude_vision_models');
    if ($cached_models !== false) {
        return $cached_models;
    }

    // Get API key for authentication
    $api_key = get_option('custom_ai_image_description_claude_api_key');
    if (empty($api_key)) {
        // Return fallback models if no API key is available
        return custom_ai_image_description_get_fallback_claude_models();
    }

    // Fetch from Anthropic API
    $response = wp_remote_get('https://api.anthropic.com/v1/models', array(
        'timeout' => 30,
        'headers' => array(
            'x-api-key' => $api_key,
            'anthropic-version' => '2023-06-01'
        )
    ));

    if (is_wp_error($response)) {
        // Return fallback models if API fails
        return custom_ai_image_description_get_fallback_claude_models();
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!isset($data['data']) || !is_array($data['data'])) {
        return custom_ai_image_description_get_fallback_claude_models();
    }

    $vision_models = array();

    foreach ($data['data'] as $model) {
        $model_id = $model['id'];

        // All Claude 3+ and 4.x models support vision
        // Skip instant models as they're deprecated
        if (strpos($model_id, 'claude-') === 0) {

            // Skip deprecated/instant models
            if (strpos($model_id, 'instant') !== false) {
                continue;
            }

            // Create display name from model info
            $display_name = isset($model['display_name']) ? $model['display_name'] : $model_id;

            // Add descriptive labels for known model types with pricing (input/output per MTok)
            if (strpos($model_id, 'opus-4-5') !== false) {
                $display_name .= ' (Most Intelligent - $5/$25)';
            } elseif (strpos($model_id, 'sonnet-4-5') !== false) {
                $display_name .= ' (Recommended - $3/$15)';
            } elseif (strpos($model_id, 'haiku-4-5') !== false) {
                $display_name .= ' (Fast & Economical - $1/$5)';
            } elseif (strpos($model_id, 'opus-4-1') !== false) {
                $display_name .= ' ($15/$75)';
            } elseif (strpos($model_id, 'opus-4-') !== false || preg_match('/opus-4-\d{8}$/', $model_id)) {
                $display_name .= ' ($15/$75)';
            } elseif (strpos($model_id, 'sonnet-4-') !== false || preg_match('/sonnet-4-\d{8}$/', $model_id)) {
                $display_name .= ' ($3/$15)';
            } elseif (strpos($model_id, '3-7-sonnet') !== false) {
                $display_name .= ' (Deprecated - $3/$15)';
            } elseif (strpos($model_id, '3-5-haiku') !== false) {
                $display_name .= ' ($0.80/$4)';
            } elseif (strpos($model_id, '3-haiku') !== false) {
                $display_name .= ' (Legacy - $0.25/$1.25)';
            }

            $vision_models[$model_id] = $display_name;
        }
    }

    // Sort models by preference (best value first)
    uksort($vision_models, function($a, $b) {
        // Priority order for model families
        $priority = array(
            'sonnet-4-5' => 1,   // Recommended - best value
            'opus-4-5' => 2,     // Most intelligent
            'haiku-4-5' => 3,    // Fast & economical
            'opus-4-1' => 4,     // Advanced
            'sonnet-4-' => 5,    // Good
            'opus-4-' => 6,      // Premium
            '3-7-sonnet' => 7,   // Hybrid reasoning
            '3-5-haiku' => 8,    // Fast
            '3-haiku' => 9,      // Legacy
        );

        $a_priority = 100;
        $b_priority = 100;

        foreach ($priority as $pattern => $p) {
            if (strpos($a, $pattern) !== false && $a_priority === 100) {
                $a_priority = $p;
            }
            if (strpos($b, $pattern) !== false && $b_priority === 100) {
                $b_priority = $p;
            }
        }

        if ($a_priority !== $b_priority) {
            return $a_priority - $b_priority;
        }

        return strcasecmp($a, $b);
    });

    // Cache for 24 hours if we got models
    if (count($vision_models) > 0) {
        set_transient('custom_ai_claude_vision_models', $vision_models, DAY_IN_SECONDS);
        return $vision_models;
    }

    // Return fallback if no vision models found
    return custom_ai_image_description_get_fallback_claude_models();
}

// Fallback models if Anthropic API is unavailable
function custom_ai_image_description_get_fallback_claude_models() {
    return array(
        'claude-sonnet-4-5-latest' => 'Claude Sonnet 4.5 (Recommended - $3/$15)',
        'claude-opus-4-5-latest' => 'Claude Opus 4.5 (Most Intelligent - $5/$25)',
        'claude-haiku-4-5-latest' => 'Claude Haiku 4.5 (Fast & Economical - $1/$5)',
        'claude-opus-4-1-latest' => 'Claude Opus 4.1 ($15/$75)',
        'claude-sonnet-4-latest' => 'Claude Sonnet 4 ($3/$15)',
        'claude-opus-4-latest' => 'Claude Opus 4 ($15/$75)',
        'claude-3-5-haiku-latest' => 'Claude Haiku 3.5 ($0.80/$4)'
    );
}

// Fetch vision-capable models from OpenAI API
function custom_ai_image_description_fetch_openai_models() {
    // Check cache first (valid for 24 hours)
    $cached_models = get_transient('custom_ai_openai_vision_models');
    if ($cached_models !== false) {
        return $cached_models;
    }
    
    // Get API key for authentication
    $api_key = get_option('custom_ai_image_description_openai_api_key');
    if (empty($api_key)) {
        // Return fallback models if no API key is available
        return custom_ai_image_description_get_fallback_openai_models();
    }
    
    // Fetch from OpenAI API
    $response = wp_remote_get('https://api.openai.com/v1/models', array(
        'timeout' => 30,
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key
        )
    ));
    
    if (is_wp_error($response)) {
        // Return fallback models if API fails
        return custom_ai_image_description_get_fallback_openai_models();
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    if (!isset($data['data']) || !is_array($data['data'])) {
        return custom_ai_image_description_get_fallback_openai_models();
    }
    
    $vision_models = array();
    
    // Known vision-capable model patterns
    $vision_patterns = array(
        'gpt-5',           // GPT-5 series with vision (2025)
        'gpt-4.5',         // GPT-4.5 with vision (2025)
        'gpt-4.1',         // GPT-4.1 series (2025)
        'gpt-4o',
        'gpt-4-turbo',
        'gpt-4-vision',
        'chatgpt-4o',
        'o1',              // o-series reasoning models with vision
        'o3'               // o3 reasoning models with vision
    );
    
    foreach ($data['data'] as $model) {
        $model_id = $model['id'];
        $is_vision_model = false;
        
        // Check if model matches known vision patterns
        foreach ($vision_patterns as $pattern) {
            if (strpos($model_id, $pattern) !== false) {
                $is_vision_model = true;
                break;
            }
        }
        
        // Only include vision-capable models
        if ($is_vision_model) {
            // Create display name
            $display_name = $model_id;
            
            // Add descriptive labels for known models
            if (strpos($model_id, 'gpt-5') !== false) {
                $display_name .= ' (💎 Latest & Most Advanced)';
            } elseif (strpos($model_id, 'gpt-4.5') !== false) {
                $display_name .= ' (💎 Advanced Vision)';
            } elseif (strpos($model_id, 'gpt-4.1') !== false) {
                if (strpos($model_id, 'nano') !== false) {
                    $display_name .= ' (💰 Ultra Fast & Cheap)';
                } elseif (strpos($model_id, 'mini') !== false) {
                    $display_name .= ' (💰 Fast & Economical)';
                } else {
                    $display_name .= ' (Excellent Performance)';
                }
            } elseif (strpos($model_id, 'gpt-4o') !== false) {
                if (strpos($model_id, 'mini') !== false) {
                    $display_name .= ' (Fast & Cost-effective)';
                } else {
                    $display_name .= ' (Recommended)';
                }
            } elseif (strpos($model_id, 'o1') !== false || strpos($model_id, 'o3') !== false) {
                $display_name .= ' (🧠 Reasoning Model)';
            } elseif (strpos($model_id, 'gpt-4-turbo') !== false) {
                $display_name .= ' (Vision capable)';
            } elseif (strpos($model_id, 'gpt-4-vision') !== false) {
                $display_name .= ' (Legacy Vision)';
            }
            
            $vision_models[$model_id] = $display_name;
        }
    }
    
    // Sort models by preference (gpt-5 first, then 4.5, 4.1, 4o, others)
    uksort($vision_models, function($a, $b) {
        // Prioritize gpt-5 models
        if (strpos($a, 'gpt-5') !== false && strpos($b, 'gpt-5') === false) return -1;
        if (strpos($b, 'gpt-5') !== false && strpos($a, 'gpt-5') === false) return 1;

        // Then gpt-4.5
        if (strpos($a, 'gpt-4.5') !== false && strpos($b, 'gpt-4.5') === false) return -1;
        if (strpos($b, 'gpt-4.5') !== false && strpos($a, 'gpt-4.5') === false) return 1;

        // Then gpt-4.1
        if (strpos($a, 'gpt-4.1') !== false && strpos($b, 'gpt-4.1') === false) return -1;
        if (strpos($b, 'gpt-4.1') !== false && strpos($a, 'gpt-4.1') === false) return 1;

        // Then gpt-4o models
        if (strpos($a, 'gpt-4o') !== false && strpos($b, 'gpt-4o') === false) return -1;
        if (strpos($b, 'gpt-4o') !== false && strpos($a, 'gpt-4o') === false) return 1;

        // Then gpt-4-turbo
        if (strpos($a, 'gpt-4-turbo') !== false && strpos($b, 'gpt-4-turbo') === false) return -1;
        if (strpos($b, 'gpt-4-turbo') !== false && strpos($a, 'gpt-4-turbo') === false) return 1;

        return strcasecmp($a, $b);
    });
    
    // Cache for 24 hours if we got models
    if (count($vision_models) > 0) {
        set_transient('custom_ai_openai_vision_models', $vision_models, DAY_IN_SECONDS);
        return $vision_models;
    }
    
    // Return fallback if no vision models found
    return custom_ai_image_description_get_fallback_openai_models();
}

// Fallback models if OpenAI API is unavailable
function custom_ai_image_description_get_fallback_openai_models() {
    return array(
        'gpt-4.1' => 'GPT-4.1 (💎 Latest - Excellent Performance)',
        'gpt-4.1-mini' => 'GPT-4.1 Mini (💰 Fast & Economical)',
        'gpt-4o' => 'GPT-4o (Recommended - Latest vision model)',
        'gpt-4o-mini' => 'GPT-4o Mini (Fast & Cost-effective)',
        'gpt-4-turbo' => 'GPT-4 Turbo (Vision capable)',
        'gpt-4-vision-preview' => 'GPT-4 Vision Preview (Legacy)'
    );
}

function custom_ai_image_description_model_callback() {
    $model = get_option('custom_ai_image_description_model', 'claude-sonnet-4-5-latest');
    $provider = get_option('custom_ai_image_description_api_provider', 'claude');
    
    // Get Claude models dynamically (with caching)
    $claude_models = custom_ai_image_description_fetch_claude_models();
    
    // Get OpenAI models dynamically (with caching)
    $openai_models = custom_ai_image_description_fetch_openai_models();
    
    // Get OpenRouter models dynamically (with caching)
    $openrouter_models = custom_ai_image_description_fetch_openrouter_models();
    
    echo '<select name="custom_ai_image_description_model" id="model_select">';
    
    // Show Claude models when Claude provider is selected
    echo '<optgroup label="Claude Models" class="model-group" data-provider="claude" style="' . ($provider !== 'claude' ? 'display:none;' : '') . '">';
    foreach ($claude_models as $model_id => $model_name) {
        echo '<option value="' . esc_attr($model_id) . '" ' . selected($model, $model_id, false) . ' data-provider="claude">' . esc_html($model_name) . '</option>';
    }
    echo '</optgroup>';
    
    // Show OpenAI models when OpenAI provider is selected
    echo '<optgroup label="OpenAI Models" class="model-group" data-provider="openai" style="' . ($provider !== 'openai' ? 'display:none;' : '') . '">';
    foreach ($openai_models as $model_id => $model_name) {
        echo '<option value="' . esc_attr($model_id) . '" ' . selected($model, $model_id, false) . ' data-provider="openai">' . esc_html($model_name) . '</option>';
    }
    echo '</optgroup>';
    
    // Show OpenRouter models when OpenRouter provider is selected
    echo '<optgroup label="OpenRouter Models" class="model-group" data-provider="openrouter" style="' . ($provider !== 'openrouter' ? 'display:none;' : '') . '">';
    foreach ($openrouter_models as $model_id => $model_name) {
        echo '<option value="' . esc_attr($model_id) . '" ' . selected($model, $model_id, false) . ' data-provider="openrouter">' . esc_html($model_name) . '</option>';
    }
    echo '</optgroup>';
    
    echo '</select>';
    
    // Add refresh buttons for dynamic model providers
    if ($provider === 'openrouter') {
        echo ' <button type="button" id="refresh_openrouter_models" class="button button-secondary" style="margin-left: 10px;">🔄 Refresh Models</button>';
        echo '<span id="refresh_status" style="margin-left: 10px; display: none;"></span>';
    } elseif ($provider === 'openai') {
        echo ' <button type="button" id="refresh_openai_models" class="button button-secondary" style="margin-left: 10px;">🔄 Refresh Models</button>';
        echo '<span id="refresh_status" style="margin-left: 10px; display: none;"></span>';
    } elseif ($provider === 'claude') {
        echo ' <button type="button" id="refresh_claude_models" class="button button-secondary" style="margin-left: 10px;">🔄 Refresh Models</button>';
        echo '<span id="refresh_status" style="margin-left: 10px; display: none;"></span>';
    }
    
    echo '<p class="description">Select the AI model to use for generating alt text. Models vary in capability, speed, and cost.</p>';
    
    // Add provider-specific notes
    if ($provider === 'openrouter') {
        echo '<p class="description"><strong>Note:</strong> OpenRouter models are fetched automatically from their API. Click "Refresh Models" to update the list with the latest available vision-capable models.</p>';
    } elseif ($provider === 'openai') {
        echo '<p class="description"><strong>Note:</strong> OpenAI models are fetched automatically from their API. Click "Refresh Models" to update the list with the latest available vision models. GPT-4o is recommended for best performance.</p>';
    }
    
    // Add JavaScript to handle provider switching and model refresh
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#api_provider_select').on('change', function() {
            var provider = $(this).val();
            
            // Show/hide API key fields
            $('.api-key-field').hide();
            $('.api-key-field[data-provider="' + provider + '"]').show();
            
            // Show/hide model groups
            $('.model-group').hide();
            $('.model-group[data-provider="' + provider + '"]').show();
            
            // Show/hide refresh buttons
            if (provider === 'openrouter') {
                $('#refresh_openrouter_models').show();
                $('#refresh_openai_models').hide();
                $('#refresh_claude_models').hide();
            } else if (provider === 'openai') {
                $('#refresh_openai_models').show();
                $('#refresh_openrouter_models').hide();
                $('#refresh_claude_models').hide();
            } else if (provider === 'claude') {
                $('#refresh_claude_models').show();
                $('#refresh_openrouter_models').hide();
                $('#refresh_openai_models').hide();
            } else {
                $('#refresh_openrouter_models').hide();
                $('#refresh_openai_models').hide();
                $('#refresh_claude_models').hide();
            }
            
            // Select first available model for the provider if current selection is incompatible
            var currentModel = $('#model_select').val();
            var currentOption = $('#model_select option[value="' + currentModel + '"]');
            if (currentOption.attr('data-provider') !== provider) {
                $('#model_select option[data-provider="' + provider + '"]:first').prop('selected', true);
            }
        });
        
        // Refresh OpenRouter models
        $('#refresh_openrouter_models').on('click', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $status = $('#refresh_status');
            
            $button.prop('disabled', true);
            $status.show().html('Fetching models...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'caidg_refresh_openrouter_models',
                    nonce: '<?php echo wp_create_nonce('caidg_refresh_models'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('✅ Models updated! Found ' + response.data.count + ' vision models. Refreshing page...');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $status.html('❌ Error: ' + response.data);
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    $status.html('❌ Network error. Please try again.');
                    $button.prop('disabled', false);
                }
            });
        });
        
        // Refresh OpenAI models
        $('#refresh_openai_models').on('click', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $status = $('#refresh_status');
            
            $button.prop('disabled', true);
            $status.show().html('Fetching models...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'caidg_refresh_openai_models',
                    nonce: '<?php echo wp_create_nonce('caidg_refresh_models'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('✅ Models updated! Found ' + response.data.count + ' vision models. Refreshing page...');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $status.html('❌ Error: ' + response.data);
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    $status.html('❌ Network error. Please try again.');
                    $button.prop('disabled', false);
                }
            });
        });

        // Refresh Claude models
        $('#refresh_claude_models').on('click', function(e) {
            e.preventDefault();
            var $button = $(this);
            var $status = $('#refresh_status');

            $button.prop('disabled', true);
            $status.show().html('Fetching models...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'caidg_refresh_claude_models',
                    nonce: '<?php echo wp_create_nonce('caidg_refresh_models'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $status.html('✅ Models updated! Found ' + response.data.count + ' models. Refreshing page...');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        $status.html('❌ Error: ' + response.data);
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    $status.html('❌ Network error. Please try again.');
                    $button.prop('disabled', false);
                }
            });
        });

        // Trigger change on page load to ensure correct visibility
        $('#api_provider_select').trigger('change');
    });
    </script>
    <?php
}

// Get model pricing information
function custom_ai_image_description_get_model_pricing($model) {
    // Claude models pricing (per million tokens: input/output)
    $claude_pricing = array(
        'claude-sonnet-4-5' => array(3, 15),
        'claude-opus-4-5' => array(5, 25),
        'claude-haiku-4-5' => array(1, 5),
        'claude-opus-4-1' => array(15, 75),
        'claude-opus-4' => array(15, 75),
        'claude-sonnet-4' => array(3, 15),
        'claude-3-7-sonnet' => array(3, 15),
        'claude-3-5-haiku' => array(0.8, 4),
        'claude-3-haiku' => array(0.25, 1.25),
    );

    // OpenAI models pricing (per million tokens: input/output)
    $openai_pricing = array(
        'gpt-4o' => array(2.5, 10),
        'gpt-4o-mini' => array(0.15, 0.6),
        'gpt-4-turbo' => array(10, 30),
        'gpt-4.1' => array(2, 8),
        'gpt-4.1-mini' => array(0.15, 0.6),
        'gpt-4.1-nano' => array(0.07, 0.28),
        'gpt-4.5' => array(2.5, 10),
        'o1' => array(15, 60),
        'o3' => array(15, 60),
    );

    // Check if it's a Claude model
    if (strpos($model, 'claude-') === 0) {
        foreach ($claude_pricing as $model_pattern => $pricing) {
            if (strpos($model, $model_pattern) !== false) {
                return $pricing;
            }
        }
    }

    // Check if it's an OpenAI model
    foreach ($openai_pricing as $model_pattern => $pricing) {
        if (strpos($model, $model_pattern) !== false) {
            return $pricing;
        }
    }

    // Default fallback pricing (moderate cost assumption)
    return array(3, 15);
}

// Estimate cost for bulk operations
function custom_ai_image_description_estimate_cost($image_count, $model) {
    $pricing = custom_ai_image_description_get_model_pricing($model);

    // Estimate: ~1000 input tokens per image (image data + prompt)
    // Estimate: ~100 output tokens per image (alt text)
    $input_tokens_per_image = 1000;
    $output_tokens_per_image = 100;

    $total_input_tokens = $image_count * $input_tokens_per_image;
    $total_output_tokens = $image_count * $output_tokens_per_image;

    // Calculate cost (pricing is per million tokens)
    $input_cost = ($total_input_tokens / 1000000) * $pricing[0];
    $output_cost = ($total_output_tokens / 1000000) * $pricing[1];
    $total_cost = $input_cost + $output_cost;

    return '$' . number_format($total_cost, 2);
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

function custom_ai_image_description_compress_callback() {
    $compress = get_option('custom_ai_image_description_compress_images', false);
    echo '<input type="checkbox" name="custom_ai_image_description_compress_images" value="1" ' . checked(1, $compress, false) . '>';
    echo '<label for="custom_ai_image_description_compress_images">Compress images before sending to API (reduces costs, recommended for images >1024px)</label>';
}

function custom_ai_image_description_skip_existing_callback() {
    $skip_existing = get_option('custom_ai_image_description_skip_existing', true);
    echo '<input type="checkbox" name="custom_ai_image_description_skip_existing" value="1" ' . checked(1, $skip_existing, false) . '>';
    echo '<label for="custom_ai_image_description_skip_existing">Skip images that already have alt text (recommended for bulk operations)</label>';
}

// Helper function to compress images if enabled
function custom_ai_image_description_maybe_compress_image($image_data, $mime_type) {
    $compress_enabled = get_option('custom_ai_image_description_compress_images', false);

    if (!$compress_enabled) {
        return $image_data;
    }

    $image_info = getimagesizefromstring($image_data);
    if ($image_info === false) {
        return $image_data;
    }

    $width = $image_info[0];
    $height = $image_info[1];

    // Only compress if dimensions exceed 1024px on either side
    if ($width <= 1024 && $height <= 1024) {
        return $image_data;
    }

    // Save to temp file
    $temp_file = wp_tempnam();
    file_put_contents($temp_file, $image_data);

    // Use WordPress image editor to resize
    $editor = wp_get_image_editor($temp_file);

    if (is_wp_error($editor)) {
        unlink($temp_file);
        return $image_data;
    }

    // Calculate new dimensions maintaining aspect ratio
    $max_size = 1024;
    if ($width > $height) {
        $new_width = $max_size;
        $new_height = intval($height * ($max_size / $width));
    } else {
        $new_height = $max_size;
        $new_width = intval($width * ($max_size / $height));
    }

    $editor->resize($new_width, $new_height, false);

    // Generate the compressed image
    $compressed_image = $editor->get_image();

    if (is_wp_error($compressed_image)) {
        unlink($temp_file);
        return $image_data;
    }

    // Save the compressed image to temp file
    $result = $editor->save($temp_file);

    // Clean up temp file and return compressed data
    if (!is_wp_error($result)) {
        $compressed_data = file_get_contents($temp_file);
        unlink($temp_file);
        return $compressed_data !== false ? $compressed_data : $image_data;
    }

    unlink($temp_file);
    return $image_data;
}

// Generate alt text using selected API provider
function custom_ai_image_description_generate($image_url, $image_title = '') {
    $provider = get_option('custom_ai_image_description_api_provider', 'claude');
    
    if ($provider === 'openrouter') {
        return custom_ai_image_description_generate_openrouter($image_url, $image_title);
    } elseif ($provider === 'openai') {
        return custom_ai_image_description_generate_openai($image_url, $image_title);
    } else {
        return custom_ai_image_description_generate_claude($image_url, $image_title);
    }
}

// Generate alt text using Claude API
function custom_ai_image_description_generate_claude($image_url, $image_title = '') {
    $api_key = get_option('custom_ai_image_description_claude_api_key');
    $model = get_option('custom_ai_image_description_model', 'claude-sonnet-4-5-latest');
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

    // Apply compression if enabled and image is large
    $image_content = custom_ai_image_description_maybe_compress_image($image_content, $mime_type);

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

// Generate alt text using OpenRouter API
function custom_ai_image_description_generate_openrouter($image_url, $image_title = '') {
    $api_key = get_option('custom_ai_image_description_openrouter_api_key');
    $model = get_option('custom_ai_image_description_model', 'anthropic/claude-3.5-sonnet');
    $prompt = get_option('custom_ai_image_description_prompt', 'Generate a brief alt text description for this image:');
    $language = get_option('custom_ai_image_description_language', 'en');
    $max_tokens = intval(get_option('custom_ai_image_description_max_tokens', 200));
    $debug_mode = get_option('custom_ai_image_description_debug_mode', false);

    if (empty($api_key)) {
        error_log('Custom AI Image Description Generator Error: OpenRouter API key is missing');
        return new WP_Error('missing_api_key', 'OpenRouter API key is missing');
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

    // Apply compression if enabled and image is large
    $image_content = custom_ai_image_description_maybe_compress_image($image_content, $mime_type);

    $base64_image = base64_encode($image_content);
    
    if ($debug_mode) {
        error_log("Image MIME type detected: " . $mime_type);
        error_log("Image size: " . strlen($image_content) . " bytes");
        error_log("Using OpenRouter model: " . $model);
    }

    // Prepare the message for OpenRouter
    $system_prompt = "You are an AI assistant that generates concise and accurate alt text descriptions for images in $language. Focus on key visual elements and provide descriptions that enhance accessibility. Be specific but concise.";
    
    $user_message = $prompt;
    if (!empty($image_title)) {
        $user_message .= " The image title is: \"$image_title\".";
    }
    $user_message .= " Please provide a clear, concise description suitable for alt text.";
    
    // OpenRouter uses OpenAI-compatible format
    $messages = [
        [
            "role" => "system",
            "content" => $system_prompt
        ],
        [
            "role" => "user",
            "content" => [
                [
                    "type" => "text",
                    "text" => $user_message
                ],
                [
                    "type" => "image_url",
                    "image_url" => [
                        "url" => "data:$mime_type;base64,$base64_image"
                    ]
                ]
            ]
        ]
    ];

    $request_body = [
        'model' => $model,
        'messages' => $messages,
        'max_tokens' => $max_tokens,
        'temperature' => 0.3
    ];

    $args = [
        'timeout' => 60,
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => get_site_url(), // Optional but recommended by OpenRouter
            'X-Title' => get_bloginfo('name') // Optional site name for OpenRouter analytics
        ],
        'body' => json_encode($request_body)
    ];

    $response = wp_remote_post('https://openrouter.ai/api/v1/chat/completions', $args);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log("Custom AI Image Description Generator Error: Error connecting to OpenRouter API: $error_message");
        return new WP_Error('api_error', 'Error connecting to OpenRouter API: ' . $error_message);
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $body = json_decode($response_body, true);
    
    if ($debug_mode) {
        error_log('OpenRouter API Response Code: ' . $response_code);
        error_log('OpenRouter API Response: ' . print_r($body, true));
    }
    
    if ($response_code !== 200) {
        $error_message = isset($body['error']['message']) ? $body['error']['message'] : wp_remote_retrieve_response_message($response);
        error_log("Custom AI Image Description Generator Error: OpenRouter API returned status $response_code: $error_message");
        
        if ($debug_mode) {
            error_log("Request body was: " . json_encode($request_body));
        }
        
        return new WP_Error('api_error', "OpenRouter API error: $error_message");
    }

    // OpenRouter returns OpenAI-compatible response format
    if (isset($body['choices'][0]['message']['content'])) {
        return trim($body['choices'][0]['message']['content']);
    }

    error_log('Custom AI Image Description Generator Error: Invalid response structure from OpenRouter API');
    return new WP_Error('invalid_response', 'Invalid response from OpenRouter API');
}

// Generate alt text using OpenAI API
function custom_ai_image_description_generate_openai($image_url, $image_title = '') {
    $api_key = get_option('custom_ai_image_description_openai_api_key');
    $model = get_option('custom_ai_image_description_model', 'gpt-4o');
    $prompt = get_option('custom_ai_image_description_prompt', 'Generate a brief alt text description for this image:');
    $language = get_option('custom_ai_image_description_language', 'en');
    $max_tokens = intval(get_option('custom_ai_image_description_max_tokens', 200));
    $debug_mode = get_option('custom_ai_image_description_debug_mode', false);

    if (empty($api_key)) {
        error_log('Custom AI Image Description Generator Error: OpenAI API key is missing');
        return new WP_Error('missing_api_key', 'OpenAI API key is missing');
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

    // Apply compression if enabled and image is large
    $image_content = custom_ai_image_description_maybe_compress_image($image_content, $mime_type);

    $base64_image = base64_encode($image_content);
    
    if ($debug_mode) {
        error_log("Image MIME type detected: " . $mime_type);
        error_log("Image size: " . strlen($image_content) . " bytes");
        error_log("Using OpenAI model: " . $model);
    }

    // Prepare the message for OpenAI
    $system_prompt = "You are an AI assistant that generates concise and accurate alt text descriptions for images in $language. Focus on key visual elements and provide descriptions that enhance accessibility. Be specific but concise.";
    
    $user_message = $prompt;
    if (!empty($image_title)) {
        $user_message .= " The image title is: \"$image_title\".";
    }
    $user_message .= " Please provide a clear, concise description suitable for alt text.";
    
    $messages = [
        [
            "role" => "system",
            "content" => $system_prompt
        ],
        [
            "role" => "user",
            "content" => [
                [
                    "type" => "text",
                    "text" => $user_message
                ],
                [
                    "type" => "image_url",
                    "image_url" => [
                        "url" => "data:$mime_type;base64,$base64_image"
                    ]
                ]
            ]
        ]
    ];

    $request_body = [
        'model' => $model,
        'messages' => $messages,
        'max_tokens' => $max_tokens,
        'temperature' => 0.3
    ];

    $args = [
        'timeout' => 60,
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode($request_body)
    ];

    $response = wp_remote_post('https://api.openai.com/v1/chat/completions', $args);

    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log("Custom AI Image Description Generator Error: Error connecting to OpenAI API: $error_message");
        return new WP_Error('api_error', 'Error connecting to OpenAI API: ' . $error_message);
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $body = json_decode($response_body, true);
    
    if ($debug_mode) {
        error_log('OpenAI API Response Code: ' . $response_code);
        error_log('OpenAI API Response: ' . print_r($body, true));
    }
    
    if ($response_code !== 200) {
        $error_message = isset($body['error']['message']) ? $body['error']['message'] : wp_remote_retrieve_response_message($response);
        error_log("Custom AI Image Description Generator Error: OpenAI API returned status $response_code: $error_message");
        
        if ($debug_mode) {
            error_log("Request body was: " . json_encode($request_body));
        }
        
        return new WP_Error('api_error', "OpenAI API error: $error_message");
    }

    if (isset($body['choices'][0]['message']['content'])) {
        return trim($body['choices'][0]['message']['content']);
    }

    error_log('Custom AI Image Description Generator Error: Invalid response structure from OpenAI API');
    return new WP_Error('invalid_response', 'Invalid response from OpenAI API');
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
add_action('wp_ajax_caidg_refresh_openrouter_models', 'custom_ai_ajax_refresh_openrouter_models');
add_action('wp_ajax_caidg_refresh_openai_models', 'custom_ai_ajax_refresh_openai_models');
add_action('wp_ajax_caidg_refresh_claude_models', 'custom_ai_ajax_refresh_claude_models');
add_action('wp_ajax_caidg_estimate_cost', 'custom_ai_ajax_estimate_cost');
add_action('wp_ajax_caidg_check_alt_text', 'custom_ai_ajax_check_alt_text');

// AJAX handler for refreshing OpenRouter models
function custom_ai_ajax_refresh_openrouter_models() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'caidg_refresh_models')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    // Clear the cache
    delete_transient('custom_ai_openrouter_vision_models');
    
    // Fetch fresh models
    $models = custom_ai_image_description_fetch_openrouter_models();
    
    if ($models && count($models) > 0) {
        wp_send_json_success(array(
            'count' => count($models),
            'models' => $models
        ));
    } else {
        wp_send_json_error('Failed to fetch models from OpenRouter API');
    }
}

// AJAX handler for refreshing OpenAI models
function custom_ai_ajax_refresh_openai_models() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'caidg_refresh_models')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    // Clear the cache
    delete_transient('custom_ai_openai_vision_models');
    
    // Fetch fresh models
    $models = custom_ai_image_description_fetch_openai_models();
    
    if ($models && count($models) > 0) {
        wp_send_json_success(array(
            'count' => count($models),
            'models' => $models
        ));
    } else {
        wp_send_json_error('Failed to fetch models from OpenAI API');
    }
}

// AJAX handler for refreshing Claude models
function custom_ai_ajax_refresh_claude_models() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'caidg_refresh_models')) {
        wp_send_json_error('Security check failed');
        return;
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }

    // Clear the cache
    delete_transient('custom_ai_claude_vision_models');

    // Fetch fresh models
    $models = custom_ai_image_description_fetch_claude_models();

    if ($models && count($models) > 0) {
        wp_send_json_success(array(
            'count' => count($models),
            'models' => $models
        ));
    } else {
        wp_send_json_error('Failed to fetch models from Anthropic API');
    }
}

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

// AJAX handler for cost estimation
function custom_ai_ajax_estimate_cost() {
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

    $image_count = intval($_POST['image_count']);
    if (!$image_count || $image_count < 1) {
        wp_send_json_error('Invalid image count');
        return;
    }

    $model = get_option('custom_ai_image_description_model', 'claude-sonnet-4-5-latest');
    $estimated_cost = custom_ai_image_description_estimate_cost($image_count, $model);

    wp_send_json_success([
        'image_count' => $image_count,
        'model' => $model,
        'estimated_cost' => $estimated_cost
    ]);
}

// AJAX handler for checking existing alt text
function custom_ai_ajax_check_alt_text() {
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

    $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
    $has_alt_text = !empty($alt_text);

    wp_send_json_success([
        'attachment_id' => $attachment_id,
        'has_alt_text' => $has_alt_text,
        'alt_text' => $has_alt_text ? $alt_text : ''
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
            skip_existing: " . (get_option('custom_ai_image_description_skip_existing', true) ? 'true' : 'false') . ",
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

                // Fetch cost estimate before proceeding
                $.ajax({
                    url: caidg_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'caidg_estimate_cost',
                        image_count: attachmentIds.length,
                        nonce: caidg_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            var confirmed = confirm(
                                'Processing ' + attachmentIds.length + ' images with model: ' + response.data.model + '.\\n\\n' +
                                'Estimated cost: ' + response.data.estimated_cost + '\\n\\n' +
                                'Continue?'
                            );

                            if (confirmed) {
                                startBulkProcessing(attachmentIds);
                            }
                        } else {
                            alert('Could not estimate cost: ' + response.data);
                            if (confirm('Proceed anyway?')) {
                                startBulkProcessing(attachmentIds);
                            }
                        }
                    },
                    error: function() {
                        // If AJAX fails, ask user if they want to proceed without estimate
                        if (confirm('Could not fetch cost estimate. Proceed with processing ' + attachmentIds.length + ' images?')) {
                            startBulkProcessing(attachmentIds);
                        }
                    }
                });

                return false;
            }
        });

        function startBulkProcessing(attachmentIds) {
            // Create progress container
            var progressHtml = '<div id=\"caidg-progress\" style=\"margin: 20px 0; padding: 20px; background: #fff; border: 1px solid #c3c4c7; box-shadow: 0 1px 1px rgba(0,0,0,.04);\">' +
                '<h3>Generating Alt Text</h3>' +
                '<div style=\"margin: 10px 0;\">Processing <span id=\"caidg-current\">1</span> of <span id=\"caidg-total\">' + attachmentIds.length + '</span> images...</div>' +
                '<div style=\"background: #f0f0f1; height: 24px; border-radius: 3px; overflow: hidden;\">' +
                '<div id=\"caidg-progress-bar\" style=\"background: #2271b1; height: 100%; width: 0%; transition: width 0.3s; border-radius: 3px;\"></div>' +
                '</div>' +
                '<div id=\"caidg-status\" style=\"margin-top: 10px; color: #50575e;\"></div>' +
                '<div id=\"caidg-summary\" style=\"margin-top: 10px; font-size: 0.9em;\"></div>' +
                '</div>';

            $('.tablenav.top').after(progressHtml);

            // Initialize counters
            var processed = 0;
            var skipped = 0;
            var errors = 0;

            // Process images sequentially
            processImages(attachmentIds, 0, processed, skipped, errors);

                function processImages(ids, index, processed, skipped, errors) {
                    if (index >= ids.length) {
                        // All images processed
                        var summaryText = '<strong>Results:</strong> ';
                        summaryText += '<span style=\"color: #00a32a;\">' + processed + ' processed</span>';
                        if (skipped > 0) {
                            summaryText += ', <span style=\"color: #646970;\">' + skipped + ' skipped</span>';
                        }
                        if (errors > 0) {
                            summaryText += ', <span style=\"color: #d63638;\">' + errors + ' errors</span>';
                        }

                        $('#caidg-status').html('<strong style=\"color: #00a32a;\">✓ Processing complete!</strong>');
                        $('#caidg-summary').html(summaryText);

                        setTimeout(function() {
                            $('#caidg-progress').fadeOut(function() {
                                $(this).remove();
                                location.reload(); // Reload to show updated alt text
                            });
                        }, 3000);
                        return;
                    }

                    var progress = ((index + 1) / ids.length) * 100;
                    $('#caidg-current').text(index + 1);
                    $('#caidg-progress-bar').css('width', progress + '%');
                    $('#caidg-status').text('Processing image #' + ids[index] + '...');

                    // Check if we should skip images with existing alt text
                    if (caidg_ajax.skip_existing) {
                        $.ajax({
                            url: caidg_ajax.ajax_url,
                            type: 'POST',
                            data: {
                                action: 'caidg_check_alt_text',
                                attachment_id: ids[index],
                                nonce: caidg_ajax.nonce
                            },
                            success: function(checkResponse) {
                                if (checkResponse.success && checkResponse.data.has_alt_text) {
                                    // Skip this image
                                    skipped++;
                                    console.log('Skipping image #' + ids[index] + ' (already has alt text)');
                                    $('#caidg-status').text('Skipped image #' + ids[index] + ' (already has alt text)');

                                    // Update summary
                                    var summaryText = '<strong>Progress:</strong> ';
                                    summaryText += '<span style=\"color: #00a32a;\">' + processed + ' processed</span>';
                                    if (skipped > 0) {
                                        summaryText += ', <span style=\"color: #646970;\">' + skipped + ' skipped</span>';
                                    }
                                    if (errors > 0) {
                                        summaryText += ', <span style=\"color: #d63638;\">' + errors + ' errors</span>';
                                    }
                                    $('#caidg-summary').html(summaryText);

                                    // Continue with next image
                                    setTimeout(function() {
                                        processImages(ids, index + 1, processed, skipped, errors);
                                    }, 100);
                                } else {
                                    // Generate alt text for this image
                                    generateAltText(ids, index, processed, skipped, errors);
                                }
                            },
                            error: function() {
                                // If check fails, proceed with generation
                                generateAltText(ids, index, processed, skipped, errors);
                            }
                        });
                    } else {
                        // Skip check disabled, generate directly
                        generateAltText(ids, index, processed, skipped, errors);
                    }
                }

                function generateAltText(ids, index, processed, skipped, errors) {
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
                                processed++;
                                console.log('Generated alt text for #' + ids[index]);
                            } else {
                                errors++;
                                console.error('Error for #' + ids[index] + ': ' + response.data);
                                $('#caidg-status').append('<div style=\"color: #d63638;\">Error for image #' + ids[index] + ': ' + response.data + '</div>');
                            }

                            // Update summary
                            var summaryText = '<strong>Progress:</strong> ';
                            summaryText += '<span style=\"color: #00a32a;\">' + processed + ' processed</span>';
                            if (skipped > 0) {
                                summaryText += ', <span style=\"color: #646970;\">' + skipped + ' skipped</span>';
                            }
                            if (errors > 0) {
                                summaryText += ', <span style=\"color: #d63638;\">' + errors + ' errors</span>';
                            }
                            $('#caidg-summary').html(summaryText);

                            // Continue with next image
                            setTimeout(function() {
                                processImages(ids, index + 1, processed, skipped, errors);
                            }, 500); // Small delay between requests
                        },
                        error: function(xhr, status, error) {
                            errors++;
                            console.error('Network error for #' + ids[index]);
                            $('#caidg-status').append('<div style=\"color: #d63638;\">Network error for image #' + ids[index] + '</div>');

                            // Update summary
                            var summaryText = '<strong>Progress:</strong> ';
                            summaryText += '<span style=\"color: #00a32a;\">' + processed + ' processed</span>';
                            if (skipped > 0) {
                                summaryText += ', <span style=\"color: #646970;\">' + skipped + ' skipped</span>';
                            }
                            if (errors > 0) {
                                summaryText += ', <span style=\"color: #d63638;\">' + errors + ' errors</span>';
                            }
                            $('#caidg-summary').html(summaryText);

                            // Continue despite error
                            setTimeout(function() {
                                processImages(ids, index + 1, processed, skipped, errors);
                            }, 500);
                        }
                    });
                }
            }
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