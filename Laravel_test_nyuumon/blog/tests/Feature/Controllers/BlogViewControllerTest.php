<?php

namespace Tests\Feature\Controllers;

use App\Http\Middleware\BlogShowLimit;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use App\StrRandom;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Facades\Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

use function PHPUnit\Framework\throwException;

/**
 * @see App\Http\Controllers\BlogViewController;
 */

class BlogViewControllerTest extends TestCase
{
    # テスト実行時にDBを初期化し、テスト終了後に初期化する
    use RefreshDatabase;
    // use WithoutMiddleware;
    /**
     * @test index
     *
     * @return void
     */
    public function ブログのTOPページを開ける()
    {
        $blog1 = Blog::factory()->hasComments(1)->create();
        $blog2 = Blog::factory()->hasComments(3)->create();
        $blog3 = Blog::factory()->hasComments(2)->create();

        $this->withoutExceptionHandling();

        $this->get('/')
            ->assertViewIs('index')
            ->assertOk()
            ->assertSee($blog1->title)
            ->assertSee($blog2->title)
            ->assertSee($blog3->title)
            ->assertSee($blog1->user->name)
            ->assertSee($blog2->user->name)
            ->assertSee($blog3->user->name)
            ->assertSee("（1件のコメント）")
            ->assertSee("（2件のコメント）")
            ->assertSee("（3件のコメント）")
            ->assertSeeInOrder([$blog2->title, $blog3->title, $blog1->title]);

        // $blog1 = Blog::factory()->create(['title' => 'あいうえお']);
        // $blog2 = Blog::factory()->create(['title' => 'かきくけこ']);
        // $blog3 = Blog::factory()->create(['title' => 'さしすせそ']);

        // $this->get('/')
        //     ->assertOk()
        //     ->assertSee('あいうえお')
        //     ->assertSee('かきくけこ')
        //     ->assertSee('さしすせそ');
    }
    /**
     * @test index
     *
     * @return void
     */
    public function ブログの一覧で、非公開のブログは表示されない()
    {
        Blog::factory()->closed()->create([
            'title' => 'ブログA'
        ]);
        Blog::factory()->create(['title' => 'ブログB']);
        Blog::factory()->create(['title' => 'ブログC']);

        $this->withoutExceptionHandling();

        $this->get('/')
            ->assertViewIs('index')
            ->assertOk()
            ->assertDontSee('ブログA')
            ->assertSee('ブログB')
            ->assertSee('ブログC');
    }

    /**
     * @test index
     *
     * @return void
     */
    public function ブログの詳細画面が表示できる()
    {
        $blog = Blog::factory()->create();

        $this->get('blogs/' . $blog->id)
            ->assertOk()
            ->assertSee($blog->title)
            ->assertSee($blog->user->name);
    }

    /**
     * @test show
     *
     * @return void
     */
    public function ブログで非公開のものは、詳細画面は表示できない()
    {
        $this->withoutMiddleware(BlogShowLimit::class);

        $blog = Blog::factory()->closed()->create();

        $this->get('blogs/' . $blog->id)
            ->assertForbidden();
    }

    /**
     * @test show
     *
     * @return void
     */
    public function クリスマスの日は、メリークリスマスと表示される()
    {
        $this->withoutMiddleware(BlogShowLimit::class);

        $blog = Blog::factory()->create();

        Carbon::setTestNow('2021-12-24');

        $this->get('blogs/' . $blog->id)
            ->assertOk()
            ->assertDontSee('メリークリスマス！');

        Carbon::setTestNow('2021-12-25');

        $this->get('blogs/' . $blog->id)
            ->assertOk()
            ->assertSee('メリークリスマス！');
    }

    /**
     * @test show
     *
     * @return void
     */
    public function ブログの詳細画面が表示でき、コメントが古い順に表示される()
    {
        $this->withoutMiddleware(BlogShowLimit::class);
        // $blog = Blog::factory()->create();

        // Comment::factory()->create([
        //     'created_at' => now()->sub('2 days'),
        //     'name' => '太郎',
        //     'blog_id' => $blog->id,
        // ]);
        // Comment::factory()->create([
        //     'created_at' => now()->sub('3 days'),
        //     'name' => '次郎',
        //     'blog_id' => $blog->id,
        // ]);
        // Comment::factory()->create([
        //     'created_at' => now()->sub('1 days'),
        //     'name' => '三郎',
        //     'blog_id' => $blog->id,
        // ]);

        $blog = Blog::factory()->withCommentData([
            ['created_at' => now()->sub('2 days'),'name' => '太郎'],
            ['created_at' => now()->sub('3 days'),'name' => '次郎'],
            ['created_at' => now()->sub('1 days'),'name' => '三郎'],
        ])->create();

        $this->get('blogs/' . $blog->id)
            ->assertOk()
            ->assertSee($blog->title)
            ->assertSee($blog->user->name)
            ->assertSeeInOrder(['次郎', '太郎', '三郎']);

        // dd($blog->comments->toArray());
    }

    /**
     * @test show
     *
     * @return void
     */
    public function ブログの詳細画面で、ランダムな文字列が10文字表示される()
    {
        $this->withoutExceptionHandling();
        // $this->withoutMiddleware(BlogShowLimit::class);

        $blog = Blog::factory()->create();

        // Facadeの場合のモック
        // Str::shouldReceive('random')
        // ->once()
        // ->with(10)
        // ->andReturn('HELLO_RAND');

        // Facade以外のモック
        // $mock = new Class ()
        // {
        //     public function random(int $len)
        //     {
        //         if ($len != 10){
        //             throw new Exception('引数が違う');
        //         }
        //         return 'HELLO_RAND';
        //     }
        // };


        // Mockeryの場合
        // $mock = Mockery::mock(StrRandom::class);

        // $mock->shouldReceive('random')->once()->with(10)->andReturn('HELLO_RAND');

        // $this->app->instance(StrRandom::class, $mock);

        $this->mock(StrRandom::class, function (MockInterface $mock)
        {
            $mock->shouldReceive('random')->once()->with(10)->andReturn('HELLO_RAND');
        });

        $this->get('blogs/'.$blog->id)
            ->assertOk()
            ->assertSee('HELLO_RAND');

    }

    /**
     * @test factoryの観察
     *
     * @return void
     */
    public function factoryの観察()
    {
        // $blog = Blog::factory()->create();
        // $blog = Blog::factory()->create(['user_id' => 5]);
        $blog = Blog::factory()->make(['user_id' => null]);

        dump($blog->toArray());
        dump(Blog::count());

        dump(User::get()->toArray());
        dump(User::count());

        $this->assertTrue(true);
    }
}
