<!DOCTYPE html>
<html>
<head>
    <title>Do not reply to this Email</title>
    <!-- Don't forget to include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome for the verified icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #7E88DE; /* Dark background color */
            color: white;
        }
        .card {
            border-radius: 25px; /* Round corners */
            background-color: #161A3A; /* White card background */
            color: #FAFF00;
            border : 3px solid #FAFF00;
        }
        h3{
            color:white;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
                <div class="card p-4">
                    <img src="{{ asset('storage/app_img/Step_up_logo.png') }}" class="card-img-top mx-auto d-block" style="max-width: 100%;" />
                    <div class="card-body text-center">
                        <h1 class="card-title">Welcome</h1>
                        <a href="{{route('login_app')}}" class="btn btn-round btn-primary">Try Login</a>
                        {{-- <h3 class="card-text">{{$msg}}</h3> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
