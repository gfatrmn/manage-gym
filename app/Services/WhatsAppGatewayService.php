<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppGatewayService
{
    public function isConfigured(): bool
    {
        return filled(config('services.whatsapp.driver'))
            && filled(config('services.whatsapp.base_url'))
            && filled(config('services.whatsapp.instance'))
            && filled(config('services.whatsapp.api_key'));
    }

    public function driver(): ?string
    {
        return config('services.whatsapp.driver');
    }

    public function sendText(string $phone, string $message): array
    {
        if (! $this->isConfigured()) {
            return [
                'ok' => false,
                'mode' => 'manual',
                'error' => 'WhatsApp gateway belum dikonfigurasi.',
            ];
        }

        return match ($this->driver()) {
            'evolution' => $this->sendViaEvolution($phone, $message),
            default => [
                'ok' => false,
                'mode' => 'manual',
                'error' => 'Driver WhatsApp belum didukung.',
            ],
        };
    }

    protected function sendViaEvolution(string $phone, string $message): array
    {
        $baseUrl = rtrim((string) config('services.whatsapp.base_url'), '/');
        $instance = (string) config('services.whatsapp.instance');
        $apiKey = (string) config('services.whatsapp.api_key');

        $response = Http::timeout((int) config('services.whatsapp.timeout', 15))
            ->withHeaders([
                'apikey' => $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post("{$baseUrl}/message/sendText/{$instance}", [
                'number' => $phone,
                'text' => $message,
                'delay' => 0,
                'linkPreview' => false,
            ]);

        if ($response->successful()) {
            return [
                'ok' => true,
                'mode' => 'automatic',
                'response' => $response->json(),
            ];
        }

        return [
            'ok' => false,
            'mode' => 'manual',
            'error' => $response->body() ?: 'Permintaan gateway WhatsApp gagal.',
            'status' => $response->status(),
        ];
    }
}
