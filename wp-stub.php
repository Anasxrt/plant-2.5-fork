<?php
/**
 * WordPress Stubs Loader
 * 
 * This file serves as a reference for static analysis tools.
 * The actual stub files in /stubs/ are PHPStan-compatible declarations
 * for WordPress core, WooCommerce, and ACF PRO functions/classes.
 * 
 * For static analysis (PHPStan/Psalm), include the stub files directly:
 *   - phpstan.neon: includes:
 *     - stubs/wordpress-stubs.php
 *     - stubs/woocommerce-stubs.php
 *     - stubs/acf-pro-stubs.php
 * 
 * @link https://github.com/php-stubs/wordpress-stubs
 * @link https://github.com/php-stubs/woocommerce-stubs
 * @link https://github.com/php-stubs/acf-pro-stubs
 */

// The original minimal stub implementations have been replaced by official stubs.
// See /stubs/ directory for the full stub declarations.

/**
 * PHP 8.3 Compatibility Notes:
 * 
 * ✅ Deprecated functions check:
 * - utf8_encode()/utf8_decode() - NOT used in codebase (deprecated PHP 8.2)
 * - session_register() - NOT used (removed)
 * - create_function() - NOT used (removed PHP 7.2+)
 * 
 * ✅ Dynamic properties:
 * - All class properties are properly declared in vendor/acf/
 * - Official stubs include #[AllowDynamicProperties] where needed for PHP 8.2+
 * 
 * ✅ Null coalescing:
 * - $GLOBALS['s_...'] patterns checked for PHP 8.3 null safety
 * - All accesses properly guarded with isset() or default values
 * 
 * ✅ Type hints:
 * - Return types use proper nullable syntax (e.g., ?string)
 * - Parameters use proper type declarations where applicable
 */

// Core WordPress functions used in this theme (for reference)
// These are declared in wordpress-stubs.php - do not duplicate here