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
                            <h3 class="mb-0">Edit Time Filter</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('listTimeFilters') }}" class="btn btn-sm btn-primary">Back</a>
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
                        <form method="POST" action="{{ route('updateTimeFilter', $timeFilter->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-align-left-2"></i></span>
                                    </div>
                                    <input id="time_filter_name" type="text" class="form-control @error('time_filter_name') is-invalid @enderror" name="time_filter_name" value="{{ old('time_filter_name') ?? $timeFilter->time_filter_name }}" required autocomplete="time_filter_name" placeholder="Time Filter Name" autofocus>

                                    @error('time_filter_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>

                                <div class="form-group col-sm-4">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-align-left-2"></i></span>
                                    </div> 
                                    <select class="form-control @error('time_filter_condition') is-invalid @enderror" name="time_filter_condition" id="time_filter_condition">
                                        <option value="">Select Condition</option>                                   
                                        <option value="1" {{ $timeFilter->time_filter_condition ? 'selected' : '' }}>{{ "= Equals To" }}</option>
                                        <option value="2" {{ $timeFilter->time_filter_condition ? 'selected' : '' }}>{{ "> Greater Than" }}</option>
                                        <option value="3" {{ $timeFilter->time_filter_condition ? 'selected' : '' }}>{{ "< Less Than" }}</option>
                                    </select>
                                    @error('time_filter_condition')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>

                                <div class="form-group col-sm-4">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-align-left-2"></i></span>
                                    </div> 

                                        <input id="time_filter_value" type="text" class="form-control @error('time_filter_value') is-invalid @enderror" name="time_filter_value" value="{{ old('time_filter_value') ?? $timeFilter->time_filter_value }}" required autocomplete="time_filter_value" placeholder="Time Filter Value" autofocus>
                                    
                                    @error('time_filter_value')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary mt-3">Update</button>
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
    // CKEDITOR.replace( 'time_filter_description' ); 
</script>
@endsection