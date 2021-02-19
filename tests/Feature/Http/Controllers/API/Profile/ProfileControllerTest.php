<?php

namespace Tests\Feature\Http\Controllers\API\Profile;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function must_be_able_to_create_a_user()
    {
        $user = collect([
            'name' => 'User test',
            'email' => 'user@test',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD
        ]);

        $this->postJson(route('api.profile.register'), $user->toArray())
            ->assertCreated();

        $this->assertDatabaseHas((new User)->getTable(), $user->except(['password', 'password_confirmation'])->toArray());
    }

    /**
     * @test
     */
    public function must_be_able_to_update_a_user()
    {
        $attributes = $this->user->toArray();
        $attributes['email'] = 'teste@validation.update';
        $attributes['password'] = self::NEW_PASSWORD;

        $this->actingAs($this->user)
            ->putJson(route('api.profile.update'), $attributes)
            ->assertOk();

        $this->assertDatabaseHas((new User)->getTable(), ['email' => $attributes['email']]);
        $this->must_be_able_to_encrypt_the_password_when_creating_a_user();
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_a_user_when_not_logged()
    {
        $this->putJson(route('api.profile.update'), [])
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function must_be_able_to_update_a_user_password()
    {
        $attributes = $this->user->toArray();
        $attributes['email'] = 'teste@validation.update';
        $attributes['current_password'] = self::PASSWORD;
        $attributes['password'] = self::NEW_PASSWORD;
        $attributes['password_confirmation'] = self::NEW_PASSWORD;

        $this->actingAs($this->user)
            ->putJson(route('api.profile.updatePassword'), $attributes)
            ->assertOk();

        $this->assertDatabaseMissing((new User)->getTable(), ['email' => $attributes['email']]);
        $this->must_be_able_to_encrypt_the_password_when_creating_a_user(self::NEW_PASSWORD);
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_the_user_password_when_not_logged()
    {
        $this->putJson(route('api.profile.updatePassword'), [])
            ->assertUnauthorized();
    }

    /**
     * @test
     * @param string|null $password
     */
    public function must_be_able_to_encrypt_the_password_when_creating_a_user(string $password = null)
    {
        $this->assertTrue(Hash::check($password ?? self::PASSWORD, $this->user->password));
    }

    /**
     * @test
     */
    public function must_be_able_to_delete_a_user()
    {
        Tool::factory()->for($this->user)->count(5);

        $this->actingAs($this->user)
            ->deleteJson(route('api.profile.destroy'))
            ->assertNoContent();

        $this->assertDeleted($this->user)
            ->assertEquals(0, $this->user->tokens()->count());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_delete_a_user_when_not_logged()
    {
        $this->deleteJson(route('api.profile.destroy'))
            ->assertUnauthorized();
    }
}
