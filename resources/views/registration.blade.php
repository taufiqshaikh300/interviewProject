@extends('template.main')
@push('title')
User Registration   
@endpush

@section('content')
<style>
    label {
    font-weight: 600;
    color: #666;
}
body {
  background: #f1f1f1;
}
.box8{
  box-shadow: 0px 0px 5px 1px #999;
}

</style>


@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>{{session('success')}}</strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif


<div class="container mt-3 ">
    <form method="POST" action="{{route('auth.store')}}">
      @csrf
      @method('POST')
      <div class="row jumbotron box8 p-4">
        <div class="col-sm-12 mx-t3 mb-4">
          <h2 class="text-center text-info">User Registration Form</h2>
        </div>
        <div class="col-sm-12 form-group">
          <label for="name-f">Name</label>
          <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name." value="{{old('name')}}" required>   
          @error('name')
              <small class="text-danger">{{$message}}</small>                    
          @enderror
        </div>
        <div class="col-sm-12 form-group">
            <label for="name-f">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email." value="{{old('email')}}" required >
            @error('email')
            <small class="text-danger">{{$message}}</small>                    
        @enderror
          </div>
          <div class="col-sm-12 form-group">
            <label for="name-f">Password</label>
            <input type="password" class="form-control" name="password" id="password"   required>
            @error('password')
            <small class="text-danger">{{$message}}</small>                    
        @enderror
          </div>
          
          <div class="col-sm-12 form-group">
            <label for="name-f">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"  required>
            @error('password_confirmation')
            <small class="text-danger">{{$message}}</small>                    
            @enderror
          </div>
          <div class="col-sm-12 form-group">
            <label for="name-f">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter your phone number." value="{{old('phone')}}" required>
            @error('phone')
            <small class="text-danger">{{$message}}</small>                    
            @enderror
          </div>
          <div class="col-sm-12 form-group">
            <label for="Country">Role</label>
            <select class="form-control custom-select browser-default" name="role">
              <option value="" selected disabled>Select Role</option>
              <option value="organizer" @if (old('role') == 'organizer')
                selected
              @endif>Organizer</option>
              <option value="attendee" @if (old('role') == 'attendee')
              selected
            @endif>Attendee</option>
            </select>
            @error('role')
            <small class="text-danger">{{$message}}</small>                    
            @enderror
          </div>
      
        <div class="col-sm-12 form-group mb-0 mt-2">
          <button class="btn btn-primary float-right">Submit</button>
        </div>

        <a href="{{route('auth.login')}}">Already a user ?</a>
  
      </div>
    </form>
  </div>

@endsection