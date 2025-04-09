<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderby('id', 'DESC')->paginate(10);
        return view('category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('category.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|unique:categories,title',
            ], [
                'title.required' => 'Title is not empty',
                'title.unique' => 'Title is unique',
            ]);

            Category::create([
                'title' => $request->title,
            ]);

            toastr()->success('Data has been saved successfully!');
            return redirect()->route('categories.index');
        } catch (\Throwable $th) {
            toastr()->error('An error occurred. Please try again later!');
            return back();
        }
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
        $category = Category::find($id);
        return view('category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|unique:categories,title,' . $id,
            ], [
                'title.required' => 'Title is not empty',
                'title.unique' => 'Title is unique',
            ]);

            $category = Category::find($id);

            if (!$category) {
                toastr()->error('Category not found!');
                return redirect()->route('categories.index');
            }

            $category->update([
                'title' => $request->title,
            ]);

            toastr()->success('Data has been updated successfully!');
            return redirect()->route('categories.index');
        } catch (\Throwable $th) {
            toastr()->error('An error occurred. Please try again later!');
            return back();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $category = Category::find($id);

            if (!$category) {
                toastr()->error('Category not found!');
                return redirect()->route('categories.index');
            }

            $category->categories()->delete();

            $category->delete();

            DB::commit();

            toastr()->success('Delete successful!');
            return redirect()->route('categories.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('An error occurred. Please try again later!');
            return redirect()->route('categories.index');
        }
    }
}
