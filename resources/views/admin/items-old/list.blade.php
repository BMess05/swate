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
                            <h3 class="mb-0">Items</h3>
                        </div>
                        <div class="col text-right"> 
                            <a href="{{route('addItem')}}" class="btn btn-sm btn-primary">Add Item</a>
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
                        <table class="table table-sm table-striped table-hover dataTable no-footer" id="dataTable">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name">Name</th>
                                    <th scope="col" class="sort" data-sort="email">Category</th>
                                    <th scope="col" class="sort" data-sort="gender">Image</th> 
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($items as $item)
                                <tr>
                                    <th>{{ $item['item_name'] }}</th>
                                    <th>{{ $item['category']['category_name'] ?? "" }}</th> 
                                    <td>
                                        <img src="{{url('uploads/items')}}/{{$item['item_image']}}" width="100" height="90" class="img img-thumbnail" alt="">
                                    </td>
                                    <th> 
                                        <a href="{{route('editItem', $item['id'])}}" class="btn btn-info btn-sm"><i class="fas fa-user-edit"></i></a>
                                        <a onclick="javascript:confirmationDelete($(this));return false;" href="{{route('deleteItem', $item['id'])}}" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a> 
                                    </th>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">No items found</td>
                                </tr>
                                @endforelse
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
            title: "Are you sure want to delete this Item?",
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
    $('#dataTable').DataTable({
        "language": {
            "paginate": {
            "previous": "<",
            "next": ">"
            }
        }
    });
} );
</script>
@endsection