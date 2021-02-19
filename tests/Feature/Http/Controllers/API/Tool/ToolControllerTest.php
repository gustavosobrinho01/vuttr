<?php

namespace Tests\Feature\Http\Controllers\API\Tool;

use App\Models\Tool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function must_be_able_to_list_the_user_tools()
    {
        Tool::factory()->create();
        $userTool = Tool::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->getJson(route('api.tools.index'))
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
            ->getJson(route('api.tools.index', ['tag' => 'reactjs']))
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
        $this->getJson(route('api.tools.index'))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function should_not_be_able_to_list_the_tools_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->getJson(route('api.tools.index'))
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
            ->postJson(route('api.tools.store'), $tool->toArray())
            ->assertCreated()
            ->assertJsonStructure(['data'])
            ->assertJsonFragment($tool->makeHidden('user_id')->toArray());

        $this->assertDatabaseMissing((new Tool)->getTable(), ['user_id' => $tool->user_id]);
        $this->assertDatabaseHas((new Tool)->getTable(), ['user_id' => $this->user->id]);
        $this->assertDatabaseHas((new Tool)->getTable(), $tool->makeHidden(['user_id', 'tags'])->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_create_a_tool_when_not_logged()
    {
        $tool = Tool::factory()->make();

        $this->postJson(route('api.tools.store'), $tool->toArray())
            ->assertUnauthorized();

        $this->assertDatabaseMissing((new Tool)->getTable(), $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function must_be_able_to_show_a_tool()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->getJson(route('api.tools.show', $tool))
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

        $this->getJson(route('api.tools.show', $tool))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function should_not_be_able_to_show_a_tool_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->getJson(route('api.tools.show', $tool))
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
            ->putJson(route('api.tools.update', $tool), $tool->toArray())
            ->assertOk()
            ->assertJsonStructure(['data'])
            ->assertJsonFragment($tool->makeHidden('user_id')->toArray());

        $this->assertDatabaseMissing((new Tool)->getTable(), ['user_id' => $tool->user_id]);
        $this->assertDatabaseHas((new Tool)->getTable(), ['user_id' => $this->user->id]);
        $this->assertDatabaseHas((new Tool)->getTable(), $tool->makeHidden(['user_id', 'tags'])->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_a_tool_when_not_logged()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->putJson(route('api.tools.update', $tool), ['title' => 'New title'])
            ->assertUnauthorized();

        $this->assertDatabaseHas((new Tool)->getTable(), $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_a_tool_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->putJson(route('api.tools.update', $tool), ['title' => 'New title'])
            ->assertForbidden();

        $this->assertDatabaseHas((new Tool)->getTable(), $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function must_be_able_to_delete_a_tool()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->actingAs($this->user)
            ->deleteJson(route('api.tools.destroy', $tool))
            ->assertNoContent();

        $this->assertDatabaseMissing((new Tool)->getTable(), $tool->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_delete_a_tool_when_not_logged()
    {
        $tool = Tool::factory()->for($this->user)->create();

        $this->deleteJson(route('api.tools.destroy', $tool))
            ->assertUnauthorized();

        $this->assertDatabaseHas((new Tool)->getTable(), $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_delete_a_tool_when_not_allowed()
    {
        $tool = Tool::factory()->create();

        $this->actingAs($this->user)
            ->deleteJson(route('api.tools.destroy', $tool))
            ->assertForbidden();

        $this->assertDatabaseHas((new Tool)->getTable(), $tool->makeHidden('tags')->toArray());
    }

    /**
     * @test
     */
    public function must_be_able_to_delete_all_tools()
    {
        $tools = Tool::factory()->for($this->user)->count(5)->create();

        $this->actingAs($this->user)
            ->deleteJson(route('api.tools.destroyAll'))
            ->assertNoContent();

        $tools->each(function ($tool) {
            $this->assertDatabaseMissing((new Tool)->getTable(), $tool->toArray());
        });
    }

    /**
     * @test
     */
    public function should_not_be_able_to_delete_all_tools_when_not_logged()
    {
        $tools = Tool::factory()->for($this->user)->count(5)->create();

        $this->deleteJson(route('api.tools.destroyAll'))
            ->assertUnauthorized();

        $tools->each(function ($tool) {
            $this->assertDatabaseHas((new Tool)->getTable(), $tool->makeHidden('tags')->toArray());
        });
    }
}
