# Changelog

All notable changes to the Custom AI Image Description Generator plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-01-11

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