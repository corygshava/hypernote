@props([
	'post' => null,
])

@if ($post == null)
	<i>no post passed</i>
@else
	<?php
		$me = $post;
		$datemade = $post['created_at'];
		$dateedit = $post['updated_at'];
		$myid = $post['id'];
		$title = $post['title'];
		$msg = json_decode($post['body']);
		$creator = $post->myuser->name;

		$rawbody = showwords($msg,10);
		$rawbody = substr($rawbody,0,50);
		// $mybody = str_replace("\n", "<br>", $rawbody);
		$mybody = str_replace("[richtext]", "", $rawbody);
	?>

	<div class="postbox card" 
		data-creator="{{ $creator }}" data-datemade="{{ $datemade }}" data-dateedit="{{ $dateedit }}" 
		data-title="{{ $title }}" data-msg="{{ $msg }}" data-myid="{{ $myid }}"
	>
		<div class="">
			<span class="text-gld">by <b class="themetxt">{{ $creator }}</b> {!! indicateStatus($post['privacy_state']) !!}</span>
			<span class="h3">{{ $title }}</span>
		</div>

		<div class="">
			@if (str_contains($post->body, '[richtext]'))
				{!! $mybody !!}
			@else
				{{ $mybody }}
			@endif
			...
		</div>

		<div class="flow left gap-tn">
			<span class="text-muted text-gld w3-block">item created on <b class="themetxt">{{ $datemade }}</b></span>
			<span class="text-muted text-gld w3-block">last update <b class="themetxt">{{ $dateedit }}</b></span>
		</div>

		@auth
			@if ($me['user_id'] == auth()->user()->id)
				<form action="./delete-post/{{ $post['id'] }}" method="post" class="w3-display-topright spacy-sm delOverlay">
					@csrf
					@method('DELETE')
					<a href="./edit-post/{{ $post['id'] }}" class="btn outline"><i class="fa fa-edit"></i></a>
					<button class="btn outline w3-text-red w3-border-red w3-hover-red"><i class="fa fa-trash"></i></button>
				</form>
			@endif
		@endauth
	</div>
@endif