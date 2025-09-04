<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Config;
use OpenAI\Laravel\Facades\OpenAI;

class TicketClassifier
{
    public function classify(Ticket $ticket): array
    {
        $enabled = filter_var(config('services.openai.enabled', env('OPENAI_CLASSIFY_ENABLED', false)), FILTER_VALIDATE_BOOLEAN);

        if (!$enabled) {
            $categories = ['billing','technical','account','sales','general'];
            $category = $categories[array_rand($categories)];
            return [
                'category' => $category,
                'explanation' => 'Classification disabled. Using fallback random category.',
                'confidence' => round(mt_rand(40, 95) / 100, 2),
            ];
        }

        $system = <<<SYS
You are a strict JSON-only classifier for support tickets. Reply with **only** a single JSON object and no extra text. Keys:
- category: one of ["billing","technical","account","sales","general"].
- explanation: brief reason for the classification.
- confidence: number between 0 and 1.
SYS;

        $user = "Subject: {$ticket->subject}\n\nBody: {$ticket->body}";

        $resp = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $user],
            ],
            'temperature' => 0.2,
        ]);

        $content = $resp->choices[0]->message->content;
        // Normalize JSON (in case of code fences)
        $json = trim(preg_replace('/^```json|```$/m', '', $content));
        $data = json_decode($json, true);

        if (!is_array($data) || !isset($data['category'])) {
            // Fallback if parsing fails
            $data = [
                'category' => 'general',
                'explanation' => 'Fallback classification due to parsing error.',
                'confidence' => 0.5,
            ];
        }

        // Coerce
        $data['category'] = (string) ($data['category'] ?? 'general');
        $data['explanation'] = (string) ($data['explanation'] ?? '');
        $data['confidence'] = (float) ($data['confidence'] ?? 0.5);

        return $data;
    }
}

