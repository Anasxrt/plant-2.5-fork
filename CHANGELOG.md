# Changelog

## [2.6.0] - 2026-05-09
### Added
- Official php-stubs for WordPress 6.9.1, WooCommerce, and ACF PRO for static analysis
- PHP 8.3+ type hints and PHPDoc annotations to key template files
- wp-stub.php updated to reference official stubs
- COMPATIBILITY-REPORT.md documenting full compatibility review

### Changed
- Updated style.css: Tested up to WordPress 6.9, Requires PHP 8.3+, Version 2.6.0
- Enhanced functions.php with improved sidebar registration (refactored)
- Improved type safety in template-tags.php, shortcode.php, and line-notify.php

### Security
- Verified no deprecated PHP functions (utf8_encode, create_function, session_register)
- Confirmed proper input sanitization and output escaping
- Validated null coalescing safety with $GLOBALS patterns

### Compatibility
- PHP 8.3+ compatible
- WordPress 6.9+ compatible
- WooCommerce integration verified
- Backward compatible with existing installations

## [2.5.9.1] - 2026-03-XX
### Maintenance
- Production readiness verification
- Security audit passed
- JavaScript, CSS, and build configuration reviewed
