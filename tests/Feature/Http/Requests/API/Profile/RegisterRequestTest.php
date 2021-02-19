<?php

namespace Tests\Feature\Http\Requests\API\Profile;

use App\Http\Requests\API\Profile\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    public function requiredFormValidationProvider(): array
    {
        return [
            ['name', null],
            ['name', ''],
            ['name', 1],
            ['name', Str::random(2)],
            ['name', Str::random(256)],

            ['email', null],
            ['email', ''],
            ['email', 'invalid-email'],
            ['email', Str::random(2)],
            ['email', Str::random(256)],

            ['password', null],
            ['password', ''],
            ['password', 1],
            ['password', Str::random(User::MIN_PASSWORD_LENGTH - 1)],
            ['password', Str::random(256)],
        ];
    }

    /**
     * @test
     * @dataProvider requiredFormValidationProvider
     * @param $formInput
     * @param $formInputValue
     */
    public function must_be_able_to_validate_register_rules($formInput, $formInputValue)
    {
        $request = new RegisterRequest;

        $validator = Validator::make([
            $formInput => $formInputValue
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains($formInput, $validator->errors()->keys());
        $this->assertTrue($request->authorize());
    }

    /**
     * @test
     */
    public function must_be_able_to_validate_register_password_confirmation_rule()
    {
        $request = new RegisterRequest;

        $validator = Validator::make([
            'password' => '123123123',
            'password_confirmation' => '1231231234'
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains('password', $validator->errors()->keys());
        $this->assertTrue($request->authorize());
    }
}
