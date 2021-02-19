<?php

namespace Tests\Feature\Http\Requests\API\Auth;

use App\Http\Requests\API\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    public function requiredFormValidationProvider(): array
    {
        return [
            ['email', null],
            ['email', ''],
            ['email', 1],
            ['email', Str::random(2)],
            ['email', Str::random(256)],
            ['email', 'invalid-email'],
            ['password', null],
            ['password', ''],
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
    public function must_be_able_to_validate_login_rules($formInput, $formInputValue)
    {
        $loginRequest = new LoginRequest;

        $validator = Validator::make([
            $formInput => $formInputValue
        ], $loginRequest->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains($formInput, $validator->errors()->keys());
        $this->assertTrue($loginRequest->authorize());
    }
}
