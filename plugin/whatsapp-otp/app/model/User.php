<?php
declare(strict_types=1);

namespace plugin\whatsappOtp\app\model;

use support\Model;

class User extends Model
{
    protected string $table = 'users';

    protected array $fillable = ['phone', 'name', 'password'];
}
