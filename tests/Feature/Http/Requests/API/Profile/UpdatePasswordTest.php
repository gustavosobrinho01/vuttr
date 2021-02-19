<?php

namespace Tests\Feature\Http\Requests\API\Profile;

use App\Http\Requests\API\Profile\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
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

        $this->actingAs($this->user);
    }

    public function requiredFormValidationProvider(): array
    {
        return [
            ['current_password', null],
            ['current_password', ''],
            ['current_password', 1],
            ['current_password', Str::random(User::MIN_PASSWORD_LENGTH - 1)],
            ['current_password', Str::random(256)],
            ['current_password', 'invalid-password'],

            ['password', null],
            ['password', ''],
            ['password', 1],
            ['password', Str::random(User::MIN_PASSWORD_LENGTH - 1)],
            ['password', Str::random(256)],
            ['password', 'password'],
        ];
    }

    /**
     * @test
     * @dataProvider requiredFormValidationProvider
     * @param $formInput
     * @param $formInputValue
     */
    public function must_be_able_to_validate_update_password_rules($formInput, $formInputValue)
    {
        $updatePasswordRequest = new UpdatePasswordRequest;

        $validator = Validator::make([
            $formInput => $formInputValue
        ], $updatePasswordRequest->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains($formInput, $validator->errors()->keys());
        $this->assertTrue($updatePasswordRequest->authorize());
    }

    /**
     * @test
     */
    public function must_be_able_to_validate_update_password_confirmation_rule()
    {
        $updatePasswordRequest = new UpdatePasswordRequest;

        $validator = Validator::make([
            'password' => '123123123',
            'password_confirmation' => '1231231234'
        ], $updatePasswordRequest->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains('password', $validator->errors()->keys());
        $this->assertTrue($updatePasswordRequest->authorize());
    }
}
