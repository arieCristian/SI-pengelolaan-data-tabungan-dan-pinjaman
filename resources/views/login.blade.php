<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    {{-- My style --}}
    <link rel="stylesheet" href="/css/style.css">


    <title>KSU Mentik Sari | {{ $title }}</title>
</head>

<body>

    <div class="container mt-4">
        <main class="form-signin">
            @if (session()->has('loginError'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('loginError') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
            
                <form action="/login" method="post">
                    @csrf
                <h1 class="h3 mb-3 fw-normal text-center">KSU Mentik Sari </h1>
                <div class="form-floating">
                    <input name="username" type="text" class="form-control" id="username" placeholder="username" required>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating">
                    <input name="password" type="password" class="form-control" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">LogIn</button>
            </form>
        </main>
    </div>




    <script src="/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>






