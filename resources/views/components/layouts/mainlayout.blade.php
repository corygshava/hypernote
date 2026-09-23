<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Hypernote</title>

	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="stylesheet" href="./cbl/_assets/_vendor/BS4/css/bootstrap.min.css">
	<link rel="stylesheet" href="./cbl/_assets/css/fa-all.css">
	<link rel="stylesheet" href="./cbl/_assets/css/w3.css">
	<link rel="stylesheet" href="./cbl/_assets/css/coryG_UIOps.css">
	<link rel="stylesheet" href="./cbl/_assets/css/coryG_base.css">
	<link rel="stylesheet" href="./cbl/_assets/css/fonts.css">
	<link rel="stylesheet" href="./cbl/_assets/css/animate.css">
	<link rel="stylesheet" href="./cbl/_assets/css/mediaoptima.css">

	<link rel="stylesheet" href="_loc_assets/css/styles.css">

	<link rel="stylesheet" type="text/css" href="./cbl/_assets/css/inter_slop.css">
	<!-- <link rel="stylesheet" type="text/css" href="./cbl/_assets/css/kimi_slop.css"> -->

	<script src="./cbl/_assets/_vendor/misc/jquery-3.6.0.min.js"></script>
	<script src="./cbl/_assets/_vendor/misc/popper.min.js"></script>
	<script src="./cbl/_assets/_vendor/misc/html2canvas.min.js"></script>
	<script src="./cbl/_assets/_vendor/misc/jspdf.umd.min.js"></script>
	<script src="./cbl/_assets/_vendor/misc/qrcode.min.js"></script>
	<script src="./cbl/_assets/js/chart.js"></script>
	<script src="./cbl/_assets/js/SuperScript.js"></script>
	<script src="./cbl/_assets/js/toappend.js"></script>
	<script src="./cbl/_assets/_vendor/BS4/js/bootstrap.bundle.min.js"></script>

	<script src="./cbl/_assets/js/anims.js"></script>
	<script src="./cbl/_assets/js/coryG_UIOps.js"></script>
	<script src="./cbl/_assets/js/customalerter.js"></script>
	<script src="./cbl/_assets/js/super_encryptor.js"></script>
	<script src="./cbl/_assets/js/app.js"></script>
</head>
<body>
	{{ $slot }}
</body>
</html>
