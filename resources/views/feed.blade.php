<x-layout>
	<x-slot:title>All posts</x-slot:title>

	<div class="w3-center">
		<h2>All posts</h2>
	</div>

	@if (count($posts) == 0)
		<div class="w3-center spacy-md">
			<div>
				<i>no posts yet</i>
			</div>
		@auth
			<div class="spacy-md">
				<a class="btn primary" href="./"><i class="fa fa-plus"></i> create one</a>
			</div>
		@endauth
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
		?>

		<div class="posts_list">
		@foreach ($posts as $me)
			<?php
				$datemade = $me['created_at'];
				$dateedit = $me['updated_at'];
				$myid = $me['id'];
				$title = $me['title'];
				$msg = json_decode($me['body']);

				$creator = $me->myuser->name;
			?>

			<div class="postbox" data-creator="{{ $creator }}" data-datemade="{{ $datemade }}" data-dateedit="{{ $dateedit }}" data-title="{{ $title }}" data-msg="{{ $msg }}" data-myid="{{ $myid }}">
				<div class="">
					<span class="text-gld">by <b class="themetxt">{{ $creator }}</b></span>
					<span class="h3">{{ $title }}</span>
				</div>

				<div class="">
					{{ showwords($msg, 10) }} ...
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
		@endforeach
		</div>

		<div class="mymodal" data-role="postmodal" data-shown="0">
			<div class="modal-content slide-in-bottom">
				<button class="w3-btn w3-display-topright" onclick="toggleShow(`[data-role='postmodal']`);"><i class="fa fa-times"></i></button>

				<div class="postbox v2">
					<div class="">
						<span class="text-gld" data-subrole="creator">by <b class="themetxt">{{ $creator }}</b></span>
						<span class="h3" data-subrole="mytitle">{{ $title }}</span>
					</div>

					<div class="" data-subrole="mybody">
						
					</div>

					<div class="flow left gap-tn" data-subrole="timestamps">
						<span class="text-muted text-gld w3-block">item created on <b class="themetxt">{{ $datemade }}</b></span>
						<span class="text-muted text-gld w3-block">last update <b class="themetxt">{{ $dateedit }}</b></span>
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
	@endif
</x-layout>