@extends('index')
@section('main')
<div class="container mt-5">
    <h1 class="mb-3">Post Edit</h1>
    <div class="container">
        <div class="col-lg-6">
            <form action="{{route('posts.update', $post->id)}}" method="post">
                @csrf @method('PUT')
                <div class="form-group mb-3">
                    <label class="mb-2">Post Name</label>
                    <input type="text" class="form-control" name="title" value="{{ $post->title}}" placeholder="Post Name">
                    @error('title')
                    <small style="font-weight: 500; color: red;">{{$message}}</small>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="mb-2">Category Name</label>
                    <select class="form-select" name="category_ids[]" multiple>
                        @foreach($categories as $category)
                        <option value="{{$category->id}}"   @if($post->categories->contains($category->id)) selected @endif >{{$category->title}}</option>
                        @endforeach
                    </select>
                    @error('category_ids')
                    <small style="font-weight: 500; color: red;">{{$message}}</small>
                    @enderror
                </div>


                <div class="form-group mb-3">
                    <label class="mb-2">Post View</label>
                    <input type="text" class="form-control" name="views" value="{{ $post->views}}" placeholder="Post View">
                    @error('views')
                    <small style="font-weight: 500; color: red;">{{$message}}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Post Content</label>
                    <textarea class="form-control" rows="3" name="content" value="" >{{ $post->content}}</textarea>
                </div>

                <button type="submit" class="btn btn-primary mt-5">Update</button>
            </form>
        </div>
    </div>
    @endsection