<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use App\Http\Middleware\ValidateJsonApiHeaders;
use PHPUnit\Framework\Attributes\Test;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::any('test_route', fn() => 'OK')->middleware(ValidateJsonApiHeaders::class);
    }

    #[Test]
    public function accept_header_must_be_present_in_all_requests()
    {
        $this->get('test_route')->assertStatus(406);

        $this->get('test_route', [
            'accept' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }   

    #[Test]
    public function content_type_header_must_be_present_on_all_posts_requests()
    {
        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->post('test_route', [], [
           'accept' => 'application/vnd.api+json',
           'content-type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    #[Test]
    public function content_type_header_must_be_present_on_all_patch_requests()
    {
        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json'
        ])->assertStatus(415);

        $this->patch('test_route', [], [
           'accept' => 'application/vnd.api+json',
           'content-type' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }
    
    #[Test]
    public function content_type_header_must_be_present_in_responses()
    {

        $this->get('test_route', [
            'accept' => 'application/vnd.api+json'
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');
    }

    #[Test]
    function content_type_header_must_be_present_in_empty_responses()
    {
        Route::any('empty_response', function() {
            return response()->noContent();
        })->middleware(ValidateJsonApiHeaders::class);

        $this->get('empty_response', [
            'accept' => 'application/vnd.api+json',
        ])->assertHeaderMissing('content-type');

        $this->post('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content_type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('content-type');

        $this->patch('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content_type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('content-type');

        $this->delete('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content_type' => 'application/vnd.api+json',
        ])->assertHeaderMissing('content-type');
    }
}
