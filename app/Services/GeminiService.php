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
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
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
    public function verifyPrcLicense(string $base64Image, string $mimeType, array $applicantData): array
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
        $geminiResult = $this->callGeminiVision($base64Image, $mimeType, $applicantData);

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
    private function callGeminiVision(string $base64Image, string $mimeType, array $applicantData): ?array
    {
        try {
            $url = "{$this->endpoint}/{$this->model}:generateContent?key={$this->apiKey}";

            Log::info('Calling Gemini API for PRC verification', [
                'url' => preg_replace('/key=.*/', 'key=***', $url),
                'model' => $this->model,
                'mime_type' => $mimeType,
                'image_size' => strlen($base64Image),
            ]);

            $response = Http::timeout(60)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'inlineData' => [
                                    'mimeType' => $mimeType,
                                    'data' => $base64Image,
                                ],
                            ],
                            [
                                'text' => $this->buildPrompt($applicantData),
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'maxOutputTokens' => 1024,
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

            $response = Http::timeout(30)
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
            // DeepSeek caught something Gemini missed — reject if DeepSeek is confident
            if ($deepseekConfidence >= 0.7) {
                $finalResult['decision'] = 'rejected';
                $finalResult['approved'] = false;
                $finalResult['reasoning'] = '[DeepSeek Override — Fraud indicators detected] Gemini approved but DeepSeek found critical issues: ' . $deepseekReasoning;
                // Add DeepSeek concerns to red flags
                $finalResult['red_flags_detected'] = array_merge(
                    $finalResult['red_flags_detected'] ?? [],
                    $deepseekConcerns
                );
            } else {
                // Not confident enough to reject outright, send to admin
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
            // DeepSeek can't override a Gemini rejection since Gemini SAW the actual image
            // But we can send it to admin for review instead of outright rejecting
            if ($geminiDecision === 'rejected') {
                $finalResult['decision'] = 'unclear';
                $finalResult['approved'] = false;
                $finalResult['reasoning'] = '[AI Disagreement — Escalated to admin] Gemini rejected but DeepSeek believes the data is valid: ' . $deepseekReasoning . ' | Original rejection: ' . ($geminiResult['reasoning'] ?? '');
            }
            // If Gemini said unclear and DeepSeek says approve, keep as unclear (let admin decide)
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

            // Build multi-turn conversation in Gemini format
            $contents = [];

            // System instruction as first user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $this->buildChatSystemPrompt()]],
            ];
            $contents[] = [
                'role' => 'model',
                'parts' => [['text' => 'Understood! I\'m RealtyLinkPH Buddy, ready to help you with anything about RealtyLinkPH and Philippine real estate. How can I assist you today?']],
            ];

            // Add conversation history (last 20 messages)
            $recent = array_slice($conversationHistory, -20);
            foreach ($recent as $msg) {
                $contents[] = [
                    'role' => $msg['role'] === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg['content']]],
                ];
            }

            // Add the new user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userMessage]],
            ];

            $response = Http::timeout(30)->post($url, [
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
You are **RealtyLinkPH Buddy**, the friendly AI assistant for RealtyLinkPH — a Philippine real estate platform.

ABOUT REALTYLINKPH:
- RealtyLinkPH is an online platform connecting property buyers with verified real estate agents in the Philippines.
- Built by a team of Filipino developers with the vision of making PH real estate more accessible and transparent.
- Key features: property listings with photos, AI-powered agent verification (PRC license scanning), real-time buyer-agent messaging, property viewing scheduling, document management with digital signatures, and saved/favorite properties.
- Buyers can browse properties, save favorites, schedule viewings, message agents, and sign documents digitally.
- Agents can list properties, manage viewings, handle documents, and communicate with potential buyers.

PLATFORM WORKFLOWS (how things work on RealtyLinkPH):
- **Becoming an Agent:** Users apply via the "Become an Agent" page, upload their PRC real estate broker/salesperson license, and our AI verifies the document. An admin reviews AI results and makes the final decision. Once approved, the account upgrades to agent status.
- **Viewing Scheduling:** Buyers request a property viewing from the listing page. The agent can confirm, reschedule, or decline. Both parties see updates in their Viewings page.
- **Document Management:** Agents upload contracts/documents (e.g., Deed of Absolute Sale, Reservation Agreement). Both buyer and agent can view, download, and digitally sign documents.
- **Digital Signing:** Documents are signed electronically through the platform. Users preview the PDF, click where to place their signature, draw or upload it, and the signed PDF is generated. Remind users that some transactions still require notarized hard copies under Philippine law.
- **Messaging:** Buyers and agents communicate through in-app messaging with file sharing support (PDF, Word, Excel).

PHILIPPINE REAL ESTATE KNOWLEDGE:
- **PRC Licensing:** Real estate agents must hold a valid PRC license — either Real Estate Broker (REB) or Real Estate Salesperson (RES). Salespersons must work under a licensed broker. PRC licenses are renewed every 3 years.
- **Typical Costs & Fees:**
  - Agent commission: 3%–5% of selling price, usually paid by the seller.
  - Capital Gains Tax (CGT): 6% of selling price or fair market value (whichever is higher), paid by seller.
  - Documentary Stamp Tax (DST): 1.5% of selling price or fair market value.
  - Transfer Tax: ~0.5%–0.75%, varies by LGU.
  - Registration Fee: Based on Registry of Deeds fee schedule.
- **Transfer of Title Process:** Execute Deed of Absolute Sale → Pay CGT & DST at BIR → Obtain Certificate Authorizing Registration (CAR) → Pay Transfer Tax at LGU → Register at Registry of Deeds → Get new TCT or CCT → Update Tax Declaration at Assessor's Office.
- **Common Property Types:** Condominium, House & Lot, Townhouse, Lot Only, Commercial/Office Space, Apartment.
- **Pricing Factors:** Location, lot/floor area, property age, amenities, developer reputation, proximity to transportation and commercial centers.

YOUR PERSONALITY:
- Friendly, professional, and helpful — like a knowledgeable real estate advisor friend.
- Warm, conversational tone with simple language. Use occasional Filipino expressions (like "po", "naman") to feel relatable, but respond mostly in English.
- Keep responses concise: 2–4 sentences for simple questions, more detail only when asked.
- If asked something outside your scope, be honest and suggest contacting support or an agent.

WHAT YOU SHOULD NOT DO:
- Never provide specific legal advice — always suggest consulting a lawyer.
- Never guarantee property values or investment returns.
- Never share personal data about other users.
- Never pretend to execute platform actions (booking viewings, signing documents) — explain how the user can do it themselves.
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

            $response = Http::timeout(15)->post($url, [
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
1. PRICE ANOMALIES: Price drastically below market rate for the location and property type in the Philippines (e.g., a 3BR condo in BGC/Makati listed at PHP 100,000 total, or a house listed at PHP 1,000)
2. CONTRADICTORY INFO: Description says "5-bedroom luxury house" but only 1 bedroom listed, or floor area of 20sqm with 5 bedrooms
3. IMPOSSIBLE ATTRIBUTES: Year built in the future, 100+ bathrooms, negative or zero floor area with many rooms, lot size of 0.01 sqm for a house
4. SUSPICIOUS DESCRIPTIONS: Overly generic copy-paste text, promises of unrealistic returns ("guaranteed 500% ROI"), urgency tactics ("act now or lose forever"), descriptions that don't match the property type
5. MISSING/VAGUE DETAILS: Very short or empty description for an expensive property, no specific address details

IMPORTANT CONTEXT FOR PHILIPPINE REAL ESTATE:
- Studio condos in Metro Manila typically start around PHP 1-3 million
- Houses in provincial areas can be PHP 500,000+ but rarely below PHP 200,000
- Lot-only in rural areas can be PHP 100,000+ but prices of PHP 1,000 or PHP 100 are suspicious
- Commercial properties are typically PHP 5 million+

Respond with ONLY valid JSON (no markdown, no code blocks):
{
  "is_suspicious": true or false,
  "confidence": 0.0 to 1.0,
  "red_flags": ["list of specific issues found"],
  "flag_reason": "Human-readable summary for admin review",
  "severity": "high" or "medium" or "low"
}

Rules:
- Set is_suspicious to true ONLY if you find clear red flags
- "high" severity: obviously fake (impossible data, scam indicators)
- "medium" severity: likely suspicious (price anomalies, contradictions)
- "low" severity: minor concerns (vague description, slightly unusual)
- If the listing looks normal and reasonable, set is_suspicious to false with confidence 0.9+
- Be conservative — only flag listings with genuine concerns, not merely unusual ones
PROMPT;
    }

    private function parsePropertyAnalysisResponse(string $responseText): array
    {
        $jsonText = $responseText;

        // Remove markdown code blocks if present
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
            Log::warning('Could not parse Gemini property analysis response');
            return ['is_suspicious' => false, 'confidence' => 0, 'flag_reason' => '', 'raw_response' => []];
        }

        $isSuspicious = $parsed['is_suspicious'] ?? false;
        $confidence = (float) ($parsed['confidence'] ?? 0);
        $severity = strtolower($parsed['severity'] ?? 'low');

        // Only flag when confidence >= 0.70 AND severity is medium or high
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

    private function buildPrompt(array $applicantData): string
    {
        $name = $applicantData['name'] ?? '';
        $accountName = $applicantData['account_name'] ?? '';
        $accountEmail = $applicantData['account_email'] ?? '';
        $licenseNumber = $applicantData['license_number'] ?? '';
        $prcId = $applicantData['prc_id'] ?? '';
        $expiryDate = $applicantData['expiry_date'] ?? '';

        return <<<PROMPT
You are RealtyLinkPH Buddy, an expert PRC license verification assistant for the Philippine real estate platform RealtyLinkPH. You have been trained to identify authentic PRC (Professional Regulation Commission) documents and detect fakes, forgeries, and fraudulent submissions.

Your task is to perform a THOROUGH AUTHENTICITY ANALYSIS of this uploaded image:
1. Is this image actually an authentic PRC document for real estate?
2. Does it have the required security features of a genuine PRC document?
3. Does the information on the document match what the applicant provided?
4. CRITICAL: Does the name on the PRC document match the applicant's ACCOUNT NAME?

The applicant provided these details in the form:
- Full Name (from form): {$name}
- PRC License Number: {$licenseNumber}
- PRC ID Number: {$prcId}
- License Expiry Date: {$expiryDate}

The applicant's REGISTERED ACCOUNT information (from their RealtyLinkPH account):
- Account Name: {$accountName}
- Account Email: {$accountEmail}

═══════════════════════════════════════════════════════════
STEP 1 — DOCUMENT TYPE & FORMAT IDENTIFICATION
═══════════════════════════════════════════════════════════

There are exactly TWO valid formats of PRC documents. Analyze which format is present:

FORMAT A — PRC PROFESSIONAL IDENTIFICATION CARD (ID Card):
Required visual elements of an authentic PRC ID Card:
✓ Credit-card sized, LANDSCAPE orientation, typically with a colored background
✓ Header text: "Republic of the Philippines" and "PROFESSIONAL REGULATION COMMISSION"
✓ City name below header (e.g., "Manila")
✓ Labeled fields: "LAST NAME", "FIRST NAME", "MIDDLE INITIAL/NAME" — printed in a structured layout
✓ A LICENSE/REGISTRATION NUMBER (numeric, typically 7 digits, e.g., "0006660")
✓ "REGISTRATION" date field (format: MM/DD/YYYY, e.g., "06/06/2011")
✓ "VALID UNTIL" date field (format: MM/DD/YYYY, e.g., "01/30/2014")
✓ Profession printed prominently: "REAL ESTATE BROKER", "REAL ESTATE SALESPERSON", or "REAL ESTATE APPRAISER"
✓ A PASSPORT-STYLE PHOTO of the licensee on the right side
✓ A BARCODE at the bottom of the card (standard linear barcode)
✓ PRC serial number printed near barcode (e.g., "01301951")
✓ The PRC circular seal/logo embossed or printed

FORMAT B — PRC CERTIFICATION / ACCREDITATION LETTER:
Required visual elements of an authentic PRC Certification:
✓ Full-page document (portrait orientation, letter-sized paper)
✓ Official PRC letterhead with: "Republic of the Philippines" (in old English/Gothic font), "Professional Regulation Commission" (in old English/Gothic font), "Regional Office No. X" and city name
✓ PRC circular seal/logo in the upper-left corner
✓ ISO/PAB certification logos in the upper-right corner (ISO 9001:2015, PAB accreditation marks)
✓ Title: "CERTIFICATION" in large, centered, spaced-out capital letters
✓ Formal body text starting with: "This is to certify that according to the records of this Commission..."
✓ Name written as: "Mr./Ms. [FULL NAME]"
✓ States: "has been accredited as [PROFESSION] with Accreditation No. [NUMBER] valid until [DATE]"
✓ Profession: "REAL ESTATE SALESPERSON", "REAL ESTATE BROKER", or "REAL ESTATE APPRAISER"
✓ States: "paid the Renewal fee of Php [AMOUNT]" with "Official Receipt No." and date
✓ States: "his/her accreditation card as [PROFESSION] is under process"
✓ Issued location and date (e.g., "Bacolod City, Philippines, May 30, 2022")
✓ A DOCUMENTARY STAMP (BIR stamp — purple/violet colored, rectangular, with serial number)
✓ AUTHORIZED SIGNATORY: Name, title, and regional office (e.g., "PRC Regional Office VI (Iloilo City)")
✓ An actual PEN SIGNATURE (wet ink signature) of the signatory
✓ Bottom section with: AMOUNT, O.R. No., DATE, "Verified and typed by" with a clerk signature
✓ Footer: "ANY ERASURE OR ALTERATION HEREON NULLIFIES THIS CERTIFICATION" and "NOT VALID WITHOUT DRY SEAL AND DOCUMENTARY STAMP"
✓ Regional office address, phone numbers, email, offsite service centers at the very bottom
✓ Document form code (e.g., "PRCRO6-ARS-28") and revision info in bottom-right

If the image is clearly NOT a PRC document (resume, CV, diploma, school ID, driver's license, selfie, random photo, screenshot, meme, birth certificate, NBI clearance, barangay clearance, TIN card, company ID, or any non-PRC document) — set "document_type" to what it actually is and "decision" to "rejected".

═══════════════════════════════════════════════════════════
STEP 2 — AUTHENTICITY & SECURITY FEATURE ANALYSIS
═══════════════════════════════════════════════════════════

Check for these SECURITY FEATURES that indicate authenticity:

FOR PRC ID CARD:
- Official PRC seal/logo present and properly positioned
- Photo appears to be an original embedded photo (not pasted/photoshopped over)
- Text is printed with consistent fonts (not handwritten, not different fonts mixed)
- Barcode is present and appears genuine (not drawn or copied)
- Card has consistent coloring and layout matching official PRC format
- No obvious signs of digital editing (misaligned text, different DPI regions, clone stamp artifacts, warping around text)

FOR PRC CERTIFICATION LETTER:
- Official PRC letterhead with old English/Gothic font headers
- PRC circular seal/logo watermark visible across the page
- Documentary stamp (BIR stamp) physically affixed — should appear as a real physical sticker, not digitally overlaid
- Documentary stamp has visible serial number
- Wet ink signature present (not a digitally pasted signature)
- Official receipt details present (OR number, date, amount)
- "Verified and typed by" section with clerk name and signature
- Footer warning about erasure/alteration present
- ISO/PAB certification logos in header
- Consistent paper color and printing quality throughout

RED FLAGS — Signs of a FAKE or FORGED document:
✗ Document looks digitally created from scratch (not a photo of a real document)
✗ Text appears photoshopped, pasted, or overlaid onto a template
✗ Inconsistent fonts, text sizes, or alignment within the document
✗ Missing the PRC circular seal/logo
✗ Missing documentary stamp on certification letters
✗ Missing barcode on ID cards
✗ No wet ink signature (certification letters should have hand-signed signatures)
✗ Photo appears pasted on or edited (for ID cards)
✗ Blurry text in some areas but sharp in others (sign of editing)
✗ Generic or placeholder text visible
✗ Wrong layout or structure that doesn't match official PRC formats
✗ Spelling errors in official text (PRC documents are professionally printed)
✗ No official receipt details on certification letters
✗ Image is a screenshot of a digital file rather than a photo/scan of a physical document
✗ Watermarks from editing software (Photoshop, Canva, etc.)
✗ The document looks too perfect/clean (no paper texture, no fold marks, no natural wear)
✗ Information fields are blank or have placeholder text
✗ License number format doesn't match PRC standards

═══════════════════════════════════════════════════════════
STEP 3 — INFORMATION VERIFICATION & CROSS-MATCHING
═══════════════════════════════════════════════════════════

If the document passes Steps 1-2, verify that ALL information matches:

A) NAME MATCHING:
- Extract the full name from the PRC document
- Compare with the applicant's form name: "{$name}"
- Compare with the applicant's account name: "{$accountName}"
- The PRC name must reasonably match BOTH
- Allow: minor spacing differences, middle name abbreviations, "Mr./Ms." prefixes, reversed first/last name order, "Jr./Sr./III" suffixes
- REJECT if clearly different people (e.g., PRC says "Juan Dela Cruz" but account is "Maria Santos")

B) LICENSE NUMBER MATCHING:
- Extract the license/accreditation number from the document
- Compare with provided number: "{$licenseNumber}"
- Numbers should match exactly (allow leading zeros difference)

C) PRC ID MATCHING:
- If visible, extract the PRC ID number
- Compare with provided: "{$prcId}"

D) EXPIRY DATE VALIDATION:
- Extract the validity/expiry date from the document
- Compare with provided date: "{$expiryDate}"
- Dates should reasonably match

E) PROFESSION VALIDATION:
- Must be one of: Real Estate Broker, Real Estate Salesperson, Real Estate Appraiser
- REJECT if the PRC license is for a completely different profession (nursing, engineering, teaching, accounting, etc.)

═══════════════════════════════════════════════════════════
RESPONSE FORMAT
═══════════════════════════════════════════════════════════

Respond with ONLY valid JSON (no markdown, no code blocks, just raw JSON):
{
  "document_type": "PRC ID Card" or "PRC Certification" or "Resume" or "Diploma" or "Random Photo" or "Fake PRC Document" etc.,
  "document_format": "id_card" or "certification_letter" or "not_prc",
  "is_prc_document": true,
  "is_valid_prc_card": true,
  "authenticity_score": 0.85,
  "security_features_found": ["PRC seal", "documentary stamp", "wet signature", "barcode", "official letterhead"],
  "security_features_missing": [],
  "red_flags_detected": [],
  "extracted_name": "Name as shown on document or null if unreadable",
  "extracted_license_number": "Number as shown on document or null if unreadable",
  "extracted_prc_id": "PRC ID if visible or null",
  "extracted_expiry_date": "YYYY-MM-DD or null if unreadable",
  "extracted_profession": "Real Estate Broker or Real Estate Salesperson etc. or null",
  "extracted_registration_date": "YYYY-MM-DD or null",
  "extracted_issuing_office": "Regional Office name or Manila or null",
  "name_matches": true,
  "name_matches_account": true,
  "license_number_matches": true,
  "expiry_date_valid": true,
  "profession_is_real_estate": true,
  "decision": "approved",
  "confidence": 0.85,
  "reasoning": "Detailed explanation including which security features were verified and any concerns"
}

FIELD DEFINITIONS:
- "authenticity_score": 0.0 to 1.0 — How likely is this a GENUINE (not fake/forged) PRC document? Score based on security features present, document quality, and consistency.
- "security_features_found": List of authentic security features you identified in the document
- "security_features_missing": List of expected security features that are absent (red flag)
- "red_flags_detected": List of any suspicious elements that suggest forgery/fakery
- "name_matches": Does the PRC document name match the form name?
- "name_matches_account": Does the PRC document name match the account name? CRITICAL identity check.

DECISION RULES (MUST be one of three values):
- "approved" — The document is clearly an authentic PRC document (ID card or certification), has the expected security features (authenticity_score >= 0.7), profession is real estate, name matches BOTH form and account name, license number matches, and overall confidence >= 0.7
- "unclear" — The document appears to be a PRC document but: image quality is poor, some security features can't be verified, name partially matches but uncertain, or some information can't be confirmed. Needs admin review.
- "rejected" — Use when ANY of these apply:
  (a) The image is clearly NOT a PRC document
  (b) The document appears to be FAKE or FORGED (too many red flags, missing critical security features, obvious editing)
  (c) The PRC name clearly does NOT match the account name (identity fraud)
  (d) The profession is NOT real estate related
  (e) The document is a PRC license for a different profession (nursing, engineering, etc.)

ADDITIONAL RULES:
- ACCEPT BOTH PRC ID cards AND PRC certification letters — both are valid proof
- Be lenient with name matching (allow minor differences) but STRICT on identity matching
- A document with many red flags or missing security features should be "rejected" as likely fake, even if it resembles a PRC format
- If the document looks like a PRC format but you suspect it was digitally fabricated, set decision to "rejected" and explain why in red_flags_detected
- If image quality is poor but the document LOOKS real, use "unclear" — let an admin see it
- Provide DETAILED reasoning that specifically mentions which security features you found or didn't find
- The reasoning should help an admin understand exactly why you made your decision
PROMPT;
    }

    private function parseVerificationResponse(string $responseText): array
    {
        // Try to extract JSON from the response (Gemini might wrap it in markdown code blocks)
        $jsonText = $responseText;

        // Remove markdown code blocks if present
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $responseText, $matches)) {
            $jsonText = trim($matches[1]);
        }

        // Try direct JSON parse
        $parsed = json_decode($jsonText, true);

        if (!$parsed || !is_array($parsed)) {
            // Try to find JSON object in the text
            if (preg_match('/\{[\s\S]*\}/', $responseText, $matches)) {
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

        // Get the AI's decision
        $aiDecision = strtolower($parsed['decision'] ?? 'unclear');
        $isPrcDocument = $parsed['is_prc_document'] ?? false;
        $authenticityScore = (float) ($parsed['authenticity_score'] ?? 0);
        $redFlags = $parsed['red_flags_detected'] ?? [];
        $securityFound = $parsed['security_features_found'] ?? [];
        $securityMissing = $parsed['security_features_missing'] ?? [];

        // If AI explicitly says it's not a PRC document, force rejected
        if (!$isPrcDocument && $aiDecision !== 'rejected') {
            $aiDecision = 'rejected';
        }

        // Normalize decision to one of 3 values
        if (!in_array($aiDecision, ['approved', 'unclear', 'rejected'])) {
            $aiDecision = 'unclear';
        }

        // AUTHENTICITY CHECK: If too many red flags detected, reject as likely fake
        if (count($redFlags) >= 3 && $aiDecision === 'approved') {
            $aiDecision = 'rejected';
            $parsed['reasoning'] = 'Document rejected due to multiple red flags detected: ' . implode(', ', $redFlags) . '. ' . ($parsed['reasoning'] ?? '');
        }

        // AUTHENTICITY CHECK: If authenticity score is too low, downgrade
        if ($authenticityScore > 0 && $authenticityScore < 0.5 && $aiDecision === 'approved') {
            $aiDecision = 'rejected';
            $parsed['reasoning'] = 'Document authenticity score too low (' . $authenticityScore . '). Possible forgery detected. ' . ($parsed['reasoning'] ?? '');
        } elseif ($authenticityScore > 0 && $authenticityScore < 0.7 && $aiDecision === 'approved') {
            $aiDecision = 'unclear';
            $parsed['reasoning'] = 'Document authenticity could not be fully confirmed (score: ' . $authenticityScore . '). Requires admin review. ' . ($parsed['reasoning'] ?? '');
        }

        // For "approved", double-check the key fields including identity match
        if ($aiDecision === 'approved') {
            $isValid = ($parsed['is_valid_prc_card'] ?? false)
                && ($parsed['name_matches'] ?? false)
                && ($parsed['name_matches_account'] ?? false)
                && ($parsed['license_number_matches'] ?? false)
                && (($parsed['confidence'] ?? 0) >= 0.7)
                && ($authenticityScore >= 0.7 || $authenticityScore === 0.0);

            if (!$isValid) {
                // If identity specifically doesn't match, reject instead of unclear
                if (!($parsed['name_matches_account'] ?? true)) {
                    $aiDecision = 'rejected';
                    $parsed['reasoning'] = 'The name on the PRC document does not match your registered account name. You may be using someone else\'s license. ' . ($parsed['reasoning'] ?? '');
                } else {
                    // Downgrade to unclear if other checks fail
                    $aiDecision = 'unclear';
                }
            }
        }

        // Even if AI said unclear/approved, reject if identity clearly doesn't match
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
        ];
    }
}
