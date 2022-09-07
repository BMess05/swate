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
                                <h3 class="mb-0">Edit Ingredient</h3>
                            </div>
                            <div class="col text-right">
                                <a href="{{ route('listItems') }}" class="btn btn-sm btn-primary">Back</a>
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
                         <form method="POST" action="{{ route('updateItem', $item->id) }}" enctype="multipart/form-data">
                            @csrf
                        <div class="row">
                            <div class="form-group col-sm-5">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                                </div>
                                <input id="item_name" type="text" class="form-control @error('item_name') is-invalid @enderror" name="item_name" value="{{ old('item_name') ?? $item->item_name }}" required autocomplete="item_name" placeholder="Ingredient Name" autofocus>

                                @error('item_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>

                            <div class="form-group col-sm-5">
                                   <div class="input-group input-group-merge input-group-alternative mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                                        </div>
                                        @php
                                        $category_id = old('category_id') ?? $item->category_id;
                                        @endphp
                                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $cat)
                                            <option value="{{ $cat['id'] }}" {{ ($category_id == $cat['id']) ? 'selected' : ''}}>{{ $cat['category_name'] }}</option>
                                            @endforeach 
                                        </select> 
                                        @error('category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-sm btn-primary float-right" id="add_more_ingredient_btn">Add More</button>
                            </div>
                             <input type="hidden" name="no_of_ingredients" value="{{count($item['item_expiry_days'])}}" id="no_of_ingredients"> 
                        </div> 
                       
                        <div id="ingredient_section">
                         @foreach($item['item_expiry_days'] as $rec_index => $expire)
                            <div class="row ingredient_row" id="{{($rec_index == 0) ? '' : 'igre_wrap'}}{{$rec_index+1}}">
                                <div class="form-group col-sm-5">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-align-left-2"></i></span>
                                        </div>
                                        @php
                                            $item_storage_type = old('item_storage_type[]') ?? $expire['storage_type'];
                                        @endphp
                                        <select name="item_storage_type[]" id="item_storage_type" class="form-control @error('item_storage_type') is-invalid @enderror" required data-id={{$rec_index}}>
                                            <option value="">Select Storage Type</option>
                                            @foreach($storage_types as $storage_type)
                                            <option value="{{ $storage_type['id'] }}" {{ ($item_storage_type == $storage_type['id']) ? 'selected' : ''}}>{{ $storage_type['storage_name'] }}</option>
                                            @endforeach 
                                        </select>
                                        @error('item_storage_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group col-sm-5">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                                </div>
                                <input id="expire_days" type="text" class="form-control @error('expire_days') is-invalid @enderror" name="expire_days[]" value="{{ old('expire_days[]')??$expire['expire_days'] }}" required autocomplete="expire_days" placeholder="Expiry Days" autofocus>

                                @error('expire_days')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                                <div class="col-md-2 pt-2"></div>
                            </div>
                            @if($rec_index > 0)
                                <div class="col-md-1 pt-2">
                                    <a class="remove_ingredient" data-id="{{ $rec_index + 1 }}"><i class="fa fa-times"></i></a>
                                </div>
                             @else
                                <div class="col-md-1"></div>
                             @endif

                            </div>
                             @endforeach
                        </div>
                           

                            {{-- <div class="form-group">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-camera-compact"></i></span>
                                </div>
                                <input id="item_image" type="file" class="form-control @error('item_image') is-invalid @enderror" name="item_image" value="{{ old('item_image') }}" required autocomplete="item_image" placeholder="Item Name" autofocus>

                                @error('item_image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div> --}}

                            
                             <label for="ingredient">Ingredient image</label>
                            <div class="form-group avatar-upload blog_image">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="blog_image">
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' name="item_image" id="item_image" accept=".png, .jpg, .jpeg, .gif"/>
                                                <input type="hidden"  value={{  $item["item_image"]   }}  name=""/>
                                                <label for="item_image"><i class="fas fa-edit"></i></label>
                                            </div>
                                            <div class="avatar-preview">
                                                @php
                                                $item_image = $item["item_image"] ?   $item["item_image"] : URL::asset('assets/img/thumbnail-default_2.jpg');
                                                    
                                                @endphp

                                                <div id="item_image_preview" style="background-image: url({{ $item_image }});">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @error('item_image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>

                            <div class="form-group"> 
                                <label for="item_description">Description</label>
                                <textarea name="item_description" id="item_description" class="form-control @error('item_description') is-invalid @enderror" placeholder="Item Name" required>{{ old('item_description')??$item['item_description'] }}</textarea>
                                @error('item_description')
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
    // CKEDITOR.replace( 'item_description' ); 
    var _URL = window.URL;
    function readURLTwo(input) {
        var file, img;
        if ((file = input.files[0])) {
            img = new Image();
            img.onload = function () {
                
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#item_image_preview').css('background-image', 'url('+e.target.result +')');
                        $('#item_image_preview').hide();
                        $('#item_image_preview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                    return true;
                
            };
            img.src = _URL.createObjectURL(file);
        }
    }
    $("#item_image").change(function() {
        readURLTwo(this);
    });

     $('#add_more_ingredient_btn').on('click', function() {
        var index = parseInt($('#no_of_ingredients').val()) + 1;
        var ihtml = `<div class="row ingredient_row" id="igre_wrap${index}">
                                <div class="form-group col-sm-5">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="ni ni-align-left-2"></i></span>
                                        </div>
                                        @php
                                            $item_storage_type = old('item_storage_type[]') ?? '';
                                        @endphp
                                        <select name="item_storage_type[]" id="item_storage_type" class="form-control @error('item_storage_type') is-invalid @enderror" required>
                                            <option value="" selected>Select Storage Type</option>
                                            @foreach($storage_types as $storage_type)
                                            <option value="{{ $storage_type['id'] }}" {{ ($item_storage_type == $storage_type['id']) ? 'selected' : ''}}>{{ $storage_type['storage_name'] }}</option>
                                            @endforeach 
                                        </select>
                                        @error('item_storage_type')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group col-sm-5">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                                </div>
                                <input id="expire_days" type="text" class="form-control @error('item_name') is-invalid @enderror" name="expire_days[]" value="{{ old('expire_days[]') }}" required autocomplete="expire_days" placeholder="Expiry Days" autofocus>

                                @error('expire_days')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>           
         <div class="col-md-2 pt-2">
                <a class="remove_ingredient" data-id="${index}"><i class="fa fa-times"></i></a>
            </div>
        </div>`;

        $(ihtml).appendTo('#ingredient_section');
        $('#no_of_ingredients').val(index);
    });

    $(document).on('click', '.remove_ingredient', function() {
        var id = $(this).data('id');
        $(`#igre_wrap${id}`).remove();
        var index = parseInt($('#no_of_ingredients').val()) - 1;
        $('#no_of_ingredients').val(index);
    });
</script>
@endsection