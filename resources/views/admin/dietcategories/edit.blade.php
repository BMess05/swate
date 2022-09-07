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
                            <h3 class="mb-0">Edit Diet category</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{ route('listDietCategories') }}" class="btn btn-sm btn-primary">Back</a>
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
                        <form method="POST" action="{{ route('updateDietCategories', $dietCategory->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                                </div>
                                <input id="diet_category_name" type="text" class="form-control @error('diet_category_name') is-invalid @enderror" name="diet_category_name" value="{{ old('diet_category_name') ?? $dietCategory->diet_category_name }}" required autocomplete="diet_category_name" placeholder="Diet Category Name" autofocus>

                                @error('diet_category_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>

                            <div class="form-group"> 
                                <label for="diet_category_description">Description</label>
                                <textarea name="diet_category_description" id="diet_category_description" class="form-control @error('diet_category_description') is-invalid @enderror" placeholder="Diet Category Ddescription" required>{{ old('diet_category_description') ?? $dietCategory->diet_category_description }}</textarea>
                                @error('diet_category_description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror 
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
    // CKEDITOR.replace( 'diet_category_description' ); 
</script>
@endsection