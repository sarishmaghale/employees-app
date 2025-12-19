<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title> Login</title>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-card h2 {
            margin-bottom: 25px;
            font-weight: 700;
            color: #333;
            text-align: center;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, .25);
        }

        .forgot-password {
            font-size: 14px;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <h2>Login</h2>
        <form id="LoginForm">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" id="email" class="form-control" name="email" placeholder="Enter email"
                    required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" class="form-control" name="password" placeholder="Enter password"
                    required>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="showPassword">
                    <label class="form-check-label" for="showPassword">Show Password</label>
                </div>
                <button type="button" id="showForgetPasswordModal" class="forgot-password">Forgot password?</button>
            </div>
            <button type="submit" class="btn btn-login w-100">Login</button>
        </form>
    </div>
    <div id="globalSpinner" class="d-none text-center mt-3">
        <div class="spinner-border text-primary" role="status">
        </div>
    </div>

    @include('partial-views.otpModal-partial')
    @include('partial-views.forgetPasswordModal-partial')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/spinner.js') }}"></script>
    <script src="{{ asset('js/validation.js') }}"></script>
    <script>
        $(document).ready(function() {
            const form = document.getElementById("LoginForm");
            const submitBtn = form.querySelector("[type='submit']");

            form.addEventListener("submit", async function(e) {
                e.preventDefault();
                showSpinner(submitBtn)
                const formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: `{{ route('login.request') }}`,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else if (response.role === 'admin') window.location.href =
                                "{{ route('dashboard') }}";
                            else {
                                $("#loginEmai").val(response.data);
                                $('#otpModal').modal('show');
                            }
                        } else {
                            console.log('error shown in main')
                            Swal.fire('Error', response.message, 'error');
                            hideSpinner(submitBtn);
                        }
                    },
                    error: function(xhr) {
                        console.error('Server error', xhr.responseText);
                        hideSpinner(submitBtn);
                    }
                });
            });

            $(document).on('click', '#showForgetPasswordModal', function() {
                console.log('call to modal')
                $("#forgetPasswordModal").modal('show');
            })
        });
        @if (session('success'))
            Swal.fire('Success', `{{ session('success') }}`, 'success');
        @endif

        @if (session('error'))
            Swal.fire('Error', '{{ session('error') }}', 'error');
        @endif
    </script>
    @stack('scripts')
</body>

</html>
