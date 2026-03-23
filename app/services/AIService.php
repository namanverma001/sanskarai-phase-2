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

        if ($type === 'budget_generation') {
            $systemPrompt = "You are Sanskar AI, an expert in Hindu ritual planning and budgeting in India. Generate detailed, realistic cost estimates in the specified JSON format. Always respond with valid JSON only, no additional text.";
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

        if ($type === 'ritual_generation' || $type === 'shop_finder' || $type === 'budget_generation') {
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
        if ($type === 'ritual_generation' || $type === 'shop_finder' || $type === 'budget_generation') {
            $parsedContent = json_decode($content, true);
            if ($parsedContent) {
                if ($type === 'ritual_generation') {
                    $responseData['ritual'] = $parsedContent;
                } elseif ($type === 'shop_finder') {
                    $responseData['shops'] = $parsedContent['shops'] ?? [];
                } elseif ($type === 'budget_generation') {
                    $responseData['categories'] = $parsedContent['categories'] ?? [];
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

    /**
     * Generate a category-wise budget estimate for a Hindu ritual
     *
     * @param int   $userId   Authenticated user ID
     * @param array $criteria Keys: ritual_type, location, guest_count, tier (basic|standard|premium)
     * @return array ['success' => bool, 'budget' => array, 'request_id' => int]
     *               or ['success' => false, 'error' => string]
     */
    public function generateBudget(int $userId, array $criteria): array
    {
        $prompt = $this->buildBudgetGenerationPrompt($criteria);

        $requestId = $this->aiRequestModel->createRequest($userId, 'budget_generation', $prompt, $criteria);

        try {
            $startTime = microtime(true);

            $response = $this->getResponse($prompt, 'budget_generation', $criteria);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            $this->aiRequestModel->updateWithResponse($requestId, $response['text'], [
                'tokens_used' => $response['tokens'] ?? 0,
                'processing_time_ms' => $processingTime,
            ]);

            $this->aiRequestModel->log('info', 'budget_generation_complete', 'Budget generated successfully', [], $requestId);

            $categories = $response['data']['categories'] ?? [];

            return [
                'success' => true,
                'budget' => $categories,
                'request_id' => $requestId,
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());
            $this->aiRequestModel->log('error', 'budget_generation_failed', $e->getMessage(), [], $requestId);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build the prompt for budget generation
     */
    private function buildBudgetGenerationPrompt(array $criteria): string
    {
        $ritualType  = $criteria['ritual_type']  ?? 'Hindu Ritual';
        $location    = $criteria['location']     ?? 'India';
        $guestCount  = $criteria['guest_count']  ?? 50;
        $tier        = ucfirst($criteria['tier'] ?? 'standard');

        return <<<PROMPT
You are an expert in Hindu ritual planning and budgeting in India. Generate a detailed, realistic cost estimate for the following ritual.

RITUAL DETAILS:
- Ritual Type: {$ritualType}
- Location: {$location}
- Number of Guests: {$guestCount}
- Budget Tier: {$tier} (Basic = economical, Standard = moderate, Premium = lavish)

INSTRUCTIONS:
1. Provide cost estimates in Indian Rupees (INR) appropriate for the tier and guest count.
2. You MUST include ALL six categories: Pandit Fee, Decoration, Puja Items, Venue, Food, Vendor Charges.
3. Each category must have at least one item.
4. For each item provide: item_name (string), estimated_amount (number in INR, no currency symbol), and an optional notes field (string) with brief context.

Respond ONLY with valid JSON in exactly this structure:
{
  "categories": [
    {
      "category": "Pandit Fee",
      "items": [
        { "item_name": "Main Pandit", "estimated_amount": 5000, "notes": "Includes samagri guidance" }
      ]
    },
    {
      "category": "Decoration",
      "items": [...]
    },
    {
      "category": "Puja Items",
      "items": [...]
    },
    {
      "category": "Venue",
      "items": [...]
    },
    {
      "category": "Food",
      "items": [...]
    },
    {
      "category": "Vendor Charges",
      "items": [...]
    }
  ]
}
PROMPT;
    }

    /**
     * Generate an invitation card HTML using OpenAI
     *
     * @param int $userId The user creating the invitation
     * @param array $details Invitation details (occasion_type, occasion_title, event_date, venue, host_name, message, additional_details)
     * @return array ['success' => bool, 'html' => string, 'request_id' => int]
     */
    public function generateInvitationCard(int $userId, array $details): array
    {
        $occasionType = $details['occasion_type'] ?? 'Celebration';
        $occasionTitle = $details['occasion_title'] ?? 'You are Invited!';
        $eventDate = $details['event_date'] ?? '';
        $venue = $details['venue'] ?? '';
        $googleMapsLink = $details['google_maps_link'] ?? '';
        $hostName = $details['host_name'] ?? '';
        $message = $details['message'] ?? '';
        $additionalDetails = $details['additional_details'] ?? '';

        $prompt = "Generate a complete, self-contained HTML page for a stunning, premium invitation card. The page must be a single HTML file with all CSS and JavaScript inline (no external dependencies except Google Font CDNs).

INVITATION DETAILS:
- Occasion Type: {$occasionType}
- Title: {$occasionTitle}
- Event Date: {$eventDate}
- Venue: {$venue}
- Google Maps Link: {$googleMapsLink}
- Host: {$hostName}
- Personal Message: {$message}
- Additional Details: {$additionalDetails}

CRITICAL DESIGN & TECHNICAL REQUIREMENTS:
1. FULLSCREEN & IMMERSIVE: The HTML body must be strictly `height: 100vh; width: 100vw; overflow: hidden; margin: 0; padding: 0;`. The entire card should be a beautifully animated, full-screen experience that fills the user's device whether on mobile or desktop.
2. NO EXTERNAL FILES: Use inline <style> and <script> tags only.
3. DYNAMIC NAME (EXTREMELY IMPORTANT — DO NOT SKIP):
   - You ABSOLUTELY MUST include the literal text {GUEST_NAME} (with curly braces, exactly as shown) in the HTML output.
   - Use it in a greeting like: <h2>Dear {GUEST_NAME},</h2> or <p>Dear {GUEST_NAME},</p>
   - Do NOT write 'Dear Guest' or 'Dear Friend' — you MUST write 'Dear {GUEST_NAME},' so we can replace it dynamically.
   - The {GUEST_NAME} placeholder must appear prominently in the visible text of the invitation, not hidden or in comments.
4. PREMIUM AESTHETICS & COLOURS (JAW-DROPPING FACTOR):
   - YOU MUST USE Google Fonts. Import and use 'Great Vibes' or 'Playfair Display' for the enormous, elegant title and names. Import and use 'Montserrat' or 'Lora' for the readable body text.
   - PERFECT ALIGNMENT: The entire invitation content MUST be perfectly center-aligned (`text-align: center`, `flex-direction: column`, `align-items: center`, `justify-content: center`). It should look symmetrical and balanced like a real printed card.
   - Use ultra-premium, modern web design techniques: smooth multi-stop CSS gradients, glassmorphism (backdrop-filter: blur, semi-transparent borders), soft glowing shadows, and generous spacing/line-height.
   - The design MUST feature a breathtaking, rich color combination based exactly on the Occasion Type: (e.g., Deep emerald/gold for luxurious weddings; Vibrant 3D neon gradients for birthdays; Soft peach/rose gold for baby showers; Warm terracotta/copper for housewarming).
   - Use high-quality CSS-drawn geometric shapes, rich SVG patterns, or gorgeous blending modes for the background.
5. 3D CSS OBJECTS & OCCASION-SPECIFIC DECORATIONS:
   - YOU MUST CREATE actual 3D CSS objects using `perspective`, `transform-style: preserve-3d`, `rotateX`, `rotateY`, `rotateZ`, and `translateZ` — NOT just flat elements with 3D animations.
   - Build real geometric 3D shapes (cubes, rings, spheres, pyramids) out of multiple CSS divs assembled in 3D space, and give them a slow, elegant continuous rotation.
   - VISIBILITY IS CRITICAL: The 3D object MUST be clearly visible. It must have a HIGH `z-index` (e.g., z-index: 50), must NOT be inside any container that has `backdrop-filter: blur` or `overflow: hidden`, and must use bright, solid, occasion-matching colors with `box-shadow` glow so it pops out visually.
   - 3D OBJECT COLOR must match the occasion: Gold/Crimson for weddings, Bright rainbow/neon for birthdays, Saffron/Orange for pujas, Soft Pink for baby showers, Rose Gold for engagement.
   - The 3D object MUST match the occasion:
     - For Weddings/Anniversaries: A 3D rotating wedding ring (torus shape using multiple thin divs arranged in a circle in 3D space) or a 3D heart — in GOLD color.
     - For Birthdays: A 3D rotating birthday cake (stacked cylinders) or a 3D gift box (cube with ribbon) — in BRIGHT NEON colors.
     - For Housewarming/Pujas/Festivals: A 3D rotating Kalash (pot shape built from CSS), or a 3D diya (lamp) with a glowing CSS flame — in SAFFRON/ORANGE.
     - For Baby Showers: A 3D rotating baby cradle, or a 3D rattle — in SOFT PINK/LAVENDER.
     - For Engagement: A 3D rotating diamond ring — in ROSE GOLD/PLATINUM.
   - Place the 3D object elegantly as a decorative element (top-right corner, or beside the title) — it must NOT overlap or block the text.
   - Additionally, add subtle background particles (falling petals, confetti, sparkles, bubbles) appropriate for the occasion using CSS @keyframes.
   - Always add subtle ritual-specific decorative elements (e.g., Om symbol, rangoli patterns for Hindu events; floral arches for weddings; balloons for birthdays).
   - Staggered and smooth entrance animations (fade-in-up, scale-in) for the text elements using CSS @keyframes.
6. EXPERT COPYWRITING & CONTENT PRESENTATION (CRITICAL):
   - DO NOT just spit back the raw input data format (e.g., do not just output 'Date: March 10' or 'Host: Sanchit').
   - You MUST act as an expert invitation copywriter and elegantly expand the raw details into a beautiful, flowing, and emotional invitation message.
   - EVEN IF the user provides very little information, YOU MUST still generate a rich, warm, and heartfelt greeting. Fill in beautiful, occasion-appropriate language. For example, if only the host name and occasion type are given, write an elaborate, emotional paragraph like: 'With hearts brimming with joy and gratitude, [Host] warmly invites you to grace this auspicious occasion with your presence and blessings. Your presence would make this celebration truly special and memorable.'
   - For example, instead of 'Host: Sanchit', write 'Sanchit cordially invites you to celebrate...'. 
   - Instead of 'Venue: Nashik', write 'Please join us at our beautiful venue in Nashik...'.
   - Weave the personal message naturally into the flow of the invitation card.
   - PERFECT ALIGNMENT & STRUCTURE: The layout must be immaculate. Use CSS Flexbox or Grid to create a perfectly symmetrical, center-aligned hierarchy (`display: flex; flex-direction: column; align-items: center; justify-content: center`). Elements must be perfectly spaced and never touch the edges.
   - Enormous, beautiful stylized title using the script font ('Great Vibes' or similar).
   - DATE & VENUE HIGHLIGHT BOXES: You MUST prominently display BOTH the Event Date (`{$eventDate}`) AND the Venue (`{$venue}`). Do not bury them in the paragraph. Create beautiful, structurally aligned highlight boxes (e.g., glassmorphism cards with subtle borders) or elegant ribbons to display the Date and Venue. They must stand out immediately to the guest as the most important information.
   - VENUE & MAPS RULE: Inside or directly below the beautifully highlighted Venue box, IF a Google Maps Link is provided (`{$googleMapsLink}`), you MUST create a premium 'Get Directions' button that opens that exact URL.
   - Ensure plenty of breathing room (padding and margin) between the different sections (Who, When, Where).
   - An elegant, subtle footer permanently fixed at the bottom reading: 'Created with ♥ by Sanskar AI'.
   - ZERO SCROLLING: The core content must fit perfectly within the viewport and scale down elegantly for smaller mobile screens using Flexbox/Grid and responsive relative units (vh, vw, rem, %).
7. SANSKAR AI BRANDING (MANDATORY — BOTTOM ONLY):
   - DO NOT add any diagonal watermark or background overlay.
   - You MUST ONLY add a small, elegant footer bar permanently fixed at the very bottom of the page.
   - The footer should read: 'Powered by SanskarAI' in a subtle, small font (e.g., 10px Montserrat), semi-transparent (opacity: 0.5), center-aligned.
   - Style it with `position: fixed; bottom: 0; width: 100%; text-align: center; padding: 6px 0; z-index: 999;` and a very subtle background that blends with the card design.

OUTPUT: Return ONLY the raw HTML code. Do NOT wrap it in markdown blockquotes like ```html. Start exactly with <!DOCTYPE html> and end with </html>.";

        $requestId = $this->aiRequestModel->createRequest($userId, 'invitation_generation', $prompt, $details);

        try {
            $startTime = microtime(true);

            if (empty($this->apiKey)) {
                throw new \Exception('OpenAI API key not configured. Please set AI_API_KEY in .env file.');
            }

            $systemPrompt = "You are an expert web designer specializing in creating beautiful, premium digital invitation cards. You generate complete, self-contained HTML pages with stunning visual designs. Always respond with ONLY the HTML code, no explanations or markdown formatting.";

            $data = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.8,
                'max_tokens' => 8000,
            ];

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
                CURLOPT_TIMEOUT => 120,
                CURLOPT_CONNECTTIMEOUT => 10,
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

            $html = $result['choices'][0]['message']['content'];
            $tokensUsed = $result['usage']['total_tokens'] ?? 0;
            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            // Clean up the response — remove markdown code fences if present
            $html = trim($html);
            if (str_starts_with($html, '```html')) {
                $html = substr($html, 7);
            } elseif (str_starts_with($html, '```')) {
                $html = substr($html, 3);
            }
            if (str_ends_with($html, '```')) {
                $html = substr($html, 0, -3);
            }
            $html = trim($html);

            // SAFETY NET: If AI didn't include {GUEST_NAME}, force-inject it
            if (stripos($html, '{GUEST_NAME}') === false) {
                // Try to replace common AI-generated greetings with our placeholder
                $patterns = [
                    '/Dear\s+Honou?red\s+Guest/i',
                    '/Dear\s+Esteemed\s+Guest/i',
                    '/Dear\s+Beloved\s+Guest/i',
                    '/Dear\s+Special\s+Guest/i',
                    '/Dear\s+Valued\s+Guest/i',
                    '/Dear\s+Guest/i',
                    '/Dear\s+Friend/i',
                ];
                $replaced = false;
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $html)) {
                        $html = preg_replace($pattern, 'Dear {GUEST_NAME}', $html, 1);
                        $replaced = true;
                        break;
                    }
                }
                // If still no match, try to inject after the first <body> tag content
                if (!$replaced && stripos($html, '<body') !== false) {
                    $html = preg_replace('/(<body[^>]*>)/i', '$1<div style="display:none" id="guest-placeholder">{GUEST_NAME}</div>', $html, 1);
                }
            }

            $this->aiRequestModel->updateWithResponse($requestId, $html, [
                'tokens_used' => $tokensUsed,
                'processing_time_ms' => $processingTime,
            ]);

            $this->aiRequestModel->log('info', 'invitation_generation_complete', 'Invitation card generated successfully', [
                'occasion_type' => $occasionType,
                'tokens_used' => $tokensUsed,
            ], $requestId);

            return [
                'success' => true,
                'html' => $html,
                'request_id' => $requestId,
            ];

        } catch (\Exception $e) {
            $this->aiRequestModel->markFailed($requestId, $e->getMessage());
            $this->aiRequestModel->log('error', 'invitation_generation_failed', $e->getMessage(), [], $requestId);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * AI Pandit Chat - Multi-turn conversational assistant
     * Behaves like a real Hindu Pandit giving guidance
     */
    public function panditChat(int $userId, array $messageHistory, array $userDetails = []): array
    {
        // Match assistant language to the user's latest message.
        $lastUserMsg = '';
        for ($i = count($messageHistory) - 1; $i >= 0; $i--) {
            if (($messageHistory[$i]['role'] ?? '') === 'user') {
                $lastUserMsg = (string) ($messageHistory[$i]['content'] ?? '');
                break;
            }
        }

        $responseLanguage = $this->detectPanditResponseLanguage($lastUserMsg);
        $systemPrompt = $this->buildPanditSystemPrompt($userDetails, $responseLanguage);

        try {
            $startTime = microtime(true);

            $response = $this->callOpenAIChat($systemPrompt, $messageHistory);

            $processingTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log the request
            $lastUserMsg = '';
            foreach (array_reverse($messageHistory) as $msg) {
                if ($msg['role'] === 'user') {
                    $lastUserMsg = $msg['content'];
                    break;
                }
            }
            $requestId = $this->aiRequestModel->createRequest($userId, 'pandit_chat', $lastUserMsg, [
                'message_count' => count($messageHistory),
            ]);
            $this->aiRequestModel->updateWithResponse($requestId, $response['text'], [
                'tokens_used' => $response['tokens'] ?? 0,
                'processing_time_ms' => $processingTime,
            ]);

            return [
                'success' => true,
                'answer' => $response['text'],
                'tokens' => $response['tokens'] ?? 0,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Detect what language the user is writing in.
     *
     * - Hindi: user typed in Devanagari script
     * - Hinglish: user typed common romanized Hindi words (heuristic)
     * - English: default (also covers roman Hinglish if heuristic doesn't trigger)
     */
    private function detectPanditResponseLanguage(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return 'English';
        }

        // Devanagari block: U+0900–U+097F (Hindi script).
        if (preg_match('/[\x{0900}-\x{097F}]/u', $text) === 1) {
            return 'Hindi';
        }

        $lower = mb_strtolower($text, 'UTF-8');

        // If user message looks English-forward, keep assistant in English.
        // This prevents cases like "how are you pandit ji" from triggering Hinglish.
        $englishEvidence = preg_match('/\b(how|what|when|where|why|please|help|guide|steps|instructions|should|do|are|is)\b/i', $text) === 1;
        if ($englishEvidence) {
            return 'English';
        }

        // Stronger romanized-Hindi signals (avoid generic "ji/aap" which appear in normal English salutation).
        $hinglishHints = [
            ' kripya', ' bataiye', ' bataa', ' kaise', ' kya', ' hain', ' hai',
            ' puja', ' vrat', ' shubh', ' samagri', ' vidhi',
            ' muhurat', ' navratri', ' griha', ' satyanarayan', ' bhagwan',
        ];

        $hits = 0;
        foreach ($hinglishHints as $hint) {
            if (mb_strpos($lower, $hint) !== false) {
                $hits++;
            }
        }

        // Prefer Hinglish only when there are multiple strong romanized-Hindi signals.
        return $hits >= 3 ? 'Hinglish' : 'English';
    }

    /**
     * Build the AI Pandit system prompt
     */
    private function buildPanditSystemPrompt(array $userDetails = [], string $responseLanguage = 'English'): string
    {
        $responseLanguage = in_array($responseLanguage, ['English', 'Hindi', 'Hinglish'], true)
            ? $responseLanguage
            : 'English';

        $languageLine = match ($responseLanguage) {
            'Hindi' => 'OUTPUT LANGUAGE: Hindi (Devanagari script).',
            'Hinglish' => 'OUTPUT LANGUAGE: Hinglish (Hindi + English mix written in Roman script).',
            default => 'OUTPUT LANGUAGE: English.',
        };

        $unsureFallback = match ($responseLanguage) {
            'Hindi' => 'अगर आप unsure हैं, तो आप अपने कुल पुरोहित से भी सलाह ले सकते हैं।',
            'Hinglish' => 'Iske baare mein aap apne kul purohit se bhi salah le sakte hain.',
            default => 'If you are unsure, you can also consult your kul purohit.',
        };

        $honorificLine = match ($responseLanguage) {
            'Hindi' => 'Use words like "बेटा", "जी", "भगवान की कृपा से", "शुभ हो" naturally.',
            'Hinglish' => 'Use words like "Beta", "Ji", "Bhagwan ki kripa se", "Shubh ho" naturally.',
            default => 'English mode: Keep the full message in English. You may use "Namaste" and "Ji" only as a salutation (not as Hindi replacements inside sentences).',
        };

        $prompt = "You are an experienced Hindu Pandit named \"Pandit Ji\".
$languageLine

Always answer in the same language as the LATEST user message (and keep it consistent for the whole response).
Ignore the language used in previous assistant messages; determine language ONLY from the latest user message.

LANGUAGE CONSTRAINTS:
- English mode: Write complete sentences in English. Avoid Hindi/romanized Hindi words inside the main explanation (except 'Namaste' / 'Ji' salutation).
- English mode: Do NOT use any Devanagari script in your response. Do NOT write romanized Hindi (e.g., 'kripya', 'kaise', 'aap') except the allowed salutation words.
- Hinglish mode: Use a natural Hindi+English mix in Roman script.
- Hindi mode: Use Devanagari script for Hindi.

CORE PERSONALITY:
- Always greet with \"Namaste 🙏\" at the very start of a new conversation.
- Behave like a real human pandit giving guidance — NOT like an AI chatbot or assistant.
- Be warm, respectful, empathetic, and culturally authentic.
- $honorificLine
- Add relevant emojis occasionally (🙏, 🕉️, 🪔, ✨, 📿) but don't overdo it.

CONVERSATION STYLE:
- Do NOT dump all information at once. Ask follow-up questions first to understand the user's situation.
- Keep responses concise (2-4 short paragraphs). Only give detailed step-by-step when specifically asked.
- If someone asks about a ritual (e.g., Griha Pravesh, Satyanarayan Katha), first ask relevant context questions before giving full guidance.
- Ask 1-3 follow-up questions in OUTPUT LANGUAGE that clarify the user's exact situation.

INFORMATION GATHERING:
- If the user hasn't shared personal details, naturally ask for them when relevant:
  * Name
  * Date of Birth
  * Time of Birth
  * Place of Birth
  * Gotra (if known)
  * Community
  * Religion
- Don't ask all of them at once. Ask naturally as the conversation flows.

KNOWLEDGE:
- You are expert in Hindu rituals, pujas, sanskars, festivals, muhurat, mantras, and traditions.
- Give practical, actionable guidance that real families can follow.
- When mentioning mantras, write them in Devanagari with Roman transliteration.
- Suggest appropriate items (samagri) for pujas when relevant.

IMPORTANT RULES:
- Never say \"I am an AI\" or \"As an AI language model\" or anything similar.
- Never break character. You ARE Pandit Ji.
- If asked something outside Hindu dharma, politely redirect.
- If unsure, say in OUTPUT LANGUAGE: \"$unsureFallback\"";

        // Add user details context if available
        if (!empty($userDetails)) {
            $prompt .= "\n\nUSER DETAILS (use these to personalize your responses):";
            if (!empty($userDetails['name'])) {
                $prompt .= "\n- Name: {$userDetails['name']}";
            }
            if (!empty($userDetails['dob'])) {
                $prompt .= "\n- Date of Birth: {$userDetails['dob']}";
            }
            if (!empty($userDetails['birth_time'])) {
                $prompt .= "\n- Time of Birth: {$userDetails['birth_time']}";
            }
            if (!empty($userDetails['birth_place'])) {
                $prompt .= "\n- Place of Birth: {$userDetails['birth_place']}";
            }
            if (!empty($userDetails['gotra'])) {
                $prompt .= "\n- Gotra: {$userDetails['gotra']}";
            }
            if (!empty($userDetails['community'])) {
                $prompt .= "\n- Community: {$userDetails['community']}";
            }
            if (!empty($userDetails['religion'])) {
                $prompt .= "\n- Religion: {$userDetails['religion']}";
            }
        }

        return $prompt;
    }

    /**
     * Call OpenAI API with full message history (multi-turn chat)
     */
    private function callOpenAIChat(string $systemPrompt, array $messages): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('OpenAI API key not configured. Please set AI_API_KEY in .env file.');
        }

        // Build messages array with system prompt + conversation history
        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($messages as $msg) {
            $apiMessages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        $data = [
            'model' => $this->model,
            'messages' => $apiMessages,
            'temperature' => 0.8,
            'max_tokens' => 1500,
            'presence_penalty' => 0.3,
            'frequency_penalty' => 0.2,
        ];

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

        return [
            'text' => $result['choices'][0]['message']['content'],
            'tokens' => $result['usage']['total_tokens'] ?? 0,
        ];
    }
}
