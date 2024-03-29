<?php

namespace Tests\Feature\Controllers\Mypage;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Mypage\UserLoginContoller
 */

class UserLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test index
     *
     * @return void
     */
    public function ログイン画面を開ける()
    {
        $this->get('mypage/login')
            ->assertOk();
    }

    /**
     * @test login
     *
     * @return void
     */
    public function ログイン時の入力チェック()
    {
        $url = 'mypage/login';

        $this->from($url)->post($url, [])
            ->assertRedirect($url);

        $this->post($url, ['email' => ''])->assertSessionHasErrors(['email' => 'メールアドレスは必ず指定してください。']);
        $this->post($url, ['email' => 'aaa@bbb@ccc'])->assertSessionHasErrors(['email' => 'メールアドレスには、有効なメールアドレスを指定してください。']);
        $this->post($url, ['email' => 'aaa@いいい.ううう'])->assertSessionHasErrors(['email' => 'メールアドレスには、有効なメールアドレスを指定してください。']);

        $this->post($url, ['password' => ''])->assertSessionHasErrors(['password' => 'パスワードは必ず指定してください。']);
    }

    /**
     * @test login
     *
     * @return void
     */
    public function ログインできる()
    {
        // $this->withoutExceptionHandling();
        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'aaa@bbb.net',
            'password' => bcrypt('abcd1234'),
        ];

        $user = User::factory()->create($dbData);

        $this->post('mypage/login', $postData)
            ->assertRedirect('mypage/blogs');

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test login
     *
     * @return void
     */
    public function IDを間違えているのでログインできない()
    {
        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'ccc@bbb.net',
            'password' => bcrypt('abcd1234'),
        ];

        $user = User::factory()->create($dbData);

        $url = 'mypage/login';

        $this->from($url)->followingRedirects()->post('mypage/login', $postData)
            ->assertSee('メールアドレスかパスワードが間違っています。')
            ->assertSee('<h1>ログイン画面</h1>', false);

    }

    /**
     * @test login
     *
     * @return void
     */
    public function パスワードを間違えているのでログインできない()
    {
        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'aaa@bbb.net',
            'password' => bcrypt('abcd5678'),
        ];

        $user = User::factory()->create($dbData);

        $url = 'mypage/login';

        $this->from($url)->followingRedirects()->post('mypage/login', $postData)
            ->assertSee('メールアドレスかパスワードが間違っています。')
            ->assertSee('<h1>ログイン画面</h1>', false);

    }

    /**
     * @test login
     *
     * @return void
     */
    public function 認証エラーなのでvalidationExceptionの例外が発生する()
    {
        $this->withoutExceptionHandling();

        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        // $dbData = [
        //     'email' => 'ccc@bbb.net',
        //     'password' => bcrypt('abcd1234'),
        // ];

        // $user = User::factory()->create($dbData);

        // $this->expectException(ValidationException::class);

        try {
            $this->post('mypage/login', $postData);
            $this->fail('ValidationExceptionの例外が発生しませんでした。');
        } catch (ValidationException $e) {
            $this->assertEquals(
                'メールアドレスかパスワードが間違っています。',
                $e->errors()['email'][0] ?? ''
            );
        }
    }

    /**
     * @test login
     *
     * @return void
     */
    public function 認証OKなのでvalidationExceptionの例外が発生しない()
    {
        $this->withoutExceptionHandling();

        $postData = [
            'email' => 'aaa@bbb.net',
            'password' => 'abcd1234',
        ];

        $dbData = [
            'email' => 'aaa@bbb.net',
            'password' => bcrypt('abcd1234'),
        ];

        $user = User::factory()->create($dbData);

        try {
            $this->post('mypage/login', $postData);
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('ValidationExceptionの例外が発生してしまいました。');
        }
    }

    /**
     * @test logout
     *
     * @return void
     */
    public function ログアウトできる()
    {
        $url = 'mypage/login';
        $this->login();

        $this->post('mypage/logout')
            ->assertRedirect($url);

        $this->get($url)
            ->assertSee('ログアウトしました。');

            $this->assertGuest();

    }
}
