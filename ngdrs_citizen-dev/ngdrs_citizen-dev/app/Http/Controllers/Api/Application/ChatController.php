<?php

namespace App\Http\Controllers\Api\Application;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = $request->message;

        $apiKey = config('services.gemini.key');

        $prompt = "
        You are an AI assistant for NGDRS 2.0.

        Your job:
        - Help users understand form errors
        - Explain validation issues clearly
        - Guide users step-by-step
        - Keep answers short and practical
        - Never generate fake legal advice

        User Message:
        {$message}
        ";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ]
                ]
            );
        // dd($response->json());

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate AI response',
                'details' => $response->json()
            ], $response->status());
        }

        $data = $response->json();

        $reply = $data['candidates'][0]['content']['parts'][0]['text']
            ?? 'No response generated';

        return response()->json([
            'success' => true,
            'reply' => $reply,
        ]);
    }
}