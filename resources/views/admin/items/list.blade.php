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
                            <h3 class="mb-0">Ingredients</h3>
                        </div>
                        <div class="col text-right"> 
                            <a href="{{route('addItem')}}" class="btn btn-sm btn-primary">Add Ingredient</a>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#ImportModel">Import Ingredient</button>
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
                        <table class="table table-sm table-striped table-hover dataTable no-footer" id="item_dataTable">
                            <thead>
                                <tr>
                                <th scope="col" class="sort" data-sort="id" style="display:none;">id</th>
                                    <th scope="col" class="sort" data-sort="name">Sr.no</th>
                                    <th scope="col" class="sort" data-sort="name">Name</th>
                                    <th scope="col" class="sort" data-sort="email">Category</th>
                                    <th scope="col" class="sort" data-sort="gender">Image</th> 
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

        <!-----model ---->
        <div class="modal fade" id="ImportModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('importIngredients') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                    <div class="modal-header">
                        <h2>Upload File</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    
                    <div class="modal-body">
                       <input type="file"  name="select_file" accept=".csv" />
                          
                    </div>
                    <div class="modal-footer">
                      <button id="button" class="btn btn-sm btn-primary" name="button">Import Ingredient</button> 
                      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>
@endsection

@section('script')
<script>


    // function thisFileUpload() {
    //     document.getElementById("file").click();
    // };
    function confirmationDelete(anchor) {
        swal({
            title: "Are you sure want to delete this Ingredient?",
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
    var table = $('#item_dataTable').DataTable({
        processing: true,
        // order: [[ 4, 'desc' ]],
        serverSide: true,
        pageLength: 50,
        "bLengthChange": false,
        "language": {
            "paginate": {
            "previous": "<",
            "next": ">"
            }
        },
        ajax: "{{ route('listItems') }}",
        columns: [
            {data: 'id', searchable: false},  
            { "data": "sno", searchable: false,
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }},

            {data: 'item_name'},
            {data: 'category_name'},
            {data: 'item_image'},
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