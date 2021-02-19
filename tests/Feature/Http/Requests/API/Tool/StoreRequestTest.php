<?php

namespace Tests\Feature\Http\Requests\API\Tool;

use App\Http\Requests\API\Tool\StoreRequest;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Tool
     */
    protected $tool;
    /**
     * @var User
     */
    protected $user;
    /**
     * @var Tool
     */
    public $userTool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tool = Tool::factory()->create();
        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    public function requiredFormValidationProvider(): array
    {
        return [
            ['title', null],
            ['title', ''],
            ['title', 1],
            ['title', Str::random(2)],
            ['title', Str::random(256)],

            ['link', null],
            ['link', ''],
            ['link', 'invalid-url'],
            ['link', Str::random(2)],
            ['link', Str::random(256)],

            ['description', null],
            ['description', ''],
            ['description', Str::random(2)],
            ['description', Str::random(1001)],

            ['tags', null],
            ['tags', ''],
            ['tags', 'invalid-array'],
            ['tags', []],
        ];
    }

    /**
     * @test
     * @dataProvider requiredFormValidationProvider
     * @param $formInput
     * @param $formInputValue
     */
    public function must_be_able_to_validate_store_rules($formInput, $formInputValue)
    {
        $storeRequest = new StoreRequest;

        $validator = Validator::make([
            $formInput => $formInputValue
        ], $storeRequest->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains($formInput, $validator->errors()->keys());
        $this->assertTrue($storeRequest->authorize());
    }

    /**
     * @test
     */
    public function must_be_able_to_validate_store_link_unique_rule()
    {
        $tool = Tool::factory()->for($this->user)->create();
        $storeRequest = new StoreRequest;

        $validator = Validator::make([
            'link' => $tool->link
        ], $storeRequest->rules());

        $this->assertFalse($validator->passes());
        $this->assertContains('link', $validator->errors()->keys());
        $this->assertTrue($storeRequest->authorize());
    }
}
