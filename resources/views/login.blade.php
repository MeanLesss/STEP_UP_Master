<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>

        .form-container {
            border-radius: 15px;
            box-shadow: 2px 2px 10px 0px #000;
            padding: 30px;
            background-color: #535A9D;
            color:#f8f9fa;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;

            background: linear-gradient(45deg, #161A3A 25%, transparent 25%),
                linear-gradient(315deg, #161A3A 25%, transparent 25%),
                linear-gradient(45deg, transparent 24%,#535A9D 25%, #535A9D 45%, transparent 45%),
                linear-gradient(315deg, transparent 24%,#535A9D 25%, #535A9D 45%, transparent 45%);
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
                        <img src="{{ asset('storage/app_img/Step_up_logo.png') }}" class="mx-auto d-block" style="max-width: 100%;"/>
                        <form method="POST" action="{{ route('login') }}" >
                            @csrf
                            <div class="form-group">
                                <label for="email">Email address:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

<script>

</script>

</html>

