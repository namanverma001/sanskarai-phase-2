<?php

namespace App\Services;

use App\Config\Database;

/**
 * RazorpayService - Handles Razorpay payment gateway integration
 */
class RazorpayService
{
    private $keyId;
    private $keySecret;
    private $apiUrl = 'https://api.razorpay.com/v1';
    private $lastError = '';

    public function __construct()
    {
        // Ensure environment variables are loaded
        Database::loadEnv();

        $this->keyId = $_ENV['RAZORPAY_KEY_ID'] ?? '';
        $this->keySecret = $_ENV['RAZORPAY_KEY_SECRET'] ?? '';
    }

    /**
     * Create a Razorpay order
     *
     * @param float $amount Amount in rupees
     * @param string $currency Currency code (default: INR)
     * @param array $notes Additional notes
     * @return array|false Order data or false on failure
     */
    public function createOrder($amount, $currency = 'INR', $notes = [])
    {
        // Convert to paise (smallest currency unit)
        $amountInPaise = (int)($amount * 100);

        $data = [
            'amount' => $amountInPaise,
            'currency' => $currency,
            'receipt' => 'rcpt_' . time() . '_' . uniqid()
        ];

        // Only add notes if not empty
        if (!empty($notes)) {
            $data['notes'] = $notes;
        }

        $response = $this->makeRequest('/orders', 'POST', $data);

        if ($response && isset($response['id'])) {
            return $response;
        }

        return false;
    }

    /**
     * Verify payment signature
     *
     * @param string $orderId Razorpay order ID
     * @param string $paymentId Razorpay payment ID
     * @param string $signature Razorpay signature
     * @return bool
     */
    public function verifyPaymentSignature($orderId, $paymentId, $signature)
    {
        $generatedSignature = hash_hmac(
            'sha256',
            $orderId . '|' . $paymentId,
            $this->keySecret
        );

        return hash_equals($generatedSignature, $signature);
    }

    /**
     * Fetch payment details
     *
     * @param string $paymentId Razorpay payment ID
     * @return array|false
     */
    public function fetchPayment($paymentId)
    {
        return $this->makeRequest('/payments/' . $paymentId, 'GET');
    }

    /**
     * Fetch order details
     *
     * @param string $orderId Razorpay order ID
     * @return array|false
     */
    public function fetchOrder($orderId)
    {
        return $this->makeRequest('/orders/' . $orderId, 'GET');
    }

    /**
     * Capture payment (for manual capture)
     *
     * @param string $paymentId Razorpay payment ID
     * @param int $amount Amount in paise
     * @param string $currency Currency code
     * @return array|false
     */
    public function capturePayment($paymentId, $amount, $currency = 'INR')
    {
        return $this->makeRequest('/payments/' . $paymentId . '/capture', 'POST', [
            'amount' => $amount,
            'currency' => $currency
        ]);
    }

    /**
     * Initiate refund
     *
     * @param string $paymentId Razorpay payment ID
     * @param int|null $amount Amount in paise (null for full refund)
     * @param array $notes Additional notes
     * @return array|false
     */
    public function refund($paymentId, $amount = null, $notes = [])
    {
        $data = ['notes' => $notes];
        if ($amount !== null) {
            $data['amount'] = $amount;
        }

        return $this->makeRequest('/payments/' . $paymentId . '/refund', 'POST', $data);
    }

    /**
     * Get Razorpay key ID (for frontend)
     *
     * @return string
     */
    public function getKeyId()
    {
        return $this->keyId;
    }

    /**
     * Get last error message
     *
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Make API request to Razorpay
     *
     * @param string $endpoint API endpoint
     * @param string $method HTTP method
     * @param array $data Request data
     * @return array|false
     */
    private function makeRequest($endpoint, $method = 'GET', $data = [])
    {
        $url = $this->apiUrl . $endpoint;

        // Check if keys are configured
        if (empty($this->keyId) || empty($this->keySecret)) {
            $this->lastError = "Razorpay API keys not configured";
            error_log("Razorpay API Error: API keys not configured");
            return false;
        }

        $ch = curl_init();

        $headers = [
            'Content-Type: application/json'
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $this->keyId . ':' . $this->keySecret,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => 30
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        curl_close($ch);

        if ($error || $errno) {
            $this->lastError = "cURL Error [$errno]: " . $error;
            error_log("Razorpay cURL Error [$errno]: " . $error);
            return false;
        }

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorMsg = isset($result['error']['description']) ? $result['error']['description'] : json_encode($result);
            $this->lastError = "API Error [$httpCode]: " . $errorMsg;
            error_log("Razorpay API Error [$httpCode]: " . json_encode($result));
            return false;
        }

        return $result;
    }

    /**
     * Generate checkout options for frontend
     *
     * @param array $order Razorpay order
     * @param array $userData User data
     * @param array $planData Plan data
     * @return array
     */
    public function getCheckoutOptions($order, $userData, $planData)
    {
        return [
            'key' => $this->keyId,
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'name' => $_ENV['APP_NAME'] ?? 'Sanskar AI',
            'description' => 'AI Pandit Subscription - ' . $planData['name'],
            'order_id' => $order['id'],
            'prefill' => [
                'name' => $userData['name'] ?? '',
                'email' => $userData['email'] ?? '',
                'contact' => $userData['phone'] ?? ''
            ],
            'notes' => [
                'plan_id' => $planData['id'],
                'plan_name' => $planData['name'],
                'user_id' => $userData['id']
            ],
            'theme' => [
                'color' => '#FF6B35'
            ]
        ];
    }

    /**
     * Validate webhook signature
     *
     * @param string $payload Raw request body
     * @param string $signature Signature from header
     * @return bool
     */
    public function validateWebhookSignature($payload, $signature)
    {
        $webhookSecret = $_ENV['RAZORPAY_WEBHOOK_SECRET'] ?? '';
        $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
        return hash_equals($expectedSignature, $signature);
    }
}
