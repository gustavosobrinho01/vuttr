<?php

namespace Tests\Feature\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    const EMAIL = 'gustavo.sobrinho01@gmail.com';
    const PASSWORD = '123123123';

    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => self::EMAIL,
            'password' => self::PASSWORD
        ]);
    }

    /**
     * @test
     */
    public function should_be_able_to_login_an_user()
    {
        $credentials = [
            'email' => self::EMAIL,
            'password' => self::PASSWORD
        ];

        $this->json('post', route('api.auth.login'), $credentials)
            ->assertOk()
            ->assertJson(['user' => $this->user->toArray()])
            ->assertJsonStructure(['user', 'token']);
    }

    /**
     * @test
     */
    public function should_be_able_to_show_user_logged()
    {
        $this->actingAs($this->user)
            ->json('get', route('api.auth.me'))
            ->assertOk()
            ->assertJsonStructure(['user'])
            ->assertJson(['user' => $this->user->toArray()]);
    }

    /**
     * @test
     */
    public function should_not_be_able_to_show_user_logged()
    {
        $this->json('get', route('api.auth.me'))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function should_be_able_to_logout_an_user()
    {
        $this->actingAs($this->user)
            ->json('post', route('api.auth.logout'))
            ->assertNoContent();

        $this->assertEquals(0, $this->user->tokens()->count());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_logout_an_user()
    {
        $this->json('post', route('api.auth.logout'))
            ->assertUnauthorized();
    }
}