<?php
/**
 * Sanskar AI - AI Service
 * ========================
 * OpenAI Integration for ritual generation and chatbot assistance
 */

namespace App\Services;

use App\Models\AIRequest;

class AIService
{
    private AIRequest $aiRequestModel;
    private string $provider;
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->aiRequestModel = new AIRequest();
        $this->provider = getenv('AI_PROVIDER') ?: 'openai';
        $this->apiKey = getenv('AI_API_KEY') ?: '';
        $this->model = getenv('AI_MODEL') ?: 'gpt-4o-mini';
    }

    /**
     * Generate ritual based on search criteria
     */
    public function generateRitual(int $userId, array $criteria): array
    {
        $prompt = $this->buildRitualGenerationPrompt($criteria);

        $requestId = $this->aiRequestModel->createRequest($userId, 'ritual_generation', $prompt, $criteria);

        try {
            $startTime = microtime(true);

            $response = $this->getResponse($prompt, 'ritual_generation', $criteria);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            $this->aiRequestModel->updateWithResponse($requestId, $response['text'], [
                'tokens_used' => $response['tokens'] ?? 0,
                'processing_time_ms' => $processingTime,
            ]);

            $this->aiRequestModel->log('info', 'ritual_generation_complete', 'Ritual generated successfully', [], $requestId);

            // Parse the ritual data from the response
            $ritualData = $response['data']['ritual'] ?? $this->parseRitualFromText($response['text'], $criteria);

            return [
                'success' => true,
                'request_id' => $requestId,
                'ritual' => $ritualData,
                'raw_response' => $response['text'],
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());
            $this->aiRequestModel->log('error', 'ritual_generation_failed', $e->getMessage(), [], $requestId);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Regenerate ritual incorporating user feedback and historical learning
     */
    public function regenerateRitualWithFeedback(int $userId, array $criteria, array $previousResponse, string $userFeedback, array $pastFeedback = []): array
    {
        $prompt = $this->buildRefinementPrompt($criteria, $previousResponse, $userFeedback, $pastFeedback);

        $requestId = $this->aiRequestModel->createRequest($userId, 'ritual_regeneration', $prompt, array_merge($criteria, [
            'feedback' => $userFeedback,
            'round' => count($pastFeedback) + 1,
        ]));

        try {
            $startTime = microtime(true);

            $response = $this->getResponse($prompt, 'ritual_generation', $criteria);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            $this->aiRequestModel->updateWithResponse($requestId, $response['text'], [
                'tokens_used' => $response['tokens'] ?? 0,
                'processing_time_ms' => $processingTime,
            ]);

            $this->aiRequestModel->log('info', 'ritual_regeneration_complete', 'Ritual regenerated with feedback', [
                'feedback' => $userFeedback,
            ], $requestId);

            $ritualData = $response['data']['ritual'] ?? $this->parseRitualFromText($response['text'], $criteria);

            return [
                'success' => true,
                'request_id' => $requestId,
                'ritual' => $ritualData,
                'raw_response' => $response['text'],
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());
            $this->aiRequestModel->log('error', 'ritual_regeneration_failed', $e->getMessage(), [], $requestId);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build refinement prompt with previous response + feedback + learning
     */
    private function buildRefinementPrompt(array $criteria, array $previousResponse, string $userFeedback, array $pastFeedback = []): string
    {
        $basePrompt = $this->buildRitualGenerationPrompt($criteria);
        
        // Build previous response summary
        $prevName = $previousResponse['name'] ?? 'Unknown';
        $prevSteps = '';
        if (!empty($previousResponse['steps'])) {
            foreach ($previousResponse['steps'] as $step) {
                $prevSteps .= "  Step {$step['step_number']}: {$step['title']}\n";
            }
        }

        // Build learning context from past feedback
        $learningContext = '';
        if (!empty($pastFeedback)) {
            $learningContext = "\n\n**IMPORTANT - LEARNINGS FROM PAST USER FEEDBACK (avoid these mistakes):**\n";
            foreach ($pastFeedback as $i => $fb) {
                $learningContext .= ($i + 1) . ". User feedback: \"{$fb['user_feedback']}\"\n";
            }
        }

        $refinementPrompt = "{$basePrompt}

**CRITICAL REFINEMENT CONTEXT:**

The following ritual was previously generated but the user was NOT satisfied:

Previous Ritual Name: {$prevName}
Previous Steps Summary:
{$prevSteps}

**USER'S FEEDBACK (MUST address this):**
\"{$userFeedback}\"

Please generate an IMPROVED version of this ritual that specifically addresses the user's feedback. 
Keep what was good but fix what the user pointed out.
The user's feedback is the top priority - make sure every point is addressed.
{$learningContext}

REMEMBER: Output must be valid JSON in the exact same format as specified above.";

        return $refinementPrompt;
    }

    /**
     * Chatbot for ritual assistance during execution
     */
    public function chatAssistant(int $userId, array $context): array
    {
        $prompt = $this->buildChatPrompt($context);

        $requestId = $this->aiRequestModel->createRequest($userId, 'ritual_chat', $prompt, $context);

        try {
            $startTime = microtime(true);

            $response = $this->getResponse($prompt, 'ritual_chat', $context);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            $this->aiRequestModel->updateWithResponse($requestId, $response['text'], [
                'tokens_used' => $response['tokens'] ?? 0,
                'processing_time_ms' => $processingTime,
            ]);

            return [
                'success' => true,
                'request_id' => $requestId,
                'answer' => $response['text'],
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get ritual suggestion based on user context
     */
    public function suggestRitual(int $userId, array $context): array
    {
        $prompt = $this->buildPrompt('ritual_suggestion', $context);

        // Create request record
        $requestId = $this->aiRequestModel->createRequest($userId, 'ritual_suggestion', $prompt, $context);

        try {
            $startTime = microtime(true);

            // Get response (mock or real)
            $response = $this->getResponse($prompt, 'ritual_suggestion', $context);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            // Update request with response
            $this->aiRequestModel->updateWithResponse($requestId, $response['text'], [
                'tokens_used' => $response['tokens'] ?? 0,
                'processing_time_ms' => $processingTime,
            ]);

            // Log success
            $this->aiRequestModel->log('info', 'ritual_suggestion_complete', 'Ritual suggestion generated successfully', [], $requestId);

            return [
                'success' => true,
                'request_id' => $requestId,
                'suggestion' => $response['text'],
                'rituals' => $response['data']['rituals'] ?? [],
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());
            $this->aiRequestModel->log('error', 'ritual_suggestion_failed', $e->getMessage(), [], $requestId);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get mantra explanation
     */
    public function explainMantra(int $userId, string $mantra): array
    {
        $prompt = "Explain the meaning and significance of this mantra: $mantra";

        $requestId = $this->aiRequestModel->createRequest($userId, 'mantra_explanation', $prompt);

        try {
            $response = $this->getResponse($prompt, 'mantra_explanation', ['mantra' => $mantra]);

            $this->aiRequestModel->updateWithResponse($requestId, $response['text']);

            return [
                'success' => true,
                'request_id' => $requestId,
                'explanation' => $response['text'],
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get ritual guidance
     */
    public function getRitualGuidance(int $userId, int $ritualId, string $question): array
    {
        $prompt = "Question about ritual #$ritualId: $question";

        $requestId = $this->aiRequestModel->createRequest($userId, 'ritual_guidance', $prompt, [
            'ritual_id' => $ritualId,
        ]);

        try {
            $response = $this->getResponse($prompt, 'ritual_guidance', [
                'ritual_id' => $ritualId,
                'question' => $question,
            ]);

            $this->aiRequestModel->updateWithResponse($requestId, $response['text']);

            return [
                'success' => true,
                'request_id' => $requestId,
                'guidance' => $response['text'],
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get auspicious timing
     */
    public function getAuspiciousTiming(int $userId, array $context): array
    {
        $prompt = $this->buildPrompt('auspicious_timing', $context);

        $requestId = $this->aiRequestModel->createRequest($userId, 'auspicious_timing', $prompt, $context);

        try {
            $response = $this->getResponse($prompt, 'auspicious_timing', $context);

            $this->aiRequestModel->updateWithResponse($requestId, $response['text']);

            return [
                'success' => true,
                'request_id' => $requestId,
                'timing' => $response['text'],
                'dates' => $response['data']['dates'] ?? [],
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Find nearby shops for item
     */
    public function findNearbyShops(string $location, string $item): array
    {
        $prompt = "Identify specific shops or market areas in or near '$location' where I can buy '$item' for a Hindu ritual. 
        Provide a list of 3-5 likely places. 
        For each place, provide a name, approximate distance/location details, and why it's a good matching shop.
        
        OUTPUT JSON FORMAT:
        {
            \"shops\": [
                {
                    \"name\": \"Shop Name\",
                    \"location\": \"Location/Area Details\",
                    \"type\": \"Type of shop (e.g. General Store, Pooja Samagri)\",
                    \"reason\": \"Why this is a good match\"
                }
            ]
        }";

        // We use a mock response logic if provider is 'mock', similar to other methods
        // But here we rely on the getResponse method which handles the provider check
        try {
            // Using 'ritual_generation' logic for structured JSON response from OpenAI if active
            // Or we handle 'shop_finder' custom type if we want mock data
            $response = $this->getResponse($prompt, 'shop_finder', ['location' => $location, 'item' => $item]);

            return [
                'success' => true,
                'shops' => $response['data']['shops'] ?? [],
                'raw' => $response['text']
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build ritual generation prompt
     */
    private function buildRitualGenerationPrompt(array $criteria): string
    {
        $communityName = $criteria['community_name'] ?? 'Hindu';
        $ritualName = $criteria['ritual_name'] ?? '';
        $religion = $criteria['religion'] ?? 'Hinduism';
        $occasion = $criteria['occasion'] ?? '';
        $additionalInfo = $criteria['additional_info'] ?? '';

        $prompt = "Generate a comprehensive and authentic ritual guide for the following:

Community/Tradition: {$communityName}
Religion: {$religion}
Ritual Name: {$ritualName}
Occasion: {$occasion}
Additional Information: {$additionalInfo}

Please rigorously follow this structure and format:

**CRITICAL FORMATTING RULE**: 
Every Hindi/Sanskrit word written in Devanagari script MUST be immediately followed by its English transliteration in brackets.
Example formats:
- गृहप्रवेश (Gruhapravesh)
- दक्षिणा (Dakshina)
- ॐ नमो भगवते वासुदेवाय (Om Namo Bhagavate Vasudevaya)

1. **Ritual Name**: English translated Name
2. **Sanskrit Name**: MUST be in format \"देवनागरी (Transliteration)\"
   Example: \"गृहप्रवेश (Gruhapravesh)\"
3. **Community**: As specified
4. **Purpose**: Clear English 1 or 2 lines
5. **Scriptural Basis**: Mention referenced texts (Vedas, Puranas, etc.) in the 'significance' field.
6. **Items Required**: Each Sanskrit/Local name MUST include transliteration in brackets
7. **Ritual Steps**: MUST include:
   - Sanskrit Title in format \"देवनागरी (Transliteration)\"
   - English Title
   - Who Performs (e.g., Kartan, Pandit)
   - Purpose of the step
   - Method (How to Perform) in numbered points
   - Mantra in format \"देवनागरी (Transliteration)\" + Meaning
8. **Concluding Rites**: Visarjan, Prasad, Dakshina instructions (Include these as the final steps)
9. **Post-Ritual Guidelines**: Clean up and observances (Include as the very last step)

OUTPUT JSON FORMAT:
{
    \"name\": \"Ritual Name in English\",
    \"name_sanskrit\": \"देवनागरी (Transliteration)\",
    \"community_name\": \"{$communityName}\",
    \"religion\": \"{$religion}\",
    \"category\": \"Category (Puja/Sanskar/Festival)\",
    \"description\": \"Brief overview of the ritual including Purpose.\",
    \"significance\": \"Scriptural Basis and Spiritual Significance.\",
    \"duration_minutes\": 60,
    \"difficulty\": \"medium\",
    \"deity\": \"Main Deity\",
    \"best_time\": \"Best time/tithi\",
    \"steps\": [
        {
            \"step_number\": 1,
            \"title\": \"English Title\",
            \"title_sanskrit\": \"देवनागरी (Transliteration)\",
            \"description\": \"Who Performs: [Role]\\nPurpose: [Why]\\n\\nHow to Perform:\\n1. [Instruction]\\n2. [Instruction]...\",
            \"mantra\": \"देवनागरी (Transliteration)\\n\\nNote: If no mantra for this step, use null\",
            \"mantra_meaning\": \"English meaning of the mantra (or null if no mantra)\",
            \"duration_minutes\": 5,
            \"is_optional\": false,
            \"special_instructions\": \"Practical Notes and Tips (use transliteration for any Devanagari words)\",
            \"items_needed\": \"List of items for this step (use transliteration for any Devanagari words)\"
        }
    ],
    \"items\": [
        {
            \"item_name\": \"English Name\",
            \"item_name_local\": \"देवनागरी (Transliteration)\",
            \"quantity\": 1,
            \"unit\": \"unit\",
            \"is_mandatory\": true,
            \"description\": \"Category (e.g., Panchamrit, Aushadhi, Vastram) - [Description]\"
        }
    ]
}

REMEMBER: Every single Devanagari word MUST have (Transliteration) in brackets immediately after it.
Provide 10-20 detailed steps covering the entire ritual from preparation to conclusion. Be religiously accurate.";

        return $prompt;
    }

    /**
     * Build chat assistant prompt
     */
    private function buildChatPrompt(array $context): string
    {
        $ritualName = $context['ritual_name'] ?? '';
        $ritualDescription = $context['ritual_description'] ?? '';
        $currentStep = $context['current_step'] ?? [];
        $stepNumber = $context['step_number'] ?? 1;
        $question = $context['question'] ?? '';
        $allSteps = $context['all_steps'] ?? [];

        $stepsContext = '';
        if (!empty($allSteps)) {
            $stepsContext = "\n\nAll steps of this ritual:\n";
            foreach ($allSteps as $step) {
                $stepsContext .= "Step {$step['step_number']}: {$step['title']}\n";
                if (!empty($step['description'])) {
                    $stepsContext .= "Description: {$step['description']}\n";
                }
                if (!empty($step['items_needed'])) {
                    $stepsContext .= "Items needed: {$step['items_needed']}\n";
                }
                $stepsContext .= "\n";
            }
        }

        $currentStepContext = '';
        if (!empty($currentStep)) {
            $currentStepContext = "\n\nUser is currently on Step {$stepNumber}: {$currentStep['title']}";
            if (!empty($currentStep['description'])) {
                $currentStepContext .= "\nStep description: {$currentStep['description']}";
            }
            if (!empty($currentStep['mantra'])) {
                $currentStepContext .= "\nMantra: {$currentStep['mantra']}";
            }
            if (!empty($currentStep['items_needed'])) {
                $currentStepContext .= "\nItems needed for this step: {$currentStep['items_needed']}";
            }
        }

        $prompt = "You are a knowledgeable spiritual assistant helping someone perform a ritual. Be helpful, respectful, and provide accurate information.

Ritual being performed: {$ritualName}
Ritual description: {$ritualDescription}
{$stepsContext}
{$currentStepContext}

User's question: {$question}

Please provide a helpful, concise answer. If the user is asking about alternatives for items, suggest appropriate substitutes. If they're confused about a step, explain it clearly. Be respectful of traditions while being practical.";

        return $prompt;
    }

    /**
     * Build prompt based on type and context
     */
    private function buildPrompt(string $type, array $context): string
    {
        switch ($type) {
            case 'ritual_suggestion':
                $occasion = $context['occasion'] ?? 'general';
                $family = $context['family_info'] ?? '';
                return "Suggest appropriate Hindu rituals for occasion: $occasion. Family details: $family";

            case 'auspicious_timing':
                $ritual = $context['ritual_name'] ?? 'puja';
                $month = $context['month'] ?? date('F');
                return "Find auspicious dates for $ritual in $month based on Hindu calendar.";

            default:
                return json_encode($context);
        }
    }

    /**
     * Get response based on provider
     */
    private function getResponse(string $prompt, string $type, array $context = []): array
    {
        if ($this->provider === 'openai') {
            return $this->callOpenAI($prompt, $type);
        }
        
        throw new \Exception('Invalid AI provider configured. Please set AI_PROVIDER to "openai" in .env file.');
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $prompt, string $type = 'general'): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API key not configured. Please set AI_API_KEY in .env file.');
        }

        $systemPrompt = "You are Sanskar AI, a knowledgeable assistant specializing in Hindu rituals, traditions, and spiritual practices. Provide accurate, respectful, and helpful information.";

        if ($type === 'ritual_generation') {
            $systemPrompt = "You are Sanskar AI, an expert in Hindu and Indian religious rituals and traditions. Generate detailed, accurate ritual guides in the specified JSON format. Always respond with valid JSON only, no additional text.";
        }

        $data = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 4000,
        ];

        if ($type === 'ritual_generation' || $type === 'shop_finder') {
            $data['response_format'] = ['type' => 'json_object'];
        }

        // Increase PHP execution time limit for AI requests (they can take 60+ seconds)
        set_time_limit(120);

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 90,
            CURLOPT_CONNECTTIMEOUT => 10,
            // SSL options for Windows development environment
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("cURL Error: $error");
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMessage = $errorData['error']['message'] ?? "HTTP Error: $httpCode";
            throw new \Exception("OpenAI API Error: $errorMessage");
        }

        $result = json_decode($response, true);

        if (!isset($result['choices'][0]['message']['content'])) {
            throw new \Exception('Invalid response from OpenAI API');
        }

        $content = $result['choices'][0]['message']['content'];
        $tokensUsed = $result['usage']['total_tokens'] ?? 0;

        $responseData = [];
        if ($type === 'ritual_generation' || $type === 'shop_finder') {
            $parsedContent = json_decode($content, true);
            if ($parsedContent) {
                if ($type === 'ritual_generation') {
                    $responseData['ritual'] = $parsedContent;
                } elseif ($type === 'shop_finder') {
                    $responseData['shops'] = $parsedContent['shops'] ?? [];
                }
            }
        }

        return [
            'text' => $content,
            'tokens' => $tokensUsed,
            'data' => $responseData,
        ];
    }

    /**
     * Parse ritual data from text response (fallback)
     */
    private function parseRitualFromText(string $text, array $criteria): array
    {
        // Try to extract JSON from text
        if (preg_match('/\{[\s\S]*\}/m', $text, $matches)) {
            $parsed = json_decode($matches[0], true);
            if ($parsed) {
                return $parsed;
            }
        }

        // Fallback structure
        return [
            'name' => $criteria['ritual_name'] ?? 'Generated Ritual',
            'community_name' => $criteria['community_name'] ?? null,
            'religion' => $criteria['religion'] ?? 'Hinduism',
            'category' => 'General',
            'description' => $text,
            'duration_minutes' => 60,
            'difficulty' => 'medium',
            'steps' => [],
            'items' => [],
        ];
    }

    /**
     * Get user's request history
     */
    public function getHistory(int $userId, int $limit = 20): array
    {
        return $this->aiRequestModel->getByUser($userId, $limit);
    }

    /**
     * Get request details
     */
    public function getRequest(int $requestId): ?array
    {
        return $this->aiRequestModel->find($requestId);
    }

    /**
     * Moderate review text for spam, abuse, and fake content
     * @param string $reviewText The review text to moderate
     * @param int $rating The rating given (1-5)
     * @param string $targetType 'pandit' or 'vendor'
     * @return array ['flagged' => bool, 'reason' => string|null, 'confidence' => float]
     */
    public function moderateReview(string $reviewText, int $rating, string $targetType): array
    {
        // Basic validation - empty or too short reviews auto-pass
        if (empty(trim($reviewText)) || mb_strlen(trim($reviewText)) < 10) {
            return ['flagged' => false, 'reason' => null, 'confidence' => 1.0];
        }

        // First, run quick local pattern checks (faster, no API cost)
        $localCheck = $this->runLocalModerationChecks($reviewText, $rating);
        if ($localCheck['flagged']) {
            return $localCheck;
        }

        // If local checks pass, use AI for deeper analysis
        if ($this->provider === 'openai' && !empty($this->apiKey)) {
            try {
                return $this->runAIModerationCheck($reviewText, $rating, $targetType);
            } catch (\Exception $e) {
                error_log("AI moderation failed: " . $e->getMessage());
                // Fall back to local check result if AI fails
                return $localCheck;
            }
        }

        return ['flagged' => false, 'reason' => null, 'confidence' => 0.7];
    }

    /**
     * Run local pattern-based moderation checks
     */
    private function runLocalModerationChecks(string $text, int $rating): array
    {
        $text = strtolower(trim($text));
        $confidence = 0.8;

        // Check for spam patterns
        $spamPatterns = [
            '/(.)\1{5,}/',                          // Repeated characters (aaaaaaa)
            '/\b(spam|fake|test)\b/i',              // Spam keywords
            '/\b(buy|sell|discount|offer|click)\s+\w*\s*(here|now)/i', // Commercial spam
            '/https?:\/\/[^\s]+/i',                 // URLs (often spam)
            '/[\$£€]\d+/',                          // Price mentions (spam indicator)
            '/\b(whatsapp|telegram|call me)\b/i',   // Contact solicitation
            '/(^|\s)@\w+/i',                        // Social media handles
        ];

        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return ['flagged' => true, 'reason' => 'Detected spam pattern', 'confidence' => $confidence];
            }
        }

        // Check for abusive/offensive language
        $abusePatterns = [
            '/\b(idiot|stupid|fool|dumb|worst|terrible|horrible|scam|fraud|cheat|liar|thief|steal|robber)\b/i',
            '/\b(hate|sucks|disgusting|pathetic|useless|garbage|trash|crap|lousy)\b/i',
        ];

        foreach ($abusePatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return ['flagged' => true, 'reason' => 'Potentially abusive language detected', 'confidence' => $confidence];
            }
        }

        // Check for fake praise patterns (5 stars with generic text)
        if ($rating === 5) {
            $fakePraisePatterns = [
                '/^(good|nice|great|excellent|best|awesome|amazing|wonderful|fantastic|superb)[.!]*$/i',
                '/^(very (good|nice)|highly recommended?)[.!]*$/i',
                '/^(five stars?|5 stars?)[.!]*$/i',
            ];

            foreach ($fakePraisePatterns as $pattern) {
                if (preg_match($pattern, $text)) {
                    return ['flagged' => true, 'reason' => 'Generic or potentially fake review', 'confidence' => 0.6];
                }
            }
        }

        // Check for copy-paste patterns (multiple sentences that look templated)
        if (preg_match('/lorem ipsum/i', $text)) {
            return ['flagged' => true, 'reason' => 'Placeholder text detected', 'confidence' => 0.95];
        }

        // Check for extreme contrast (5 stars with negative words or 1 star with positive words)
        $positiveWords = ['excellent', 'amazing', 'wonderful', 'great', 'best', 'love', 'perfect', 'outstanding'];
        $negativeWords = ['bad', 'poor', 'terrible', 'worst', 'horrible', 'disappointing', 'awful', 'never'];

        $hasPositive = false;
        $hasNegative = false;

        foreach ($positiveWords as $word) {
            if (stripos($text, $word) !== false) {
                $hasPositive = true;
                break;
            }
        }

        foreach ($negativeWords as $word) {
            if (stripos($text, $word) !== false) {
                $hasNegative = true;
                break;
            }
        }

        // Flag if there's a mismatch between rating and sentiment
        if (($rating >= 4 && $hasNegative && !$hasPositive) || ($rating <= 2 && $hasPositive && !$hasNegative)) {
            return ['flagged' => true, 'reason' => 'Rating-sentiment mismatch detected', 'confidence' => 0.7];
        }

        return ['flagged' => false, 'reason' => null, 'confidence' => $confidence];
    }

    /**
     * Run AI-based moderation check using OpenAI
     */
    private function runAIModerationCheck(string $text, int $rating, string $targetType): array
    {
        $targetLabel = $targetType === 'pandit' ? 'Pandit (Hindu priest)' : 'Vendor';

        $prompt = "Analyze this review for a {$targetLabel} service. The user gave {$rating} out of 5 stars.

Review text: \"{$text}\"

Check for:
1. Spam content (promotional, irrelevant, repetitive)
2. Fake praise (generic, templated, bot-like patterns)
3. Abusive or offensive language
4. Rating-sentiment mismatch (positive words with low rating or vice versa)
5. Copy-pasted or bot-generated content

Respond in JSON format:
{
    \"flagged\": true/false,
    \"reason\": \"reason if flagged, null if not\",
    \"confidence\": 0.0-1.0
}

Be lenient with genuine reviews. Only flag clearly problematic content.";

        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a content moderation assistant. Analyze reviews for authenticity and appropriateness. Respond only with valid JSON.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3,
            'max_tokens' => 200,
            'response_format' => ['type' => 'json_object'],
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || $httpCode !== 200) {
            throw new \Exception("AI moderation API error: " . ($error ?: "HTTP $httpCode"));
        }

        $result = json_decode($response, true);
        $content = $result['choices'][0]['message']['content'] ?? '{}';
        $parsed = json_decode($content, true);

        return [
            'flagged' => $parsed['flagged'] ?? false,
            'reason' => $parsed['reason'] ?? null,
            'confidence' => (float)($parsed['confidence'] ?? 0.8),
        ];
    }
}
