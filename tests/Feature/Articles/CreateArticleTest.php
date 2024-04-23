<?php

namespace Tests\Feature\Articles;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Article;
use PHPUnit\Framework\Attributes\Test;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_create_articles()
    {
        
        
        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo articulo',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo'
        ])->assertCreated();

        $article = Article::first();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => 'Nuevo articulo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'contenido del articulo'
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
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo'
        ])->assertJsonApiValidationErrors('title');
    }

    #[Test]
    public function title_must_be_at_least_4_characters()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'sd',
            'slug' => 'nuevo-articulo',
            'content' => 'contenido del articulo'
        ])->assertJsonApiValidationErrors('title');
    }

    #[Test]
    public function slug_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'nuevo Articulo',
            'content' => 'contenido del articulo'
        ])->assertJsonApiValidationErrors('slug');
    }

    #[Test]
    public function content_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'nuevo Articulo',
            'slug' => 'nuevo-artiuclo'
        ])->assertJsonApiValidationErrors('content');
    }
}
