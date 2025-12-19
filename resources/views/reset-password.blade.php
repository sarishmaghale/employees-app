<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Reset Password</title>
    <script src="{{ asset('js/validation.js') }}"></script>
    <script src="{{ asset('js/spinner.js') }}"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .card h2 {
            margin-bottom: 25px;
            font-weight: 700;
            color: #333;
            text-align: center;
        }

        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
        }

        .btn:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, .25);
        }
    </style>
</head>

<body>
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <h2>Reset Your Password</h2>
        <form id="passwordResetForm" method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">
            <input type="hidden" name="email" value="{{ request('email') }}">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" value="{{ request('email') }}" readonly>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" id="reset-password" class="form-control" name="password"
                    placeholder="Enter your password" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Confirm Password</label>
                <input type="password" id="reset-password-confirmed" class="form-control" name="password_confirmation"
                    placeholder="Rewrite your password" required>
            </div>
            <button type="submit" id="resetPasswordBtn" class="btn w-100">Reset</button>
        </form>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $('#passwordResetForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $("#resetPasswordBtn")[0];
            showSpinner(btn);
            const form = this;
            const formData = new FormData(form);
            $.ajax({
                url: form.action,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success').then(() => {
                            window.location.href = "{{ route('login') }}";
                        });
                    } else {
                        hideSpinner(btn);
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    hideSpinner(btn);
                    if (xhr.status === 422) handleValidationErrors(xhr,
                        '#passwordResetForm');
                    else {
                        Swal.fire('Error', 'Something went wrong', 'error');
                        console.error('Error:' + xhr.responseText);
                    }
                }
            })
        })
    })
</script>
