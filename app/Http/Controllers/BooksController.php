<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Datatables;
use App\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $htmlBuilder)
    {
        if ($request -> ajax()) {
            $books = Book::with('author');
            return Datatables::of($books) 
            -> addColumn('action', function($book){
                return view('datatable._action', [
                    'model' => $book,
                    'form_url' => route('books.destroy', $book->id),
                    'edit_url' => route('books.edit', $book->id),
                    'confirm_message' => 'Yakin mau menghapus ' . $book->title . '?'
                ]);
            })->make(true);
        }
        
        $html = $htmlBuilder
        -> columns([
            ['data' => 'title', 'name' => 'title', 'title' => 'Judul'],
            ['data' => 'amount', 'name' => 'amount', 'title' => 'Jumlah'],
            ['data' => 'author.name', 'name' => 'author.name', 'title' => 'Penulis'],
            ['data' => 'action', 'name' => 'action', 'title' => '', 'orderable' => false, 'searchable' => false]
        ]);

        return view('books.index')->with(compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookRequest $request)
    {
        // $this->validate($request, [
        //     'title' => 'required|unique:books,title',
        //     'author_id' => 'required|exists:authors,id',
        //     'amount' => 'required|numeric',
        //     'cover' => 'image|max:2048'
        // ]);

        $book = Book::create($request->except('cover'));

        // isi field cover jika ada cover yang diupload
        if($request->hasFile('cover')) {
            // Mengambil file yang di upload
            $uploaded_cover = $request->file('cover');

            // Mengambil extension file
            $extension = $uploaded_cover->getClientOriginalExtension();

            // Membuat nama file random berikut extension
            $filename = md5(time()) . '.' . $extension;

            // Menyimpan cover ke folder public/img
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';
            $uploaded_cover->move($destinationPath, $filename);

            // Mengisi field cover di book dengan filename yang baru dibuat
            $book->cover = $filename;
            $book->save();
        }

        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil menyimpan $book->title"
        ]);

        return redirect()->route('books.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $book = Book::find($id);
        return view('books.edit')->with(compact('book'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, $id)
    {
        // $this->validate($request, [
        //     'title'     => 'required|unique:books,title,' . $id,
        //     'author_id' => 'required|exists:authors,id',
        //     'amount'    => 'required|numeric',
        //     'cover'     => 'image:max:2048'
        // ]);

        $book = Book::find($id);
        $book->update($request->all());

        if($request->hasFile('cover')) {
            // Mengambil cover yang diupload berikut extensinya
            $filename = null;
            $uploaded_cover = $request->file('cover');
            $extension = $uploaded_cover->getClientOriginalExtension();

            // Membuat nama file random dengan extension
            $filename = md5(time()) . '.' . $extension;
            $destinationPath = public_path() . DIRECTORY_SEPARATOR . 'img';

            // Memindahkan file ke folder public/img
            $uploaded_cover->move($destinationPath, $filename);

            // Menghapus cover menggunakan function deleteCover
            $this->deleteCover($id);

            // Ganti field cover dengan cover yang baru
            $book->cover = $filename;
            $book->save();
        }

        Session::flash("flash_notification", [
            "level" => "success",
            "message" => "Berhasil mengupdate $book->title"
        ]);

        return redirect()->route('books.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        $this->deleteCover($id);
        $book->delete();
        
        Session::flash("flash_notification", [
            "level"     => "success",
            "message"   => "Buku $book->title berhasil dihapus"
        ]);

        return redirect()->route('books.index');
    }

    private function deleteCover($id)
    {
        $book = Book::find($id);
        // Hapus cover lama, jika ada
        if($book->cover) {
            $old_cover = $book->cover;
            $filepath = public_path() . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $book->cover;

            try {
                File::delete($filepath);
            } catch (FileNotFoundException $ex) {
                // File sudah dihapus/tidak ada
            }
        }
    }
}
