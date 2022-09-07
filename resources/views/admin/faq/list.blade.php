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
                            <h3 class="mb-0">FAQs</h3>
                        </div>
                        <div class="col text-right"> 
                            <a href="{{route('addFaq')}}" class="btn btn-sm btn-primary">Add Question</a>
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
                                    <th scope="col" class="sort" data-sort="dish_type_name">Id</th>
                                    <th scope="col" class="sort" data-sort="dish_type_name">Question</th>
                                    <th scope="col" class="sort" data-sort="dish_type_name">Answer</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @php $count=1 @endphp
                                @forelse($questions as $type)
                                <tr>
                                    <th>{{ $count++ }}</th>
                                    <th>{{ $type['question'] }}</th>
                                    <th>{!! $type['answer'] !!}</th>
                                    <th> 
                                        <a href="{{route('editFaq', $type['id'])}}" class="btn btn-info btn-sm"><i class="fas fa-user-edit"></i></a>
                                        <a onclick="javascript:confirmationDelete($(this));return false;" href="{{route('deleteFaq', $type['id'])}}" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>
                                    </th>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center"colspan="5">No Questions found</td>
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
            title: "Are you sure want to delete this Faq?",
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
