<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqQuestion;

class DodotController extends Controller
{
    // -------------------------------------------------------
    // Load static intents from aiconfig.json
    // (used for non-FAQ intents: admissions, tuition, etc.)
    // -------------------------------------------------------
    private function loadStaticIntents(): array
    {
        $path = public_path('assets/chatbotasset/aiconfig.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    // -------------------------------------------------------
    // Load FAQ intents dynamically from the database.
    // Keywords are extracted from each question's meaningful words.
    // Response is built from all active answers for that question.
    // -------------------------------------------------------
    private function loadFaqIntents(): array
    {
        $questions = FaqQuestion::with([
            'answers' => fn($q) => $q->where('is_active', true)->whereNull('deleted_at')
        ])
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $intents = [];

        // Common stop words to exclude from keyword extraction
        $stopWords = [
            'the', 'is', 'are', 'was', 'were', 'and', 'but', 'for',
            'with', 'that', 'this', 'from', 'have', 'has', 'had',
            'not', 'can', 'will', 'how', 'what', 'when', 'where',
            'who', 'why', 'does', 'did', 'its', 'your', 'our', 'about',
            'also', 'into', 'their', 'there', 'which', 'would', 'could',
        ];

        foreach ($questions as $faq) {
            // Skip questions with no active answers
            if ($faq->answers->isEmpty()) {
                continue;
            }

            // Extract meaningful keywords from the question text
            $words = preg_split('/\s+/', strtolower(preg_replace('/[^a-zA-Z0-9\s]/', '', $faq->question)));
            $keywords = array_values(array_filter($words, fn($w) =>
                strlen($w) >= 3 && !in_array($w, $stopWords)
            ));

            // Also add the full lowercased question for near-exact matching
            $keywords[] = strtolower(trim($faq->question));

            // Build the response HTML
            if ($faq->answers->count() === 1) {
                $answerHtml = $faq->answers->first()->answer;
            } else {
                $answerHtml = '<ul style="margin:6px 0; padding-left:18px;">';
                foreach ($faq->answers as $answer) {
                    $answerHtml .= '<li>' . $answer->answer . '</li>';
                }
                $answerHtml .= '</ul>';
            }

            $intents[] = [
                'category' => 'faq_db',
                'question' => $faq->question,
                'keywords' => array_unique($keywords),
                'response' => $answerHtml,
            ];
        }

        return $intents;
    }

    // -------------------------------------------------------
    // Match user input against a list of intents.
    // Returns the matched response string or null.
    // -------------------------------------------------------
    private function matchIntent(string $userInput, array $intents): ?string
    {
        foreach ($intents as $intent) {
            foreach ($intent['keywords'] as $keyword) {
                if (str_contains($userInput, strtolower($keyword))) {
                    return $intent['response'];
                }
            }
        }
        return null;
    }

    // -------------------------------------------------------
    // POST /dodot/chat
    // Priority:
    //   1. Static intents (aiconfig.json) — greetings, contact, etc.
    //   2. Database FAQs — dynamic questions & answers
    //   3. Fallback message
    // -------------------------------------------------------
    public function chat(Request $request)
    {
        $request->validate(['Prompt' => 'required|string|max:500']);

        $userInput = strtolower(trim($request->input('Prompt')));

        // 1️⃣ Match against static aiconfig.json intents
        $response = $this->matchIntent($userInput, $this->loadStaticIntents());
        if ($response) {
            return response()->json(['response' => $response]);
        }

        // 2️⃣ Match against live database FAQs
        $response = $this->matchIntent($userInput, $this->loadFaqIntents());
        if ($response) {
            return response()->json(['response' => $response]);
        }

        // 3️⃣ No match — return fallback
        return response()->json([
            'response' => "I'm sorry, I didn't quite understand that. You can ask me about <b>admissions, tuition fees, programs, the LMS, academic calendar</b>, and more. How can I help you?"
        ]);
    }
}