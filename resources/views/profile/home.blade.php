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

        <x-addpostmodal/>

		<x-mypostmodal/>

		<x-postscode/>