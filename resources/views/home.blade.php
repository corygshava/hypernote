<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <link rel="shortcut icon" href="lrvl icon.png" type="image/png">
    <link rel="stylesheet" href="_assets/BS4/css/bootstrap.min.css">
    <link rel="stylesheet" href="_assets/css/fa-all.css">
    <link rel="stylesheet" href="_assets/css/styles.css">
    <link rel="stylesheet" href="_assets/css/w3.css">
    <link rel="stylesheet" href="_assets/css/coryG_base.css">

    <!-- Bootstrap JS (Optional) -->
    <script src="_assets/js/jquery-3.5.1.slim.min.js"></script>
    <script src="_assets/bs4/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    @auth
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            <strong>Koala!</strong> This is a primary alert.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="content flow centroid">
            <div class="formguy spacy-md mycon w3-center">
                <h2>You are logged in</h2>
                <form action="./logout" method="post">
                    @csrf
                    <button href="./logout" class="btn outline">logout</button>
                </form>
            </div>
        </div>
    @else
        <div class="w3-animate-zoom spacy-sm w3-border w3-border-red w3-text-red">
            <span>you aint logged in</span>
        </div>

        {{-- registration form --}}
        <div class="content flow centroid">
            <div class="formguy spacy-md mycon">
                <h2>register</h2>

                @if ($errors->any())
                    <div class="w3-animate-zoom spacy-sm w3-border w3-border-red w3-text-red">
                        @foreach ($errors->all() as $err)
                            <span>{{$err}}</span><br>
                        @endforeach
                    </div>
                @endif

                <form action="./register" method="post">
                    @csrf
                    <input class="w3-input spacy-tn distance-md" type="text" name="name" id="name" placeholder="enter name here..." value="{{ old('name')}}">
                    <input class="w3-input spacy-tn distance-md" type="email" name="email" id="email" placeholder="enter email here..." value="{{ old('email')}}">
                    <input class="w3-input spacy-tn distance-md" type="password" name="password" id="password" placeholder="enter password here..." value="{{ old('password')}}">
                    <button class="btn primary w-100">register your account <i class="fa fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    @endauth
</body>
</html>