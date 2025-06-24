<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TenderWiz</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<style>
    /* Login Page Styling */
.login-page {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.login-page .card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.login-page .brand .bx {
    transition: transform 0.3s ease;
}

.login-page .brand:hover .bx {
    transform: rotate(360deg);
}

.login-page .form-label {
    font-size: 0.95rem;
    color: #343a40;
}

.login-page .input-group-text {
    background: #f8f9fa;
    border-right: none;
    color: #6c757d;
}

.login-page .form-control {
    border-left: none;
    border-radius: 0 8px 8px 0;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.login-page .form-control:focus {
    box-shadow: 0 0 0 2px rgba(60, 145, 230, 0.1);
    border-color: var(--blue);
}

.login-page .btn-primary {
    background: linear-gradient(90deg, var(--blue), #2a73c0);
    border: none;
    padding: 0.75rem;
    font-weight: 500;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.login-page .btn-primary:hover {
    background: linear-gradient(90deg, #2a73c0, var(--blue));
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(60, 145, 230, 0.3);
}

.login-page .alert {
    border-left: 4px solid #dc3545;
    padding: 0.75rem;
    font-size: 0.9rem;
}

.login-page .text-muted {
    font-size: 0.85rem;
}

/* Responsive Adjustments */
@media screen and (max-width: 576px) {
    .login-page .col-md-4 {
        padding: 1rem;
    }

    .login-page .card-body {
        padding: 1.5rem !important;
    }

    .login-page .btn-primary {
        font-size: 1rem;
        padding: 0.5rem;
    }
}
</style>
<body class="professional-theme login-page d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-lg animate__animated animate__fadeInUp">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <a href="#" class="brand d-flex align-items-center justify-content-center mb-3">
                                <i class='bx bxs-smile fs-3 text-primary me-2'></i>
                                <span class="fs-4 fw-bold text-dark">TenderWiz Portal</span>
                            </a>
                            <!-- <h2 class="fs-3 fw-semibold text-dark">Admin Login</h2> -->
                        </div>
                      

                        @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->has('email'))
    <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX" role="alert">
        {{ $errors->first('email') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
                         <form action="{{ route('check') }}" method="post">
                            @csrf
                            <div class="mb-3 position-relative">
                                <label for="email" class="form-label fw-medium text-dark">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class='bx bx-envelope'></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 @error('email') is-invalid @enderror" placeholder="Enter your email" required value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4 position-relative">
                                <label for="password" class="form-label fw-medium text-dark">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class='bx bx-lock-alt'></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0 @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm animate__animated animate__pulse animate__infinite">Login</button>
                        </form>
                    </div>
                </div>
                <p class="text-center mt-3 text-muted small">&copy; {{ date('Y') }} TenderWiz. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- jQuery 3.6.0 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- Bootstrap 5.3.0 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <!-- jQuery Validate 1.19.5 -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js" integrity="sha256-JPVFF+oDUE84hpg2v2Y6lAPVv3LGx6MPJH2CNrW5qOI=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            // Form validation
            $('#loginForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    email: {
                        required: "Please enter your email address.",
                        email: "Please enter a valid email address."
                    },
                    password: {
                        required: "Please enter your password.",
                        minlength: "Your password must be at least 6 characters long."
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.mb-3, .mb-4').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    $(form).find('button[type="submit"]').addClass('animate__animated animate__pulse');
                    form.submit();
                }
            });

            // Input focus animations
            $('.form-control').on('focus', function () {
                $(this).closest('.input-group').addClass('animate__animated animate__bounceIn');
            });
        });
    </script>
</body>
</html>