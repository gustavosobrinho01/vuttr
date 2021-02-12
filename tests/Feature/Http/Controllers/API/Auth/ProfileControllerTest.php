<?php

namespace Tests\Feature\Http\Controllers\API\Auth;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    const EMAIL = 'gustavo.sobrinho01@gmail.com';
    const PASSWORD = '123123123';
    const NEW_PASSWORD = '321321321';

    /**
     * @var string
     */
    protected $userTable;
    /**
     * @var User
     */
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userTable = (new User())->getTable();
        $this->user = User::factory()->create([
            'email' => self::EMAIL,
            'password' => self::PASSWORD
        ]);
    }

    /**
     * @test
     */
    public function should_be_able_to_register_an_user()
    {
        $user = collect([
            'name' => 'User test',
            'email' => 'user@test',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD
        ]);

        $this->json('post', route('api.profile.register'), $user->toArray())
            ->assertCreated();

        $this->assertDatabaseHas($this->userTable, $user->except(['password', 'password_confirmation'])->toArray());
    }

    /**
     * @test
     */
    public function should_be_able_to_update_an_user()
    {
        $attributes = $this->user->toArray();
        $attributes['email'] = 'teste@validation.update';
        $attributes['password'] = self::NEW_PASSWORD;

        $this->actingAs($this->user)
            ->json('put', route('api.profile.update'), $attributes)
            ->assertOk();

        $this->assertDatabaseHas($this->userTable, ['email' => $attributes['email']]);
        $this->should_encrypt_password_when_new_user_created();
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_an_user()
    {
        $this->json('put', route('api.profile.update'), [])
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function should_be_able_to_update_password_an_user()
    {
        $attributes = $this->user->toArray();
        $attributes['email'] = 'teste@validation.update';
        $attributes['current_password'] = self::PASSWORD;
        $attributes['password'] = self::NEW_PASSWORD;
        $attributes['password_confirmation'] = self::NEW_PASSWORD;

        $this->actingAs($this->user)
            ->json('put', route('api.profile.updatePassword'), $attributes)
            ->assertOk();

        $this->assertDatabaseMissing($this->userTable, ['email' => $attributes['email']]);
        $this->should_encrypt_password_when_new_user_created(self::NEW_PASSWORD);
    }

    /**
     * @test
     */
    public function should_not_be_able_to_update_password_an_user()
    {
        $this->json('put', route('api.profile.updatePassword'), [])
            ->assertUnauthorized();
    }

    /**
     * @test
     * @param string|null $password
     */
    public function should_encrypt_password_when_new_user_created(string $password = null)
    {
        $this->assertTrue(Hash::check($password ?? self::PASSWORD, $this->user->password));
    }

    /**
     * @test
     */
    public function should_be_able_to_destroy_an_user()
    {
        Tool::factory()->for($this->user)->count(5);

        $this->actingAs($this->user)
            ->json('delete', route('api.profile.destroy'))
            ->assertNoContent();

        $this->assertDeleted($this->user)
            ->assertEquals(0, $this->user->tokens()->count());
    }

    /**
     * @test
     */
    public function should_not_be_able_to_destroy_an_user()
    {
        $this->json('delete', route('api.profile.destroy'))
            ->assertUnauthorized();
    }
}
