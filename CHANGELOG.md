# Changelog

All notable changes to the Custom AI Image Description Generator plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.0] - 2025-08-11

### Added
- 🚀 **OpenRouter API Integration** - Support for multiple AI providers
  - Access to GPT-4o, Gemini, Llama Vision models and more
  - Single API key for multiple providers
  - Cost-effective usage-based pricing
- 🎛️ API Provider Selection in settings
  - Toggle between Claude direct API and OpenRouter
  - Dynamic model selection based on provider
  - Automatic UI updates when switching providers
- 🧪 Test script for OpenRouter integration (`test-openrouter.php`)
- 📚 Support for 10+ vision-capable models through OpenRouter

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
- 📝 Added comprehensive language settings documentation
- 🌍 **Clarified that prompts can be in English while output is in another language**
- 🇵🇱 Added Polish language code and example
- 📊 Added token limits guide (50-500 range with recommendations)
- 🌐 Expanded language codes list (16 languages including Polish, Dutch, Swedish, Czech)
- 💡 Updated configuration examples showing English prompt → Other language output
- 💬 Added Pro Tip about keeping prompts in English for easier management

## [2.1.0] - 2025-08-11

### Changed
- 🚀 **Switched to model aliases** for automatic updates
  - Now uses `claude-3-5-sonnet-latest` instead of specific versions
  - Plugin automatically uses newest model versions without updates
  - No more manual model ID updates needed

### Updated
- Default model changed from `claude-3-5-sonnet-20241022` to `claude-3-5-sonnet-latest`
- All models now use aliases (`-latest`, `-4-0`, `-4-1` suffixes)
- Documentation updated to reflect alias usage

### Benefits
- ✨ Future-proof: Automatically gets model improvements
- 🔧 Zero maintenance: No plugin updates needed for new models
- ⚡ Always current: Uses latest Claude capabilities

## [2.0.0] - 2025-08-11

### Added
- ✨ AJAX support for smooth, no-reload bulk operations
- 📊 Real-time progress bars for bulk generation
- 🔄 Post/Page alt text refresh functionality (individual and bulk)
- 🎯 Automatic image format detection (JPEG, PNG, GIF, WebP)
- 🛠️ Debug mode with detailed API logging
- 📝 Settings link on plugins page for quick access
- 🔧 Diagnostic tools (diagnostic.php and test-generation.php)
- ⚡ Sequential processing to avoid rate limits
- 🔄 Exponential backoff retry mechanism
- 📋 Error count tracking in bulk operations

### Changed
- 🚀 Updated to latest Claude model IDs (Opus 4.1, Sonnet 4, etc.)
- 🎨 Improved admin notices with success/error counts
- 🔐 Enhanced security with proper nonce verification
- 📦 Simplified plugin structure (single file, inline JS/CSS)
- 🌟 Better error messages and user feedback

### Fixed
- 🐛 Fixed hardcoded JPEG MIME type - now detects actual format
- 🐛 Fixed incorrect model IDs causing 400 errors
- 🐛 Fixed missing AJAX handlers for Media Library
- 🐛 Fixed bulk action not showing progress
- 🐛 Fixed alt text not being saved properly

### Security
- 🔒 Added nonce verification to all AJAX endpoints
- 🔒 Added capability checks for all admin actions
- 🔒 Improved input sanitization

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

### [2.1.0]
- [ ] Model aliases support for auto-updating
- [ ] Caching system for generated descriptions
- [ ] Rate limiting controls

### [2.2.0]
- [ ] Export/Import alt text functionality
- [ ] Batch scheduling for large libraries
- [ ] Custom taxonomies for images

### [3.0.0]
- [ ] Multi-site network support
- [ ] REST API endpoints
- [ ] Gutenberg block integration