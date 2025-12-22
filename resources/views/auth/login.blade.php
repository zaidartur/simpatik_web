<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="author" content="Diskominfo Karanganyar">
    <meta name="description" content="Sistem Informasi Persuratan Masuk dan Keluar Kabupaten Karanganyar">
    <title>Login | Sipermas </title>
    <link rel="icon" type="image/x-icon" href="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" />
    <link href="{{ asset('templates/layouts/vertical-light-menu/css/light/loader.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/layouts/vertical-light-menu/css/dark/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('templates/layouts/vertical-light-menu/loader.js') }}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('templates/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('templates/layouts/vertical-light-menu/css/light/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/light/authentication/auth-cover.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{ asset('templates/layouts/vertical-light-menu/css/dark/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/dark/authentication/auth-cover.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/assets/css/light/elements/alert.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('templates/assets/css/light/elements/alert.css') }}">
    <!--  END CUSTOM STYLE FILE  -->
    
</head>
<body class="form">

    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

    <div class="auth-container d-flex">

        <div class="container mx-auto align-self-center">
    
            <div class="row">
    
                <div class="col-6 d-lg-flex d-none h-100 my-auto top-0 start-0 text-center justify-content-center flex-column">
                    <div class="auth-cover-bg-image" style="background-image: url({{ asset('templates/images/setda02.jpg') }}) !important; background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.5"></div>
                    <div class="auth-overlay"></div>
                        
                    <div class="auth-cover">
    
                        <div class="position-relative">
    
                            {{-- <img src="{{ asset('templates/assets/img/auth-cover.svg') }}" alt="auth-img"> --}}
                            <img src="{{ asset('templates/images/Lambang_Kabupaten_Karanganyar.png') }}" alt="auth-img" style="width: 200px; height: auto;">
    
                            <h1 class="mt-5 text-white font-weight-bolder px-2">SIPERMAS</h1>
                            <h2 class="mt-5 text-white font-weight-bolder px-2">Sistem Informasi Persuratan Masuk dan Keluar Kabupaten Karanganyar</h2>
                        </div>
                        
                    </div>

                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center ms-lg-auto me-lg-0 mx-auto">
                    <div class="card">
                        <div class="card-body">
    
                            <form method="POST" class="login-form" action="{{ route('login') }}" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        
                                        <h2>Login</h2>
                                        <p>Masukkan username dan password Anda</p>
                                        
                                    </div>
                                    @error('username')
                                    <div class="col-md-12" id="alert-login"> 
                                        <div class="alert alert-arrow-right alert-icon-right alert-light-danger mb-4" role="alert">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
                                            <strong>Warning!</strong> {{ $message }}
                                        </div> 
                                    </div>
                                    @enderror
                                    <div class="col-md-12"> 
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus maxlength="40">
                                            <div class="invalid-feedback">
                                                Username tidak boleh kosong.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                                            <div class="invalid-feedback">
                                                Password tidak boleh kosong.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <div class="form-check form-check-primary form-check-inline">
                                                <input class="form-check-input me-3" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="remember">
                                                    Saya ingat
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-secondary w-100">LOGIN</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </form>
                            
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>

    </div>
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('templates/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('templates/assets/js/forms/bootstrap_validation/bs_validation_script.js') }}"></script>

    <script>
        window.addEventListener('load', function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('login-form');
            var invalid = $('.login-form .invalid-feedback');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                    invalid.css('display', 'block');
                } else {

                    invalid.css('display', 'none');

                    form.classList.add('was-validated');
                }
            }, false);
            });

        }, false);
    </script>


</body>
</html>