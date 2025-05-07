<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PM - HMA</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Zain:ital,wght@0,200;0,300;0,400;0,700;0,800;0,900;1,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/error.css') }}">

</head>
<body>
    <div id="error">
        <div class="error-page container">
            <div class="col-md-8 col-12 offset-md-2">
                <center><img class="img-error" src="assets/images/samples/error-404.png" alt="Not Found" style="width:400px;"/></center>
                <div class="text-center">
                    <h1 class="error-title">NOT FOUND</h1>
                    <p class='fs-5 text-gray-600'>Halaman yang Anda cari tidak ditemukan.</p>
                    <a href="{{ route('home') }}" class="btn btn-lg btn-outline-primary mt-3">Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>