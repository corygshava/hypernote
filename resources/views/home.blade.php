<x-layout>
    <x-slot:title>Your Profile</x-slot:title>

    @auth
        <x-slot:role>dashboard</x-slot:role>
        {{-- if a user session exists --}}

        {{-- get deets --}}
        <?php
            $user = Auth::user();
            $uname = $user->name;
            $creator = "";
        ?>

        <script>
        @if (session('success'))
            alert_success(`{{ session('success') }}`);
        @endif
        @if (session('danger'))
            alert_danger(`{{ session('danger') }}`);
        @endif

        {{-- error handling --}}

        @if ($errors->any())
            @foreach ($errors->all() as $err)
                alert_danger(`{{$err}}`);
            @endforeach
        @endif
        </script>

        <div class="flow centroid">
            <div class="spacy-md w3-center">
                <span class="h3">Welcome <b class="themetxt">{{ $uname }}</b></span>
                <button class="btn outline" data-toggler="#newpostmodal" data-onshow="flex"><i class="fa fa-plus"></i> create post</button>
                <a class="btn outline" href="./feed"><i class="fa fa-list"></i> All posts</a>
                <form action="./logout" method="post" class="spacy-sm d-i-b">
                    @csrf
                    <button href="./logout" class="btn outline"><i class="fa fa-user-slash"></i> logout</button>
                    <!-- <a href="./posts" class="btn outline">view posts</a> -->
                </form>
            </div>
        </div>
        <hr>

        <div class="stack centroid gap-md spacy-md">
            <div class="hedsect">
                <span class="h3">Your posts</span>
            </div>

            <div class="flowline gap-md overflow-safe">
            @if (count($posts) == 0)
                <div class="w3-center spacy-md">
                    <div>
                        <i>no posts yet</i>
                    </div>
                    <div class="spacy-md">
                        <button class="btn outline" data-toggler="#newpostmodal" data-onshow="flex"><i class="fa fa-plus"></i> create post</button>
                    </div>
                </div>
            @else
                <?php
                    function showwords($str,$words=20){
                        $str = $str ?? "This is a long string that has way more than twenty words just for the sake of showing you how to cut it properly without breaking words apart like substr would do.";
                        $items = explode(' ', $str); // split into array of words

                        if(count($items) > $words){
                            $thechars = array_slice($items, 0, $words); // take first 20
                            $result = implode(' ', $thechars)." ...";
                        } else {
                            $result = $str;
                        }

                        return $result;
                    }
                    function indicateStatus($state = 'public'){
                        if($state === "private"){
                            return "<i class=\"fa fa-lock\"></i>";
                        } else {
                            return '';
                        }
                    }
                    
                    //print_r($posts);
                ?>

                @foreach ($posts as $post)
                    <?php
                        $me = $post;
                        $datemade = $post['created_at'];
                        $dateedit = $post['updated_at'];
                        $myid = $post['id'];
                        $title = $post['title'];
                        $msg = json_decode($post['body']);
                        $creator = $post->myuser->name;

                        $rawbody = showwords($msg,20);
                        $mybody = str_replace("\n", "<br>", $rawbody);
                    ?>

                    <div class="postbox" data-creator="{{ $creator }}" data-datemade="{{ $datemade }}" data-dateedit="{{ $dateedit }}" data-title="{{ $title }}" data-msg="{{ $msg }}" data-myid="{{ $myid }}">
                        <div class="">
                            <span class="text-gld">by <b class="themetxt">{{ $creator }}</b> {!! indicateStatus($post['privacy_state']) !!}</span>
                            <span class="h3">{{ $title }}</span>
                        </div>

                        <div class="">
                            {!! $mybody !!} ...
                        </div>

                        <div class="flow left gap-tn">
                            <span class="text-muted text-gld w3-block">item created on <b class="themetxt">{{ $datemade }}</b></span>
                            <span class="text-muted text-gld w3-block">last update <b class="themetxt">{{ $dateedit }}</b></span>
                        </div>
                        
                        <form action="./delete-post/{{ $post['id'] }}" method="post" class="w3-display-topright spacy-sm delOverlay">
                            @csrf
                            @method('DELETE')
                            <a href="./edit-post/{{ $post['id'] }}" class="btn outline"><i class="fa fa-edit"></i></a>
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

                    <select name="privacy_s" id="privacy_s">
                        <option value="private">private (only you can see it)</option>
                        <option value="public" selected>public (anyone can see it)</option>
                        <option value="unlisted" selected>unlisted (anyone can see it if they have the link)</option>
                    </select>
                    <input type="text" name="post_title" id="post_title" value="{{old('post_title')}}" placeholder="what do we call this adventure" autofocus>
                    <textarea name="post_message" id="post_message" rows="3" placeholder="what's on your mind">{{old('post_message')}}</textarea>
                    <button class="btn outline"><i class="fa fa-plus"></i> add post</button>
                </form>
            </div>
        </div>

		<div class="mymodal" data-role="postmodal" data-shown="0">
			<div class="modal-content slide-in-bottom">
				<button class="w3-btn w3-display-topright" onclick="toggleShow(`[data-role='postmodal']`);"><i class="fa fa-times"></i></button>

				<div class="postbox v2">
					<div class="">
						<span class="text-gld" data-subrole="creator">by <b class="themetxt">creator</b></span>
						<span class="h3" data-subrole="mytitle">title</span>
					</div>

					<div class="" data-subrole="mybody"></div>

					<div class="flow left gap-tn" data-subrole="timestamps">
						<span class="text-muted text-gld w3-block">item created on <b class="themetxt"></b></span>
						<span class="text-muted text-gld w3-block">last update <b class="themetxt"></b></span>
					</div>
				</div>
			</div>
		</div>

		<script>
			let boxes = undefined;
			let mdl = undefined;

			window.addEventListener('load', () => {
				boxes = document.querySelectorAll('.postbox');
				mdl = document.querySelector('[data-role="postmodal"]');

				init_boxes();
			});

			function init_boxes() {
				boxes.forEach((el,m) => {
					el.addEventListener('click',(e) => {
						console.log('click registered',e);
						if(e.target.className.includes('fa') || e.target.className.includes('btn')){
							return;
						}

						// alert_warning('warkin', 8 * Math.random());

						toggleShowB('[data-role="postmodal"]','flex','none');

						let ui_creator = mdl.querySelector('[data-subrole="creator"]');
						let ui_title = mdl.querySelector('[data-subrole="mytitle"]');
						let ui_mybody = mdl.querySelector('[data-subrole="mybody"]');
						let ui_timestamps = mdl.querySelector('[data-subrole="timestamps"]');

						ui_creator.innerHTML = `<span class="text-gld">by <b class="themetxt">${el.dataset.creator}</b></span>`;
						ui_title.innerText = `${el.dataset.title}`;
						ui_mybody.innerText = `${el.dataset.msg}`;
						ui_timestamps.innerHTML = `
							<span class="text-muted text-gld w3-block">item created on <b class="themetxt">${el.dataset.datemade}</b></span>
							<span class="text-muted text-gld w3-block">last update <b class="themetxt">${el.dataset.dateedit}</b></span>
						`;

						console.log(el.dataset.creator, el.dataset.title, el.dataset.msg, el.dataset.timestamps);
					})
				});
			}
		</script>
    @else
        <x-slot:role>signup</x-slot:role>

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
</x-layout>