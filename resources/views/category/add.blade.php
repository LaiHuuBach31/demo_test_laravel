@extends('index')
@section('main')
<div class="container mt-5">
    <h1 class="mb-3">Category Add</h1>
    <div class="container">
        <div class="col-lg-4">
        <form action="{{route('categories.store')}}" method="post">
        @csrf
        <div class="form-group">
            <label class="mb-2">Category Name</label>
            <input type="text" class="form-control" name="title"  placeholder="Category Name">
            @error('title')
            <small style="font-weight: 500; color: red;">{{$message}}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-5">Create</button>
    </form>
        </div>
    </div>
    @endsection