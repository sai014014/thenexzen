<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - TheNexZen</title>
    <link rel="icon" href="{{ asset('homePage/assets/img/businesslogowhite.svg') }}" type="image/x-icon" />
    <link href="{{ asset('dist/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Racing+Sans+One&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dist/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/common.css') }}">
</head>
<body>
    <div id="loader" class="loader-container">  
        <div class="loader"></div>
    </div>
    <div class="login">
        <div class="row">
            <div class="col-md-6">
                <div class="logo">
                    <img src="{{ asset('images/login.png') }}" alt="Business Logo">
                </div>
            </div>
            <div class="col-md-6">
                <div class="fixed_position">
                    <div class="card">
                        <div class="welcom_text">
                            <svg width="46" height="44" viewBox="0 0 46 44" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23 0L23.823 3.36707C25.109 8.62855 25.752 11.2593 27.1233 13.3821C28.336 15.2593 29.9527 16.8418 31.8554 18.0139C34.0071 19.3395 36.651 19.926 41.9388 21.0991L46 22L41.9388 22.9009C36.651 24.074 34.0071 24.6605 31.8554 25.9861C29.9527 27.1582 28.336 28.7407 27.1233 30.6179C25.752 32.7407 25.109 35.3714 23.823 40.6329L23 44L22.177 40.6329C20.891 35.3714 20.248 32.7407 18.8767 30.6179C17.664 28.7407 16.0473 27.1582 14.1446 25.9861C11.9929 24.6605 9.34898 24.074 4.06116 22.9009L0 22L4.06116 21.0991C9.34897 19.926 11.9929 19.3395 14.1446 18.0139C16.0473 16.8418 17.664 15.2593 18.8767 13.3821C20.248 11.2593 20.891 8.62855 22.177 3.36707L23 0Z" fill="black" />
                            </svg>
                            <h1>Reset Password</h1>
                        </div>
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('business.password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <label for="email">Email address</label>
                            <input class="loginInput" type="email" id="email" name="email" value="{{ $email }}" readonly style="background-color: #f5f5f5;">
                            
                            <label for="password">New Password</label>
                            <input class="loginInput" type="password" id="password" name="password" required placeholder="Enter new password (minimum 6 characters)">
                            
                            <label for="password_confirmation">Confirm New Password</label>
                            <input class="loginInput" type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm new password">
                            
                            <button class="submit loginSubmit" type="submit">Reset Password</button>
                        </form>

                        <div class="register" style="text-align:center">
                            <p>
                                <a href="{{ route('business.login') }}">Back to Login</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('dist/jquery.min.js') }}"></script>
    <script src="{{ asset('dist/js/common.js') }}"></script>
</body>
</html>

