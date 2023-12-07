<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="plugins/css/sweetalert2.css">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .form-container {
            z-index: 1;
            border-radius: 15px;
            box-shadow: 2px 2px 10px 0px #000;
            padding: 30px;
            background-color: #535A9D;
            color: #f8f9fa;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;

            background: linear-gradient(45deg, #161A3A 25%, transparent 25%),
                linear-gradient(315deg, #161A3A 25%, transparent 25%),
                linear-gradient(45deg, transparent 24%, #535A9D 25%, #535A9D 45%, transparent 45%),
                linear-gradient(315deg, transparent 24%, #535A9D 25%, #535A9D 45%, transparent 45%);
            background-size: 6em 6em;
            background-color: #161A3A;
            opacity: 1
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card form-container">
                    <div class="card-body">
                        <img src="{{ asset('storage/app_img/Step_up_logo.png') }}" class="mx-auto d-block"
                            style="max-width: 100%;" />
                        <form id="loginForm" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address:</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('plugins/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="{{ asset('plugins/js/sweetalert2.all.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $("#loginForm").on('submit', function(event) {
                event.preventDefault();
                var settings = {
                    "url": "{{route('login_submit')}}",
                    "method": "POST",
                    "timeout": 0,
                    "headers": {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    "data": JSON.stringify({
                        "email": $('#email').val(),
                        "password": $('#password').val()
                    }),
                    "beforeSend": function() {
                        var valid = isValidEmail($('#email').val());
                        if (!valid) {
                            Swal.fire("Invalid Email or Password!");
                        }
                    },
                    "success": function(response) {
                        console.log("Success:", response);
                        if(!response.verified){
                            Swal.fire({
                                icon: response.status,
                                title: "Error",
                                text: response.error_msg,
                            });
                            return;
                        }
                        sessionStorage.setItem('user_token', response.data.user_token);
                        window.location.href = "/index";
                    },
                    "error": function(jqXHR, textStatus, errorThrown) {
                        console.log("Error:", textStatus, errorThrown);
                    }
                };

                $.ajax(settings).done(function(response) {
                    console.log(response);
                });




            });

            function isValidEmail(email) {
                var emailReg = /^([a-zA-Z0-9_.+-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
                return emailReg.test(email);
            }
        });
    </script>
</body>

</html>
