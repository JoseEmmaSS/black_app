<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Article;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;
  
    #[Test]
    public function can_update_articles()
    {
        $article = Article::factory()->create();
        
        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'update articulo',
            'slug' => 'update-articulo',
            'content' => 'update del articulo'
        ])->assertOk();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'update articulo',
                    'slug' => 'update-articulo',
                    'content' => 'update del articulo'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }
    
    #[Test]
    public function title_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'update-articulo',
            'content' => 'Article Content'
        ])->assertJsonApiValidationErrors('title');
    }

    #[Test]
    public function title_must_be_at_least_4_characters()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'sd',
            'slug' => 'update-articulo',
            'content' => 'Article Content'
        ])->assertJsonApiValidationErrors('title');
    }

    #[Test]
    public function slug_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'update articulo',
            'content' => 'Article Content'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function content_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'update articulo',
            'slug' => 'update-articulo'
        ])->assertJsonApiValidationErrors('content');
    }

}