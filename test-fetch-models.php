<?php
/**
 * Test fetching vision models from OpenRouter API
 * Run: php test-fetch-models.php
 */

echo "Testing OpenRouter Vision Models Fetch\n";
echo "======================================\n\n";

// Make the API call
$ch = curl_init('https://openrouter.ai/api/v1/models');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $http_code !== 200) {
    echo "❌ Error fetching models. HTTP Code: $http_code\n";
    exit(1);
}

$data = json_decode($response, true);
if (!isset($data['data']) || !is_array($data['data'])) {
    echo "❌ Unexpected response format\n";
    exit(1);
}

echo "✅ Successfully fetched " . count($data['data']) . " total models\n\n";

// Filter vision models
$vision_models = array();
foreach ($data['data'] as $model) {
    $has_vision = false;
    
    if (isset($model['architecture']['input_modalities']) && 
        is_array($model['architecture']['input_modalities']) &&
        in_array('image', $model['architecture']['input_modalities'])) {
        $has_vision = true;
    }
    
    if (!$has_vision && isset($model['architecture']['modality']) && 
        strpos($model['architecture']['modality'], 'image') !== false) {
        $has_vision = true;
    }
    
    if ($has_vision) {
        $vision_models[] = $model;
    }
}

echo "✅ Found " . count($vision_models) . " vision-capable models\n\n";

// Show top 10 models
echo "Top Vision Models:\n";
echo "------------------\n";
$count = 0;
foreach ($vision_models as $model) {
    if ($count >= 10) break;
    
    $id = $model['id'];
    $name = $model['name'] ?? $id;
    
    // Add pricing info
    if (isset($model['pricing'])) {
        $prompt_cost = floatval($model['pricing']['prompt'] ?? 0) * 1000000;
        if ($prompt_cost == 0) {
            $pricing = "(Free)";
        } else {
            $pricing = "($" . number_format($prompt_cost, 2) . "/1M tokens)";
        }
    } else {
        $pricing = "";
    }
    
    echo ($count + 1) . ". $id - $name $pricing\n";
    $count++;
}

echo "\n✅ Test completed successfully!\n";