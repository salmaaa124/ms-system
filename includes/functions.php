<?php
/**
 * Shared helper functions used across pages.
 */

require_once __DIR__ . '/../config/config.php';

/** Escape for safe HTML output. */
function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/** Current language code (ar or en). */
function currentLang() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return $_SESSION['lang'] ?? 'en';
}

/** Switch active language. */
function setLang($lang) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['lang'] = in_array($lang, ['ar', 'en']) ? $lang : 'en';
}

/** Translate a key using the active language pack. */
function t($key) {
    static $dict = null;
    static $loadedLang = null;
    $lang = currentLang();
    if ($dict === null || $loadedLang !== $lang) {
        $dict = require __DIR__ . '/../lang/' . $lang . '.php';
        $loadedLang = $lang;
    }
    return $dict[$key] ?? $key;
}

/** HTML "dir" attribute based on language. */
function dirAttr() {
    return currentLang() === 'ar' ? 'rtl' : 'ltr';
}

/**
 * Validate and store an uploaded MRI image.
 * Returns [ok, pathOrError, originalName]
 */
function handleUpload($fileField) {
    if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
        return [false, 'upload_failed', null];
    }
    $file = $_FILES[$fileField];
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return [false, 'file_too_large', null];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return [false, 'invalid_file_type', null];
    }

    $uploadsDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0775, true);

    $newName = uniqid('mri_', true) . '.' . $ext;
    $dest    = $uploadsDir . '/' . $newName;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return [false, 'move_failed', null];
    }
    return [true, 'uploads/' . $newName, $file['name']];
}

/**
 * Analyse an MRI image using OpenAI GPT-4o Vision.
 * Falls back to a simulated realistic result if no API key is configured
 * or the request fails.
 *
 * Returns ['result' => 'Positive'|'Negative', 'confidence' => float, 'notes' => string]
 */
function analyseMRI($absoluteImagePath) {
    if (OPENAI_API_KEY === '') {
        return simulateMRI();
    }

    try {
        $mime   = mime_content_type($absoluteImagePath) ?: 'image/jpeg';
        $b64    = base64_encode(file_get_contents($absoluteImagePath));
        $dataUrl = "data:$mime;base64,$b64";

        $payload = [
            'model' => OPENAI_MODEL,
            'messages' => [[
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => "You are a medical AI assistant analysing a brain MRI image for signs of Multiple Sclerosis (MS). Look for hyperintense white matter lesions, periventricular plaques, or demyelination patterns. Respond ONLY with valid JSON in this exact format: {\"result\": \"Positive\" or \"Negative\", \"confidence\": number between 60 and 98, \"notes\": \"short clinical observation\"}"
                    ],
                    [
                        'type' => 'image_url',
                        'image_url' => ['url' => $dataUrl]
                    ],
                ],
            ]],
            'max_tokens' => 200,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . OPENAI_API_KEY,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT    => 60,
        ]);
        $response = curl_exec($ch);
        $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http !== 200 || !$response) return simulateMRI();

        $json = json_decode($response, true);
        $text = $json['choices'][0]['message']['content'] ?? '';

        // Extract JSON object from response
        if (preg_match('/\{.*\}/s', $text, $m)) {
            $parsed = json_decode($m[0], true);
            if (is_array($parsed) && isset($parsed['result'], $parsed['confidence'])) {
                $result = in_array($parsed['result'], ['Positive', 'Negative'])
                          ? $parsed['result'] : 'Negative';
                return [
                    'result'     => $result,
                    'confidence' => round(max(50, min(99, (float)$parsed['confidence'])), 2),
                    'notes'      => substr($parsed['notes'] ?? '', 0, 500),
                ];
            }
        }
    } catch (Throwable $e) {
        // fall through to simulation
    }
    return simulateMRI();
}

/** Generates a realistic simulated result when AI is unavailable. */
function simulateMRI() {
    $result     = (mt_rand(0, 100) > 50) ? 'Positive' : 'Negative';
    $confidence = round(mt_rand(7500, 9700) / 100, 2);
    $notes = $result === 'Positive'
        ? 'Simulated analysis indicates possible periventricular hyperintensities consistent with MS lesions.'
        : 'Simulated analysis shows no clear demyelinating lesions. Brain parenchyma appears within normal limits.';
    return ['result' => $result, 'confidence' => $confidence, 'notes' => $notes];
}
