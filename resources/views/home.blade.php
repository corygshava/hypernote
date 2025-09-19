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
    <link rel="stylesheet" href="_assets/css/coryG_UIOps.css">
    <link rel="stylesheet" href="_assets/css/animations.css">
    <link rel="stylesheet" href="_assets/css/fonts.css">

    <!-- s-auto -->
    <link rel="stylesheet" href="_assets/css/s-auto.css">
    <link rel="stylesheet" href="_assets/css/s-auto/autoforms.css">

    <!-- Bootstrap JS (Optional) -->
    <script src="_assets/js/jquery-3.5.1.slim.min.js"></script>
    <script src="_assets/bs4/js/bootstrap.bundle.min.js"></script>
    <script src="_assets/js/SuperScript.js"></script>
    <script src="_assets/js/toappend.js"></script>
    <script src="_assets/js/coryG_UIOps.js"></script>
    <script src="_assets/js/customalerter.js"></script>
</head>
<body>
    @auth
        {{-- if a user session exists --}}
        @if (session('success'))
            <script>
                alert_success(`{{ session('success') }}`);
            </script>
        @endif

        {{-- error handling --}}

        @if ($errors->any())
            <script>
                @foreach ($errors->all() as $err)
                    alert_danger(`{{$err}}`);
                @endforeach
            </script>
        @endif

        <div class="flow centroid">
            <div class="formguy spacy-md mycon w3-center">
                <span class="h3">You are logged in</span>
                <form action="./logout" method="post" class="spacy-md">
                    @csrf
                    <button href="./logout" class="btn outline">logout</button>
                    <a href="./posts" class="btn outline">view posts</a>
                </form>
            </div>
        </div>
        
        <div class="flow centroid">
            <div class="formguy spacy-md mycon w3-center">
                <span class="h3">new post</span>
                <div class="spacy-md">
                    <button class="btn outline" data-toggler="#newpostmodal" data-onshow="flex"><i class="fa fa-plus"></i> create post</button>
                </div>
            </div>
        </div>
        
        <hr>

        <div class="stack centroid gap-md spacy-md">
            <div class="hedsect">
                <span class="h3">Your posts</span>
            </div>

            <div class="flowline gap-md overflow-safe">
            @if (count($posts))
                <?php //print_r($posts);?>
                @foreach ($posts as $post)
                    <div class="spacy-sm panelbg postbox mycon w3-card">
                        <span class="h3">{{ $post['title'] }}</span>
                        <p>{{ $post['body'] }}</p>
                        <p>
                            <a href="./edit-post/{{ $post['id'] }}" class="btn outline"><i class="fa fa-edit"></i> Edit post</a>
                        </p>
                        <form action="./delete-post/{{ $post['id'] }}" method="post" class="w3-display-topright spacy-sm">
                            @csrf
                            @method('DELETE')
                            <button class="btn outline w3-text-red w3-border-red w3-hover-red"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                @endforeach
            @endif
            </div>
        </div>

        <div class="mymodal" id="newpostmodal" data-shown="0">
            <div class="modal-content slide-in-bottom mycon">
                <button class="w3-btn w3-display-topright" data-toggler="#newpostmodal"><i class="fa fa-times"></i></button>
                <span class="h3">Add post</span>
                <form action="./mek-post" method="post" class="s-autoform-v">
                    @csrf
                    <input type="text" name="post_title" id="post_title" value="{{old('post_title')}}" placeholder="what do we call this adventure" autofocus>
                    <textarea name="post_message" id="post_message" rows="3" placeholder="what's on your mind">{{old('post_message')}}</textarea>
                    <button class="btn outline"><i class="fa fa-plus"></i> add post</button>
                </form>
            </div>
        </div>
    @else
        {{-- if a user session doesnt exist --}}
        <div class="w3-animate-zoom spacy-sm w3-border w3-border-red w3-text-red w3-hide">
            <span>you aint logged in</span>
        </div>

        <div class="tabnav flowline centroid gap-sm spacy-md w3-animate-opacity">
            <button class="btn outline" data-role="tab-btn">login</button>
            <button class="btn outline" data-role="tab-btn">register</button>
        </div>

        {{-- login form --}}
        <div class="flow centroid slide-in-bottom" data-role="tab-content">
            <div class="formguy spacy-md mycon">
                <div>
                    <span class="h3"><b>Login</b></span>
                    <p>enter your account</p>
                </div>

                @if ($errors->any())
                    <div class="w3-animate-zoom spacy-sm w3-border w3-border-red w3-text-red">
                        @foreach ($errors->all() as $err)
                            <span>{{$err}}</span><br>
                        @endforeach
                    </div>
                @endif

                <form action="./login" method="post" class="s-autoform-v">
                    @csrf
                    <input class="spacy-tn" type="text" name="username" id="login_username" placeholder="enter username here..." value="{{ old('username')}}" autofocus>
                    <input class="spacy-tn" type="password" name="password" id="login_password" placeholder="enter password here..." value="{{ old('password')}}">
                    <button class="btn primary w-100">login <i class="fa fa-angle-double-right"></i></button>
                </form>
            </div>
        </div>

        {{-- registration form --}}
        <div class="flow centroid slide-in-bottom" data-role="tab-content">
            <div class="formguy spacy-md mycon">
                <div>
                    <span class="h3"><b>register</b></span>
                    <p>create a new account</p>
                </div>

                @if ($errors->any())
                    <div class="w3-animate-zoom spacy-sm w3-border w3-border-red w3-text-red">
                        @foreach ($errors->all() as $err)
                            <span>{{$err}}</span><br>
                        @endforeach
                    </div>
                @endif

                <form action="./register" method="post" class="s-autoform-v">
                    @csrf
                    <input class="spacy-tn" type="text" name="name" id="register_name" placeholder="enter your new username here..." value="{{ old('name')}}" autofocus>
                    <input class="spacy-tn" type="email" name="email" id="register_email" placeholder="enter email here..." value="{{ old('email')}}">
                    <input class="spacy-tn" type="password" name="password" id="register_password" placeholder="enter password here..." value="{{ old('password')}}">
                    <button class="btn primary w-100">register your account <i class="fa fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    @endauth
</body>
</html>