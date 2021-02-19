<?php

namespace Tests\Unit\Rules;

use App\Models\User;
use App\Rules\CurrentPasswordCheckRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrentPasswordCheckRuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function should_not_be_able_to_check_current_password_when_not_correct()
    {
        $this->actingAs($this->user);

        $currentPasswordCheckRule = new CurrentPasswordCheckRule;

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
        $this->actingAs($this->user);

        $currentPasswordCheckRule = new CurrentPasswordCheckRule;

        $validation = $currentPasswordCheckRule->passes('password', 'password');

        $this->assertTrue($validation);
    }
}
