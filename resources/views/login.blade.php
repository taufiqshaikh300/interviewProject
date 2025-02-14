@extends('template.main')
@push('title')
User Login   
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
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>{{session('error')}}</strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="container mt-3 ">
    <form method="POST" action="{{route('auth.login')}}">
      @csrf
      @method('POST')
      <div class="row jumbotron box8 p-4">
        <div class="col-sm-12 mx-t3 mb-4">
          <h2 class="text-center text-info">User Login</h2>
        </div>

        <div class="col-sm-12 form-group">
            <label for="name-f">Email or Phone</label>
            <input type="text" class="form-control" name="email_or_phone" id="email_or_phone" placeholder="Enter your email or phone." value="{{old('email')}}" required >
            @error('email_or_phone')
            <small class="text-danger">{{$message}}</small>                    
        @enderror
          </div>
          <div class="col-sm-12 form-group">
            <label for="name-f">Password</label>
            <input type="password" class="form-control" name="password" id="password"  placeholder="Enter your password."    required>
            @error('password')
            <small class="text-danger">{{$message}}</small>                    
        @enderror
          </div>
          
      
        
      
        <div class="col-sm-12 form-group mb-0 mt-2">
          <button class="btn btn-primary float-right">Submit</button>
        </div>

        <a href="{{route('auth.register')}}">New user ?</a>
  
      </div>
    </form>
  </div>

@endsection