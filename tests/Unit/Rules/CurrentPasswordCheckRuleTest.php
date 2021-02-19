<?php

namespace Tests\Unit\Rules;

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

        $validation = $currentPasswordCheckRule->passes('password', 'password');

        $this->assertFalse($validation);
    }

    /**
     * @test
     */
    public function must_be_able_to_check_current_password_when_not_logged()
    {
        $currentPasswordCheckRule = new CurrentPasswordCheckRule;

        $this->actingAs($this->user);

        $validation = $currentPasswordCheckRule->passes('password', 'password');

        $this->assertTrue($validation);
    }
}
