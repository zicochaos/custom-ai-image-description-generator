<?php
/**
 * Test OpenRouter Models API
 * 
 * This script fetches and displays all vision-capable models from OpenRouter.
 * Run it from command line: php test-openrouter-models.php
 */

function fetch_openrouter_models() {
    echo "Fetching models from OpenRouter API...\n";
    echo "=====================================\n\n";
    
    // No API key needed for models endpoint
    $ch = curl_init('https://openrouter.ai/api/v1/models');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response === false || $http_code !== 200) {
        echo "Error fetching models. HTTP Code: $http_code\n";
        return false;
    }
    
    $data = json_decode($response, true);
    if (!isset($data['data']) || !is_array($data['data'])) {
        echo "Unexpected response format\n";
        return false;
    }
    
    echo "Total models found: " . count($data['data']) . "\n\n";
    
    // Filter for vision-capable models
    $vision_models = [];
    foreach ($data['data'] as $model) {
        // Check if model supports image input
        $has_vision = false;
        
        // Check architecture.modality
        if (isset($model['architecture']['modality']) && 
            strpos($model['architecture']['modality'], 'image') !== false) {
            $has_vision = true;
        }
        
        // Check architecture.input_modalities array
        if (isset($model['architecture']['input_modalities']) && 
            is_array($model['architecture']['input_modalities']) &&
            in_array('image', $model['architecture']['input_modalities'])) {
            $has_vision = true;
        }
        
        // Some models might specify vision in the name or description
        if (!$has_vision) {
            $name_desc = strtolower($model['name'] ?? '') . ' ' . strtolower($model['description'] ?? '');
            if (strpos($name_desc, 'vision') !== false || 
                strpos($name_desc, 'multimodal') !== false ||
                strpos($name_desc, 'image') !== false) {
                // Double check if it's actually vision capable (not just mentioning it)
                if (isset($model['architecture']['modality']) || isset($model['architecture']['input_modalities'])) {
                    // Already checked above, skip
                } else {
                    // Might be vision capable but not properly tagged
                    $has_vision = true;
                }
            }
        }
        
        if ($has_vision) {
            $vision_models[] = $model;
        }
    }
    
    echo "Vision-capable models found: " . count($vision_models) . "\n";
    echo "=====================================\n\n";
    
    // Sort by popularity/usage (if available) or alphabetically
    usort($vision_models, function($a, $b) {
        // You could sort by various criteria
        return strcasecmp($a['id'], $b['id']);
    });
    
    // Display vision models
    foreach ($vision_models as $model) {
        echo "ID: " . $model['id'] . "\n";
        echo "Name: " . ($model['name'] ?? 'N/A') . "\n";
        
        // Show pricing
        if (isset($model['pricing'])) {
            $prompt_price = $model['pricing']['prompt'] ?? '0';
            $completion_price = $model['pricing']['completion'] ?? '0';
            $image_price = $model['pricing']['image'] ?? '0';
            
            // Convert to readable format (price per 1M tokens)
            $prompt_cost = floatval($prompt_price) * 1000000;
            $completion_cost = floatval($completion_price) * 1000000;
            $image_cost = floatval($image_price);
            
            echo "Pricing: ";
            echo "\$" . number_format($prompt_cost, 2) . "/1M prompt tokens, ";
            echo "\$" . number_format($completion_cost, 2) . "/1M completion tokens";
            if ($image_cost > 0) {
                echo ", \$" . number_format($image_cost, 4) . "/image";
            }
            echo "\n";
        }
        
        // Show context length
        if (isset($model['context_length'])) {
            echo "Context: " . number_format($model['context_length']) . " tokens\n";
        }
        
        // Show modalities
        if (isset($model['architecture'])) {
            if (isset($model['architecture']['modality'])) {
                echo "Modality: " . $model['architecture']['modality'] . "\n";
            }
            if (isset($model['architecture']['input_modalities'])) {
                echo "Inputs: " . implode(', ', $model['architecture']['input_modalities']) . "\n";
            }
        }
        
        echo "---\n\n";
    }
    
    // Generate PHP array for the plugin
    echo "\n=====================================\n";
    echo "PHP Array for Plugin Integration:\n";
    echo "=====================================\n\n";
    
    echo "\$openrouter_vision_models = array(\n";
    foreach ($vision_models as $model) {
        $id = $model['id'];
        $name = $model['name'] ?? $id;
        
        // Extract provider and model name
        $parts = explode('/', $id, 2);
        $provider = ucfirst($parts[0] ?? 'Unknown');
        $model_name = $parts[1] ?? $id;
        
        // Create display name
        $display_name = $name;
        if (!empty($model['pricing'])) {
            $prompt_cost = floatval($model['pricing']['prompt'] ?? 0) * 1000000;
            if ($prompt_cost == 0) {
                $display_name .= ' (Free)';
            } else if ($prompt_cost < 0.5) {
                $display_name .= ' (Cheap)';
            } else if ($prompt_cost > 5) {
                $display_name .= ' (Premium)';
            }
        }
        
        echo "    '" . addslashes($id) . "' => '" . addslashes($display_name) . "',\n";
    }
    echo ");\n";
    
    return $vision_models;
}

// Run the test
$models = fetch_openrouter_models();

if ($models) {
    echo "\n✅ Successfully fetched " . count($models) . " vision-capable models from OpenRouter!\n";
} else {
    echo "\n❌ Failed to fetch models from OpenRouter.\n";
}