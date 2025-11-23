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

		<x-postmodal/>

		{{-- <x-postscode/> --}}
	@endif
</x-layout>