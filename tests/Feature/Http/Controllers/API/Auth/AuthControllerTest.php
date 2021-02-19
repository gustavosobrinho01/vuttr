<?php

namespace Tests\Feature\Http\Controllers\API\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function must_be_able_to_login()
    {
        $credentials = [
            'email' => self::EMAIL,
            'password' => self::PASSWORD
        ];

        $this->postJson(route('api.auth.login'), $credentials)
            ->assertOk()
            ->assertJson(['user' => $this->user->toArray()])
            ->assertJsonStructure(['user', 'token']);
    }

    /**
     * @test
     */
    public function should_not_be_able_to_login_when_credentials_are_incorrect()
    {
        $credentials = [
            'email' => 'user@non-existing',
            'password' => self::PASSWORD
        ];

        $this->postJson(route('api.auth.login'), $credentials)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);

        $credentials = [
            'email' => self::EMAIL,
            'password' => self::NEW_PASSWORD
        ];

        $this->postJson(route('api.auth.login'), $credentials)
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     */
    public function must_be_able_to_show_logged_user()
    {
        $this->actingAs($this->user)
            ->getJson(route('api.auth.me'))
            ->assertOk()
            ->assertJsonStructure(['user'])
            ->assertJson(['user' => $this->user->toArray()]);
    }

    /**
     * @test
     */
    public function should_not_be_able_to_show_user_data_when_not_logged()
    {
        $this->getJson(route('api.auth.me'))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function must_be_able_to_logout()
    {
        $this->actingAs($this->user)
            ->postJson(route('api.auth.logout'))
            ->assertNoContent();

        $this->assertEquals(0, $this->user->tokens()->count());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_logout_user_when_not_logged()
    {
        $this->postJson(route('api.auth.logout'))
            ->assertUnauthorized();
    }
}
