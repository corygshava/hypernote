@props([
	'appname' => 'Hypernote',
	'title' => '--',
	'shownav' => null,
	'nav_hide_override' => true,
	'pagename' => '??',
	'goback' => 'no',
])

<?php
	$appname = 'Hypernote';

	$is_dev = config('app.debug');

	$data = App\Http\Controllers\Controller::commondata();
	$sdata = $data['sitedata'];
	$_udata = $sdata['user_data'];
	$sitelink = $is_dev ? $_udata['dev_sitelink'] : $_udata['sitelink'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{{ $appname }} - {{ $title }}</title>

	@if (isset($basehref))
		<base href="{{$basehref}}">
	@endif

	
	<link rel="shortcut icon" href="favicon.png" type="image/png">
	<link rel="stylesheet" href="_assets/BS4/css/bootstrap.min.css">
	<link rel="stylesheet" href="_assets/css/fa-all.css">
	<link rel="stylesheet" href="_assets/css/styles.css">
	<link rel="stylesheet" href="_assets/css/w3.css">
	<link rel="stylesheet" href="_assets/css/coryG_base.css">
	<link rel="stylesheet" href="_assets/css/coryG_UIOps.css">
	<link rel="stylesheet" href="_assets/css/animations.css">
	<link rel="stylesheet" href="_assets/css/fonts.css">

	<!-- s-auto -->
	<link rel="stylesheet" href="_assets/css/s-auto.css">
	<link rel="stylesheet" href="_assets/css/s-auto/autoforms.css">

	<!-- Bootstrap JS (Optional) -->
	<script src="_assets/js/jquery-3.5.1.slim.min.js"></script>
	<script src="_assets/BS4/js/bootstrap.bundle.min.js"></script>
	<script src="_assets/js/SuperScript.js"></script>
	<script src="_assets/js/toappend.js"></script>
	<script src="_assets/js/coryG_UIOps.js"></script>
	<script src="_assets/js/customalerter.js"></script>
	<script src="_assets/js/app.js"></script>

	<style>
		.content{
			min-height: 80vh;
		}
		.topbar{
			position: sticky;
			top: 0;
			left: 0;
			border-bottom: 1px solid var(--clr-text-muted);
		}
	</style>

	<style>
		/* ---------- Post Card Base ---------- */
		.posts_list {
			display: flex;
			flex-direction: row;
			justify-content: center;
			align-items: center;
			flex-wrap: wrap;
			gap: var(--size-md);
			padding: var(--size-nm);
		}

		.postbox:not(.v2) {
			flex: 0 0 300px;
			position: relative;
			background: var(--clr-panelbg);
			border: 1px solid var(--clr-darkglass);
			border-radius: var(--roundness);
			padding: var(--size-sm);          /* spacy-sm */
			box-shadow: var(--themeshadow);
			cursor: pointer;
		}
		.postbox:not(.v2):hover {
			transform: translateY(-2px);
			box-shadow: var(--themeglow);
		}

		/* ---------- Header Row ---------- */
		.postbox > div:first-child {
			display: flex;
			flex-direction: column;
			gap: var(--size-tn);              /* gap-tn */
			border-bottom: 1px solid var(--clr-text-muted);
		}

		/* ---------- Content ---------- */
		.postbox > div:nth-child(2) {
			color: var(--clr-text);
			padding: var(--size-md) 0;
		}

		/* ---------- Footer ---------- */
		.postbox > div:nth-child(3) {
			border-top: 1px solid #222;
		}
	</style>

	<?php
		$hidenav = [
			'dashboard'
		];
		$special = [
			'signup'
		];

		// print_r($shownav);
		// echo "<br>";

		// $shownav = $shownav == null ? (isset($role) ? !(in_array($role,$hidenav)) : true) : $shownav;
		$isspecial = isset($role) ? in_array($role,$special) : false;
		$pname = isset($pagename) ? $pagename : 'All posts';

		// echo !isset($role) ? "its not up" : "it is";
	?>
</head>

<body>
	@auth
		@if ($shownav && $nav_hide_override === true)
			<div class="spacy-sm topbar flowline spread">
				<div class="flowline gap-sm centroid">
					@if (isset($goback) && $goback == "yes")
						<a href="javascript:history.back()" class="btn outline"><i class="fa fa-chevron-left"></i></a>
					@endif
					<b class="themetxt">{{ $appname }} - {{ $pname }}</b>
				</div>
				<div>
					@if ($isspecial)
						<a class="btn outline" href="./posts"><i class="fa fa-list"></i> public posts</a>
					@else
						<a class="btn outline" href="./"><i class="fa fa-home"></i> home</a>
						{{-- <i class="fa fa-list"></i> --}}
					@endif
				</div>
			</div>
		@else
			<div class="spacy-sm w3-hide">
				nav hidden!
			</div>
		@endif
	@else
		<div class="spacy-sm topbar flowline spread">
			<div class="flowline gap-sm centroid">
				<b class="themetxt">{{ $appname }}</b>
			</div>
			<div>
				@if ($isspecial)
					<a class="btn outline" href="./posts"><i class="fa fa-list"></i> public posts</a>
				@else
					<a class="btn outline" href="./"><i class="fa fa-home"></i> home</a>
				@endif
			</div>
		</div>
	@endauth

	<div class="content">
		{{ $slot }}
	</div>

	<script>
		const sitelink = `{!! $sitelink !!}`;
	</script>

	<footer class="spacy-md">
		&copy; CoryG prod
	</footer>
</body>
</html>