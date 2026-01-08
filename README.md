# Custom AI Image Description Generator

A powerful WordPress plugin that automatically generates accessible alt text for images using multiple AI providers: Claude API, OpenAI API, and OpenRouter.

![WordPress Version](https://img.shields.io/badge/WordPress-5.0%2B-blue)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple)
![Plugin Version](https://img.shields.io/badge/Version-2.6-green)
![License](https://img.shields.io/badge/License-GPL%20v2-red)

## Features

### Core Functionality

- **Automatic Alt Text Generation** - Generates alt text automatically when images are uploaded
- **Bulk Processing** - Process multiple existing images at once with progress tracking
- **Post/Page Refresh** - Update alt text in existing post/page content
- **AJAX Operations** - Smooth, no-reload experience with real-time progress bars
- **Smart Retry Logic** - Automatic retry with exponential backoff for failed requests

### Technical Features

- **Multi-Format Support** - Automatically detects JPEG, PNG, GIF, and WebP formats
- **Multi-Language** - Generate alt text in any language
- **Debug Mode** - Detailed logging for troubleshooting
- **Efficient Processing** - Sequential processing to avoid API rate limits
- **Secure** - Nonce verification and capability checks on all operations

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- One or more API keys:
  - Anthropic Claude API key, or
  - OpenAI API key (v2.4+), or
  - OpenRouter API key (access to 90+ models)
- Active internet connection

## Installation

1. **Download the Plugin**
   - Download `custom-ai-image-description-generator.php` and helper files

2. **Upload to WordPress**
   - Create folder `/wp-content/plugins/custom-ai-image-description-generator/`
   - Upload all plugin files to this folder

3. **Activate the Plugin**
   - Go to WordPress Admin > Plugins
   - Find "Custom AI Image Description Generator"
   - Click "Activate"

4. **Configure Settings**
   - Go to Settings > Custom AI Image Description
   - Select your preferred API provider (Claude, OpenAI, or OpenRouter)
   - Enter the corresponding API key
   - Select your preferred model
   - Configure other options as needed

## Configuration

### Getting API Keys

#### Claude API Key

1. Visit [console.anthropic.com](https://console.anthropic.com/)
2. Sign up or log in to your account
3. Navigate to API Keys section
4. Create a new API key
5. Copy the key (starts with `sk-ant-api`)

#### OpenAI API Key (v2.4+)

1. Visit [platform.openai.com/api-keys](https://platform.openai.com/api-keys)
2. Sign up or log in to your account
3. Click "Create new secret key"
4. Name your key and create it
5. Copy the key (starts with `sk-`)

#### OpenRouter API Key

1. Visit [openrouter.ai/keys](https://openrouter.ai/keys)
2. Sign up or log in to your account
3. Click "Create Key"
4. Copy the key (starts with `sk-or-`)

### Plugin Settings

| Setting           | Description                          | Default                                                 | Range/Options                                             |
| ----------------- | ------------------------------------ | ------------------------------------------------------- | --------------------------------------------------------- |
| **API Provider**  | Choose your AI provider              | claude                                                  | claude / openai / openrouter                              |
| **API Key**       | Your provider API key                | Required                                                | Claude: `sk-ant-api`, OpenAI: `sk-`, OpenRouter: `sk-or-` |
| **Model**         | AI model to use                      | Provider-dependent                                      | See models table below                                    |
| **Custom Prompt** | Instructions for alt text generation | "Generate a brief alt text description for this image:" | Any text                                                  |
| **Language**      | Output language for alt text         | en                                                      | Any language code (en, es, fr, de, it, pt, ja, zh, etc.)  |
| **Max Tokens**    | Maximum length of generated alt text | 200                                                     | 50-500 tokens                                             |
| **Debug Mode**    | Enable detailed logging              | Off                                                     | On/Off                                                    |

#### Language Settings

The plugin can generate alt text in any language. **Important**: The prompt can remain in English while the output is generated in your chosen language.

**How it works:**

- **Custom Prompt**: Can be in English (or any language you're comfortable with)
- **Language Setting**: Controls the output language of the generated alt text
- **Example**: Prompt in English > "Generate a brief alt text description for this image:" + Language: `pl` > Output in Polish

**Common language codes:**

- `en` - English (default)
- `es` - Spanish
- `fr` - French
- `de` - German
- `it` - Italian
- `pt` - Portuguese
- `pl` - Polish
- `ja` - Japanese
- `zh` - Chinese
- `ar` - Arabic
- `ru` - Russian
- `hi` - Hindi
- `ko` - Korean
- `nl` - Dutch
- `sv` - Swedish
- `cs` - Czech

#### Token Limits

- **50-100 tokens**: Very brief descriptions (1-2 sentences)
- **150-200 tokens**: Standard alt text (2-3 sentences) - **Recommended**
- **300-500 tokens**: Detailed descriptions (paragraph length)

### Available Models

#### Claude Models (Direct API - Automatic Discovery)

The plugin automatically fetches available models from Anthropic's API. Models are sorted by recommendation.

| Model             | Pricing (Input/Output per MTok) | Notes                        |
| ----------------- | ------------------------------- | ---------------------------- |
| Claude Sonnet 4.5 | $3 / $15                        | **Recommended** - Best value |
| Claude Opus 4.5   | $5 / $25                        | Most intelligent             |
| Claude Haiku 4.5  | $1 / $5                         | Fast & economical            |
| Claude Opus 4.1   | $15 / $75                       | Advanced                     |
| Claude Sonnet 4   | $3 / $15                        | Good performance             |
| Claude Opus 4     | $15 / $75                       | Premium                      |
| Claude Haiku 3.5  | $0.80 / $4                      | Budget option                |

> **Note:** Models use aliases that automatically point to the newest versions. Refresh button available to update model list.

#### OpenAI Models (Direct API - v2.4+)

**Automatic Model Discovery (v2.5):** The plugin automatically fetches the latest vision-capable models from OpenAI's API.

| Model                  | Description          | Best for                              |
| ---------------------- | -------------------- | ------------------------------------- |
| `gpt-4o`               | GPT-4o               | **Recommended** - Latest vision model |
| `gpt-4o-mini`          | GPT-4o Mini          | Fast & Cost-effective                 |
| `gpt-4-turbo`          | GPT-4 Turbo          | Vision capable, good balance          |
| `gpt-4-vision-preview` | GPT-4 Vision Preview | Legacy (for compatibility)            |

**Features:**

- Automatic model discovery - always up-to-date
- Refresh button to update model list on-demand
- 24-hour caching for optimal performance
- Vision-only filtering (shows only models that support images)

#### OpenRouter Models (Multiple Providers)

**Automatic Model Discovery:** The plugin automatically fetches all vision-capable models from OpenRouter's API. You get access to 90+ vision models that are updated in real-time.

**Popular Vision Models Include:**

- **Anthropic:** Claude 3.5 Sonnet, Claude 3 Opus, Claude 3 Haiku
- **OpenAI:** GPT-5, GPT-4o, GPT-4o Mini, GPT-4 Turbo
- **Google:** Gemini 2.5 Flash, Gemini Pro 1.5
- **Meta:** Llama 3.2 Vision (11B, 90B)
- **xAI:** Grok 4
- **And 80+ more models...**

**Features:**

- Automatic model discovery - always up-to-date
- Pricing indicators (Free, Cheap, Premium)
- Context length indicators for large-context models
- Refresh button to update model list on-demand
- 24-hour caching for optimal performance

> **OpenRouter Benefits:** Access to 90+ vision models from all major providers with one API key, automatic model updates, and pay-per-use pricing.

### Configuration Examples

#### Example 1: Polish E-commerce (English Prompt > Polish Output)

- **Model**: `claude-sonnet-4-5-latest` (balanced quality/speed)
- **Custom Prompt**: "Generate SEO-friendly alt text focusing on product features, color, and style:" (in English)
- **Language**: `pl` (outputs in Polish)
- **Max Tokens**: `150`
- **Result**: English instructions > Polish alt text

#### Example 2: Spanish News Website

- **Model**: `claude-haiku-4-5-latest` (fast for high volume)
- **Custom Prompt**: "Generate alt text describing people, actions, and news context:" (in English)
- **Language**: `es` (outputs in Spanish)
- **Max Tokens**: `200`
- **Result**: English instructions > Spanish alt text

#### Example 3: Multilingual Art Gallery

- **Model**: `claude-opus-4-5-latest` (highest quality)
- **Custom Prompt**: "Describe this artwork focusing on style, medium, and mood:" (in English)
- **Language**: `fr` / `de` / `it` (change per image/gallery section)
- **Max Tokens**: `300`
- **Result**: Same English prompt > French/German/Italian output as needed

## Usage Guide

### Automatic Generation on Upload

When you upload images to the Media Library, alt text is automatically generated:

1. Upload image(s) to Media Library
2. Plugin automatically generates alt text
3. Alt text is saved to image metadata
4. Ready to use in posts/pages

### Bulk Generation for Existing Images

To generate alt text for images already in your Media Library:

1. Go to **Media Library** (list view)
2. Select images using checkboxes
3. Choose **"Generate AI Alt Text"** from Bulk Actions dropdown
4. Click **Apply**
5. Watch the progress bar as alt text is generated
6. Page automatically refreshes when complete

### Refreshing Alt Text in Posts/Pages

To update alt text in existing post/page content:

#### Individual Post/Page:

1. Go to **Posts** or **Pages**
2. Hover over the post/page title
3. Click **"Refresh Alt Text"** link
4. Alt text in the content is updated

#### Multiple Posts/Pages:

1. Go to **Posts** or **Pages**
2. Select multiple items using checkboxes
3. Choose **"Refresh Alt Text"** from Bulk Actions
4. Click **Apply**

## Troubleshooting

### Common Issues & Solutions

#### "Invalid API Key" Error

- Ensure your API key starts with `sk-ant-api`
- Check for extra spaces before/after the key
- Verify the key hasn't been revoked

#### No Alt Text Generated

1. Enable Debug Mode in settings
2. Check `/wp-content/debug.log` for errors
3. Verify image URL is accessible
4. Check API key has sufficient credits

#### 400 Bad Request Error

- Ensure you're using a valid model ID
- Check image file isn't corrupted
- Verify image size is under 10MB

#### Rate Limiting Issues

- Reduce the number of images processed at once
- Add delays between bulk operations
- Check your Anthropic account limits

### Debug Mode

Enable debug mode to see detailed information:

1. Go to Settings > Custom AI Image Description
2. Check "Enable debug mode"
3. Save settings
4. Check `/wp-content/debug.log` for detailed logs

### Testing Tools

The plugin includes diagnostic tools:

- `diagnostic.php` - Test API connection and settings
- `test-generation.php` - Test alt text generation for specific images

Access these at:

- `yoursite.com/wp-content/plugins/custom-ai-image-description-generator/diagnostic.php`
- `yoursite.com/wp-content/plugins/custom-ai-image-description-generator/test-generation.php`

## Best Practices

### For Optimal Results

1. **Use Clear Image Titles** - The plugin uses image titles to provide context
2. **Choose the Right Model** - Use Sonnet for balance, Haiku for speed, Opus for quality
3. **Keep Prompts in English** - You can write prompts in English even when outputting to other languages
4. **Set Your Output Language** - Use language code (pl, es, fr, etc.) to generate alt text in any language
5. **Customize Prompts** - Tailor the prompt to your specific needs (SEO, accessibility, etc.)
6. **Set Appropriate Token Limits** - 150-200 tokens usually sufficient for alt text
7. **Process in Batches** - For large libraries, process 10-20 images at a time

> **Pro Tip:** You don't need to translate your prompts. Write them in English and set the Language field to your desired output language (e.g., `pl` for Polish). Claude will understand your English instructions and generate alt text in Polish.

### Accessibility Guidelines

Good alt text should:

- Be concise (typically under 125 characters)
- Describe the image's content and function
- Avoid phrases like "image of" or "picture of"
- Include relevant context
- Be written in the specified language

## Security

The plugin implements several security measures:

- Nonce verification on all AJAX requests
- Capability checks for user permissions
- Sanitization of all user inputs
- Secure API key storage (password field type)
- No direct file access allowed

## Performance

### Optimization Tips

1. **Use Haiku Model** for faster processing of large batches
2. **Enable caching** (if using enhanced version) to avoid regenerating alt text
3. **Process during off-peak hours** for large libraries
4. **Monitor API usage** in your Anthropic dashboard

### API Limits

Be aware of Anthropic's rate limits:

- Requests per minute vary by tier
- Token limits depend on your plan
- Monitor usage at console.anthropic.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

## Contributing

Contributions are welcome! Please:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

## License

This plugin is licensed under the GPL v2 or later.

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

## Support

For support:

1. Check the Troubleshooting section above
2. Enable Debug Mode and check logs
3. Use the diagnostic tools included
4. Report issues on GitHub

## Credits

- Developed using [Anthropic's Claude API](https://www.anthropic.com/)
- Built for the WordPress community
- Icons from WordPress Dashicons

## Future Enhancements

Planned features:

- [ ] Caching system for generated descriptions
- [ ] Rate limiting controls
- [ ] Export/Import alt text
- [ ] Batch scheduling
- [ ] WebP and AVIF support
- [ ] Custom taxonomies for image categorization
- [ ] Multi-site support

---

**Note:** This plugin requires an active Anthropic API key and incurs costs based on API usage. Monitor your usage at [console.anthropic.com](https://console.anthropic.com/).
