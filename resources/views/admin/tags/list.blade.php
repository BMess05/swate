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
                            <h3 class="mb-0">Tags</h3>
                        </div>
                        <div class="col text-right"> 
                            <a href="{{route('addTag')}}" class="btn btn-sm btn-primary">Add Tags</a>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#ImportModel">Import Tags</button>
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
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($tags as $tag)
                                <tr>
                                    <th>{{ $tag['name'] }}</th>
                                    <th>
                                        <a href="{{route('editTag', $tag['id'])}}" class="btn btn-info btn-sm"><i class="fas fa-user-edit"></i></a>
                                        <a onclick="javascript:confirmationDelete($(this));return false;" href="{{route('deleteTag', $tag['id'])}}" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>
                                    </th>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">No tags found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ImportModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('importTags') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                    <div class="modal-header">
                        <h2>Upload File </h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <input type="file" class="form-control" name="select_file" accept=".csv,.xlsx,.xls" />
                        </div>
                       <a href="{{URL::asset('/assets/samples/Tags_sample.xlsx')}}" download >Download Sample <i class="fa fa-download" aria-hidden="true"></i></a>
                    </div>
                    <div class="modal-footer">
                      <button id="button" class="btn btn-sm btn-primary" name="button">Import Tags</button> 
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
    function confirmationDelete(anchor) {
        swal({
            title: "Are you sure want to delete this Tag?",
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
        pageLength: 50,
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