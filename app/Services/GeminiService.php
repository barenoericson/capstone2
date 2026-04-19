<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $endpoint;

    private string $deepseekApiKey;
    private string $deepseekModel;
    private string $deepseekEndpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash-preview-04-17');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');

        $this->deepseekApiKey = config('services.deepseek.api_key', '');
        $this->deepseekModel = config('services.deepseek.model', 'deepseek-chat');
        $this->deepseekEndpoint = config('services.deepseek.endpoint', 'https://api.deepseek.com/v1/chat/completions');
    }

    /**
     * Verify a PRC license image using Gemini vision.
     *
     * Returns 3 possible decisions:
     * - 'approved': Clearly a valid PRC license with matching info
     * - 'unclear': Might be a PRC license but can't fully verify (admin review)
     * - 'rejected': Clearly NOT a PRC license (resume, selfie, random photo, etc.)
     *
     * @param string $base64Image  Base64-encoded image data (no prefix)
     * @param string $mimeType     e.g., 'image/jpeg'
     * @param array  $applicantData ['name', 'license_number', 'prc_id', 'expiry_date']
     * @return array ['decision' => string, 'approved' => bool, 'extracted' => [...], 'reasoning' => string, 'confidence' => float]
     */
    public function verifyPrcLicense(string $base64Image, string $mimeType, array $applicantData, ?string $selfieBase64 = null, string $selfieType = 'image/jpeg'): array
    {
        if (empty($this->apiKey)) {
            Log::warning('Gemini API key not configured. Falling back to admin review.');
            return [
                'decision' => 'unclear',
                'approved' => false,
                'extracted' => [],
                'reasoning' => 'AI verification is not configured. Application will be reviewed by an admin.',
                'confidence' => 0,
            ];
        }

        // ═══════════════════════════════════════════════════
        // STEP 1: Gemini Vision Analysis (primary — sees the image)
        // ═══════════════════════════════════════════════════
        $geminiResult = $this->callGeminiVision($base64Image, $mimeType, $applicantData, $selfieBase64, $selfieType);

        // If Gemini completely failed, fall back to admin review
        if ($geminiResult === null) {
            return [
                'decision' => 'unclear',
                'approved' => false,
                'extracted' => [],
                'reasoning' => 'AI verification service temporarily unavailable. Application will be reviewed by an admin.',
                'confidence' => 0,
            ];
        }

        // ═══════════════════════════════════════════════════
        // FACE CHECK: If selfie provided, enforce face comparison
        // ═══════════════════════════════════════════════════
        if ($selfieBase64 !== null) {
            $faceMatch = $geminiResult['face_match'] ?? null;
            $faceConfidence = (float)($geminiResult['face_confidence'] ?? 0);

            Log::info('Face verification result', [
                'face_match' => $faceMatch,
                'face_confidence' => $faceConfidence,
                'face_reasoning' => $geminiResult['face_reasoning'] ?? 'N/A',
            ]);

            // CASE A: Face explicitly does NOT match → immediate rejection
            if ($faceMatch === false) {
                return [
                    'decision'               => 'rejected',
                    'approved'               => false,
                    'face_match'             => false,
                    'face_confidence'        => $faceConfidence,
                    'face_reasoning'         => $geminiResult['face_reasoning'] ?? 'Face in selfie does not match the PRC license photo.',
                    'extracted'              => $geminiResult['extracted'] ?? [],
                    'reasoning'              => '[Face Verification Failed] The selfie you submitted does not match the face on the PRC license. Please ensure you are submitting your own license. You may reapply after 12 hours.',
                    'confidence'             => 0,
                    'authenticity_score'     => $geminiResult['authenticity_score'] ?? 0,
                    'security_features_found'=> $geminiResult['security_features_found'] ?? [],
                    'security_features_missing'=> $geminiResult['security_features_missing'] ?? [],
                    'red_flags_detected'     => array_merge($geminiResult['red_flags_detected'] ?? [], ['Face mismatch: selfie does not match PRC license photo']),
                    'verification_method'    => 'face_rejected',
                ];
            }

            // CASE B: Face match is null/not determined → force admin review (never auto-approve)
            if ($faceMatch === null) {
                $geminiResult['decision'] = 'unclear';
                $geminiResult['approved'] = false;
                $geminiResult['reasoning'] = '[Face comparison inconclusive — sent for admin review] ' . ($geminiResult['reasoning'] ?? '');
                $geminiResult['face_match'] = null;
                $geminiResult['face_reasoning'] = 'Face comparison could not be completed. The AI was unable to extract or compare faces.';
            }

            // CASE C: Face matches but low confidence → force admin review
            if ($faceMatch === true && $faceConfidence < 0.5) {
                $geminiResult['decision'] = 'unclear';
                $geminiResult['approved'] = false;
                $geminiResult['reasoning'] = '[Low face match confidence (' . round($faceConfidence * 100) . '%) — sent for admin review] ' . ($geminiResult['reasoning'] ?? '');
            }
        }

        // ═══════════════════════════════════════════════════
        // STEP 2: DeepSeek Cross-Verification (secondary — reviews Gemini's findings)
        // ═══════════════════════════════════════════════════
        $deepseekResult = $this->callDeepSeekVerification($geminiResult, $applicantData);

        // ═══════════════════════════════════════════════════
        // STEP 3: Cross-Reference Both AI Results
        // ═══════════════════════════════════════════════════
        return $this->crossVerifyResults($geminiResult, $deepseekResult, $applicantData);
    }

    /**
     * Call Gemini Vision API to analyze the PRC license image.
     */
    private function callGeminiVision(string $base64Image, string $mimeType, array $applicantData, ?string $selfieBase64 = null, string $selfieType = 'image/jpeg'): ?array
    {
        try {
            $url = "{$this->endpoint}/{$this->model}:generateContent?key={$this->apiKey}";

            Log::info('Calling Gemini API for PRC verification', [
                'url' => preg_replace('/key=.*/', 'key=***', $url),
                'model' => $this->model,
                'mime_type' => $mimeType,
                'image_size' => strlen($base64Image),
            ]);

            // ✅ FIX 1: Added withoutVerifying() to bypass SSL certificate check
            $response = Http::withoutVerifying()->timeout(60)->post($url, [
                'contents' => [
                    [
                        'parts' => array_values(array_filter([
                            [
                                'text' => $this->buildPrompt($applicantData, $selfieBase64 !== null),
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data'      => $base64Image,
                                ],
                            ],
                            $selfieBase64 ? [
                                'inline_data' => [
                                    'mime_type' => $selfieType,
                                    'data'      => $selfieBase64,
                                ],
                            ] : null,
                        ])),
                    ],
                ],
                'generationConfig' => [
                    'temperature'    => 0.1,
                    'maxOutputTokens'=> 2048,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Gemini API error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 1000),
                ]);
                return null;
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            Log::info('Gemini raw response', ['text' => substr($responseText, 0, 1000)]);

            if (empty($responseText)) {
                Log::warning('Gemini returned empty response.');
                return null;
            }

            return $this->parseVerificationResponse($responseText);

        } catch (\Exception $e) {
            Log::error('Gemini verification exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Call DeepSeek API to cross-verify Gemini's PRC analysis.
     * DeepSeek reviews the extracted data and reasoning from Gemini's vision analysis.
     */
    private function callDeepSeekVerification(array $geminiResult, array $applicantData): ?array
    {
        if (empty($this->deepseekApiKey)) {
            Log::info('DeepSeek API key not configured. Skipping cross-verification.');
            return null;
        }

        try {
            $prompt = $this->buildDeepSeekVerificationPrompt($geminiResult, $applicantData);

            Log::info('Calling DeepSeek API for PRC cross-verification');

            // ✅ FIX 2: Added withoutVerifying() to bypass SSL certificate check
            $response = Http::withoutVerifying()->timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->deepseekApiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->deepseekEndpoint, [
                    'model' => $this->deepseekModel,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are RealtyLinkPH Buddy, a PRC license verification expert for a Philippine real estate platform. You provide second opinions on PRC license authenticity by analyzing data extracted by another AI from the document image. Respond with ONLY valid JSON, no markdown or code blocks.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 512,
                ]);

            if (!$response->successful()) {
                Log::error('DeepSeek API error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return null;
            }

            $data = $response->json();
            $responseText = $data['choices'][0]['message']['content'] ?? '';

            Log::info('DeepSeek raw response', ['text' => substr($responseText, 0, 1000)]);

            if (empty($responseText)) {
                return null;
            }

            return $this->parseDeepSeekResponse($responseText);

        } catch (\Exception $e) {
            Log::error('DeepSeek verification exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build the prompt for DeepSeek to cross-verify Gemini's findings.
     */
    private function buildDeepSeekVerificationPrompt(array $geminiResult, array $applicantData): string
    {
        $extracted = $geminiResult['extracted'] ?? [];
        $name = $applicantData['name'] ?? '';
        $accountName = $applicantData['account_name'] ?? '';
        $licenseNumber = $applicantData['license_number'] ?? '';
        $prcId = $applicantData['prc_id'] ?? '';
        $expiryDate = $applicantData['expiry_date'] ?? '';

        $geminiDecision = $geminiResult['decision'] ?? 'unclear';
        $geminiConfidence = $geminiResult['confidence'] ?? 0;
        $geminiReasoning = $geminiResult['reasoning'] ?? '';
        $authenticityScore = $geminiResult['authenticity_score'] ?? 0;
        $securityFound = implode(', ', $geminiResult['security_features_found'] ?? []);
        $securityMissing = implode(', ', $geminiResult['security_features_missing'] ?? []);
        $redFlags = implode(', ', $geminiResult['red_flags_detected'] ?? []);

        $extractedName = $extracted['extracted_name'] ?? 'N/A';
        $extractedLicense = $extracted['extracted_license_number'] ?? 'N/A';
        $extractedPrcId = $extracted['extracted_prc_id'] ?? 'N/A';
        $extractedExpiry = $extracted['extracted_expiry_date'] ?? 'N/A';
        $extractedProfession = $extracted['extracted_profession'] ?? 'N/A';
        $documentType = $extracted['document_type'] ?? 'Unknown';
        $documentFormat = $extracted['document_format'] ?? 'unknown';
        $nameMatches = isset($extracted['name_matches']) ? ($extracted['name_matches'] ? 'Yes' : 'No') : 'Unknown';
        $nameMatchesAccount = isset($extracted['name_matches_account']) ? ($extracted['name_matches_account'] ? 'Yes' : 'No') : 'Unknown';
        $licenseMatches = isset($extracted['license_number_matches']) ? ($extracted['license_number_matches'] ? 'Yes' : 'No') : 'Unknown';

        return <<<PROMPT
You are providing a SECOND OPINION on a PRC (Professional Regulation Commission) license verification for a Philippine real estate platform.

Another AI (Gemini) has already analyzed the actual document image. Your job is to CROSS-VERIFY the findings by checking for inconsistencies, logical errors, or suspicious patterns in the extracted data.

═══════════════════════════════════════════════
APPLICANT'S CLAIMED INFORMATION (from their form):
═══════════════════════════════════════════════
- Full Name: {$name}
- Account Name: {$accountName}
- PRC License Number: {$licenseNumber}
- PRC ID Number: {$prcId}
- License Expiry Date: {$expiryDate}

═══════════════════════════════════════════════
GEMINI'S ANALYSIS (from the actual document image):
═══════════════════════════════════════════════
- Document Type Detected: {$documentType}
- Document Format: {$documentFormat}
- Gemini's Decision: {$geminiDecision}
- Gemini's Confidence: {$geminiConfidence}
- Authenticity Score: {$authenticityScore}
- Gemini's Reasoning: {$geminiReasoning}

EXTRACTED FROM DOCUMENT:
- Name on Document: {$extractedName}
- License Number: {$extractedLicense}
- PRC ID: {$extractedPrcId}
- Expiry Date: {$extractedExpiry}
- Profession: {$extractedProfession}

VERIFICATION RESULTS:
- Name Matches Form: {$nameMatches}
- Name Matches Account: {$nameMatchesAccount}
- License Number Matches: {$licenseMatches}

SECURITY FEATURES FOUND: {$securityFound}
SECURITY FEATURES MISSING: {$securityMissing}
RED FLAGS DETECTED: {$redFlags}

═══════════════════════════════════════════════
YOUR CROSS-VERIFICATION TASK:
═══════════════════════════════════════════════

Analyze the above data and check for:

1. NAME CONSISTENCY: Does the extracted name match BOTH the form name and account name? Check for obvious mismatches, different genders, completely different names (fraud indicator).

2. LICENSE NUMBER VALIDITY: Philippine PRC license numbers follow specific patterns:
   - Real Estate Broker: typically 4-7 digit numbers (e.g., 0006660, 9266)
   - Accreditation numbers may have different formats
   - Check if the number format is plausible for a PRC license

3. DATE VALIDATION:
   - PRC licenses are typically valid for 3 years from issuance/renewal
   - If the expiry date is more than 5 years from now or in the distant past, it may be suspicious
   - Verify the claimed expiry matches what was extracted

4. PROFESSION CHECK: Must be one of: Real Estate Broker, Real Estate Salesperson, Real Estate Appraiser. Reject if it's a different profession.

5. CROSS-CHECK GEMINI'S REASONING: Does Gemini's reasoning make logical sense? Are there any contradictions in its analysis? If Gemini approved a document but flagged red flags, that's suspicious.

6. SECURITY FEATURE ANALYSIS: If critical security features are missing (no PRC seal, no signature, no documentary stamp on a certification letter), the document may be fake regardless of what Gemini decided.

7. OVERALL CONSISTENCY: Do all the pieces fit together? A real PRC document should have consistent information throughout.

Respond with ONLY valid JSON:
{
  "deepseek_decision": "agree_approved" or "agree_rejected" or "disagree_should_reject" or "disagree_should_approve" or "disagree_needs_review",
  "confidence": 0.0 to 1.0,
  "name_check_passed": true or false,
  "license_format_valid": true or false,
  "date_check_passed": true or false,
  "profession_valid": true or false,
  "gemini_reasoning_consistent": true or false,
  "concerns": ["list of specific concerns found"],
  "reasoning": "Detailed explanation of your second opinion"
}

DECISION RULES for deepseek_decision:
- "agree_approved": You agree with Gemini's approval — the data looks consistent and legitimate
- "agree_rejected": You agree with Gemini's rejection — the data confirms issues
- "disagree_should_reject": Gemini approved/unclear, but YOU found issues that warrant rejection (name mismatch, fake indicators, wrong profession)
- "disagree_should_approve": Gemini was uncertain/rejected, but the data actually looks consistent and valid
- "disagree_needs_review": The data has some inconsistencies that need human admin review
PROMPT;
    }

    /**
     * Parse DeepSeek's cross-verification response.
     */
    private function parseDeepSeekResponse(string $responseText): ?array
    {
        $jsonText = $responseText;

        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $responseText, $matches)) {
            $jsonText = trim($matches[1]);
        }

        $parsed = json_decode($jsonText, true);

        if (!$parsed || !is_array($parsed)) {
            if (preg_match('/\{[\s\S]*\}/', $responseText, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!$parsed || !is_array($parsed)) {
            Log::warning('Could not parse DeepSeek response as JSON');
            return null;
        }

        return $parsed;
    }

    /**
     * Cross-reference Gemini (vision) and DeepSeek (data analysis) results
     * to produce a final, more accurate verification decision.
     */
    private function crossVerifyResults(array $geminiResult, ?array $deepseekResult, array $applicantData): array
    {
        // If DeepSeek was unavailable, return Gemini result with a note
        if ($deepseekResult === null) {
            $geminiResult['verification_method'] = 'single_ai';
            $geminiResult['reasoning'] = '[Verified by Gemini AI] ' . ($geminiResult['reasoning'] ?? '');
            return $geminiResult;
        }

        $geminiDecision = $geminiResult['decision'] ?? 'unclear';
        $deepseekDecision = $deepseekResult['deepseek_decision'] ?? 'disagree_needs_review';
        $deepseekConfidence = (float) ($deepseekResult['confidence'] ?? 0);
        $deepseekConcerns = $deepseekResult['concerns'] ?? [];
        $deepseekReasoning = $deepseekResult['reasoning'] ?? '';

        Log::info('Cross-verification results', [
            'gemini_decision' => $geminiDecision,
            'deepseek_decision' => $deepseekDecision,
            'deepseek_confidence' => $deepseekConfidence,
        ]);

        $finalResult = $geminiResult;
        $finalResult['verification_method'] = 'dual_ai';
        $finalResult['deepseek_decision'] = $deepseekDecision;
        $finalResult['deepseek_confidence'] = $deepseekConfidence;
        $finalResult['deepseek_concerns'] = $deepseekConcerns;
        $finalResult['deepseek_reasoning'] = $deepseekReasoning;

        // ── CROSS-VERIFICATION LOGIC ──

        // CASE 1: Both AIs agree on approval → HIGH confidence approval
        if ($geminiDecision === 'approved' && $deepseekDecision === 'agree_approved') {
            $finalResult['decision'] = 'approved';
            $finalResult['approved'] = true;
            $finalResult['confidence'] = min(1.0, (($geminiResult['confidence'] ?? 0) + $deepseekConfidence) / 2 + 0.1);
            $finalResult['reasoning'] = '[Dual AI Verified — Both Gemini and DeepSeek confirmed] ' . ($geminiResult['reasoning'] ?? '');
        }

        // CASE 2: Both AIs agree on rejection → HIGH confidence rejection
        elseif ($geminiDecision === 'rejected' && $deepseekDecision === 'agree_rejected') {
            $finalResult['decision'] = 'rejected';
            $finalResult['approved'] = false;
            $finalResult['reasoning'] = '[Dual AI Rejected — Both Gemini and DeepSeek flagged issues] ' . ($geminiResult['reasoning'] ?? '') . ' | DeepSeek: ' . $deepseekReasoning;
        }

        // CASE 3: Gemini approved but DeepSeek found issues → DOWNGRADE to rejected or unclear
        elseif ($geminiDecision === 'approved' && $deepseekDecision === 'disagree_should_reject') {
            if ($deepseekConfidence >= 0.7) {
                $finalResult['decision'] = 'rejected';
                $finalResult['approved'] = false;
                $finalResult['reasoning'] = '[DeepSeek Override — Fraud indicators detected] Gemini approved but DeepSeek found critical issues: ' . $deepseekReasoning;
                $finalResult['red_flags_detected'] = array_merge(
                    $finalResult['red_flags_detected'] ?? [],
                    $deepseekConcerns
                );
            } else {
                $finalResult['decision'] = 'unclear';
                $finalResult['approved'] = false;
                $finalResult['reasoning'] = '[AI Disagreement — Needs admin review] Gemini approved but DeepSeek raised concerns: ' . $deepseekReasoning;
            }
        }

        // CASE 4: Gemini approved but DeepSeek says needs review → DOWNGRADE to unclear
        elseif ($geminiDecision === 'approved' && $deepseekDecision === 'disagree_needs_review') {
            $finalResult['decision'] = 'unclear';
            $finalResult['approved'] = false;
            $finalResult['reasoning'] = '[DeepSeek flagged for review] Gemini approved but DeepSeek has concerns: ' . $deepseekReasoning;
        }

        // CASE 5: Gemini rejected/unclear but DeepSeek says it should be approved
        elseif (in_array($geminiDecision, ['rejected', 'unclear']) && $deepseekDecision === 'disagree_should_approve') {
            if ($geminiDecision === 'rejected') {
                $finalResult['decision'] = 'unclear';
                $finalResult['approved'] = false;
                $finalResult['reasoning'] = '[AI Disagreement — Escalated to admin] Gemini rejected but DeepSeek believes the data is valid: ' . $deepseekReasoning . ' | Original rejection: ' . ($geminiResult['reasoning'] ?? '');
            }
        }

        // CASE 6: Gemini unclear + DeepSeek agrees needs review → stay unclear
        elseif ($geminiDecision === 'unclear' && in_array($deepseekDecision, ['agree_rejected', 'disagree_needs_review'])) {
            $finalResult['decision'] = 'unclear';
            $finalResult['approved'] = false;
            $finalResult['reasoning'] = '[Both AIs uncertain — Admin review required] ' . ($geminiResult['reasoning'] ?? '') . ' | DeepSeek: ' . $deepseekReasoning;
        }

        // CASE 7: Gemini unclear + DeepSeek says should reject → DOWNGRADE to rejected
        elseif ($geminiDecision === 'unclear' && $deepseekDecision === 'disagree_should_reject' && $deepseekConfidence >= 0.8) {
            $finalResult['decision'] = 'rejected';
            $finalResult['approved'] = false;
            $finalResult['reasoning'] = '[DeepSeek strongly recommends rejection] ' . $deepseekReasoning . ' | Gemini was uncertain: ' . ($geminiResult['reasoning'] ?? '');
            $finalResult['red_flags_detected'] = array_merge(
                $finalResult['red_flags_detected'] ?? [],
                $deepseekConcerns
            );
        }

        // Update extracted data with DeepSeek info
        if (isset($finalResult['extracted']) && is_array($finalResult['extracted'])) {
            $finalResult['extracted']['deepseek_verification'] = [
                'decision' => $deepseekDecision,
                'confidence' => $deepseekConfidence,
                'concerns' => $deepseekConcerns,
                'name_check_passed' => $deepseekResult['name_check_passed'] ?? null,
                'license_format_valid' => $deepseekResult['license_format_valid'] ?? null,
                'date_check_passed' => $deepseekResult['date_check_passed'] ?? null,
                'profession_valid' => $deepseekResult['profession_valid'] ?? null,
            ];
        }

        return $finalResult;
    }

    /**
     * Chat with RealtyLinkPH Buddy — conversational AI assistant.
     *
     * @param array  $conversationHistory Array of ['role' => 'user'|'assistant', 'content' => '...']
     * @param string $userMessage The new user message
     * @return string The assistant's reply text
     */
    public function chat(array $conversationHistory, string $userMessage): string
    {
        if (empty($this->apiKey)) {
            return 'RealtyLinkPH Buddy is currently unavailable. Please try again later.';
        }

        try {
            $url = "{$this->endpoint}/{$this->model}:generateContent?key={$this->apiKey}";

            $contents = [];

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $this->buildChatSystemPrompt()]],
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'Understood! I\'m RealtyLinkPH Buddy, ready to help you with anything about RealtyLinkPH and Philippine real estate. How can I assist you today?']],
            ];

            $recent = array_slice($conversationHistory, -20);
            foreach ($recent as $msg) {
                $contents[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['content']]],
                ];
            }

            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ];

            // ✅ FIX 3: Added withoutVerifying() to bypass SSL certificate check
            $response = Http::withoutVerifying()->timeout(30)->post($url, [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 512,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Gemini chat API error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return 'I\'m having trouble connecting right now. Please try again in a moment.';
            }

            $data = $response->json();
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            return !empty($reply)
                ? $reply
                : 'I\'m sorry, I didn\'t catch that. Could you try rephrasing your question?';

        } catch (\Exception $e) {
            Log::error('Gemini chat exception: ' . $e->getMessage());
            return 'Something went wrong on my end. Please try again later.';
        }
    }

    private function buildChatSystemPrompt(): string
    {
        return <<<PROMPT
You are **RealtyLinkPH Buddy**, the friendly AI assistant for RealtyLinkPH — a Philippine real estate platform. You are also the AI verification expert for agent applications and have deep knowledge of PRC licensing, RESA law (RA 9646), and the complete process of becoming a licensed real estate professional in the Philippines.

ABOUT REALTYLINKPH:
- RealtyLinkPH is an online platform connecting property buyers with verified real estate agents in the Philippines.
- Built by a team of Filipino developers with the vision of making PH real estate more accessible and transparent.
- Key features: property listings with photos, AI-powered agent verification (PRC license scanning), real-time buyer-agent messaging, property viewing scheduling, document management with digital signatures, and saved/favorite properties.
- Buyers can browse properties, save favorites, schedule viewings, message agents, and sign documents digitally.
- Agents can list properties, manage viewings, handle documents, and communicate with potential buyers.

PLATFORM WORKFLOWS (how things work on RealtyLinkPH):
- **Becoming an Agent on RealtyLinkPH:** Buyers apply via the "Become an Agent" page, fill in professional details, upload their PRC real estate license photo, and I (RealtyLinkPH Buddy) verify it using dual AI (Gemini Vision + DeepSeek). See the full rules below.
- **Viewing Scheduling:** Buyers request a property viewing from the listing page. The agent can confirm, reschedule, or decline. Both parties see updates in their Viewings page.
- **Document Management:** Agents upload contracts/documents (e.g., Deed of Absolute Sale, Reservation Agreement). Both buyer and agent can view, download, and digitally sign documents.
- **Digital Signing:** Documents are signed electronically through the platform. Users preview the PDF, click where to place their signature, draw or upload it, and the signed PDF is generated. Remind users that some transactions still require notarized hard copies under Philippine law.
- **Messaging:** Buyers and agents communicate through in-app messaging with file sharing support (PDF, Word, Excel).

YOUR PERSONALITY:
- Friendly, professional, and helpful — like a knowledgeable real estate advisor friend.
- Warm, conversational tone with simple language. Use occasional Filipino expressions (like "po", "naman") to feel relatable, but respond mostly in English.
- Keep responses concise: 2–4 sentences for simple questions, more detail only when asked.
- When users ask about becoming an agent or PRC licensing, give them clear and complete guidance.
- If asked something outside your scope, be honest and suggest contacting support or an agent.

WHAT YOU SHOULD NOT DO:
- Never provide specific legal advice — always suggest consulting a lawyer or the PRC directly.
- Never guarantee property values or investment returns.
- Never share personal data about other users.
- Stay focused and relevant to real estate and the platform.

Remember: You are RealtyLinkPH Buddy. Always identify yourself as such if asked.
PROMPT;
    }

    /**
     * Analyze a property listing for suspicious/fake indicators.
     */
    public function analyzeProperty(array $propertyData): array
    {
        if (empty($this->apiKey)) {
            return ['is_suspicious' => false, 'confidence' => 0, 'flag_reason' => '', 'raw_response' => []];
        }

        try {
            $url = "{$this->endpoint}/{$this->model}:generateContent?key={$this->apiKey}";

            // ✅ FIX 4: Added withoutVerifying() to bypass SSL certificate check
            $response = Http::withoutVerifying()->timeout(15)->post($url, [
                'contents' => [[
                    'parts' => [['text' => $this->buildPropertyAnalysisPrompt($propertyData)]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 512,
                ],
            ]);

            if (!$response->successful()) {
                Log::error('Gemini property analysis API error', ['status' => $response->status()]);
                return ['is_suspicious' => false, 'confidence' => 0, 'flag_reason' => '', 'raw_response' => []];
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($responseText)) {
                return ['is_suspicious' => false, 'confidence' => 0, 'flag_reason' => '', 'raw_response' => []];
            }

            return $this->parsePropertyAnalysisResponse($responseText);

        } catch (\Exception $e) {
            Log::error('Gemini property analysis exception: ' . $e->getMessage());
            return ['is_suspicious' => false, 'confidence' => 0, 'flag_reason' => '', 'raw_response' => []];
        }
    }

    private function buildPropertyAnalysisPrompt(array $data): string
    {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? 0;
        $city = $data['city'] ?? '';
        $province = $data['province'] ?? '';
        $propertyType = $data['property_type'] ?? '';
        $bedrooms = $data['bedrooms'] ?? 0;
        $bathrooms = $data['bathrooms'] ?? 0;
        $floorArea = $data['floor_area'] ?? 0;
        $lotSize = $data['lot_size'] ?? 0;

        return <<<PROMPT
You are RealtyLinkPH Buddy, a property listing verification assistant for a Philippine real estate platform.

Analyze this property listing and determine if it appears suspicious or potentially fake.

PROPERTY DETAILS:
- Title: {$title}
- Description: {$description}
- Price: PHP {$price}
- Location: {$city}, {$province}
- Property Type: {$propertyType}
- Bedrooms: {$bedrooms}
- Bathrooms: {$bathrooms}
- Floor Area: {$floorArea} sqm
- Lot Size: {$lotSize} sqm

CHECK FOR THESE RED FLAGS:
1. PRICE ANOMALIES: Price drastically below market rate for the location and property type in the Philippines
2. CONTRADICTORY INFO: Description says "5-bedroom luxury house" but only 1 bedroom listed
3. IMPOSSIBLE ATTRIBUTES: Year built in the future, 100+ bathrooms, negative or zero floor area
4. SUSPICIOUS DESCRIPTIONS: Overly generic copy-paste text, promises of unrealistic returns
5. MISSING/VAGUE DETAILS: Very short or empty description for an expensive property

Respond with ONLY valid JSON (no markdown, no code blocks):
{
  "is_suspicious": true or false,
  "confidence": 0.0 to 1.0,
  "red_flags": ["list of specific issues found"],
  "flag_reason": "Human-readable summary for admin review",
  "severity": "high" or "medium" or "low"
}
PROMPT;
    }

    private function parsePropertyAnalysisResponse(string $responseText): array
    {
        $jsonText = trim($responseText);
        $jsonText = preg_replace('/^```(?:json)?\s*/s', '', $jsonText);
        $jsonText = preg_replace('/\s*```\s*$/s', '', $jsonText);
        $jsonText = trim($jsonText);

        $parsed = json_decode($jsonText, true);

        if (!$parsed || !is_array($parsed)) {
            if (preg_match('/\{[\s\S]*\}/s', $responseText, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!$parsed || !is_array($parsed)) {
            Log::warning('Could not parse Gemini property analysis response');
            return ['is_suspicious' => false, 'confidence' => 0, 'flag_reason' => '', 'raw_response' => []];
        }

        $isSuspicious = $parsed['is_suspicious'] ?? false;
        $confidence = (float) ($parsed['confidence'] ?? 0);
        $severity = strtolower($parsed['severity'] ?? 'low');

        if ($isSuspicious && ($confidence < 0.70 || $severity === 'low')) {
            $isSuspicious = false;
        }

        return [
            'is_suspicious' => $isSuspicious,
            'confidence' => $confidence,
            'flag_reason' => $parsed['flag_reason'] ?? '',
            'raw_response' => $parsed,
        ];
    }

    private function buildPrompt(array $applicantData, bool $withFaceScan = false): string
    {
        $name = $applicantData['name'] ?? '';
        $accountName = $applicantData['account_name'] ?? '';
        $accountEmail = $applicantData['account_email'] ?? '';
        $licenseNumber = $applicantData['license_number'] ?? '';
        $prcId = $applicantData['prc_id'] ?? '';
        $expiryDate = $applicantData['expiry_date'] ?? '';

        $faceSection = $withFaceScan ? '
FACE VERIFICATION: A second image is a LIVE SELFIE. Compare the face in the selfie with the face on the PRC license.
Add these fields to your JSON:
{
  "face_comparison_performed": true,
  "face_match": true or false,
  "face_confidence": 0.0 to 1.0,
  "face_reasoning": "Brief explanation",
  "selfie_quality": "clear" or "blurry" or "too_dark" or "obscured",
  "prc_photo_quality": "clear" or "blurry" or "low_resolution"
}
' : '
Add this to your JSON: { "face_comparison_performed": false }
';

        return <<<PROMPT
You are RealtyLinkPH Buddy, an expert PRC license verification assistant for the Philippine real estate platform RealtyLinkPH.

The applicant provided:
- Full Name: {$name}
- Account Name: {$accountName}
- Account Email: {$accountEmail}
- PRC License Number: {$licenseNumber}
- PRC ID Number: {$prcId}
- License Expiry Date: {$expiryDate}

Analyze the uploaded PRC document image and respond with ONLY valid JSON:
{
  "document_type": "PRC ID Card" or "PRC Certification" or other,
  "document_format": "id_card" or "certification_letter" or "not_prc",
  "is_prc_document": true or false,
  "is_valid_prc_card": true or false,
  "authenticity_score": 0.0 to 1.0,
  "security_features_found": [],
  "security_features_missing": [],
  "red_flags_detected": [],
  "extracted_name": "name or null",
  "extracted_license_number": "number or null",
  "extracted_prc_id": "id or null",
  "extracted_expiry_date": "YYYY-MM-DD or null",
  "extracted_profession": "profession or null",
  "extracted_registration_date": "YYYY-MM-DD or null",
  "extracted_issuing_office": "office or null",
  "name_matches": true or false,
  "name_matches_account": true or false,
  "license_number_matches": true or false,
  "expiry_date_valid": true or false,
  "profession_is_real_estate": true or false,
  "decision": "approved" or "unclear" or "rejected",
  "confidence": 0.0 to 1.0,
  "reasoning": "Detailed explanation"
}
{$faceSection}
PROMPT;
    }

    private function parseVerificationResponse(string $responseText): array
    {
        $jsonText = trim($responseText);
        $jsonText = preg_replace('/^```(?:json)?\s*/s', '', $jsonText);
        $jsonText = preg_replace('/\s*```\s*$/s', '', $jsonText);
        $jsonText = trim($jsonText);

        $parsed = json_decode($jsonText, true);

        if (!$parsed || !is_array($parsed)) {
            if (preg_match('/\{[\s\S]*\}/s', $responseText, $matches)) {
                $parsed = json_decode($matches[0], true);
            }
        }

        if (!$parsed || !is_array($parsed)) {
            Log::warning('Could not parse Gemini response as JSON: ' . substr($responseText, 0, 500));
            return [
                'decision' => 'unclear',
                'approved' => false,
                'extracted' => ['raw_response' => substr($responseText, 0, 500)],
                'reasoning' => 'AI response could not be processed. Application will be reviewed by an admin.',
                'confidence' => 0,
            ];
        }

        $aiDecision = strtolower($parsed['decision'] ?? 'unclear');
        $isPrcDocument = $parsed['is_prc_document'] ?? false;
        $authenticityScore = (float) ($parsed['authenticity_score'] ?? 0);
        $redFlags = $parsed['red_flags_detected'] ?? [];
        $securityFound = $parsed['security_features_found'] ?? [];
        $securityMissing = $parsed['security_features_missing'] ?? [];

        if (!$isPrcDocument && $aiDecision !== 'rejected') {
            $aiDecision = 'rejected';
        }

        if (!in_array($aiDecision, ['approved', 'unclear', 'rejected'])) {
            $aiDecision = 'unclear';
        }

        if (count($redFlags) >= 3 && $aiDecision === 'approved') {
            $aiDecision = 'rejected';
            $parsed['reasoning'] = 'Document rejected due to multiple red flags: ' . implode(', ', $redFlags) . '. ' . ($parsed['reasoning'] ?? '');
        }

        if ($authenticityScore > 0 && $authenticityScore < 0.5 && $aiDecision === 'approved') {
            $aiDecision = 'rejected';
            $parsed['reasoning'] = 'Document authenticity score too low (' . $authenticityScore . '). ' . ($parsed['reasoning'] ?? '');
        } elseif ($authenticityScore > 0 && $authenticityScore < 0.7 && $aiDecision === 'approved') {
            $aiDecision = 'unclear';
            $parsed['reasoning'] = 'Document authenticity could not be fully confirmed (score: ' . $authenticityScore . '). ' . ($parsed['reasoning'] ?? '');
        }

        if ($aiDecision === 'approved') {
            $isValid = ($parsed['is_valid_prc_card'] ?? false)
                && ($parsed['name_matches'] ?? false)
                && ($parsed['name_matches_account'] ?? false)
                && ($parsed['license_number_matches'] ?? false)
                && (($parsed['confidence'] ?? 0) >= 0.7)
                && ($authenticityScore >= 0.7 || $authenticityScore === 0.0);

            if (!$isValid) {
                if (!($parsed['name_matches_account'] ?? true)) {
                    $aiDecision = 'rejected';
                    $parsed['reasoning'] = 'The name on the PRC document does not match your registered account name. ' . ($parsed['reasoning'] ?? '');
                } else {
                    $aiDecision = 'unclear';
                }
            }
        }

        if ($aiDecision !== 'rejected' && isset($parsed['name_matches_account']) && $parsed['name_matches_account'] === false) {
            $aiDecision = 'rejected';
            $parsed['reasoning'] = 'The name on the PRC document does not match your registered account name. ' . ($parsed['reasoning'] ?? '');
        }

        return [
            'decision' => $aiDecision,
            'approved' => $aiDecision === 'approved',
            'extracted' => $parsed,
            'reasoning' => $parsed['reasoning'] ?? 'No reasoning provided.',
            'confidence' => (float) ($parsed['confidence'] ?? 0),
            'authenticity_score' => $authenticityScore,
            'security_features_found' => $securityFound,
            'security_features_missing' => $securityMissing,
            'red_flags_detected' => $redFlags,
            'face_match'             => $parsed['face_match'] ?? null,
            'face_confidence'        => isset($parsed['face_confidence']) ? (float)$parsed['face_confidence'] : null,
            'face_reasoning'         => $parsed['face_reasoning'] ?? null,
            'face_comparison_performed' => $parsed['face_comparison_performed'] ?? false,
            'selfie_quality'         => $parsed['selfie_quality'] ?? null,
            'prc_photo_quality'      => $parsed['prc_photo_quality'] ?? null,
        ];
    }
}