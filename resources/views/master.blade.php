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
    <h1>{{ Auth::user()->name }}</h1>
    <img src="{{ asset('storage/uploads/1/aW1hZ2VfMjAyMy0xMi0xN18wMS00Ny0zOA==.png') }}"
     class="mx-auto d-block"
        style="max-width: 100%;" />
    <div id="aa">

    </div>


    <script src="{{ asset('plugins/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="{{ asset('plugins/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var settings = {
                "url": "{{url('/api/service/32/view')}}",
                "method": "GET",
                "timeout": 0,
                "headers": {
                    "X-CSRF-TOKEN": "",
                    "Content-Type": "application/json"
                },
                "data": " ",
                "success": function(response) {
                    if(response.data.attachments != null){
                        $.each(response.data.attachments, function(key, value) {
                            $('#aa').append('<img src="' + value + '" alt="' + key + '" />');
                        });
                    }
                }
            };

            $.ajax(settings).done(function(response) {
                console.log(response);
            });
        });
    </script>

</body>

</html>
