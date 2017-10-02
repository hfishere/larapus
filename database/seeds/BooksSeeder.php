<?php

use Illuminate\Database\Seeder;
use App\Author;
use App\Book;

class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample Penulis
        $author1 = Author::create(['name' => 'Felix Siauw']);
        $author2 = Author::create(['name' => 'Salim A Fillah']);

        // Sample Buku
        $book1 = Book::create(['title' => 'Pacaran Setelah Menikah', 'amount' => 3,
            'author_id' => $author2->id]);
        $book2 = Book::create(['title' => 'Habits', 'amount' => 4,
            'author_id' => $author1->id]);
        $book3 = Book::create(['title' => 'Beyond the Inspiration', 'amount' => 4,
            'author_id' => $author1->id]);
    }
}
