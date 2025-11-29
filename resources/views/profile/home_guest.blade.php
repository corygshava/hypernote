<x-slot:role>signup</x-slot:role>

        {{-- if a user session doesnt exist --}}
        <div class="w3-animate-zoom spacy-sm w3-border w3-border-red w3-text-red w3-hide">
            <span>you aint logged in</span>
        </div>

        <div class="tabnav flowline centroid gap-sm spacy-md w3-animate-opacity">
            <button class="btn outline active" data-role="tab-btn">login</button>
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
        <div class="flow centroid slide-in-bottom" data-role="tab-content" data-shown="0">
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