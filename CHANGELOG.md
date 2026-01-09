# Changelog

All notable changes to the Custom AI Image Description Generator plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.7.0] - 2026-01-09

### Added

- **Image Compression Option**
  - New setting to compress images before sending to API
  - Resizes images >1024px to reduce token costs
  - Uses WordPress image editor for quality resizing
  - Optional - can be enabled/disabled in settings

- **Cost Estimation UI**
  - Shows estimated cost before bulk operations
  - Confirmation dialog: "Processing X images with [model]. Estimated cost: $Y"
  - Pricing data for all Claude, OpenAI, and OpenRouter models
  - Helps users make informed decisions about API costs

- **Skip Existing Alt Text Option**
  - New setting to skip images that already have alt text (default: ON)
  - Avoids regenerating and overwriting existing descriptions
  - Progress shows: "Processed X, Skipped Y, Errors Z"
  - Significant cost savings for bulk operations on existing libraries

### Technical

- Added `custom_ai_image_description_maybe_compress_image()` function
- Added `custom_ai_image_description_get_model_pricing()` function
- Added `custom_ai_image_description_estimate_cost()` function
- Added `custom_ai_ajax_estimate_cost()` AJAX handler
- Added `custom_ai_ajax_check_alt_text()` AJAX handler
- Enhanced bulk action JavaScript with cost confirmation dialog

## [2.6.0] - 2026-01-08

### Added

- **Claude API Automatic Model Discovery**
  - Fetches latest models from Anthropic `/v1/models` API
  - Displays all available Claude models with pricing info
  - API Reference: https://platform.claude.com/docs/en/about-claude/pricing#model-pricing
- **Claude Refresh Models Button**
  - Manual model list refresh for Claude provider
  - Shows discovered model count
  - AJAX-powered with progress feedback
- **Claude Model Caching**
  - 24-hour cache for Claude models
  - Reduces API calls and improves performance
  - Automatic fallback to static models if API fails
- **New Claude 4.5 Models Support**
  - Claude Opus 4.5 ($5/$25 per MTok)
  - Claude Sonnet 4.5 ($3/$15 per MTok) - New default
  - Claude Haiku 4.5 ($1/$5 per MTok)

### Changed

- Default model changed from `claude-3-5-sonnet-latest` to `claude-sonnet-4-5-latest`
- Claude models now fetched dynamically instead of static list
- Updated model pricing information for all Claude models
- Improved model sorting (Sonnet 4.5 recommended first)

### Technical

- Added `custom_ai_image_description_fetch_claude_models()` function
- Added `custom_ai_ajax_refresh_claude_models()` AJAX handler
- Added `custom_ai_claude_vision_models` transient cache
- Vision model filtering for Claude 3+ and 4.x models

## [2.5.0] - 2025-08-13

### Added

- **OpenAI API Automatic Model Discovery**
  - Fetches latest vision-capable models from OpenAI `/v1/models` API
  - Smart filtering for vision models only (GPT-4o, GPT-4 Turbo, etc.)
  - Dynamic model labels and recommendations
- **OpenAI Refresh Models Button**
  - Manual model list refresh for OpenAI provider
  - Shows discovered model count
  - AJAX-powered with progress feedback
- **OpenAI Model Caching**
  - 24-hour cache for OpenAI models
  - Reduces API calls and improves performance
  - Automatic fallback to static models if API fails

### Changed

- OpenAI models now fetched dynamically instead of static list
- Improved model sorting (GPT-4o prioritized)
- Enhanced admin UI with provider-specific refresh buttons

### Technical

- Added `custom_ai_image_description_fetch_openai_models()` function
- Added `custom_ai_ajax_refresh_openai_models()` AJAX handler
- Added `custom_ai_openai_vision_models` transient cache
- Vision model pattern matching for automatic filtering

## [2.4.0] - 2025-08-13

### Added

- **OpenAI API Direct Integration**
  - Direct access to OpenAI API without OpenRouter
  - Support for GPT-4o, GPT-4o Mini, GPT-4 Turbo models
  - Native OpenAI request format and authentication
- **OpenAI API Key Settings**
  - Dedicated OpenAI API key field in settings
  - Provider-specific UI elements and documentation
  - Secure password field for API key storage
- **Enhanced Provider Selection**
  - Three-way provider selection: Claude, OpenAI, OpenRouter
  - Dynamic model lists based on selected provider
  - Automatic API key field switching

### Changed

- Plugin description updated to mention OpenAI support
- Settings interface reorganized for three providers
- Model selection now includes OpenAI-specific models

### Technical

- Added `custom_ai_image_description_generate_openai()` function
- Updated main generation router for OpenAI provider
- Enhanced JavaScript for three-provider switching
- OpenAI uses standard OpenAI API format with Bearer auth

## [2.3.0] - 2025-08-11

### Added

- **Automatic Vision Model Discovery** for OpenRouter
  - Dynamically fetches all vision-capable models from OpenRouter API
  - Access to 90+ vision models (up from 10 hardcoded)
  - Real-time model availability updates
- **Refresh Models Button** in settings
  - On-demand model list updates
  - Shows count of discovered models
  - AJAX-powered for smooth experience
- **Smart Caching System**
  - 24-hour cache for model list
  - Reduces API calls
  - Manual refresh option available
- **Pricing Indicators** for models
  - Free models marked clearly
  - Cheap/Premium indicators
  - Context length shown for large models

### Changed

- OpenRouter models now fetched dynamically instead of hardcoded
- Model dropdown populates from live API data
- Improved model sorting (prioritizes major providers)

### Technical

- Added `custom_ai_image_description_fetch_openrouter_models()` function
- Added `custom_ai_ajax_refresh_openrouter_models()` AJAX handler
- Uses WordPress transients for caching
- Fallback models if API unavailable

## [2.2.0] - 2025-08-11

### Added

- **OpenRouter API Integration** - Support for multiple AI providers
  - Access to GPT-4o, Gemini, Llama Vision models and more
  - Single API key for multiple providers
  - Cost-effective usage-based pricing
- API Provider Selection in settings
  - Toggle between Claude direct API and OpenRouter
  - Dynamic model selection based on provider
  - Automatic UI updates when switching providers
- Test script for OpenRouter integration (`test-openrouter.php`)
- Support for 10+ vision-capable models through OpenRouter

### Changed

- Plugin name simplified to "Custom AI Image Description Generator"
- Settings page reorganized with provider selection
- Model dropdown now shows provider-specific models

### Technical

- Added `custom_ai_image_description_generate_openrouter()` function
- Added provider switching JavaScript in admin
- OpenRouter uses OpenAI-compatible request format
- Both APIs support same features (retry, debug, bulk processing)

## [2.1.1] - 2025-08-11

### Documentation

- Added comprehensive language settings documentation
- **Clarified that prompts can be in English while output is in another language**
- Added Polish language code and example
- Added token limits guide (50-500 range with recommendations)
- Expanded language codes list (16 languages including Polish, Dutch, Swedish, Czech)
- Updated configuration examples showing English prompt to Other language output
- Added Pro Tip about keeping prompts in English for easier management

## [2.1.0] - 2025-08-11

### Changed

- **Switched to model aliases** for automatic updates
  - Now uses `claude-3-5-sonnet-latest` instead of specific versions
  - Plugin automatically uses newest model versions without updates
  - No more manual model ID updates needed

### Updated

- Default model changed from `claude-3-5-sonnet-20241022` to `claude-3-5-sonnet-latest`
- All models now use aliases (`-latest`, `-4-0`, `-4-1` suffixes)
- Documentation updated to reflect alias usage

### Benefits

- Future-proof: Automatically gets model improvements
- Zero maintenance: No plugin updates needed for new models
- Always current: Uses latest Claude capabilities

## [2.0.0] - 2025-08-11

### Added

- AJAX support for smooth, no-reload bulk operations
- Real-time progress bars for bulk generation
- Post/Page alt text refresh functionality (individual and bulk)
- Automatic image format detection (JPEG, PNG, GIF, WebP)
- Debug mode with detailed API logging
- Settings link on plugins page for quick access
- Diagnostic tools (diagnostic.php and test-generation.php)
- Sequential processing to avoid rate limits
- Exponential backoff retry mechanism
- Error count tracking in bulk operations

### Changed

- Updated to latest Claude model IDs (Opus 4.1, Sonnet 4, etc.)
- Improved admin notices with success/error counts
- Enhanced security with proper nonce verification
- Simplified plugin structure (single file, inline JS/CSS)
- Better error messages and user feedback

### Fixed

- Fixed hardcoded JPEG MIME type - now detects actual format
- Fixed incorrect model IDs causing 400 errors
- Fixed missing AJAX handlers for Media Library
- Fixed bulk action not showing progress
- Fixed alt text not being saved properly

### Security

- Added nonce verification to all AJAX endpoints
- Added capability checks for all admin actions
- Improved input sanitization

## [1.9.0] - 2024-12-01

### Added

- Initial release
- Basic alt text generation using Claude API
- Bulk action support in Media Library
- Settings page with API configuration
- Multiple Claude model support
- Custom prompt configuration
- Multi-language support
- Token limit controls

### Known Issues in 1.9

- Used incorrect model IDs
- Hardcoded JPEG format
- No AJAX support
- No progress indicators
- Limited error handling

## [1.0.0] - 2024-10-01

### Added

- Beta version
- Proof of concept implementation
- Basic API integration

---

## Upgrade Notes

### From 1.9 to 2.0

1. Deactivate the old plugin
2. Replace with new version
3. Reactivate plugin
4. Re-enter API key if needed
5. Select model from updated list

### Breaking Changes in 2.0

- Model IDs have changed - you must reselect your preferred model
- Bulk action name changed to "Generate AI Alt Text"

## Future Releases (Planned)

### [2.7.0]

- [ ] Caching system for generated descriptions
- [ ] Rate limiting controls

### [2.8.0]

- [ ] Export/Import alt text functionality
- [ ] Batch scheduling for large libraries
- [ ] Custom taxonomies for images

### [3.0.0]

- [ ] Multi-site network support
- [ ] REST API endpoints
- [ ] Gutenberg block integration
