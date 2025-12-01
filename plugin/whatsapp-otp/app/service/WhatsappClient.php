<?php
declare(strict_types=1);

namespace plugin\whatsappOtp\app\service;

class WhatsappClient
{
    public function sendTemplateMessage(string $tokenId, string $to, string $templateName, string $language, array $components): array
    {
        $url = sprintf('https://graph.facebook.com/v21.0/%s/messages', $tokenId);
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $language],
                'components' => $components
            ]
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . getenv('WABA_TOKEN'),
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err !== '') {
            return ['success' => false, 'error' => $err];
        }
        $json = json_decode($resp, true);
        return ['success' => true, 'response' => $json];
    }
}
