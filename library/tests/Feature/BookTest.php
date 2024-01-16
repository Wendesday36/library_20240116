<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testSimpleGet(): void
    {
        $response = $this->get('/api/books');
        $response->assertStatus(200);
    }

    public function testSimplePost(): void
    {
        $response = $this->post('/api/books', ['author' => 'Marquez', 'title' => 'Száz év magány']);
        $response->assertStatus(200);
    }

    public function testBookId() : void 
    {
        //a make nem rögzíti az adatbázisban a felh-t
        $book = Book::factory()->make();
        $this->get('/api/books/' . $book->book_id)
        ->assertStatus(200);
    }

    public function testUserIdAuth() : void 
    {
        $this->withoutExceptionHandling(); 
        // create rögzíti az adatbázisban a felh-t
        $user = User::factory()->create();
        //belépteti, bármilyen jogosultsággal is rendelkezik
        $response = $this->actingAs($user)->get('/api/users/' . $user->id);
        $response->assertStatus(200);
    }    

}
