<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PM</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }
    </style>
</head>

<body>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-6 col-12">
                <div id="auth-left">
                    <div class="row mb-0">
                        <div class="col-md-4 col-4 auth-logo">
                            <img class="d-none d-md-block" src="assets/images/full-logo.png" alt="Logo">
                            <img class="d-block d-md-none" src="assets/images/logo/logo.png" alt="Logo" style="width: 100px">
                        </div>
                        <div class="col-md-8 col-8">
                        </div>
                    </div>
                    <p class="auth-subtitle mb-5">Aplikasi Internal Manajemen Proyek</p>
                    <form action="{{ route('login.process') }}" method="POST">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="username" class="form-control form-control-xl @error('username') is-invalid @enderror" placeholder="Nama Akun" autocomplete="off" />
                            {{-- <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div> --}}
                            @error('username')
                                <div class="invalid-feedback">
                                    <i class="bx bx-radio-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password" id="password" class="form-control form-control-xl @error('password') is-invalid @enderror" placeholder="Kata Sandi" autocomplete="off" />
                            <span toggle="#password" class="fa fa-fw fa-eye password-toggle"></span>
                            {{-- <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div> --}}
                            @error('password')
                            <div class="invalid-feedback">
                                <i class="bx bx-radio-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        </div>
                        {{-- <div class="form-check form-check-lg d-flex align-items-end">
                            <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                Keep me logged in
                            </label>
                        </div> --}}
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5" type="submit">Masuk</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <div class="footer clearfix mb-0 text-muted">
                            <div class="float-start">
                                <p>2025 &copy;</p>
                            </div>
                            <div class="float-end">
                                <p><a href="https://hanatekiondo.com">PT. Hanatekindo Mulia Abadi</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div id="auth-right" class="d-flex justify-content-center">
                    {{-- <div class="p-lg-5 p-3 text-start">
                        <h1 class="title-right-content">Proyek Manajemen</h1>
                        <div class="text-center">
                            <img src="{{ asset('assets/images/bg/login-bg.png') }}" alt="Ilustrasi Latar Login" style="width: 80%;">
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

    </div>
</body>

@if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
        });
    </script>
@endif

@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ $errors->first() }}",
        });
    </script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.querySelector(".password-toggle");
        const passwordInput = document.querySelector("#password");

        toggle.addEventListener("click", function () {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });
</script>

</html>
