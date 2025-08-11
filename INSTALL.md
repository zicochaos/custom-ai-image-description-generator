# Installation Guide

## Quick Start (5 minutes)

### Step 1: Download the Plugin
Download all files from this repository:
- `custom-ai-image-description-generator.php` (main plugin file)
- `diagnostic.php` (optional troubleshooting tool)
- `test-generation.php` (optional testing tool)

### Step 2: Upload to WordPress

#### Method A: Via WordPress Admin (Recommended)
1. Zip all plugin files into `custom-ai-image-description-generator.zip`
2. Go to **WordPress Admin → Plugins → Add New**
3. Click **Upload Plugin**
4. Choose your zip file
5. Click **Install Now**
6. Click **Activate**

#### Method B: Via FTP/File Manager
1. Connect to your server via FTP or file manager
2. Navigate to `/wp-content/plugins/`
3. Create folder `custom-ai-image-description-generator`
4. Upload all files to this folder
5. Go to **WordPress Admin → Plugins**
6. Find the plugin and click **Activate**

### Step 3: Get Your API Key
1. Visit [console.anthropic.com](https://console.anthropic.com/)
2. Sign up or log in
3. Go to **API Keys** section
4. Click **Create Key**
5. Copy the key (starts with `sk-ant-api`)
6. Save it somewhere safe (you won't see it again!)

### Step 4: Configure the Plugin
1. Go to **Settings → Custom AI Image Description**
2. Paste your API key
3. Select **Claude 3.5 Sonnet** (recommended)
4. Click **Save Settings**

### Step 5: Test It Works
1. Go to **Media Library**
2. Upload a test image
3. Check if alt text was generated
4. If not, check Troubleshooting below

## Detailed Installation

### System Requirements Check

Before installing, verify your system meets these requirements:

```php
<?php
// Add this to any PHP file to check requirements
echo 'PHP Version: ' . PHP_VERSION . ' (Need 7.4+)' . PHP_EOL;
echo 'WordPress Version: ' . get_bloginfo('version') . ' (Need 5.0+)' . PHP_EOL;
echo 'SSL: ' . (is_ssl() ? 'Enabled' : 'Disabled (Recommended)') . PHP_EOL;
```

### File Structure

After installation, your plugin folder should look like this:

```
/wp-content/plugins/custom-ai-image-description-generator/
├── custom-ai-image-description-generator.php  (main plugin file)
├── diagnostic.php                              (optional)
├── test-generation.php                         (optional)
├── README.md                                   (documentation)
├── CHANGELOG.md                                (version history)
└── INSTALL.md                                  (this file)
```

### Permissions

Ensure proper file permissions:
- Files: 644 (`-rw-r--r--`)
- Folders: 755 (`drwxr-xr-x`)

```bash
# Fix permissions via SSH
chmod 644 custom-ai-image-description-generator.php
chmod 644 *.php
chmod 755 ../custom-ai-image-description-generator/
```

## Configuration Options

### Basic Settings

| Setting | Recommended Value | Notes |
|---------|------------------|-------|
| API Key | Your key | Keep it secret! |
| Model | claude-3-5-sonnet-20241022 | Best balance |
| Language | en | Your content language |
| Max Tokens | 200 | Good for alt text |

### Advanced Configuration

#### Custom Prompt Examples

For e-commerce sites:
```
Generate SEO-friendly alt text focusing on product features, color, and style:
```

For news sites:
```
Create descriptive alt text emphasizing people, actions, and context:
```

For art galleries:
```
Describe the artwork's style, medium, subject, and mood:
```

#### Language Codes

Common language codes for multi-language sites:
- `en` - English
- `es` - Spanish
- `fr` - French
- `de` - German
- `it` - Italian
- `pt` - Portuguese
- `ja` - Japanese
- `zh` - Chinese

## Troubleshooting Installation

### Plugin Won't Activate

**Error: "Plugin could not be activated because it triggered a fatal error"**

1. Check PHP version (need 7.4+):
   ```php
   <?php phpinfo(); ?>
   ```

2. Check error logs:
   - Look in `/wp-content/debug.log`
   - Or hosting error logs

3. Enable WordPress debug mode:
   ```php
   // In wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

### Settings Page Not Appearing

1. Check user permissions - need `manage_options` capability
2. Deactivate and reactivate the plugin
3. Clear browser cache
4. Check for plugin conflicts

### API Key Not Saving

1. Check database permissions
2. Verify WordPress can write to options table
3. Try a different browser
4. Check for security plugins blocking saves

## Testing Your Installation

### Using the Diagnostic Tool

1. Navigate to:
   ```
   yoursite.com/wp-content/plugins/custom-ai-image-description-generator/diagnostic.php
   ```

2. Check all green checkmarks:
   - ✅ API Key configured
   - ✅ Valid model selected
   - ✅ PHP version compatible

3. Click **Test API Connection**

4. Should see: "✅ API connection successful!"

### Using the Test Generation Tool

1. Navigate to:
   ```
   yoursite.com/wp-content/plugins/custom-ai-image-description-generator/test-generation.php
   ```

2. Enter an image attachment ID

3. Click **Test Generation**

4. Should see generated alt text

## Updating the Plugin

### Manual Update Process

1. **Backup first!** 
   - Export your settings
   - Note your API key

2. Deactivate the current plugin

3. Delete old plugin files (settings are preserved)

4. Upload new version

5. Activate plugin

6. Verify settings are intact

### Preserving Settings

Settings are stored in WordPress database and survive updates:
- Table: `wp_options`
- Keys: `custom_ai_image_description_*`

To backup settings:
```sql
SELECT * FROM wp_options 
WHERE option_name LIKE 'custom_ai_image_description_%';
```

## Uninstallation

### Complete Removal

1. Deactivate plugin in WordPress admin
2. Delete plugin from Plugins page
3. Remove settings from database (optional):

```sql
DELETE FROM wp_options 
WHERE option_name LIKE 'custom_ai_image_description_%';
```

4. Remove generated alt text (optional):
```sql
-- This removes ALL alt text, be careful!
DELETE FROM wp_postmeta 
WHERE meta_key = '_wp_attachment_image_alt';
```

## Getting Help

### Resources

1. **Documentation**: Read the README.md file
2. **Diagnostic Tool**: Check diagnostic.php
3. **Debug Logs**: Enable debug mode in settings
4. **Error Logs**: Check `/wp-content/debug.log`

### Common Success Indicators

You'll know it's working when:
- ✅ Settings page appears under Settings menu
- ✅ "Generate AI Alt Text" appears in Media Library bulk actions
- ✅ New uploads automatically get alt text
- ✅ Diagnostic tool shows all green
- ✅ No errors in debug log

### Still Having Issues?

1. Double-check your API key
2. Verify you selected a valid model
3. Test with a small image first
4. Check your Anthropic account has credits
5. Look for plugin conflicts (deactivate others temporarily)

---

**Installation typically takes 5-10 minutes.** If you encounter issues, the diagnostic tools will help identify the problem quickly.