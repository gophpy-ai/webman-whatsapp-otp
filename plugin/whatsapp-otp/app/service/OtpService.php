<?php
declare(strict_types=1);

namespace plugin\whatsappOtp\app\service;

use plugin\whatsappOtp\app\dto\OtpRequestDto;

class OtpService
{
    public function __construct(
        private WhatsappClient $client,
    ) {}

    public function generateOtp(int $length = 6): string
    {
        $min = (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }

    public function sendOtp(OtpRequestDto $dto): array
    {
        $config = config('plugin.whatsapp-otp.whatsapp');
        $template = $config['template_name'] ?? 'otp_login_code';
        $lang = $config['template_language'] ?? 'en_US';

        $otp = $this->generateOtp((int)config('plugin.whatsapp-otp.otp.otp_length', 6));
        // store otp in cache
        cache()->set('otp:' . $dto->phone, $otp, (int)config('plugin.whatsapp-otp.otp.expire_seconds', 300));

        $components = [
            [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $otp]
                ]
            ]
        ];

        return $this->client->sendTemplateMessage(
            $config['phone_number_id'] ?? '',
            $dto->phone,
            $template,
            $lang,
            $components
        );
    }
}
