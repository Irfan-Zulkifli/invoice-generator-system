@extends('layouts.app-no-nav')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="card overflow-hidden">
                <div class="bg-primary-subtle">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-4">
                                <h5 class="text-primary">Welcome Back !</h5>
                                <p>Sign in to continue to Sacker.</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="auth-logo">
                        <a href="{{ url('/') }}" class="d-block">
                            <div class="d-inline-block rounded" style="margin-top: -150px;">
                                <img src="{{ asset('assets/logos/1.png') }}" alt="Sacker Logo" width="120">
                            </div>
                        </a>
                    </div>
                    <div class="p-2">
                        @if (session('success'))
                            <div class="alert alert-success text-center mb-4">
                                {{ session('success') }}
                            </div>
                        @endif
                        @error('status')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        <form class="form-horizontal" action="{{ route('login.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" placeholder="Enter email"
                                    name="email">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group auth-pass-inputgroup">
                                    <input type="password" class="form-control" placeholder="Enter password"
                                        aria-label="Password" aria-describedby="password-addon" name="password">
                                    <button class="btn btn-light " type="button" id="password-addon"><i
                                            class="mdi mdi-eye-outline"></i></button>
                                </div>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember-check">
                                <label class="form-check-label" for="remember-check">
                                    Remember me
                                </label>
                            </div>

                            <div class="mt-3 d-grid">
                                <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                            </div>

                            <div class="mt-4 text-center">
                                <a href="{{ route('password-reset-page') }}" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot
                                    your password?</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="mt-5 text-center">

                <div>
                    {{-- <p>Don't have an account ? <a href="auth-register.html" class="fw-medium text-primary"> Signup now </a>
                    </p> --}}
                    <p>©
                        <script>
                            document.write(new Date().getFullYear())
                        </script> Irfan. Crafted with <i class="mdi mdi-heart text-danger"></i> by
                        Muhammad Irfan
                    </p>
                </div>
            </div>

        </div>
    </div>
@endsection
