@props([
	'post' => null,
])


@if ($post == null)
	<i>No post defined</i>
@else
	<?php
		$me = $post;

		$datemade = $me['created_at'];
		$dateedit = $me['updated_at'];
		$myid = $me['id'];
		$title = $me['title'];
		$msg = json_decode($me['body']);

		$creator = $me->myuser->name;
	?>

	<div class="postbox card" data-creator="{{ $creator }}" data-datemade="{{ $datemade }}" data-dateedit="{{ $dateedit }}" data-title="{{ $title }}" data-msg="{{ $msg }}" data-myid="{{ $myid }}">
		<div class="">
			<span class="text-gld">by <b class="themetxt">{{ $creator }}</b></span>
			<span class="h3">{{ $title }}</span>
		</div>

		<div class="">
			{!! showwords($msg, 10) !!} ...
		</div>

		<div class="flow left gap-tn">
			<span class="text-muted text-gld w3-block">item created on <b class="themetxt">{{ $datemade }}</b></span>
			<span class="text-muted text-gld w3-block">last update <b class="themetxt">{{ $dateedit }}</b></span>
		</div>

		@auth
			@if ($me['user_id'] == auth()->user()->id)
				<form action="./delete-post/{{ $myid }}" method="post" class="w3-display-topright spacy-sm">
					{{-- <a class="btn outline" href="#"><i class="fa fa-car"></i></a> --}}
					<a class="btn outline" href="./edit-post/{{$myid}}"><i class="fa fa-edit"></i></a>
					@csrf
					@method('DELETE')
					<button class="btn outline w3-text-red w3-border-red w3-hover-red"><i class="fa fa-trash"></i></button>
				</form>
			@endif
		@endauth
	</div>
@endif