<?php

namespace Tests\Feature\Http\Requests\API\Profile;

use App\Http\Requests\API\Profile\UpdateRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateRequestTest extends TestCase
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
        ];
    }

    /**
     * @test
     * @dataProvider requiredFormValidationProvider
     * @param $formInput
     * @param $formInputValue
     */
    public function must_be_able_to_validate_update_rules($formInput, $formInputValue)
    {
        $updateRequest = new UpdateRequest;

        $validator = Validator::make([
            $formInput => $formInputValue
        ], $updateRequest->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains($formInput, $validator->errors()->keys());
        $this->assertTrue($updateRequest->authorize());
    }

    /**
     * @test
     */
    public function must_be_able_to_validate_update_unique_email_rule()
    {
        $user = User::factory()->create();

        $updateRequest = new UpdateRequest;

        $validator = Validator::make([
            'email' => $user->email
        ], $updateRequest->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains('email', $validator->errors()->keys());
        $this->assertTrue($updateRequest->authorize());
    }
}
