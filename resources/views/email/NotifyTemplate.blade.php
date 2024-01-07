<!DOCTYPE html>
<html>
<head>
    <title>Do not reply to this Email</title>
    <style>
        .im {
            color: #FAFF00 !important;
        }
        body {
            background-color: #7E88DE;
            color: white !important;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .card {
            border-radius: 25px;
            background-color: #7E88DE;
            color: white !important;
            border: 3px solid #FAFF00;
            padding: 20px;
            margin-top: 20px;
            text-align: start;
            width: 100%;
            box-sizing: border-box;
        }
        h3, h6 {
            color: white !important;
        }
        p {
            color: white !important;
            font-size: 18px;
        }
        img {
            max-width: 100%;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="{{ asset('storage/app_img/Step_up_logo.png') }}" />
            <div class="card-body">
                @isset($content)
                <p class="card-text">{!! $content !!}</p>
                @endisset
                @empty($content)
                <h3 class="card-text">Invalid Response please contact our support to resolve</h3>
                @endempty
            </div>
        </div>
    </div>
</body>
</html>
