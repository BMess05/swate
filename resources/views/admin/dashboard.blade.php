@extends('layouts.main')
@section('content')
<div class="header pb-6" id="main_content">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row align-items-center py-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                    <!-- Card body -->
                        <div class="card-body">
                            <a href="{{ route('users') }}">
                                <div class="row">
                                    <div class="col"> 
                                        <h5 class="card-title text-uppercase text-muted mb-0">All Users</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $app_users_count }}</span> 
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-yellow text-white rounded-circle shadow">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                    <!-- Card body -->
                        <div class="card-body">
                            <a href="{{ route('listCategories') }}">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Ingredient Categories</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $category_count }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-purple text-white rounded-circle shadow">
                                            <img src="{{asset('assets/img/icons/ingredient-cat-fill.svg')}}" class="main-images">
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats"> 
                        <div class="card-body">
                            <a href="{{ route('listItems') }}">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Ingredient</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $items_count }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                            <img src="{{asset('assets/img/icons/ingredient-cat-fill.svg')}}" class="main-images">
                                        </div>
                                    </div>
                                </div> 
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body"> 
                            <a href="{{ route('listReceipes') }}">
                                <div class="row">
                                    <div class="col"> 
                                        <h5 class="card-title text-uppercase text-muted mb-0">Recipes</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $receipe_count }}</span> 
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                            <img src="{{asset('assets/img/icons/recipe-fill.svg')}}" class="main-images">
                                        </div>
                                    </div>
                                </div> 
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body"> 
                            <a href="{{ route('listDietCategories') }}">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Diet Categories</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $diet_categories }}</span> 
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                             <img src="{{asset('assets/img/icons/diet-fill.svg')}}" class="main-images">
                                        </div>
                                    </div>
                                </div> 
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body"> 
                            <a href="{{ route('listCuisineTypes') }}">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Cuisine Types</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $cuisine_types }}</span> 
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                             <img src="{{asset('assets/img/icons/cusine-fill.svg')}}" class="main-images">
                                        </div>
                                    </div>
                                </div> 
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body"> 
                            <a href="{{ route('listDishTypes') }}">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Dish Types</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $dish_types }}</span> 
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                             <img src="{{asset('assets/img/icons/dish-type.svg')}}" class="main-images">
                                        </div>
                                    </div>
                                </div> 
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats"> 
                        <div class="card-body">
                            <a href="{{ route('listTags') }}">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Tags</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $tags_count }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                            <i class="ni ni-tag"></i>
                                        </div>
                                    </div>
                                </div> 
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body"> 
                            <a href="{{ route('listFaqs') }}">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">FAQs</h5>
                                        <span class="h2 font-weight-bold mb-0">{{ $questions }}</span> 
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                            <i class="fas fa-question"></i>
                                        </div>
                                    </div>
                                </div> 
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Page content -->

@include('layouts.footer')
@endsection

@section('script')
<script>
    function confirmationDelete(anchor) {
        swal({
            title: "Are you sure want to delete this Category?",
            text: "Once deleted, you will not be able to recover this category!",
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

</script>
@endsection