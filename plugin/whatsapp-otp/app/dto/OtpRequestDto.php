<?php
declare(strict_types=1);

namespace plugin\whatsappOtp\app\dto;

readonly class OtpRequestDto
{
    public function __construct(
        public string $phone,
    ) {}
}
