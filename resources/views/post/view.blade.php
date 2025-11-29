<?php
	$is_dev = config('app.debug');

	$sdata = $data['sitedata'];
	$_udata = $sdata['user_data'];
	$sitelink = $is_dev ? $_udata['dev_sitelink'] : $_udata['sitelink'];
	$postlink = "{$sitelink}post/$id";

	// dd($post);

	/*
	"id" => 1
    "created_at" => "2025-11-23 12:47:45"
    "updated_at" => "2025-11-23 12:47:45"
    "title" => "Welcome"
    "body" => ""welcome to hypernote, hope you'll make good use of it""
    "imgpath" => null
    "privacy_state" => "public"
    "user_id" => 1
	// */

    $connn = false;
    $rawbody = json_decode($post->body);
    $mybody = str_replace("[richtext]", "", $rawbody);
?>

<x-layout title="View post" pagename="chosen post" :nav_hide_override="$connn">
    <style>
        .topbar{display: none;}
    </style>
	@if ($post == null)
		<div class="spacy-md w3-center flow centroid" style="height: 80vh;">
            <div class="flow gap-sm">
                <i>{{$message}}</i>
                <div>
                    <a href="./feed" class="btn outline">view public posts</a>
                </div>
            </div>
		</div>
	@else
		<div class="spacy-md">
			<div class="postbox v2">
				<div class="w3-content-container">
					<span class="text-gld" data-subrole="creator">by <b class="themetxt">{{$post->myuser->name}}</b></span>
					<span class="h3" data-subrole="mytitle">{{$post->title}}</span>

					<div class="w3-display-topright">
						<button class="btn primary" data-copythis="#postlink" data-isquiet="no" data-successtxt="note copied successfully"><i class="fa fa-share"></i></button>
						<button class="btn primary" data-copythis="#modalmessage" data-isquiet="no" data-successtxt="note copied successfully"><i class="fa fa-copy"></i></button>
						<button class="btn outline" data-goto="./feed" data-toggler="[data-role='postmodal_']" data-special="yes"><i class="fa fa-times"></i> close</button>
					</div>
				</div>

				<div class="" data-subrole="mybody" id="modalmessage">
					@if (str_contains($post->body, '[richtext]'))
						{!! $mybody !!}
					@else
						{{ $mybody }}
					@endif
				</div>

				<span class="w3-hide" id="postlink">{{$postlink}}</span>

				<div class="flow left gap-tn" data-subrole="timestamps">
					<span class="text-muted text-gld w3-block">item created on <b class="themetxt">{{$post->created_at}}</b></span>
					<span class="text-muted text-gld w3-block">last update <b class="themetxt">{{$post->created_at}}</b></span>
				</div>
			</div>
		</div>

		<script>
			alert_info(`{!! $message !!}`);
		</script>
	@endif
</x-layout>