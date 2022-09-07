@extends('layouts.main')
@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-xl-12">
            <div class="col text-right">
                <a href="{{url('users')}}" class="btn btn-sm btn-primary">Back</a>
            </div>
        </div>
    </div>
</div>
<div class="profile">
    <h2>Profile</h2>
    <div class="name">
        <span>Full name</span>
        <h5>{{$user->name}}</h5>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <span>Email:</span>
            <strong>{{$user->email}}</strong>
        </div>
        <div class="col-lg-6">
            <span>Birthday:</span>
            @if($user->dob == NULL)
            <strong>Not Mentioned</strong>
            @else
            <strong>{{date('d M, Y', strtotime($user->dob))}}</strong>
            @endif
        </div>
        <div class="col-lg-6">
            <span>Gender:</span>
            <strong>@if($user->gender == 1) 
                    Male
                    @elseif($user->gender == 2)
                    Female
                    @elseif($user->gender == 3)
                    Other
                    @else
                    Not Mentioned
                    @endif</strong>
        </div>
        <div class="col-lg-6">
            <span>Interested In:</span>
            <strong>@if($user->interested_in == 1)
                    Male
                    @elseif($user->interested_in == 2)
                    Female
                    @elseif($user->interested_in == 3)
                    Both
                    @else
                    Not Mentioned
                    @endif</strong>
        </div>
        <div class="col-lg-6">
            <span>University:</span>
            <strong>{{ $user->university }}</strong>
        </div>
        <div class="col-lg-6">
            <span>Business:</span>
            <strong>{{ $user->business }}</strong>
        </div>
        <div class="col-lg-6">
            <span>Interests:</span>
            <strong>{{ $user->interests }}</strong>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $('.sm-img').on('click', function() {
        var id = $(this).data('id');
        $('.carousel-item').removeClass('active');
        $(`.img_active_${id}`).addClass('active');
        $('.bs-example-modal-lg').modal('show');
    });
    $('.carousel').carousel({
        interval: 8000
    })
</script>
@endsection