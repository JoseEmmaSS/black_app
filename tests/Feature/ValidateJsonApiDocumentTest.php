<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('test_route', fn() => 'OK')->middleware([ValidateJsonApiDocument::class]);
    }

    #[Test]
    public function only_accepts_valid_json_api_document()
    {
         $this -> postJson('test_route',[
             'data' => [
                 'type' => 'string',
                 'attributes' => [
                     'name' => 'test'
                 ]
             ]
         ])->assertSuccessful();
             

         $this -> patchJson('test_route',[

             'data' => [
                 'id' => '1',
                 'type' => 'string',
                 'attributes' => [
                     'name' => 'test'
                 ]
             ]
         ])->assertSuccessful();
    }

    #[Test]
    public function data_is_required()
    {
        $this->postJson('test_route', [])
            ->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [])
            ->assertJsonApiValidationErrors('data');
    }

    #[Test]
    public function data_must_be_an_array()
    {
        $this->postJson('test_route', [
            'data' => 'string'
        ])->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [
            'data' => 'string'
        ])->assertJsonApiValidationErrors('data');
    }

    #[Test]
     public function data_type_is_required()
     {
         $this->postJson('test_route', [
             'data' => [
                'attributes' => []
             ]
         ])->assertJsonApiValidationErrors('data.type');
 
         $this->patchJson('test_route', [
             'data' => [
                'attributes' => []
             ]
         ])->assertJsonApiValidationErrors('data.type');
     }

     #[Test]
     public function data_type_must_be_a_string()
     {
         $this->postJson('test_route', [
             'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test']
             ]
         ])->assertJsonApiValidationErrors('data.type');
 
         $this->patchJson('test_route', [
             'data' => [
                'type' => 1,
                'attributes' => ['name' => 'test']
             ]
         ])->assertJsonApiValidationErrors('data.type');
     }

     #[Test]
     public function data_attribute_is_required()
     {
         $this->postJson('test_route', [
             'data' => [
                'type' => 'string'
             ]
         ])->assertJsonApiValidationErrors('data.attributes');
 
         $this->patchJson('test_route', [
             'data' => [
                'type' => 'string'
             ]
         ])->assertJsonApiValidationErrors('data.attributes');
     }

     #[Test]
     public function data_attribute_must_be_an_array()
     {
         $this->postJson('test_route', [
             'data' => [
                'type' => 'string',
                'attributes' => 'string'
             ]
         ])->assertJsonApiValidationErrors('data.attributes');
 
         $this->patchJson('test_route', [
             'data' => [
                'type' => 'string',
                'attributes' => 'string'
             ]
         ])->assertJsonApiValidationErrors('data.attributes');
     }

     #[Test]
    public function data_id_is_required()
    {
        $this -> patchJson('test_route',[
           'data' => [
             'type' => 'string',
              'attributes' => [
                 'name' => 'test'
              ]
           ]
        ])->assertJsonApiValidationErrors('data.id');
    }

    #[Test]
    public function data_id_must_be_a_string()
    {
        $this -> patchJson('test_route',[
           'data' => [
             'id' => 1,
             'type' => 'string',
              'attributes' => [
                 'name' => 'test'
              ]
           ]
        ])->assertJsonApiValidationErrors('data.id');
    }
}
