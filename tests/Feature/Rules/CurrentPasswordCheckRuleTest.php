<?php

namespace Tests\Feature\Rules;

use App\Models\User;
use App\Rules\CurrentPasswordCheckRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrentPasswordCheckRuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_not_be_able_to_check_current_password_when_not_correct()
    {
        $currentPasswordCheckRule = new CurrentPasswordCheckRule;

        $this->actingAs($this->user);

        $validation = $currentPasswordCheckRule->passes('password', 'invalid-current-password');

        $this->assertFalse($validation);
    }

    /**
     * @test
     */
    public function should_not_be_able_to_check_current_password_when_not_logged()
    {
        $currentPasswordCheckRule = new CurrentPasswordCheckRule;

        $validation = $currentPasswordCheckRule->passes('password', User::DEFAULT_PASSWORD);

        $this->assertFalse($validation);
    }

    /**
     * @test
     */
    public function must_be_able_to_check_current_password_when_not_logged()
    {
        $currentPasswordCheckRule = new CurrentPasswordCheckRule;

        $this->actingAs($this->user);

        $validation = $currentPasswordCheckRule->passes('password', User::DEFAULT_PASSWORD);

        $this->assertTrue($validation);
    }
}
