<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    const EMAIL = 'gustavo.sobrinho01@gmail.com';
    const PASSWORD = 'password';
    const NEW_PASSWORD = 'new-password';

    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => self::EMAIL,
            'password' => self::PASSWORD
        ]);
    }
}
