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
                                <h3 class="mb-0">Add Question</h3>
                            </div>
                            <div class="col text-right">
                                <a href="{{ route('listFaqs') }}" class="btn btn-sm btn-primary">Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-{{ Session::get('status') }}" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                {{ Session::get('message') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('storeFaq') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                 <textarea class="form-control form-control-user @error('question') is-invalid @enderror" rows="3" style="resize:none" placeholder="Enter your question..." name="question"  >{{old('question')}}</textarea> 
                                @error('question')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div> 

                            <div class="form-group">
                                <textarea class="form-control ckeditor form-control-user @error('answer') is-invalid @enderror" rows="3" style="resize:none" placeholder="Enter your answer..." name="answer"  >{{old('answer')}}</textarea>

                                @error('answer')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>                            

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary mt-3">Save</button>
                            </div>
                        </form>
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
    // CKEDITOR.replace( 'dish_type_description' ); 
</script>
@endsection
