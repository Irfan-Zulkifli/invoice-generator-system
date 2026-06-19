@extends('layouts.app-no-nav')
    @section('content')

        <div class="account-pages pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary-subtle">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary"> Set New Password</h5>
                                            <p>Create a new password for your account.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div>
                                    <a href="/">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="assets/images/logo.svg" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                @if (session('success'))
                                    <div class="alert alert-success text-center mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                <div class="p-2">
                                    <form class="form-horizontal" action="{{ route('password.update') }}" method="POST">
                                        @csrf
                                        
                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="mb-3">
                                            <label for="useremail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="useremail" name="email" value="{{ $email ?? old('email') }}" readonly>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="userpassword" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="userpassword" placeholder="Enter new password" name="password" required autofocus>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" placeholder="Re-enter new password" name="password_confirmation" required>
                                        </div>
                                        
                                        <div class="text-end">
                                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Save Password</button>
                                        </div>
                                    </form>
                                </div>
            
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>Remember It ? <a href="{{ route('login') }}" class="fw-medium text-primary"> Sign In here</a> </p>
                            <p>© <script>document.write(new Date().getFullYear())</script> IrfanZul. Crafted with <i class="mdi mdi-heart text-danger"></i> by Muhammad Irfan Bin Zulkifli</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>        
    @endsection