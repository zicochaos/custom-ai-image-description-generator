<?php
/**
 * Test OpenRouter API Integration
 * 
 * This standalone script tests the OpenRouter API integration.
 * Run it from command line: php test-openrouter.php
 */

// Configuration - Replace with your actual API key
$OPENROUTER_API_KEY = 'YOUR_API_KEY_HERE';
$MODEL = 'anthropic/claude-3.5-sonnet'; // You can change this to test different models

// Test image URL (using a public image)
$TEST_IMAGE_URL = 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/PNG_transparency_demonstration_1.png/280px-PNG_transparency_demonstration_1.png';

function test_openrouter_api($api_key, $model, $image_url) {
    echo "Testing OpenRouter API Integration\n";
    echo "===================================\n";
    echo "Model: $model\n";
    echo "Image URL: $image_url\n\n";
    
    // Get image content
    $image_content = file_get_contents($image_url);
    if ($image_content === false) {
        echo "Error: Failed to fetch image content\n";
        return false;
    }
    
    // Detect image type
    $image_info = getimagesizefromstring($image_content);
    if ($image_info === false) {
        echo "Error: Invalid image format\n";
        return false;
    }
    
    $mime_type = $image_info['mime'];
    $base64_image = base64_encode($image_content);
    
    echo "Image MIME type: $mime_type\n";
    echo "Image size: " . strlen($image_content) . " bytes\n\n";
    
    // Prepare the request
    $system_prompt = "You are an AI assistant that generates concise and accurate alt text descriptions for images. Focus on key visual elements and provide descriptions that enhance accessibility.";
    
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
                    "text" => "Generate a brief alt text description for this image:"
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
        'max_tokens' => 200,
        'temperature' => 0.3
    ];
    
    // Make the API request
    $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json',
        'HTTP-Referer: http://localhost',
        'X-Title: Test Script'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_body));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    
    echo "Sending request to OpenRouter API...\n";
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Response Code: $http_code\n\n";
    
    if ($response === false) {
        echo "Error: Failed to connect to OpenRouter API\n";
        return false;
    }
    
    $body = json_decode($response, true);
    
    if ($http_code !== 200) {
        echo "Error Response:\n";
        echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
        return false;
    }
    
    if (isset($body['choices'][0]['message']['content'])) {
        $alt_text = trim($body['choices'][0]['message']['content']);
        echo "Success! Generated Alt Text:\n";
        echo "\"$alt_text\"\n\n";
        
        if (isset($body['usage'])) {
            echo "Token Usage:\n";
            echo "- Prompt tokens: " . $body['usage']['prompt_tokens'] . "\n";
            echo "- Completion tokens: " . $body['usage']['completion_tokens'] . "\n";
            echo "- Total tokens: " . $body['usage']['total_tokens'] . "\n";
        }
        
        return true;
    } else {
        echo "Error: Invalid response structure\n";
        echo json_encode($body, JSON_PRETTY_PRINT) . "\n";
        return false;
    }
}

// Test different models
$models_to_test = [
    'anthropic/claude-3.5-sonnet' => 'Claude 3.5 Sonnet',
    'openai/gpt-4o-mini' => 'GPT-4o Mini',
    'google/gemini-flash-1.5' => 'Gemini Flash 1.5'
];

echo "\n";
echo "========================================\n";
echo "     OpenRouter API Integration Test    \n";
echo "========================================\n\n";

if ($OPENROUTER_API_KEY === 'YOUR_API_KEY_HERE') {
    echo "ERROR: Please set your OpenRouter API key in this file first!\n";
    echo "Get your API key from: https://openrouter.ai/keys\n";
    exit(1);
}

// Test with the configured model
$success = test_openrouter_api($OPENROUTER_API_KEY, $MODEL, $TEST_IMAGE_URL);

if ($success) {
    echo "\n✅ OpenRouter API integration test PASSED!\n";
} else {
    echo "\n❌ OpenRouter API integration test FAILED!\n";
}

echo "\nTo test other models, modify the \$MODEL variable in this script.\n";
echo "Available models:\n";
foreach ($models_to_test as $model_id => $model_name) {
    echo "  - $model_id ($model_name)\n";
}