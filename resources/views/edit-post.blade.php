<!DOCTYPE html>
<html lang="en">
<head>
    <base href="../">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    
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
        <div class="w3-top spacy-md">
            <a href="javascript:history.back()" class="btn outline"><i class="fa fa-chevron-left"></i> go back</a>
        </div>
        <div class="content fullheight t3">
            <div class="formguy spacy-md mycon w3-center slide-in-bottom">
                <span class="h3">Edit Post</span>
                
                @if (auth()->id() != $post['user_id'])
                    <div class="spacy-md w3-center">
                        You arent allowed to edit posts you didnt create
                    </div>
                    <a href="javascript:history.back()" class="btn themetxt"><i class="fa fa-chevron-left"></i> go back</a>
                @else
                    <form action="./edit-post/{{$post['id']}}" method="post" class="s-autoform-v">
                        @csrf
                        @method('put')
                        <span class="text-muted text-gld">item created on <b class="themetxt">{{ $post['created_at'] }}</b></span><br>
                        <span class="text-muted text-gld">last update: <b class="themetxt">{{ $post['updated_at'] }}</b></span>
                        {{-- error handling --}}
                        
                        @if ($errors->any())
                            <div class="w3-animate-zoom spacy-sm w3-border w3-border-red w3-text-red">
                                @foreach ($errors->all() as $err)
                                    <span>{{$err}}</span><br>
                                @endforeach
                            </div>
                        @endif

                        <input type="text" name="post_title" id="post_title" value="{{ $post['title'] }}" placeholder="type the title here">
                        <textarea name="post_message" id="post_message" placeholder="type the title here" rows="6">{{ $post['body'] }}</textarea>
                        <button class="btn outline"><i class="fa fa-save"></i> save changes</button>
                    </form>
                @endif
            </div>
        </div>
    @else
        <div>Login first</div>
    @endauth
</body>
</html>