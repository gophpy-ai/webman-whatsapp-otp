# webman-whatsapp-otp

[![Packagist Version](https://img.shields.io/packagist/v/gophpy-ai/webman-whatsapp-otp?label=packagist)](https://packagist.org/packages/gophpy-ai/webman-whatsapp-otp)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**WhatsApp OTP Login plugin for Webman** — provides a simple, secure OTP (one-time password) flow using WhatsApp Cloud API and JWT-based authentication.

## Features

- Send OTP to users via WhatsApp Cloud API (template messages)
- Store OTP in cache (Redis or framework cache)
- Verify OTP and issue JWT (HS256)
- Small, composer-installable Webman plugin (auto copies config/routes)
- Ready for production with recommended security practices

## Quick Install

```bash
composer require gophpy-ai/webman-whatsapp-otp
composer require firebase/php-jwt
```

> The package type is `webman-plugin`. Webman will copy plugin `src/config` -> `config/plugin` and `src/app` -> `app/plugin` automatically.

## Configuration

Copy or edit `config/plugin/whatsapp-otp/config.php`:

```php
<?php
return [
    'waba_token' => 'YOUR_WABA_TOKEN',              // WhatsApp Cloud API token
    'phone_number_id' => 'YOUR_PHONE_NUMBER_ID',    // Phone number id from Meta
    'jwt_key' => 'YOUR_SECRET_KEY',                 // Keep this secret (use env in production)
    'jwt_expire' => 3600,                           // Token TTL in seconds
];
```

## Routes

Plugin provides these endpoints:

- `POST /otp/send` — body: `{ "phone": "9665xxxxxxx" }`
- `POST /otp/verify` — body: `{ "phone": "9665xxxxxxx", "code": "123456" }`

On success `/otp/verify` returns `{ "token": "<jwt>" }`.

## Usage

1. Configure WhatsApp message template (e.g. `otp_login_code`) in your Meta Business Manager.
2. Set `waba_token` and `phone_number_id` in plugin config.
3. Use your frontend to call `/otp/send`, then `/otp/verify`, store token in client.

## Security Recommendations

- Never commit real tokens or secrets. Use environment variables.
- Use Redis for OTP storage (webman cache recommended).
- Rate-limit `/otp/send` per IP and per phone number.
- Enforce HTTPS and secure JWT secret (>=32 bytes).
- Do not return OTP in API responses in production.

## Contributing

1. Fork the repo.
2. Create feature branch.
3. Submit a PR with tests and documentation.

## License

MIT. See [LICENSE](LICENSE).
