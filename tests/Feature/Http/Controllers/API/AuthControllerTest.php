<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_be_able_to_register_an_user()
    {
        $user = collect([
            'name' => 'Gustavo Sobrinho',
            'email' => 'gustavo.sobrinho01@gmail.com',
            'password' => '123123123',
            'password_confirmation' => '123123123'
        ]);

        $this->json('post', route('api.auth.register'), $user->toArray())
            ->assertCreated();

        $this->assertDatabaseHas('users', $user->except(['password', 'password_confirmation'])->toArray());
    }

    /**
     * @test
     */
    public function should_be_able_to_update_an_user()
    {
        $user = User::factory()->create([
            'password' => '123123123'
        ]);

        $attributes = $user->toArray();
        $attributes['email'] = 'teste@validation.update';
        $attributes['password'] = '321321321';

        $this->json('put', route('api.auth.update'), $attributes)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->actingAs($user)
            ->json('put', route('api.auth.update'), $attributes)
            ->assertOk();

        $this->assertDatabaseHas('users', ['email' => $attributes['email']]);
        $this->assertTrue(Hash::check('123123123', $user->password));
    }

    /**
     * @test
     */
    public function should_be_able_to_update_password_an_user()
    {
        $user = User::factory()->create([
            'password' => '123123123'
        ]);

        $attributes = $user->toArray();
        $attributes['email'] = 'teste@validation.update';
        $attributes['current_password'] = '123123123';
        $attributes['password'] = '321321321';
        $attributes['password_confirmation'] = '321321321';

        $this->json('put', route('api.auth.updatePassword'), $attributes)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->actingAs($user)
            ->json('put', route('api.auth.updatePassword'), $attributes)
            ->assertOk();

        $this->assertDatabaseMissing('users', ['email' => $attributes['email']]);
        $this->assertFalse(Hash::check('123123123', $user->password));
    }

    /**
     * @test
     */
    public function should_encrypt_user_password_when_new_user_created()
    {
        $passwordToCheck = '123123123';

        $user = User::factory()->create([
            'password' => $passwordToCheck
        ]);

        $this->assertTrue(Hash::check($passwordToCheck, $user->password));
    }

    /**
     * @test
     */
    public function should_be_able_to_login_an_user()
    {
        $user = User::factory()->create([
            'email' => 'gustavo.sobrinho01@gmail.com',
            'password' => '123123123'
        ]);

        $credentials = [
            'email' => 'gustavo.sobrinho01@gmail.com',
            'password' => '123123123'
        ];

        $this->json('post', route('api.auth.login'), $credentials)
            ->assertOk()
            ->assertJson(['user' => $user->toArray()])
            ->assertJsonStructure(['user', 'token']);
    }

    /**
     * @test
     */
    public function should_be_able_to_logout_an_user()
    {
        $user = User::factory()->create();

        $this->json('post', route('api.auth.logout'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->actingAs($user)
            ->json('post', route('api.auth.logout'))
            ->assertNoContent();

        $this->assertEquals(0, $user->tokens()->count());
    }

    /**
     * @test
     */
    public function should_be_able_to_destroy_an_user()
    {
        $user = User::factory()->has(Tool::factory()->count(5))->create();

        $this->json('delete', route('api.auth.destroy'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->actingAs($user)
            ->json('delete', route('api.auth.destroy'))
            ->assertNoContent();

        $this->assertDeleted($user)
            ->assertEquals(0, $user->tokens()->count());
    }
}
