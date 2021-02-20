<?php

namespace Tests\Unit\Rules;

use App\Models\User;
use App\Rules\CurrentPasswordCheckRule;
use Tests\TestCase;

class CurrentPasswordCheckRuleTest extends TestCase
{
    /**
     * @var User
     */
    private $userInstance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userInstance = new User([
            'password' => User::DEFAULT_PASSWORD
        ]);
    }

    /**
     * @test
     */
    public function must_be_able_to_check_current_password_when_not_logged()
    {
        $this->actingAs($this->userInstance);

        $this->assertTrue(
            (new CurrentPasswordCheckRule)->passes('current_password', User::DEFAULT_PASSWORD)
        );
    }

    /**
     * @test
     */
    public function should_not_be_able_to_check_current_password_when_not_logged()
    {
        $this->assertFalse(
            (new CurrentPasswordCheckRule)->passes('current_password', User::DEFAULT_PASSWORD)
        );
    }

    /**
     * @test
     */
    public function should_not_be_able_to_check_current_password_when_not_correct()
    {
        $this->actingAs($this->userInstance);

        $this->assertFalse(
            (new CurrentPasswordCheckRule)->passes('current_password', 'invalid-current-password')
        );
    }
}
