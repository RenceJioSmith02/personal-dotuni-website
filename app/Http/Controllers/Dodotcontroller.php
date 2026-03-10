<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DodotController extends Controller
{
    // Load intent data from JSON file
    private function loadIntents(): array
    {
        $path = public_path('assets/chatbotasset/aiconfig.json');
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    public function chat(Request $request)
    {
        $request->validate(['Prompt' => 'required|string|max:500']);

        $userInput = strtolower(trim($request->input('Prompt')));
        $intents = $this->loadIntents();

        // Keyword matching
        foreach ($intents as $intent) {
            foreach ($intent['keywords'] as $keyword) {
                if (str_contains($userInput, strtolower($keyword))) {
                    return response()->json(['response' => $intent['response']]);
                }
            }
        }

        // Fallback response
        return response()->json([
            'response' => "I'm sorry, I didn't quite understand that. 😊 You can ask me about <b>admissions, tuition fees, programs, the LMS, academic calendar</b>, and more. How can I help you?"
        ]);
    }
}