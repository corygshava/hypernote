<x-layout>
	<x-slot:title>All public posts</x-slot:title>

	<div class="w3-center">
		<h2>All Public posts</h2>
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
		<x-poststyles/>
		<x-postfunctions/>

		<div class="posts_list">
		@foreach ($posts as $me)
			<x-mypostcard :post="$me"/>
		@endforeach
		</div>

		<div class="w3-center">
			{!! $posts->links('vendor.pagination.thecustom') !!}
		</div>

		<div class="mymodal" data-role="postmodal" data-shown="0">
			<div class="modal-content slide-in-bottom">
				<button class="w3-btn w3-display-topright" onclick="toggleShow(`[data-role='postmodal']`);"><i class="fa fa-times"></i></button>

				<div class="postbox v2">
					<div class="">
						<span class="text-gld" data-subrole="creator">by <b class="themetxt">Who</b></span>
						<span class="h3" data-subrole="mytitle">What</span>
					</div>

					<div class="" data-subrole="mybody">
						why
					</div>

					<div class="flow left gap-tn" data-subrole="timestamps">
						<span class="text-muted text-gld w3-block">item created on <b class="themetxt">when</b></span>
						<span class="text-muted text-gld w3-block">last update <b class="themetxt">how</b></span>
					</div>
				</div>
			</div>
		</div>

		<x-postscode/>
	@endif
</x-layout>