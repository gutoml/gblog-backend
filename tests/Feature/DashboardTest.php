<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category1;
    protected Category $category2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Cria apenas 2 categorias para os posts do usuário
        $this->category1 = Category::factory()->create();
        $this->category2 = Category::factory()->create();

        // Cria posts apenas para essas 2 categorias
        Post::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category1->id
        ]);

        Post::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category2->id
        ]);

        // Não cria posts de outros usuários para evitar categorias extras
    }

    public function test_it_returns_unauthorized_for_guests(): void
    {
        $response = $this->getJson(route('dashboard'));
        $response->assertUnauthorized();
    }

    public function test_it_returns_correct_stats_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('dashboard'));

        $response->assertOk();

        // Verifica a contagem de posts do usuário (5 + 3 = 8)
        $this->assertEquals(8, $response->json('stats.posts_count'));

        // Verifica o ranking de categorias (apenas 2 categorias)
        $categoriesRanking = $response->json('categories_ranking');
        $this->assertCount(2, $categoriesRanking);

        // Verifica ordenação decrescente
        $this->assertGreaterThanOrEqual(
            $categoriesRanking[1]['posts_count'],
            $categoriesRanking[0]['posts_count']
        );

        // Verifica se as categorias retornadas são as esperadas
        $returnedCategoryIds = array_column($categoriesRanking, 'id');
        $this->assertContains($this->category1->id, $returnedCategoryIds);
        $this->assertContains($this->category2->id, $returnedCategoryIds);
    }

    public function test_it_respects_per_page_parameter(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('dashboard', ['perPage' => 3]));

        $this->assertCount(3, $response->json('latest_posts.data'));
    }
}
