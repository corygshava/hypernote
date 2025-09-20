<x-layout>
    <x-slot:title>s</x-slot:title>
    <x-slot:basehref>../</x-slot:basehref>
    <x-slot:goback>yes</x-slot:goback>
    <x-slot:pagename>Edit post</x-slot:pagename>

    @auth
        <div class="content fullheight t3">
            <div class="formguy spacy-md mycon w3-center slide-in-bottom">
                <span class="h3">Edit Post</span>

                
                @if (auth()->id() != $post['user_id'])
                <div class="spacy-md w3-center">
                    You arent allowed to edit posts you didnt create
                    </div>
                    <a href="javascript:history.back()" class="btn themetxt"><i class="fa fa-chevron-left"></i> go back</a>
                @else
                    <?php
                        $thebody = json_decode($post['body']);
                    ?>
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

                        <select name="privacy_s" id="privacy_s">
                            <option value="" disabled selected>-- pick a privacy status --</option>
                            <option value="private">private (only you can see it)</option>
                            <option value="public">public (anyone can see it)</option>
                            <option value="unlisted">unlisted (anyone can see it if they have the link)</option>
                        </select>
                        <input type="text" name="post_title" id="post_title" value="{{ $post['title'] }}" placeholder="type the title here">
                        <textarea name="post_message" id="post_message" placeholder="type the title here" rows="6">{{ $thebody }}</textarea>
                        <button class="btn outline"><i class="fa fa-save"></i> save changes</button>
                    </form>
                @endif
            </div>
        </div>
    @else
        <div>Login first</div>
    @endauth
</x-layout>