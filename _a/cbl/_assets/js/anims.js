const timing = {
	duration: 700,
	easing: 'ease-out',
	fill: 'forwards',
};

// animations
	let slideout = [
		{
			"transform": "translateX(0)",
			"opacity": 1
		},
		{
			"transform": "translateX(1000px)",
			"opacity": 0
		}
	];
	let slidein = [
		{
			"transform": "rotateX(-30deg) translateX(-300px) skewX(-30deg)",
			"opacity": 0
		},
		{
			"transform": "rotateX(0deg) translateX(0) skewX(0deg)",
			"opacity": 1
		}
	]

	let hideme = [
		{opacity: 0},
		{opacity: 0}
	]

	let fadeout = [
		{opacity: 1},
		{opacity: 0}
	]
	let fadein = [
		{opacity: 0},
		{opacity: 1}
	]

	let entrance = [
		{opacity: 0,"transform": "rotateX(-30deg) translateX(-300px) skewX(-30deg)",},
		{opacity: 1,"transform": "rotateX(0deg) translateX(0px) skewX(0deg)",}
	]

	let frames_slatein = [
		{opacity: 0,translate: "-20px 0"},
		{opacity: 1,translate: "0 0"}
	]

	let frames_spacen = [
		{opacity: 0,letterSpacing: "0"},
		{opacity: 1,letterSpacing: "7px"}
	];

	let floatin = [
		{opacity: 0,translate: '0 40px'},
		{opacity: 1,translate: '0 0'},
	];
