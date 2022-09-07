@extends('layouts.main')
@section('content')
<style>
    .selectize-input.items.not-full.has-options.has-items .item {
        background: linear-gradient( 
    346.79deg
    , #1C9391 0%, rgba(139,227,140, 5) 100%), #8BE38C;
        border-color: #8BE38C !important;
        color: #fff;
        padding: 5px;
    }
    .selectize-input.items.not-full.has-options{
        min-height: 46px;
        border: 1px solid #dee2e6;
    }
    .selectize-control.multi .selectize-input.has-items{
        height: 46px;
        border: 1px solid #dee2e6;
    }
    li.select2-selection__choice {
        padding: 0 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color: #fff !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected], .select2-container--default .select2-results__option[aria-selected='true']:hover{
        background-color: #ccc;
    }
    .select2-container--default .select2-results__option--selected{
        background-color: #ccc;
    }
</style>
<script src="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.js"></script>
<link href="https://cdn.jsdelivr.net/timepicker.js/latest/timepicker.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<div class="">
    <div class="container-fluid mt-3">
        <div class="row" id="main_content">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Add Recipe</h3>
                        </div>
                        <div class="col text-right">
                            <a href="{{route('listReceipes')}}" class="btn btn-sm btn-primary">Back</a>
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
                        <form method="POST" action="{{ route('saveReceipe') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-bullet-list-67"></i></span>
                                </div>
                                <input id="receipe_name" type="text" class="form-control @error('receipe_name') is-invalid @enderror" name="receipe_name" value="{{ old('receipe_name') }}" required autocomplete="receipe_name" placeholder="Recipe Name" autofocus>

                                @error('receipe_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-3">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-watch-time"></i></span>
                                    </div>
                                   <input id="time" type="text" class="form-control @error('cooking_time') is-invalid @enderror " name="cooking_time" value="{{ old('cooking_time') }}" required autocomplete="off" placeholder="Cooking Time" autofocus readonly>
                                     {{-- <input id="cooking_time" type="time" class="form-control @error('cooking_time') is-invalid @enderror" name="cooking_time" value="{{ old('cooking_time') }}" required autocomplete="cooking_time" placeholder="Cooking Time" autofocus> --}}
                                    @error('cooking_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>

                                {{-- <div class="form-group">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-satisfied"></i></span>
                                    </div> 
                                    <select class="form-control @error('cooking_time') is-invalid @enderror" name="cooking_time" id="dish_type">
                                        <option value="">Cooking Time</option>                                   
                                        @foreach($timeFilters as $timeFilter)
                                        <option value="{{ $timeFilter['id'] }}" {{ $timeFilter['id']== old('cooking_time') ? 'selected' : '' }}>{{$timeFilter['time_filter_name']}}
                                        </option>
                                        @endforeach 
                                    </select>
                                    @error('cooking_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div> --}}

                                <div class="form-group col-sm-3">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-satisfied"></i></span>
                                    </div> 
                                    <select class="form-control @error('dish_type') is-invalid @enderror" name="dish_type" id="dish_type">
                                        <option value="">Dish Type</option>                                   
                                        @foreach($dishTypes as $dishType)
                                        <option value="{{ $dishType['id'] }}" {{ $dishType['id']== old('dish_type') ? 'selected' : '' }}>{{$dishType['dish_type_name']}}
                                        </option>
                                        @endforeach 
                                    </select>
                                    @error('dish_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>

                                <div class="form-group col-sm-3">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-satisfied"></i></span>
                                    </div> 
                                    <select class="form-control @error('diet_type') is-invalid @enderror" name="diet_type" id="diet_type">
                                        <option value="">Diet Type</option>                                   
                                        @foreach($dietTypes as $dietType)
                                        <option value="{{ $dietType['id'] }}" {{ $dietType['id']== old('diet_type') ? 'selected' : '' }}>{{$dietType['diet_category_name']}}
                                        </option>
                                        @endforeach 
                                    </select>
                                    @error('diet_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>

                                <div class="form-group col-sm-3">
                                    <div class="input-group input-group-merge input-group-alternative mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="ni ni-satisfied"></i></span>
                                    </div> 
                                    <select class="form-control @error('cuisine_type') is-invalid @enderror" name="cuisine_type" id="cuisine_type">
                                        <option value="">Cuisine Type</option>                                   
                                        @foreach($cuisineTypes as  $cuisineType)
                                        <option value="{{ $cuisineType['id'] }}" {{ $cuisineType['id']== old('cuisine_type') ? 'selected' : '' }}>{{$cuisineType['cuisine_type_name']}}
                                        </option>
                                        @endforeach 
                                    </select>
                                    @error('cuisine_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-camera-compact"></i></span>
                                </div>
                                <input id="receipe_image" type="file" class="form-control @error('receipe_image') is-invalid @enderror" name="receipe_image" value="{{ old('receipe_image') }}" required autocomplete="receipe_image" placeholder="Recipe Image" autofocus>

                                @error('receipe_image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                            </div> --}}

                           
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6"><label for="ingredients">Ingredients</label></div>
                                    <div class="col-md-5">
                                        <button type="button" class="btn btn-sm btn-primary float-right" id="add_more_ingredient_btn">Add More Ingredients</button>
                                    </div>
                                    <div class="col-md-1"></div>
                                </div> 
                                @php
                                $old_ingre = old('ingredients') ?? [];
                                @endphp
                               
                                <div id="ingredient_section">
                                    <div class="row ingredient_row" id="1">
                                        <div class="col-md-4">
                                        <input  autocomplete="off" type="text" name="ingredients[]" class="ingredients form-control" placeholder="Ingredient Name" required data-id=1>
                                            
                                        <div class="ingredientsList"></div>

                                        
                                        </div>
                                       
                                        <div class="col-md-3">
                                            <input type="number" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="ingredient_qty[]" class="form-control" placeholder="Quantity" required>
                                        </div>
                                        <div class="col-md-3">
                                             <select class="form-control @error('unit') is-invalid @enderror" name="unit[]" id="unit" required onchange="getval(this);" data-id=1>
                                                <option value="">unit</option>W
                                                @foreach($units as $unit)
                                                <option value="{{ $unit['id'] }}" {{ in_array($unit['id'], $old_ingre) ? 'selected' : '' }}>{{$unit['unit']}}</option> 
                                                @endforeach 
                                            </select>
                                        </div>
                                        <div class="col-md-1 pt-2"></div>
                                        
                                    </div> 
                                </div>
                                <input type="hidden" name="no_of_ingredients" value="1" id="no_of_ingredients"> 
                                @error('ingredients')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="directions">Directions: </label>
                                <textarea id="directions" type="text" class="form-control @error('directions') is-invalid @enderror" name="directions" required autocomplete="directions" placeholder="Directions" autofocus>{{ old('directions') }}</textarea>

                                @error('directions')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="tags">Tags: </label>
                                <select id="tags" name="tags[]" class="js-example-placeholder-multiple js-states form-control" multiple>
                                    @foreach($tags as $tag)
                                        <option value="{{$tag['name']}}">{{$tag['name']}}</option>
                                    @endforeach
                                </select>
                                @error('tags')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>


                             <label for="ingredients">Recipe image</label>
                            <div class="form-group avatar-upload blog_image">
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                
                                {{-- <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="ni ni-camera-compact"></i></span>
                                </div> --}}
                                
                                {{-- <div class="col-lg-6 mb-4"> --}}
                                    <div class="blog_image">
                                        <div class="avatar-upload">
                                            <div class="avatar-edit">
                                                <input type='file' required name="receipe_image" id="receipe_image" accept=".png, .jpg, .jpeg, .gif"/>
                                                <input type="hidden"  name="{{ old("receipe_image") }}" value=""/>
                                                <label for="receipe_image"><i class="fas fa-edit"></i></label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="receipe_image_preview" style="background-image: url({{URL::asset('assets/img/thumbnail-default_2.jpg')}});">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {{-- </div> --}}

                                @error('receipe_image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $('#tags').selectize({
            sortField: 'text'
        });
    });
    // $(".js-example-placeholder-multiple").select2({
    //     placeholder: "Select tags"
    // });
    $(".ingredients").each(function() {
        // initializeSelect2($(this));
        initializeautosearch();
    });

    function initializeautosearch(){
        $('.ingredients').keyup(function(){ 
            var search = $(this).val();
            var ids =  $(this).data('id');
            if(ids !=1){
                ids = 'igre_wrap'+ $(this).data('id');;
            }
            if(search != '')
            {
                $.ajax({
                    url:"{{ route('getIngredients') }}",
                    method:"POST",
                    data:{search:search, _token:"{{ csrf_token() }}",id:ids},
                    success:function(data){ 
                        
                        $('#'+ids).find(".ingredientsList").fadeIn();  
                        $('#'+ids).find(".ingredientsList").html(data);
                    }
                });
            }
        });
        $(document).on('click', '.select_suggestion', function() {
            var id = $(this).data('id');
            $('#'+id).find('.ingredients').val($(this).text());  
            $('#'+id).find('.ingredientsList').fadeOut();  
        });
    }
    $('#add_more_ingredient_btn').on('click', function() {
        var index = parseInt($('#no_of_ingredients').val()) + 1;
        
        var ihtml = `<div class="row ingredient_row" id="igre_wrap${index}">
            <div class="col-md-4">
               <input type="text"  autocomplete="off" name="ingredients[]" class="ingredients form-control" placeholder="Ingredient Name" required data-id="${index}">
                                            
                <div class="ingredientsList">
                </div>

            </div>
            <div class="col-md-3">
                <input type="text" name="ingredient_qty[]" class="form-control" placeholder="Quantity" required>
            </div>
             <div class="col-md-3">
             <select class="form-control @error('unit') is-invalid @enderror" name="unit[]" id="unit" required onchange="getval(this);" data-id=1>
                <option value="">unit</option>W
                @foreach($units as $unit)
                <option value="{{ $unit['id'] }}" {{ in_array($unit['id'], $old_ingre) ? 'selected' : '' }}>{{$unit['unit']}}</option> 
                @endforeach 
            </select>
            </div>
            <div class="col-md-1 pt-2">
                <a class="remove_ingredient" data-id="${index}"><i class="fa fa-times"></i></a>
            </div>
        </div>`;
        var newSelect=`<select class="ingredients form-control @error('ingredients') is-invalid @enderror" name="ingredients[]"  required onchange="getval(this);" data-id=${index}>
                    <option value="">Select Ingredients</option>W
                   
                </select>`;
        $(ihtml).appendTo('#ingredient_section');
        // initializeSelect2(newSelect);
        initializeautosearch();
        $('#no_of_ingredients').val(index);
    });

    $(document).on('click', '.remove_ingredient', function() {
        var id = $(this).data('id');
        $(`#igre_wrap${id}`).remove();
        var index = parseInt($('#no_of_ingredients').val()) - 1;
        $('#no_of_ingredients').val(index);
    });

    $(function () {
        /*$('#cooking_time.bs-timepicker').timepicker(
            {
                minuteStep: 1,
                // template: 'modal',
                // appendWidgetTo: 'body',
                showSeconds: false,
                showMeridian: false
            }
        ); */
    });
    
    $(document).ready(function() {
        var timepicker = new TimePicker('time', {
           lang: 'en',
           theme: 'dark'
        });
        timepicker.on('change', function(evt) {
            var value = (evt.hour || '00') + ':' + (evt.minute || '00');
            evt.element.value = value;
        });
    });

    var _URL = window.URL;
    function readURLTwo(input) {
        var file, img;
        if ((file = input.files[0])) {
            img = new Image();
            img.onload = function () {
                
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#receipe_image_preview').css('background-image', 'url('+e.target.result +')');
                        $('#receipe_image_preview').hide();
                        $('#receipe_image_preview').fadeIn(650);
                    }
                    reader.readAsDataURL(input.files[0]);
                    return true;
                
            };
            img.src = _URL.createObjectURL(file);
        }
    }
    $("#receipe_image").change(function() {
        readURLTwo(this);
    });

function getval(sel)
{
  var item_id=sel.value;
  var id=$(sel).data("id")
  $.get( "{{ route('getUnit') }}", { item_id: item_id,_token:"{{ csrf_token() }}" } )
  .done(function( data ) {
      $(`#unit${id}`).val(data);

  });
}
     
</script>
@endsection