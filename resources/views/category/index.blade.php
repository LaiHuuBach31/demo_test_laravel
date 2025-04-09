@extends('index')
@section('main')

<div class="container mt-5">
    <h1>Category List</h1>
    <a href="{{route('categories.create')}}" class="btn btn-success mb-3">Add</a>

    <table class="table text-center">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Total Post</th>
                <th>View</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{$category->id}}</td>
                    <td>
                        <a href="{{ route('categories.edit', $category->id) }}" class="text-dark">
                            {{$category->title}}
                        </a>
                    </td>
                    <td>{{$category->categories->count()}}</td>
                    <td>{{$category->views}}</td>
                    <td>{{$category->created_at}}</td>
                    <td>
                        <form action="{{route('categories.destroy', $category->id)}}" method="POST" onsubmit="return confirm('Delete?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{$categories->appends(request()->all())->links()}}
</div>

@endsection
