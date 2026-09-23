var audiosources = new Array();
var theIntervals = new Array();
var progressInterval = undefined;

function setupSource(holder,dft) {
	let hold = document.querySelector(holder);
	if(hold != null){
		let el = document.createElement("audio");
		el.controls = false;
		el.src = (dft == undefined) ? "" : dft;
		hold.appendChild(el);
		item = hold.querySelector('audio');
		audiosources.push(item);
		return audiosources.length;
	} else {
		console.log("the holder doesnt exist");
		return undefined;
	}
}

function togglePlay(id){
	let theaud = audiosources[id];
	if(theaud.paused){
		theaud.play();
	} else {
		theaud.pause();
	}
}

function pauseme(n,con) {
	let theaud = audiosources[n];
	if(con){
		theaud.play();
	} else {
		theaud.pause();
	}
}

function updateSource(id,what) {
	let theaud = audiosources[id];
	theaud.src = what;
}

function checkPausedOrEnded(n) {
	let theaud = audiosources[n];
	return theaud.paused || theaud.ended;
}

function checkEnded(n) {
	let theaud = audiosources[n];
	return theaud.ended;
}

function checkPaused(n) {
	let theaud = audiosources[n];
	return theaud.paused;
}

function updateProgress(n,bar) {
	let theaud = audiosources[n];

	// if(progressInterval != undefined)
	progressInterval = setInterval(() => {
		if(checkEnded(0)){
			// clearInterval(progressInterval);
		} else {
			let ratio = theaud.currentTime / theaud.duration;
			let item = document.querySelector(bar);
			item.style.width = `${ratio * 100}%`;
		}
	}, 10);

	if(n >= theIntervals.length){
		theIntervals.push(progressInterval);
	}
}

function setProgress(n,what) {
	let theaud = audiosources[n];
	theaud.currentTime = what * theaud.duration;
}