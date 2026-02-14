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
        $this->provider = getenv('AI_PROVIDER') ?: 'mock';
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

1. **Ritual Name**: English Name (Sanskrit Name in Devanagari)
2. **Community**: As specified
3. **Purpose**: Clear English 1 or 2 lines
4. **Scriptural Basis**: Mention referenced texts (Vedas, Puranas, etc.) in the 'significance' field.
5. **Items Required**: Authentically listed with Sanskrit/Local names.
6. **Ritual Steps**: MUST include:
   - Sanskrit Title (e.g., संकल्पः) in Devanagari
   - English Title & Transliteration
   - Who Performs (e.g., Kartan, Pandit)
   - Purpose of the step
   - Method (How to Perform) in numbered points
   - Mantra in Devanagari + Transliteration + Meaning
7. **Concluding Rites**: Visarjan, Prasad, Dakshina instructions (Include these as the final steps)
8. **Post-Ritual Guidelines**: Clean up and observances (Include as the very last step)

OUTPUT JSON FORMAT:
{
    \"name\": \"Ritual Name in English\",
    \"name_sanskrit\": \"Ritual Name in Devanagari\",
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
            \"title\": \"English Title (Transliteration)\",
            \"title_sanskrit\": \"Devanagari Title Only (e.g. संकल्पः)\",
            \"description\": \"Who Performs: [Role]\\nPurpose: [Why]\\n\\nHow to Perform:\\n1. [Instruction]\\n2. [Instruction]...\",
            \"mantra\": \"Devanagari Mantra\\n\\n(Transliteration)\",
            \"mantra_meaning\": \"English meaning of the mantra\",
            \"duration_minutes\": 5,
            \"is_optional\": false,
            \"special_instructions\": \"Practical Notes and Tips\",
            \"items_needed\": \"List of items for this step\"
        }
    ],
    \"items\": [
        {
            \"item_name\": \"English Name\",
            \"item_name_local\": \"Sanskrit/Local Name\",
            \"quantity\": 1,
            \"unit\": \"unit\",
            \"is_mandatory\": true,
            \"description\": \"Category (e.g., Panchamrit, Aushadhi, Vastram) - [Description]\"
        }
    ]
}

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
        switch ($this->provider) {
            case 'openai':
                return $this->callOpenAI($prompt, $type);

            case 'mock':
            default:
                return $this->getMockResponse($type, $context);
        }
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
     * Mock response generator
     */
    private function getMockResponse(string $type, array $context): array
    {
        // Simulate processing delay
        usleep(rand(100000, 500000)); // 100-500ms

        switch ($type) {
            case 'ritual_generation':
                return $this->getMockRitualGeneration($context);

            case 'ritual_chat':
                return $this->getMockChatResponse($context);

            case 'ritual_suggestion':
                return [
                    'text' => "Based on your query, I recommend the following rituals:\n\n" .
                        "1. **Ganesh Puja** - Always begin with Lord Ganesha for removing obstacles\n" .
                        "2. **Satyanarayan Puja** - For prosperity and fulfillment of wishes\n" .
                        "3. **Navgraha Shanti** - For planetary peace and balance\n\n" .
                        "These rituals are suitable for your occasion and can be performed with a qualified pandit.",
                    'tokens' => rand(100, 300),
                    'data' => [
                        'rituals' => [
                            ['id' => 1, 'name' => 'Ganesh Puja', 'relevance' => 'high'],
                            ['id' => 2, 'name' => 'Satyanarayan Puja', 'relevance' => 'high'],
                            ['id' => 4, 'name' => 'Navgraha Shanti', 'relevance' => 'medium'],
                        ],
                    ],
                ];

            case 'mantra_explanation':
                return [
                    'text' => "This is a sacred mantra with deep spiritual significance.\n\n" .
                        "**Meaning**: The mantra invokes divine blessings and protection.\n\n" .
                        "**Pronunciation Guide**: Each syllable should be pronounced clearly with proper intonation.\n\n" .
                        "**When to Chant**: Best recited during morning prayers or before important activities.\n\n" .
                        "**Benefits**: Regular chanting brings peace, clarity, and spiritual growth.",
                    'tokens' => rand(150, 250),
                    'data' => [],
                ];

            case 'ritual_guidance':
                return [
                    'text' => "Here's guidance for your question:\n\n" .
                        "**Preparation**: Ensure the puja area is clean and all items are arranged.\n\n" .
                        "**Key Steps**:\n" .
                        "1. Begin with Sankalp (intention setting)\n" .
                        "2. Perform Dhyan (meditation)\n" .
                        "3. Follow the prescribed mantras\n" .
                        "4. Complete with Aarti and Prasad distribution\n\n" .
                        "**Note**: For complex rituals, consulting a qualified pandit is recommended.",
                    'tokens' => rand(120, 220),
                    'data' => [],
                ];

            case 'auspicious_timing':
                $dates = [];
                $currentMonth = date('n');
                for ($i = 1; $i <= 3; $i++) {
                    $day = rand(1, 28);
                    $dates[] = [
                        'date' => date('Y') . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT),
                        'tithi' => ['Purnima', 'Ekadashi', 'Chaturthi'][rand(0, 2)],
                        'nakshatra' => ['Rohini', 'Pushya', 'Uttara Phalguni'][rand(0, 2)],
                        'auspicious_time' => sprintf('%02d:00 - %02d:00', rand(6, 10), rand(11, 14)),
                    ];
                }

                return [
                    'text' => "Based on the Hindu Panchang, here are auspicious dates:\n\n" .
                        implode("\n", array_map(function ($d) {
                            return "📅 **{$d['date']}** - {$d['tithi']} ({$d['nakshatra']})\n   Best time: {$d['auspicious_time']}";
                        }, $dates)),
                    'tokens' => rand(100, 200),
                    'data' => ['dates' => $dates],
                ];

            case 'shop_finder':
                $items = ['Puja Bhandar', 'General Store', 'Supermarket'];
                $location = $context['location'] ?? 'Market';
                return [
                    'text' => "Found 3 shops near $location",
                    'tokens' => 150,
                    'data' => [
                        'shops' => [
                            [
                                'name' => 'Shri Ganesh Puja Bhandar',
                                'location' => "Near $location Main Chowk (0.5 km)",
                                'type' => 'Pooja Samagri',
                                'reason' => 'Specializes in all ritual items with good quality.'
                            ],
                            [
                                'name' => 'City General Store',
                                'location' => "$location Market Road (1.2 km)",
                                'type' => 'General Store',
                                'reason' => 'Likely to have common items like rice, ghee, and coconut.'
                            ],
                            [
                                'name' => 'Aggarwal Sweet & Pooja',
                                'location' => "Opposite Temple, $location (0.8 km)",
                                'type' => 'Sweets & Rituals',
                                'reason' => 'Good for prasad items and basic puja needs.'
                            ]
                        ]
                    ]
                ];

            default:
                return [
                    'text' => "I understand your query. For detailed guidance, please consult with a qualified pandit through our platform.",
                    'tokens' => rand(50, 100),
                    'data' => [],
                ];
        }
    }

    /**
     * Mock ritual generation
     */
    private function getMockRitualGeneration(array $context): array
    {
        $ritualName = $context['ritual_name'] ?? 'Traditional Puja';
        $community = $context['community_name'] ?? 'Hindu';
        $religion = $context['religion'] ?? 'Hinduism';

        $ritual = [
            'name' => $ritualName,
            'name_sanskrit' => 'पूजा',
            'community_name' => $community,
            'religion' => $religion,
            'category' => 'Puja',
            'description' => "A sacred $ritualName ceremony performed in the $community tradition. This ritual honors the divine and brings blessings to the family.",
            'significance' => 'This ritual connects the devotee with divine energy, purifies the mind and soul, and brings peace and prosperity to the household.',
            'duration_minutes' => 60,
            'difficulty' => 'medium',
            'deity' => 'Lord Ganesha',
            'best_time' => 'Morning (6 AM - 9 AM) or Evening (5 PM - 7 PM)',
            'steps' => [
                [
                    'step_number' => 1,
                    'title' => 'Sankalp (Setting Intention)',
                    'title_sanskrit' => 'संकल्प',
                    'description' => 'Begin by taking a sankalp, declaring your intention for performing this puja. Hold water and rice in your palms while reciting your intention.',
                    'mantra' => 'ॐ विष्णुर्विष्णुर्विष्णुः',
                    'mantra_meaning' => 'Invoking Lord Vishnu as witness to your sacred vow',
                    'duration_minutes' => 5,
                    'is_optional' => false,
                    'special_instructions' => 'Face east while taking sankalp',
                    'items_needed' => 'Water, Rice (Akshat)',
                ],
                [
                    'step_number' => 2,
                    'title' => 'Ganesh Vandana',
                    'title_sanskrit' => 'गणेश वंदना',
                    'description' => 'Begin by invoking Lord Ganesha to remove all obstacles from the puja.',
                    'mantra' => 'ॐ गं गणपतये नमः',
                    'mantra_meaning' => 'Salutations to Lord Ganesha, the remover of obstacles',
                    'duration_minutes' => 5,
                    'is_optional' => false,
                    'special_instructions' => 'Offer modak or laddu to Lord Ganesha',
                    'items_needed' => 'Ganesh idol or image, Modak/Laddu, Flowers',
                ],
                [
                    'step_number' => 3,
                    'title' => 'Kalash Sthapana',
                    'title_sanskrit' => 'कलश स्थापना',
                    'description' => 'Establish the sacred kalash (pot) filled with water, topped with coconut and mango leaves.',
                    'mantra' => 'कलशस्य मुखे विष्णुः कण्ठे रुद्रः समाश्रितः',
                    'mantra_meaning' => 'Vishnu resides at the mouth of the kalash, Rudra at its neck',
                    'duration_minutes' => 5,
                    'is_optional' => false,
                    'special_instructions' => 'Place kalash on rice grains',
                    'items_needed' => 'Copper or brass pot, Water, Coconut, Mango leaves, Rice',
                ],
                [
                    'step_number' => 4,
                    'title' => 'Dhyan (Meditation)',
                    'title_sanskrit' => 'ध्यान',
                    'description' => 'Close your eyes and meditate on the deity you are worshiping. Visualize their divine form.',
                    'mantra' => 'ॐ शान्तिः शान्तिः शान्तिः',
                    'mantra_meaning' => 'Om, Peace, Peace, Peace',
                    'duration_minutes' => 10,
                    'is_optional' => false,
                    'special_instructions' => 'Sit in a comfortable position with spine straight',
                    'items_needed' => 'Asana (mat) for sitting',
                ],
                [
                    'step_number' => 5,
                    'title' => 'Avahan (Invocation)',
                    'title_sanskrit' => 'आवाहन',
                    'description' => 'Invoke the presence of the deity into the idol or image.',
                    'mantra' => 'आवाहयामि देवेश सर्वलोकहितैषिणम्',
                    'mantra_meaning' => 'I invoke the Lord of Gods who wishes well for all worlds',
                    'duration_minutes' => 5,
                    'is_optional' => false,
                    'special_instructions' => 'Ring the bell gently while reciting',
                    'items_needed' => 'Bell, Flowers',
                ],
                [
                    'step_number' => 6,
                    'title' => 'Shodashopachara Puja',
                    'title_sanskrit' => 'षोडशोपचार पूजा',
                    'description' => 'Offer the 16 forms of worship including water for feet, arghya, achamana, etc.',
                    'mantra' => null,
                    'mantra_meaning' => null,
                    'duration_minutes' => 15,
                    'is_optional' => false,
                    'special_instructions' => 'Offer each item with devotion and proper mantras',
                    'items_needed' => 'Panchamrit, Flowers, Incense, Lamp, Naivedya',
                ],
                [
                    'step_number' => 7,
                    'title' => 'Aarti',
                    'title_sanskrit' => 'आरती',
                    'description' => 'Perform aarti by waving the lit lamp in circular motion before the deity.',
                    'mantra' => 'जय जगदीश हरे, स्वामी जय जगदीश हरे',
                    'mantra_meaning' => 'Victory to the Lord of the Universe',
                    'duration_minutes' => 5,
                    'is_optional' => false,
                    'special_instructions' => 'Ring the bell while performing aarti',
                    'items_needed' => 'Aarti thali, Ghee lamp, Incense, Bell',
                ],
                [
                    'step_number' => 8,
                    'title' => 'Prasad Distribution',
                    'title_sanskrit' => 'प्रसाद वितरण',
                    'description' => 'Distribute the blessed prasad to all family members and devotees.',
                    'mantra' => null,
                    'mantra_meaning' => null,
                    'duration_minutes' => 5,
                    'is_optional' => false,
                    'special_instructions' => 'First offer to elders, then to others',
                    'items_needed' => 'Prasad (fruits, sweets)',
                ],
            ],
            'items' => [
                ['item_name' => 'Ganesh Idol', 'item_name_local' => 'गणेश मूर्ति', 'quantity' => 1, 'unit' => 'piece', 'is_mandatory' => true, 'description' => 'Small brass or clay idol', 'alternatives' => 'Picture of Lord Ganesha'],
                ['item_name' => 'Kalash', 'item_name_local' => 'कलश', 'quantity' => 1, 'unit' => 'piece', 'is_mandatory' => true, 'description' => 'Copper or brass pot', 'alternatives' => 'Steel pot'],
                ['item_name' => 'Coconut', 'item_name_local' => 'नारियल', 'quantity' => 1, 'unit' => 'piece', 'is_mandatory' => true, 'description' => 'Fresh coconut with husk', 'alternatives' => null],
                ['item_name' => 'Rice', 'item_name_local' => 'चावल (अक्षत)', 'quantity' => 250, 'unit' => 'grams', 'is_mandatory' => true, 'description' => 'Unbroken rice grains', 'alternatives' => null],
                ['item_name' => 'Flowers', 'item_name_local' => 'फूल', 'quantity' => 1, 'unit' => 'bunch', 'is_mandatory' => true, 'description' => 'Fresh flowers, preferably marigold', 'alternatives' => 'Rose petals'],
                ['item_name' => 'Incense', 'item_name_local' => 'अगरबत्ती', 'quantity' => 1, 'unit' => 'pack', 'is_mandatory' => true, 'description' => 'Incense sticks', 'alternatives' => 'Dhoop'],
                ['item_name' => 'Ghee Lamp', 'item_name_local' => 'घी का दीया', 'quantity' => 1, 'unit' => 'piece', 'is_mandatory' => true, 'description' => 'Brass lamp', 'alternatives' => 'Oil lamp'],
                ['item_name' => 'Ghee', 'item_name_local' => 'घी', 'quantity' => 100, 'unit' => 'grams', 'is_mandatory' => true, 'description' => 'Pure ghee for lamp', 'alternatives' => 'Sesame oil'],
                ['item_name' => 'Cotton Wicks', 'item_name_local' => 'रुई की बत्ती', 'quantity' => 10, 'unit' => 'pieces', 'is_mandatory' => true, 'description' => 'For lighting lamp', 'alternatives' => null],
                ['item_name' => 'Fruits', 'item_name_local' => 'फल', 'quantity' => 5, 'unit' => 'pieces', 'is_mandatory' => true, 'description' => 'Seasonal fruits for offering', 'alternatives' => null],
                ['item_name' => 'Sweets', 'item_name_local' => 'मिठाई', 'quantity' => 250, 'unit' => 'grams', 'is_mandatory' => false, 'description' => 'For prasad', 'alternatives' => 'Homemade halwa'],
                ['item_name' => 'Bell', 'item_name_local' => 'घंटी', 'quantity' => 1, 'unit' => 'piece', 'is_mandatory' => true, 'description' => 'Brass bell', 'alternatives' => null],
                ['item_name' => 'Red Cloth', 'item_name_local' => 'लाल कपड़ा', 'quantity' => 1, 'unit' => 'piece', 'is_mandatory' => false, 'description' => 'For covering puja thali', 'alternatives' => 'Yellow cloth'],
            ],
        ];

        return [
            'text' => json_encode($ritual),
            'tokens' => rand(500, 800),
            'data' => ['ritual' => $ritual],
        ];
    }

    /**
     * Mock chat response
     */
    private function getMockChatResponse(array $context): array
    {
        $question = strtolower($context['question'] ?? '');

        $responses = [
            'alternative' => "If you don't have the exact item, here are some alternatives:\n\n" .
                "• **Ghee**: You can use sesame oil (til ka tel) or mustard oil as an alternative.\n" .
                "• **Mango leaves**: You can use betel leaves (paan) or any auspicious leaves.\n" .
                "• **Specific flowers**: Any fresh, fragrant flowers can be used.\n" .
                "• **Specific fruits**: Any seasonal fruits are acceptable.\n\n" .
                "The most important thing is your devotion and pure intention. The divine accepts offerings made with a sincere heart.",

            'confused' => "Let me help clarify this step:\n\n" .
                "1. First, ensure your puja space is clean and you are facing east or north.\n" .
                "2. Take a few deep breaths to center yourself.\n" .
                "3. Follow the instructions step by step, don't rush.\n" .
                "4. If you don't know the mantra pronunciation, you can say it in your mind with intention.\n\n" .
                "Would you like me to explain any specific part in more detail?",

            'default' => "Thank you for your question! Here's what I can tell you:\n\n" .
                "In traditional practice, the ritual steps are designed to create a sacred space and invoke divine presence. " .
                "The key is to perform each action with mindfulness and devotion.\n\n" .
                "If you're unsure about anything, it's always acceptable to proceed with what you have available. " .
                "The sincerity of your worship is what matters most.\n\n" .
                "Is there anything specific you'd like me to explain further?",
        ];

        $responseText = $responses['default'];

        if (
            strpos($question, 'alternative') !== false || strpos($question, 'substitute') !== false ||
            strpos($question, 'don\'t have') !== false || strpos($question, 'dont have') !== false ||
            strpos($question, 'instead') !== false || strpos($question, 'replace') !== false
        ) {
            $responseText = $responses['alternative'];
        } elseif (
            strpos($question, 'confused') !== false || strpos($question, 'understand') !== false ||
            strpos($question, 'explain') !== false || strpos($question, 'how to') !== false
        ) {
            $responseText = $responses['confused'];
        }

        return [
            'text' => $responseText,
            'tokens' => rand(100, 200),
            'data' => [],
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
}
