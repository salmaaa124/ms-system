<?php
/**
 * Application Configuration
 * --------------------------
 * - Set your OpenAI API key to enable real AI-powered MRI analysis.
 * - Leave it empty to run in SIMULATION mode (randomised realistic output).
 */

// OpenAI API Key (https://platform.openai.com/api-keys)
define('OPENAI_API_KEY', '');

// Model used for vision analysis
define('OPENAI_MODEL', 'gpt-4o');

// Upload limits
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// App meta
define('APP_NAME', 'NeuroScan MS');
define('APP_VERSION', '1.0.0');

// Session lifetime (seconds)
define('SESSION_LIFETIME', 3600 * 4);
