@extends('index')
@section('main')
<div class="container mt-5">
    <h1 class="text-center p-5">Post List</h1>
    <a href="{{route('posts.create')}}" class="btn btn-success">Add</a>

    <div class="row mb-3">
        <div class="col-md-12 mb-5">
            <input type="text" id="keyword" class="form-control" placeholder="Nhập từ khóa">
        </div>
        <div class="col-md-12  mb-5">
            @foreach($categories as $category)
            <label class="me-2">
                <input type="checkbox" class="category-filter" id="category_{{ $category->id }}" value="{{ $category->id }}"> {{ $category->title }}
            </label>
            @endforeach
        </div>
    </div>
    <table class="table text-center" id="table-data">
        <thead>
            <tr>
                <th scope="col" class="text-center">ID</th>
                <th scope="col" class="text-center">Title</th>
                <th scope="col" class="text-center">Category</th>
                <th scope="col" class="text-center">Views</th>
                <th scope="col" class="text-center">Created At</th>
                <th scope="col" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        console.log('getAllPostData');
        let table = new DataTable('#table-data', {
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            searching: false,
            destroy: true,
            stateSave: true,
            processing: true,
            serverSide: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: '{{ route("getAllPostData") }}',
                type: 'GET',
                data: function(d) {                    
                    d.keyword = $('#keyword').val();
                    d.category = [];
                    $('.category-filter:checked').each(function() {
                        d.category.push($(this).val());
                    });
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'title',
                    name: 'title',
                },
                {
                    data: 'category',
                    name: 'category',
                },
                {
                    data: 'views',
                    name: 'views'
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                },
                {
                    data: 'status',
                    name: 'status'
                },
            ],
            language: {
                emptyTable: "Not found data!",
                searchPlaceholder: "Search...",
                paginate: {
                    next: "Next",
                    previous: "Previous"
                },
                processing: "Loading...",

                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                lengthMenu: "Show _MENU_ entries",
            },
            responsive: true
        });

        $('#keyword, .category-filter').on('change keyup', function() {
            table.ajax.reload();
        });
    });
</script>

@endpush