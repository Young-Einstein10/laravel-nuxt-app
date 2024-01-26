<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'isbn' => 'required|string|unique:books',
            'published_date' => 'required|string',
            'cover_image' => 'nullable|image|max:2048',
        ]);

        try {
            $published_date = Carbon::parse($data['published_date'])->toDateString();
            $data['published_date'] = $published_date;
            $data['user_id'] = 1; // Making the assumption the id of the user is 1 
        } catch (\Exception $e) {
            return response()->json(['Error' => 'Bad Request'], 400);
        }

        $book = new Book($data);

        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $coverImageName = time() . '.' . $coverImage->getClientOriginalExtension();
            $coverImage->move(public_path('images'), $coverImageName);
            $book->cover_image = $coverImageName;
        }

        $book->save();

        return response()->json(['message' => 'Book added successfully'], 201);
    }

    public function index()
    {
        $books = Book::all();

        return response()->json(['books' => $books], 200);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $data = $request->all();

        try {
            $published_date = Carbon::parse($data['published_date'])->toDateString();
            $data['published_date'] = $published_date;
            $data['user_id'] = $book->user_id;
        } catch (\Throwable $th) {
            Log::error('Error ', $th);
            return response()->json(['Error' => 'Bad Request'], 400);
        }

        $book->update($data);

        if ($request->hasFile('cover_image')) {
            // Delete the previous cover image if it exists
            if ($book->cover_image) {
                Storage::delete('public/images/' . $book->cover_image);
            }

            $coverImage = $request->file('cover_image');
            $coverImageName = time() . '.' . $coverImage->getClientOriginalExtension();
            $coverImage->storeAs('public/images', $coverImageName);
            $book->cover_image = $coverImageName;
            $book->save();
        }

        return response()->json(['message' => 'Book updated successfully'], 200);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        // Delete the cover image if it exists
        if ($book->cover_image) {
            Storage::delete('public/images/' . $book->cover_image);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted successfully'], 200);
    }
}
