/*
	*	Hi there, Cory here, this is a script full of functions to make your work  easier
	*	Go check out www.nebulaworks.42web.io/res/ to find out how to use it or download the latest version
	*	last modified on 12/nov/24 at 11:49
	*	small mod on 3rd August 2026
	*	enjoy
*/

//common variables

var lbs = localStorage;
var sessionSupported = true;
var sbs = sessionSupported ? sessionStorage : lbs;

//common reusables

function toggleShowB(me,ifoff,iffon) {
	//requires data-shown parameter to be attached to HTML object me
	var item = document.querySelector(me);
	var def = Number(item.dataset.shown);

	if(def == 0){
		item.style.display = ifoff;
		item.dataset.shown = 1;
	} else {
		item.style.display = iffon;
		item.dataset.shown = 0;
	}
}

function toggleShowC(me,rn) {
	//requires data-shown parameter to be attached to HTML object me
	var item = rn == 1 ? me : document.querySelector(me);
	var def = Number(item.dataset.shown);

	if(def == 0){
		item.style.display = 'block';
		item.dataset.shown = 1;
	} else {
		item.style.display = 'none';
		item.dataset.shown = 0;
	}
}

function toggleClass(what,class1,class2,rno) {
	var item = Number(rno) == 1 ? what : document.querySelector(what);
	if(item.className == class1){
		item.className = class2;
	} else {
		item.className = class1;
	}
	//item.className.replace(class1,class2);
}

function seTxt(what,element,no,mth) {
	var rn = Number(no);
	var item = mth == 1 ? element : document.querySelector(element);
	if(rn == 1){
		item.innerHTML = what;
	} else if (rn == 2){
		item.innerHTML += what;
	} else {
		item.innerHTML = what + item.innerHTML;
	}
}

function setTxt(what,element,no,mth = 1) {
	var rn = Number(no);
	var item = mth == 1 ? element : document.querySelector(element);
	if(rn == 1){
		item.innerHTML = what;
	} else if (rn == 2){
		item.innerHTML += what;
	} else {
		item.innerHTML = what + item.innerHTML;
	}
}

function seTnpt(what,element,mth = 1,rno) {
	var initial,rn = Number(mth);
	var item = Number(rno) == 1 ? element : document.querySelector(element);
	if(rn == 1){
		item.value = what;
	} else if (rn == 2){
		item.value += what;
	} else {
		initial = item.value;
		item.value = what + initial;
	}
}

function setElArray(part1,max,part2,element,start,rno) {
	var item = document.querySelector(element),outp,final = '';

	for(var x = start;x <= max;x++){
		outp = part1 + x + part2;
		final += outp;
	}

	seTxt(final,element,rno);
}

function GetElement(me) {
	var outp = document.querySelector(me);
	return me;
}

function tabSwitch(no,series,norm,select,mode = 'classswitch') {
	var items = document.querySelectorAll(series);
	console.log('tabswitch items ',items);
	window['globaldebug'] = items;

	norm = norm != '' ? norm : mekRandomString(3);
	select = select != '' ? select : mekRandomString(3);

	if(no > items.length){
		no = no % items.length;
	}

	items.forEach((item,id) => {
		if(item == undefined){
			alert_danger('invalid item');
			return;
		}

		globaldebug = item;

		let cl = id != no ? 'add' : 'remove';
		let cl2 = id == no ? 'add' : 'remove';

		item.classList[cl](norm);
		item.classList[cl2](select);
	})
}

function tabSwitch2(no,series,norm,select) {
	var items = document.querySelectorAll(series);

	if(no > items.length){
		no = no % items.length;
	}

	console.log(items,no)

	if(items.length > 0){
		if(items[no] == undefined){
			console.log("item doesnt exist")
			return;
		}
	} else{
		console.log("items dont exist")
		return;
	}

	for (var i = 0; i < items.length; i++) {
		items[i].style.display = norm;
	}

	items[no].style.display = select;
}

function toggleContent(what,part1,part2,mth) {
	let item = mth == 1 ? what : document.querySelector(what);

	if(item.innerHTML == part1){
		item.innerHTML = part2;
	} else {
		item.innerHTML = part1;
	}
}

//localStorage and sessionStorage utilities

function checkIt(what,dft,loc){
	loc = Number(loc);
	if(loc == 1){
		if(lbs.getItem(what) == null){lbs.setItem(what,dft);}
	} else {
		if(sbs.getItem(what) == null){sbs.setItem(what,dft);}
	}
}

function setIt(what,val,loc){
	loc = Number(loc);
	if(loc == 1){
		lbs.setItem(what,val);
	} else {
		sbs.setItem(what,val);
	}
}

function removeIt(what,loc){
	loc = Number(loc);
	if(loc == 1){
		if(lbs.getItem(what) != null){lbs.removeItem(what);}
	} else {
		if(sbs.getItem(what) != null){sbs.removeItem(what);}
	}
}

function getIt(me,loc) {
	// meant to return a value
	var out;
	loc = Number(loc);

	if(loc == 1){
		out = lbs.getItem(me);
	} else {
		out = sbs.getItem(me);
	}

	return out;
}

// -----added on 9/10/2023 ---------- around 2-3 years after these ^

// new ops 

// this one removes array elements from a certain index going forwards

function splitfrom(what,startat){
	var it = [];
	for(let x=startat;x<what.length;x++){
		it.push(what[x]);
	}
	return it;
}

// -----added on 6/3/2024 ---------- around 4 months after these ^

function showIt(selector,display){
	let item = document.querySelector(selector);
	display = display == undefined ? "block" : display;
	if(item != undefined){
		item.style.display = display;
	}

	item.dataset.shown = 1;
}

function getRandom(min,max) {
	let res = 0;
	if(max == undefined && min == undefined){
		res = Math.random();
	} else if (max == undefined){
		res = math.random() * min;
	} else {
		res = lerp(min,max,Math.random());
	}
	return res;
}

function lerp(min,max,factor) {
	// factor must always be in the range 0,1
	/* lerp calculation functions (they both work tho)
		a,b,f
		= ((b - a) * f) + a
		= ((1 - f) * a) + (t * b)
	*/

	return (min + (factor * (max - min)));
}

function getRandomColor(hasopacity) {
	let res = (!hasopacity) ? `rgb(${getRandom(0,255)},${getRandom(0,255)},${getRandom(0,255)})` : `rgba(${getRandom(0,255)},${getRandom(0,255)},${getRandom(0,255)},${getRandom()})`;
	return res;
}

// -----added on 26/3/2024 ---------- around 20 days after these ^

function toggleClass2(what,class1,class2) {
	let item = document.querySelector(what);
	if(item != null){
		let m = item.className;
		let con = m.includes(class1);
		m = (con) ? m.replace(class1,class2) : m.replace(class2,class1);

		item.className = m;
	}
}

// -----added on 19/4/2024 ---------- around 23 days after these ^

function randomRange(min,max) {
	return (Math.random() * (max - min)) + min;
}

function fakecurve(min,max,val){
	let half = lerp(min,max,0.5);
	let myval = (val > half) ? (val % half) : (val % half) + half;
}

function realcurve(min,max,val) {
	// body...
	let factor = val;

	// Ensure factor is between 0 and 1
	factor = Math.min(1, Math.max(0, factor));

	// Calculate the value using a Bezier curve distribution
	let t = factor < 0.5 ? 2 * factor : 2 * (1 - factor);
	let value = min + t * t * (3 - 2 * t) * (max - min);

	return value;
}

// -----added on 4/7/2024 ---------- around 76 days after these ^

function toggleClass3(sel,c1,c2) {
	let item = GetElement(sel);

	if(item != null){
		item.classList.replace(c1,c2);
	}
}

// -----added on 7/7/2024 ---------- around 3 days after these ^

function copyToClipboard(selector) {
	// Get the element by selector
	const element = document.querySelector(selector);
	
	if (element) {
		// Create a temporary textarea element to hold the text
		const tempTextarea = document.createElement('textarea');
		tempTextarea.value = element.innerText || element.value; // Use innerText or value depending on element type
		document.body.appendChild(tempTextarea);
		
		// Select the text
		tempTextarea.select();
		tempTextarea.setSelectionRange(0, 99999); // For mobile devices
		
		// Copy the text to clipboard
		try {
			const successful = document.execCommand('copy');
			console.log(successful ? 'Text copied to clipboard' : 'Unable to copy text');
		} catch (err) {
			console.error('Oops, unable to copy', err);
		}
		
		// Remove the temporary textarea
		document.body.removeChild(tempTextarea);
	} else {
		console.error(`Element not found for selector: ${selector}`);
	}
}

// -----added on 9/7/2024 ---------- around 2 days after these ^

function isvalidid(id,list) {
	return id >= 0 && id < list.length;
}

// -----added on 11/7/2024 ---------- around 2 days after these ^

function getrange(min,max,amt) {
	return (amt - min ) / (max - min);
}

// -----added on 12/7/2024 ---------- around 1 day after these ^

function formatTime(n) {
	const timeUnits = [
		{ name: 'year', duration: 31536000 },
		{ name: 'month', duration: 2592000 },
		{ name: 'week', duration: 604800 },
		{ name: 'day', duration: 86400 },
		{ name: 'hour', duration: 3600 },
		{ name: 'minute', duration: 60 },
		{ name: 'second', duration: 1 }
	];

	let remsecs = n;
	let stringparts = [];

	for (var x = 0; x < timeUnits.length; x++) {
		let unit = timeUnits[x];

		if (remsecs >= unit.duration){
			let secs = Math.floor(remsecs / unit.duration);
			remsecs %= unit.duration;
			stringparts.push(`${secs} ${plural(unit.name,secs)}`);
		}
	}

	return stringparts.join(',');
}

function formatTime2(n) {
	const timeUnits = [
		{ name: 'year', duration: 31536000 },
		{ name: 'month', duration: 2592000 },
		{ name: 'week', duration: 604800 },
		{ name: 'day', duration: 86400 },
		{ name: 'hour', duration: 3600 },
		{ name: 'minute', duration: 60 },
		{ name: 'second', duration: 1 }
	];

	let remsecs = n;
	let finObj = [];

	for (var x = 0; x < timeUnits.length; x++) {
		let unit = timeUnits[x];

		if (remsecs >= unit.duration){
			let secs = Math.floor(remsecs / unit.duration);
			remsecs %= unit.duration;
			finObj.push({num: secs,title: plural(unit.name,secs)});
		}
	}

	return finObj;
}

// -----modified on 2/9/2024 ---------- around 50 days after these ^

function plural(wad,n) {
	wad = `${wad}`;
	if (n === 1) {
		res = wad;
	} else if (wad.endsWith('us')) {
		res = wad.slice(0, -2) + 'i';
	} else if (wad.endsWith('s')) {
		res = wad + 'es';
	} else if (wad.endsWith('ay')) {
		res = wad + 's';
	} else if (wad.endsWith('y')) {
		res = wad.slice(0,-1) + 'ies';
	} else {
		res = wad + 's';
	}

	return res;
}

// -----modified on 3/9/2024 ---------- around 1 day after this ^

function toggleShow(me) {
	//requires data-shown parameter to be attached to HTML object me
	let item = document.querySelector(me),def = 0;
	let ondisplay = item.dataset.on == undefined ? "block" : item.dataset.on;

	if(item.dataset.shown == null){
		def = item.style.display == "none" ? 0 : 1;
	} else {
		def = Number(item.dataset.shown);
	}

	if(def == 0){
		item.style.display = ondisplay;
		item.dataset.shown = 1;
	} else {
		item.style.display = 'none';
		item.dataset.shown = 0;
	}
}

function setdp(num,dp) {
	// reduces decimal places in a number
	let res = Number((((num - Math.floor(num)) + "").padStart(dp,"0")).substring(2,2 + dp)) / (10 ** dp);
	res += Math.floor(num);
	
	return res;
}

function getdps(num) {
	let rem = num - Math.floor(num);
	let res = (rem + "").substring(2,2 + (rem.length));
	return res;
}

// -----modified on 2/11/2024 ---------- around 63 days after this ^

// to append to superscript (from the diamondeng gig)

// update comments for setxt onto the latest one

function startCountdown(targetDate,format,ifexpired,suffix) {
	ifexpired = ifexpired == undefined ? 'PASSED!' : ifexpired;
	suffix = suffix == undefined ? '' : suffix;
	format = format == undefined ? 0 : format;

	const target = new Date(targetDate).setHours(0, 0, 0, 0);

	function updateCountdown() {
		const now = new Date().getTime();
		const timeLeft = target - now;
		let outxt = '';

		if (timeLeft <= 0) {
			return ifexpired;
		}

		const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
		const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
		const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

		if(format == 0){
			outxt = `${days} ${plural('day',days)}, ` +
				`${String(hours).padStart(2, '0')} hours, ` +
				`${String(minutes).padStart(2, '0')} min ` +
				`${String(seconds).padStart(2, '0')} sec` +
				suffix;
		} else {
			outxt = `${days}:` +
				`${String(hours).padStart(2, '0')}:` +
				`${String(minutes).padStart(2, '0')}:` +
				`${String(seconds).padStart(2, '0')}` +
				suffix;
		}

		return outxt;
	}

	return updateCountdown();
}

function findIndex(arr, searchString) {
	return arr.findIndex(element => element.includes(searchString));
}

function typetext(duration,word) {
	let letr = 0,wad = "";

	let myinter = setInterval(() => {
		letr += 1;
		wad = `${word.slice(0,letr)}_`;
		subtxt.innerHTML = `${wad}`;

		if(letr >= word.length){
			clearInterval(myinter);
		}
	},(duration * 1000) / (word.length))
}

function openinnewtab(url) {
    window.open(url, '_blank');
}

function clamp01(n,min,max) {
	min = min == undefined ? 0 : min;
	max = max == undefined ? 1 : max;

	let res = 0;

	res = (n > max) ? max : (n < min) ? min : n;

	return res;
}

function openWhatsApp(number) {
    const url = `https://wa.me/${number}`;
    window.open(url, '_blank', 'noopener,noreferrer');
}

function sendWhatsAppMessage(phoneNumber, message) {
    // Sanitize the phone number and encode the message for URL
    const sanitizedNumber = phoneNumber.replace(/\D/g, ''); // Remove non-digit characters using regex
    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${sanitizedNumber}?text=${encodedMessage}`;

    window.open(whatsappUrl, '_blank');
}

function hasDatePassed(dateString) {
    const inputDate = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    return inputDate < today;
}

function changeAllClasses(theclass,newclass) {
	let all = document.querySelectorAll(`.${theclass}`);

	all.forEach(el => {
		el.dataset.oldclass = theclass;
		el.dataset.changedclass = newclass;
		el.classList.add(newclass);
		el.classList.remove(theclass);
	});
}

function revertAllClasses() {
	// requires elements to be set up with data-oldclass and data-changedclass
	// basically switches around the class names

	let all = document.querySelectorAll('[data-oldclass]');

	all.forEach(el => {
		let newclass = el.dataset.oldclass;
		let theclass = el.dataset.changedclass;

		el.dataset.oldclass = theclass;
		el.dataset.changedclass = newclass;
		el.classList.add(newclass);
		el.classList.remove(theclass);
	})
}

function scrollToElement(sel) {
    const element = document.querySelector(sel);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function runAfter(what,delay) {
	setTimeout(() => {
		what();
	}, delay);
}

function animateCSSVariable(element, variable, from, to, duration, suffix) {
	// enables smooth transitioning of variables (doesnt work for colors though)
	suffix = suffix == undefined ? '' : suffix;
	const startTime = performance.now();

	function update() {
		const elapsedTime = performance.now() - startTime;
		const progress = Math.min(elapsedTime / duration, 1);
		const currentValue = lerp(from,to,progress);

		// Set the CSS variable
		element.style.setProperty(variable, `${currentValue}`);

		if (progress < 1) {
			requestAnimationFrame(update);
		}
	}

	requestAnimationFrame(update);
}
