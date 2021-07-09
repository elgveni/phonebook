<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Phones;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Show form add user
     *
     * @return \Illuminate\View\View
     */
    public function add_user_form()
    {
        return view('add-user-form');
    }

    /**
     * Add new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addUser(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:books|max:255'
        ]);

        $user = new Book();
        $user->name = $request->name;
        $user->save();

        return ['success' => true];
    }

    /**
     * Add new user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addUserPhone(Request $request)
    {
        $results = json_decode(htmlspecialchars_decode($request->getContent()));
        $id = $results->id;

        Phones::where('book_id', '=', $id)->delete();

        foreach ($results->items as $item) {
            $model = new Phones();
            $model->phone = $item->phone;
            $model->book_id = $id;
            $model->save();
        }

        return ['success' => true];
    }

    /**
     * Show all phones from user
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserPhones(Request $request)
    {
        $data = Phones::where('book_id', '=', $request->id)->get();

        return response()->json($data);
    }

    /**
     * Show all phone books
     *
     * @return \Illuminate\Http\Response
     */
    public function getBooks(Request $request)
    {
        $data = Book::where('name', 'LIKE', '%'.$request->keyword.'%')->get();

        return response()->json($data);
    }

    /**
     * Remove user
     *
     * @return \Illuminate\Http\Response
     */
    public function removeUser(Request $request)
    {
        $id = $request->id;

        Book::where('id', '=', $id)->delete();

        return back()->with('success', 'Remove successfully!');
    }
}
