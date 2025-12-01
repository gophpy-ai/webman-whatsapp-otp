# Webman WhatsApp OTP (PHP 8.2+)

WhatsApp OTP plugin for Webman 2.x, written for PHP 8.2+.

## Features
- OTP via WhatsApp Cloud API (template)
- JWT access token + refresh token
- Middleware for protecting routes
- Migrations for users table
- All config lives inside plugin directory

## Install
Place plugin folder under your project `plugin/` directory or use composer.

## Configuration
Set environment variables in your `.env`:
```
WABA_TOKEN=...
PHONE_NUMBER_ID=...
JWT_ACCESS_SECRET=...
JWT_REFRESH_SECRET=...
```

Edit plugin config files if needed: `plugin/whatsapp-otp/config/*.php`

## Routes
- POST /otp/send
- POST /otp/login
- POST /auth/refresh
