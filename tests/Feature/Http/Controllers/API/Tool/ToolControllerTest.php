<?php

namespace Tests\Feature\Http\Controllers\API\Tool;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string
     */
    protected $toolTable;
    /**
     * @var Tool
     */
    protected $tool;
    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->toolTable = (new Tool())->getTable();
        $this->tool = Tool::factory()->create();
        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function must_be_able_to_list_the_user_tools()
    {
        Tool::factory()->create();
        $userTool = Tool::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->json('get', route('api.tools.index'))
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(1, 'data')
            ->assertJson(['data' => [
                $userTool->toArray()
            ]]);
    }

    /**
     * @test
     */
    public function must_be_able_to_list_user_tools_by_tag()
    {
        $languageTool = Tool::factory()->for($this->user)->create([
            'tags' => ['javascript', 'php']
        ]);

        $frameworksTool = Tool::factory()->for($this->user)->create([
            'tags' => ['reactjs', 'laravel']
        ]);

        $this->actingAs($this->user)
            ->json('get', route('api.tools.index', ['tag' => 'reactjs']))
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(1, 'data')
            ->assertJsonMissing(['data' => [
                $languageTool->toArray()
            ]])
            ->assertJson(['data' => [
                $frameworksTool->toArray()
            ]]);
    }

    /**
     * @test
     */
    public function should_not_be_able_to_list_the_user_tools_when_not_logged()
    {
        $this->json('get', route('api.tools.index'))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function should_not_be_able_to_list_the_tools_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->json('get', route('api.tools.index'))
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(0, 'data')
            ->assertJsonMissing(['data' => [
                $tool->toArray()
            ]]);
    }

    /**
     * @test
     */
    public function must_be_able_to_create_a_tool()
    {
        $tool = Tool::factory()->make();

        $this->actingAs($this->user)
            ->json('post', route('api.tools.store'), $tool->toArray())
            ->assertCreated()
            ->assertJsonStructure(['data'])
            ->assertJsonFragment($tool->makeHidden('user_id')->toArray());

        $this->assertDatabaseMissing($this->toolTable, ['user_id' => $tool->user_id]);
        $this->assertDatabaseHas($this->toolTable, ['user_id' => $this->user->id]);
        $this->assertDatabaseHas($this->toolTable, $tool->makeHidden(['user_id', 'tags'])->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_create_a_tool_when_not_logged()
    {
        $tool = Tool::factory()->make();

        $this->json('post', route('api.tools.store'), $tool->toArray())
            ->assertUnauthorized();

        $this->assertDatabaseMissing($this->toolTable, $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function must_be_able_to_show_a_tool()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->json('get', route('api.tools.show', $tool))
            ->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonFragment($tool->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_show_a_tool_when_not_logged()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->json('get', route('api.tools.show', $tool))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function should_not_be_able_to_show_a_tool_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->json('get', route('api.tools.show', $tool))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function must_be_able_to_update_a_tool()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $tool->user_id = 99;
        $tool->title = "New title";
        $tool->description = "New description";

        $this->actingAs($this->user)
            ->json('put', route('api.tools.update', $tool), $tool->toArray())
            ->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonFragment($tool->makeHidden('user_id')->toArray());

        $this->assertDatabaseMissing($this->toolTable, ['user_id' => $tool->user_id]);
        $this->assertDatabaseHas($this->toolTable, ['user_id' => $this->user->id]);
        $this->assertDatabaseHas($this->toolTable, $tool->makeHidden(['user_id', 'tags'])->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_a_tool_when_not_logged()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->json('put', route('api.tools.update', $tool), ['title' => 'New title'])
            ->assertUnauthorized();

        $this->assertDatabaseHas($this->toolTable, $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_a_tool_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->json('put', route('api.tools.update', $tool), ['title' => 'New title'])
            ->assertForbidden();

        $this->assertDatabaseHas($this->toolTable, $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function must_be_able_to_delete_a_tool()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->json('delete', route('api.tools.destroy', $tool))
            ->assertNoContent();

        $this->assertDatabaseMissing($this->toolTable, $tool->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_delete_a_tool_when_not_logged()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->json('delete', route('api.tools.destroy', $tool))
            ->assertUnauthorized();

        $this->assertDatabaseHas($this->toolTable, $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_delete_a_tool_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->json('delete', route('api.tools.destroy', $tool))
            ->assertForbidden();

        $this->assertDatabaseHas($this->toolTable, $tool->makeHidden('tags')->toArray());
    }
}
