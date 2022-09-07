@extends('layouts.main')
@section('content')
<div class="">
    <div class="container-fluid mt-3">
        <div class="row" id="main_content">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Recipes</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('addReceipe') }}" class="btn btn-sm btn-primary">Add Recipe</a>
                        </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        @if(session('status'))
                            <div class="alert alert-{{ Session::get('status') }}" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        <!-- Projects table -->
                        <table class="table table-sm table-striped table-hover data-table dataTable no-footer">
                            <thead>
                                <tr>
                                 <th scope="col" class="sort" data-sort="id" style="display:none;">ID</th>
                                    <th scope="col" class="sort" data-sort="name">Name</th>
                                    <th scope="col" class="sort" data-sort="email">Dish Type</th>
                                    <th scope="col" class="sort" data-sort="diet_type">Diet Type</th>
                                    <th scope="col" class="sort" data-sort="cuisine_type">Cuisine Type</th>
                                    <th scope="col" class="sort" data-sort="gender">Cooking Time</th>
                                    <th scope="col" class="sort" data-sort="gender">Author</th>
                                    <th scope="col" class="sort" data-sort="gender">Author Profile</th>
                                    <th scope="col" class="sort" data-sort="gender">Tags</th>
                                   <th scope="col" class="sort" data-sort="receipe_image">Recipe Image</th>
                                     <th scope="col" class="sort" data-sort="by">By</th>
                                     
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>
@endsection

@section('script')
<script>

    function confirmationDelete(anchor) {
        swal({
            title: "Are you sure, you want to delete this Recipe?",
            text: "Once deleted, you will not be able to recover this data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            })
            .then((willDelete) => {
            if (willDelete) {
                window.location = anchor.attr("href");
            }
        });
        //   var conf = confirm("Are you sure want to delete this User?");
        //   if (conf) window.location = anchor.attr("href");
    }
   
  
    
 

$(document).ready(function() {
      
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength:50,
        stateSave : false,
        ajax: "{{ route('listReceipes') }}",
        "bLengthChange": false,
        "language": {
            "paginate": {
            "previous": "<",
            "next": ">"
            }
        },
      
       
        columns: [
            {data: 'id',searchable: false},       
            {data: 'receipe_name'},
            {data: 'dish_type'},
            {data: 'diet_type'},
            {data: 'cuisine_type'},
            {data: 'cooking_time'},
            {data: 'author'},
            {data: 'author_profile'},
            {data: 'tags'},
            {data: 'receipe_image'},
            {data: 'by'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        columnDefs: [
            {visible:false, targets: [ 0] } //This part is ok now
        ],
        order: [[ 0, 'desc' ]],
    });

    // $('#dataTable').DataTable({
    //     "language": {
    //         "paginate": {
    //         "previous": "<",
    //         "next": ">"
    //         }
    //     }
    // });
} );
</script>
@endsection
