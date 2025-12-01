<?php
declare(strict_types=1);

use support\Migration;
use support\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function ($table) {
            $table->bigIncrements('id');
            $table->string('phone', 20)->unique();
            $table->string('name', 100)->nullable();
            $table->string('password', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
