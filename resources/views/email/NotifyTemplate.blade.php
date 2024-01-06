<!DOCTYPE html>
<html>
<head>
    <title>Do not reply to this Email</title>
    <style>
        body {
            background-color: #7E88DE;
            color: white !important;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 100%;
            margin: auto;
        }
        .row {
            display: flex;
            justify-content: center;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        .card {
            border-radius: 25px;
            background-color: gray;
            color: white!important;
            border: 3px solid #FAFF00;
            padding: 20px;
            margin-top: 20px;
        }
        h3, h6 {
            color: white!important;
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
                        @isset($content)
                        <h6 class="card-text">{!! $content !!}</h6>
                        @endisset
                        @empty($content)
                        <h3 class="card-text">Invalid Response please contact our support to resolve</h3>
                        @endempty
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
