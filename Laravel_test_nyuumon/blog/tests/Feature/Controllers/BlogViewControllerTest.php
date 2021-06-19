<?php

namespace Tests\Feature\Controllers;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BlogViewControllerTest extends TestCase
{
    # テスト実行時にDBを初期化し、テスト終了後に初期化する
    use RefreshDatabase;
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
        Blog::factory()->create([
            'status' => Blog::CLOSED,
            'title' => 'ブログA',
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

        $this->get('blogs/'.$blog->id)
            ->assertOk()
            ->assertSee($blog->title)
            ->assertSee($blog->user->name);
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
