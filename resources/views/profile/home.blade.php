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

            <div class="flowline gap-md overflow-safe overflow">
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
                <x-poststyles/>
		        <x-postfunctions/>

                @foreach ($posts as $post)
                    <x-mypostcard :post="$post"/>
                @endforeach

                <div class="w3-center">
                    {!! $posts->links('vendor.pagination.thecustom') !!}
                </div>
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
                        <option value="unlisted">unlisted (anyone can see it if they have the link)</option>
                    </select>
                    <input type="text" name="post_title" id="post_title" value="{{old('post_title')}}" placeholder="what do we call this adventure" autofocus>
                    <textarea name="post_message" id="post_message" rows="3" placeholder="what's on your mind">{{old('post_message')}}</textarea>
                    <button class="btn outline"><i class="fa fa-plus"></i> add post</button>
                </form>
            </div>
        </div>

		<x-mypostmodal/>

		<x-postscode/>