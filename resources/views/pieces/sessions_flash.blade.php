        @if ($errors->any())
			<script>
				@foreach ($errors->all() as $err)
					alert_danger(`{{$err}}`,10);
				@endforeach
			</script>
		@endif

		@if (session()->has('message'))
			<script>alert_success(`{{session('message')}}`,15);</script>
		@endif
