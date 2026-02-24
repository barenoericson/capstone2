<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models');
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
                return [
                    'decision' => 'unclear',
                    'approved' => false,
                    'extracted' => [],
                    'reasoning' => 'AI verification service temporarily unavailable. Application will be reviewed by an admin.',
                    'confidence' => 0,
                ];
            }

            $data = $response->json();
            $responseText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            Log::info('Gemini raw response', ['text' => substr($responseText, 0, 1000)]);

            if (empty($responseText)) {
                Log::warning('Gemini returned empty response.');
                return [
                    'decision' => 'unclear',
                    'approved' => false,
                    'extracted' => [],
                    'reasoning' => 'AI could not process the image. Application will be reviewed by an admin.',
                    'confidence' => 0,
                ];
            }

            return $this->parseVerificationResponse($responseText);

        } catch (\Exception $e) {
            Log::error('Gemini verification exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'decision' => 'unclear',
                'approved' => false,
                'extracted' => [],
                'reasoning' => 'AI verification encountered an error. Application will be reviewed by an admin.',
                'confidence' => 0,
            ];
        }
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
- RealtyLinkPH is an online real estate platform connecting property buyers and verified real estate agents in the Philippines.
- Founded by a team of Filipino developers as a capstone project, with the vision of making Philippine real estate more accessible and transparent.
- Key features: property listings with photos, AI-powered agent verification (PRC license scanning), real-time messaging between buyers and agents, property viewing scheduling, document management with digital signatures, saved/favorite properties.
- Buyers can browse properties, save favorites, schedule viewings, message agents, and sign documents.
- Agents can list properties, manage viewings, handle documents, and communicate with potential buyers.
- The platform verifies agents using AI (you!) by scanning their PRC (Professional Regulation Commission) license.

YOUR PERSONALITY:
- Friendly, professional, and helpful — like a knowledgeable real estate advisor friend.
- You speak in a warm, conversational tone. Use simple language.
- You can use occasional Filipino expressions (like "po", "naman") to feel more relatable, but keep responses mostly in English.
- Keep responses concise — 2-4 sentences for simple questions, longer for detailed advice.
- If asked something you don't know or that's outside your scope, be honest and suggest they contact support or an agent.

WHAT YOU CAN HELP WITH:
1. Platform questions: How to use RealtyLinkPH, features, navigation, account help
2. Real estate advice: General tips on buying/selling property in the Philippines, investment advice, market trends
3. Philippine real estate basics: Property types (condo, house & lot, townhouse, lot only), locations, pricing factors
4. Agent info: How to become a verified agent, PRC license requirements, accreditation
5. General guidance: Document signing process, viewing scheduling, messaging tips

WHAT YOU SHOULD NOT DO:
- Never provide specific legal advice — suggest consulting a lawyer
- Never guarantee property values or investment returns
- Never share personal data about other users
- Never pretend to execute actions (like booking viewings or signing documents) — just explain how the user can do it
- Keep responses focused and relevant

Remember: You are RealtyLinkPH Buddy. Always identify yourself as such if asked who you are.
PROMPT;
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
You are RealtyLinkPH Buddy, a verification assistant for a Philippine real estate platform called RealtyLinkPH.

Your task is to analyze this uploaded image and determine:
1. Is this image actually a PRC (Professional Regulation Commission) document for real estate?
2. If yes, does the information match what the applicant provided?
3. CRITICAL: Does the name on the PRC document match the applicant's ACCOUNT NAME? This prevents users from submitting someone else's license.

The applicant provided these details in the form:
- Full Name (from form): {$name}
- PRC License Number: {$licenseNumber}
- PRC ID Number: {$prcId}
- License Expiry Date: {$expiryDate}

The applicant's REGISTERED ACCOUNT information (from their RealtyLinkPH account):
- Account Name: {$accountName}
- Account Email: {$accountEmail}

IMPORTANT IDENTITY CHECK:
The name on the PRC document must reasonably match BOTH the form name AND the account name. If the PRC document shows a completely different person's name than the account name, the applicant may be using someone else's PRC license — this should be flagged and NOT approved.

STEP 1 - DOCUMENT TYPE CHECK:
First, determine what type of document/image this is. There are TWO valid formats of PRC documents for real estate professionals:

FORMAT A — PRC PROFESSIONAL ID CARD:
- A physical ID card (usually credit-card sized, landscape orientation)
- Header: "Republic of the Philippines" and "PROFESSIONAL REGULATION COMMISSION"
- Contains: LAST NAME, FIRST NAME, MIDDLE INITIAL/NAME fields
- Contains: A registration/license number (numeric, typically 7 digits)
- Contains: REGISTRATION date and VALID UNTIL/EXPIRY date
- Contains: A profession title like "REAL ESTATE BROKER" or "REAL ESTATE APPRAISER" or "REAL ESTATE SALESPERSON"
- Contains: A passport-style photo of the licensee
- Contains: A barcode at the bottom
- Has the PRC logo (circular seal with "Republic of the Philippines")
- May have the city name (e.g., "Manila")

FORMAT B — PRC CERTIFICATION / ACCREDITATION LETTER:
- A full-page letter/certificate on official PRC letterhead
- Header: "Republic of the Philippines", "Professional Regulation Commission", "Regional Office No. X", city name
- Title: "CERTIFICATION" in large text
- Body text: "This is to certify that according to the records of this Commission, Mr./Ms. [NAME] has been accredited as [PROFESSION] with Accreditation No. [NUMBER] valid until [DATE]"
- Profession: "REAL ESTATE SALESPERSON", "REAL ESTATE BROKER", or "REAL ESTATE APPRAISER"
- Contains: PRC circular seal/logo (often as a watermark)
- Contains: An authorized signatory name and title (e.g., "PRC Regional Office")
- Contains: A documentary stamp (BIR stamp, often purple/violet)
- Contains: An official receipt number and date
- May contain: ISO certification logos, regional office address, contact info at bottom
- Footer often says: "ANY ERASURE OR ALTERATION HEREON NULLIFIES THIS CERTIFICATION"

BOTH formats are VALID and should be accepted. The key identifiers are:
- PRC logo/seal or "Professional Regulation Commission" text
- Real estate profession (Broker, Salesperson, or Appraiser)
- A license/accreditation number
- A name and validity period

If the image is clearly NOT a PRC document — for example a resume, CV, diploma, school ID, driver's license, selfie, random photo, screenshot, meme, birth certificate, NBI clearance, or any non-PRC document — set "document_type" to what it actually is and set "decision" to "rejected".

STEP 2 - VERIFICATION (only if it looks like a PRC document):
If it appears to be a PRC card or certification, try to extract and compare:
- Name on the document vs applicant's name
- License/accreditation number vs provided number
- Expiry/validity date vs provided date
- Profession should be real estate related (Broker, Salesperson, or Appraiser)

IMPORTANT: Respond with ONLY valid JSON in this exact format (no markdown, no code blocks, just raw JSON):
{
  "document_type": "PRC ID Card" or "PRC Certification" or "Resume" or "Diploma" or "Random Photo" or "Unknown Document" etc.,
  "document_format": "id_card" or "certification_letter" or "not_prc",
  "is_prc_document": true,
  "is_valid_prc_card": true,
  "extracted_name": "Name as shown on document or null if unreadable",
  "extracted_license_number": "Number as shown on document or null if unreadable",
  "extracted_expiry_date": "YYYY-MM-DD or null if unreadable",
  "extracted_profession": "Real Estate Broker or Real Estate Salesperson etc. or null",
  "name_matches": true,
  "name_matches_account": true,
  "license_number_matches": true,
  "expiry_date_valid": true,
  "profession_is_real_estate": true,
  "decision": "approved",
  "confidence": 0.85,
  "reasoning": "Brief explanation of your decision"
}

"name_matches" = Does the name on the PRC document match the name the applicant typed in the form?
"name_matches_account" = Does the name on the PRC document match the applicant's REGISTERED ACCOUNT name? This is the critical identity check — if false, the applicant may be using someone else's license.

Rules for the "decision" field (MUST be one of these three values):
- "approved" — The image is clearly a PRC document (either ID card or certification letter), the profession is real estate related, the name matches BOTH the form name AND the account name, the license number matches, and you are fairly confident (confidence >= 0.7)
- "unclear" — The image might be a PRC document but is too blurry, partially obscured, low quality, or you cannot confirm the details match. Also use this if the name on the PRC partially matches but you are not fully sure. Needs human admin review.
- "rejected" — The image is clearly NOT a PRC document. It is some other type of document (resume, diploma, random photo, selfie, screenshot, etc.) or is obviously fake/manipulated. ALSO use "rejected" if the PRC document name clearly does NOT match the account name (identity fraud attempt).

Additional rules:
- ACCEPT BOTH PRC ID cards AND PRC certification letters — both are valid proof
- Be lenient with name matching (allow minor differences in spacing, middle name abbreviations, "Mr./Ms." prefixes, reversed first/last name order)
- IDENTITY CHECK is critical: The PRC document name must reasonably match the account name. If the PRC shows "Juan Dela Cruz" but the account name is "Maria Santos", this is a clear mismatch — set name_matches_account to false and decision to "rejected" with reasoning about identity mismatch
- If the names are similar but not exact (e.g., nickname vs full name, minor typo), use "unclear" to let an admin verify
- If the image quality is poor but it still LOOKS like a PRC document, use "unclear" not "rejected"
- Only use "rejected" when you are confident the document is NOT a PRC document at all OR when there is a clear identity mismatch
- The profession MUST be real estate related (Broker, Salesperson, Appraiser) — reject if it's a PRC license for a different profession (e.g., nursing, engineering)
- Set confidence between 0.0 and 1.0 based on how certain you are of your decision
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

        // If AI explicitly says it's not a PRC document, force rejected
        if (!$isPrcDocument && $aiDecision !== 'rejected') {
            $aiDecision = 'rejected';
        }

        // Normalize decision to one of 3 values
        if (!in_array($aiDecision, ['approved', 'unclear', 'rejected'])) {
            $aiDecision = 'unclear';
        }

        // For "approved", double-check the key fields including identity match
        if ($aiDecision === 'approved') {
            $isValid = ($parsed['is_valid_prc_card'] ?? false)
                && ($parsed['name_matches'] ?? false)
                && ($parsed['name_matches_account'] ?? false)
                && ($parsed['license_number_matches'] ?? false)
                && (($parsed['confidence'] ?? 0) >= 0.7);

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
        ];
    }
}
