<?php
/**
 * Sanskar AI - Base Controller
 * =============================
 * Base controller with view rendering and common utilities
 */

namespace App\Core;

use App\Config\App;

abstract class Controller
{
    /**
     * Data passed to views
     */
    protected array $viewData = [];

    /**
     * Current authenticated user
     */
    protected ?array $user = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Load authenticated user
        $this->user = Auth::user();

        // Set common view data
        $this->viewData['user'] = $this->user;
        $this->viewData['appName'] = App::APP_NAME;
        $this->viewData['csrfToken'] = Auth::csrfToken();
    }

    /**
     * Render a view
     */
    protected function view(string $view, array $data = []): void
    {
        // Merge data
        $data = array_merge($this->viewData, $data);

        // Extract data for view
        extract($data);

        // Build view path
        $viewPath = App::viewsPath(str_replace('.', '/', $view) . '.php');

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View '$view' not found at '$viewPath'");
        }

        // Start output buffering
        ob_start();

        include $viewPath;

        // Get content and output
        $content = ob_get_clean();
        echo $content;
    }

    /**
     * Render view with layout
     */
    protected function viewWithLayout(string $view, string $layout = 'layouts/main', array $data = []): void
    {
        // Merge data
        $data = array_merge($this->viewData, $data);

        // Extract data for view
        extract($data);

        // Build view path
        $viewPath = App::viewsPath(str_replace('.', '/', $view) . '.php');

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View '$view' not found at '$viewPath'");
        }

        // Capture view content
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // Build layout path
        $layoutPath = App::viewsPath(str_replace('.', '/', $layout) . '.php');

        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout '$layout' not found at '$layoutPath'");
        }

        // Render layout with content
        include $layoutPath;
    }

    /**
     * Return JSON response
     */
    protected function json($data, int $statusCode = 200): void
    {
        // Clear any output buffers to prevent HTML errors from breaking JSON
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Return success JSON response
     */
    protected function success($data = null, string $message = 'Success', int $statusCode = 200): void
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Return error JSON response
     */
    protected function error(string $message, int $statusCode = 400, mixed $errors = null): void
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url, array $flash = []): void
    {
        // Set flash messages
        foreach ($flash as $key => $value) {
            $_SESSION['flash'][$key] = $value;
        }

        Router::redirect($url);
    }

    /**
     * Redirect back with message
     */
    protected function back(array $flash = []): void
    {
        // Set flash messages
        foreach ($flash as $key => $value) {
            $_SESSION['flash'][$key] = $value;
        }

        Router::back();
    }

    /**
     * Get flash message
     */
    protected function flash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    /**
     * Check flash message exists
     */
    protected function hasFlash(string $key): bool
    {
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Validate request data
     */
    protected function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $params = [];

                if (strpos($rule, ':') !== false) {
                    list($rule, $paramString) = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                }

                $error = $this->validateRule($field, $value, $rule, $params, $data);

                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }

        return $errors;
    }

    /**
     * Validate single rule
     */
    private function validateRule(string $field, $value, string $rule, array $params, array $data): ?string
    {
        $fieldLabel = ucwords(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    return "$fieldLabel is required.";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "$fieldLabel must be a valid email address.";
                }
                break;

            case 'min':
                $min = (int) $params[0];
                if (!empty($value) && strlen($value) < $min) {
                    return "$fieldLabel must be at least $min characters.";
                }
                break;

            case 'max':
                $max = (int) $params[0];
                if (!empty($value) && strlen($value) > $max) {
                    return "$fieldLabel must not exceed $max characters.";
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if ($value !== ($data[$confirmField] ?? null)) {
                    return "$fieldLabel confirmation does not match.";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    return "$fieldLabel must be a number.";
                }
                break;

            case 'in':
                if (!empty($value) && !in_array($value, $params)) {
                    return "$fieldLabel must be one of: " . implode(', ', $params);
                }
                break;
        }

        return null;
    }

    /**
     * Get request input
     */
    protected function input(?string $key = null, mixed $default = null): mixed
    {
        $data = array_merge($_GET, $_POST);

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    /**
     * Get only specific inputs
     */
    protected function only(array $keys): array
    {
        $data = $this->input();
        return array_intersect_key($data, array_flip($keys));
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if request is POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verify CSRF token
     */
    protected function verifyCsrf(): bool
    {
        $token = $this->input('_token') ?? $this->input('csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return Auth::verifyCsrfToken($token);
    }

    /**
     * Check whether ritual text contains disallowed non-Hindu religious keywords.
     */
    protected function hasRestrictedRitualContent(array $values): bool
    {
        $chunks = [];
        foreach ($values as $value) {
            if (is_scalar($value) && $value !== null) {
                $chunks[] = (string) $value;
            }
        }

        $haystack = trim(strtolower(implode(' ', $chunks)));
        if ($haystack === '') {
            return false;
        }

        $blockedWords = [
            'islam', 'islamic', 'muslim', 'allah', 'quran', 'ramadan', 'eid', 'masjid', 'mosque', 'namaz', 'salah', 'salat',
            'christian', 'christianity', 'christ', 'jesus', 'church', 'bible', 'mass', 'christmas', 'easter', 'xmas',
        ];

        // Fast exact word match.
        if ((bool) preg_match('/\b(' . implode('|', array_map('preg_quote', $blockedWords)) . ')\b/i', $haystack)) {
            return true;
        }

        // Typo-tolerant match for words like "creismas" -> "christmas".
        $normalized = preg_replace('/[^a-z0-9\s]+/i', ' ', $haystack) ?? $haystack;
        $tokens = preg_split('/\s+/', $normalized) ?: [];

        foreach ($tokens as $token) {
            $token = trim($token);
            if ($token === '' || strlen($token) < 4) {
                continue;
            }

            foreach ($blockedWords as $blocked) {
                if ($this->isNearKeywordMatch($token, $blocked)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isNearKeywordMatch(string $token, string $keyword): bool
    {
        if ($token === $keyword) {
            return true;
        }

        $distance = levenshtein($token, $keyword);
        $maxLen = max(strlen($token), strlen($keyword));

        if ($maxLen <= 6) {
            return $distance <= 1;
        }

        if ($maxLen <= 10) {
            return $distance <= 2;
        }

        return $distance <= 3;
    }

    /**
     * Shared message for ritual policy violations.
     */
    protected function restrictedRitualMessage(): string
    {
        return 'Only Hindu rituals are allowed. Islamic or Christian ritual content cannot be created.';
    }
}
