<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Master page</title>
</head>
<body>
    <h1>Master page</h1>
    <h1>{{Auth::user()->name}}</h1>
    <img src="{{ asset('storage/uploads/1/Screenshot 2023-11-23 200712.png') }}" class="mx-auto d-block"
    style="max-width: 100%;" />
</body>
</html>
