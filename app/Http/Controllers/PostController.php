<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return view('post.index', compact('categories'));
    }

    public function getAllPostData(Request $request)
    {
        $query = Post::with('categories');

        if ($request->keyword) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        if (!empty($request->category)) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->whereIn('categories.id', $request->category);
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('title', function ($query) {
                return '<a class="text-dark" href="' . route('posts.edit', $query->id) . '">' . $query->title . '</a>';
            })
            ->addColumn('category', function ($query) {
                if($query->categories->count() > 0){
                    $html = '';
                    foreach ($query->categories as $category) {
                        $html .= '-' . $category->title . '<br>';
                    }
                    return $html;
                }
                return 'No Category';
            })
            ->addColumn('views', function ($query) {
                return $query->views ?? 0;
            })
            ->addColumn('created_at', function ($query) {
                return $query->created_at;
            })
            ->addColumn('status', function ($query) {
                return '
                <form action="' . route('posts.destroy', $query->id) . '" method="POST" style="display:inline-block;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                </form>
            ';
            })
            ->rawColumns(['title', 'category', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('post.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:posts,title',
            'content' => 'required',
            'views' => 'nullable|integer',
            'category_ids' => 'required|array',
        ], [
            'title.required' => 'Post title is required',
            'title.unique' => 'Post title must be unique',
            'content.required' => 'Post content is required',
            'category_ids.required' => 'Please select at least one category',
        ]);

        DB::beginTransaction();
        try {
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'views' => $request->views ?? 0,
            ]);
            $post->categories()->attach($request->category_ids);

            foreach ($request->category_ids as $category_id) {
                $category = Category::find($category_id);
                $totalViews = $category->posts()->sum('views');
                $category->update(['views' => $totalViews]);
            }


            DB::commit();
            toastr()->success('Post created successfully!');
            return redirect()->route('posts.index');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            toastr()->error('Something went wrong, please try again!');
            return back()->withInput();
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
        $categories = Category::all();
        $post = Post::find($id);
        return view('post.edit', compact('categories', 'post'));
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
        $request->validate([
            'title' => 'required|unique:posts,title,' . $id,
            'content' => 'required',
            'views' => 'nullable|integer',
            'category_ids' => 'required|array',
        ], [
            'title.required' => 'Post title is required',
            'title.unique' => 'Post title must be unique',
            'content.required' => 'Post content is required',
            'category_ids.required' => 'Please select at least one category',
        ]);

        DB::beginTransaction();
        try {
            $post = Post::findOrFail($id);

            $post->update([
                'title' => $request->title,
                'content' => $request->content,
                'views' => $request->views ?? 0,
            ]);

            $post->categories()->sync($request->category_ids);

            foreach ($request->category_ids as $category_id) {
                $category = Category::find($category_id);
                $totalViews = $category->posts()->sum('views');
                $category->update(['views' => $totalViews]);
            }

            DB::commit();
            toastr()->success('Post updated successfully!');
            return redirect()->route('posts.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Something went wrong, please try again!');
            return back()->withInput();
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

            $post = Post::find($id);

            if (!$post) {
                toastr()->error('Post not found!');
                return redirect()->route('posts.index');
            }

            $categoryIds = $post->posts->pluck('category_id')->toArray();

            foreach ($categoryIds as $category_id) {
                $category = Category::find($category_id);
                $category->update(['views' => $category->views - $post->views]);
            }
           
            $post->posts()->delete();

            $post->delete();

            DB::commit();

            toastr()->success('Delete post successful!');
            return redirect()->route('posts.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('An error occurred. Please try again later!');
            return redirect()->route('posts.index');
        }
    }
}
