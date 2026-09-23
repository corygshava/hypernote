// added from Faith project on 27/02/2025 same day as these ^

function copytext1(txt) {
	let has_error = true;

	if(txt != "" || txt != undefined){
		let b = document.createElement('textarea');
		b.value = txt;
		document.body.appendChild(b);

		b.select()
		b.setSelectionRange(0,99000);

		try{
			let suc = document.execCommand('copy');
			console.log(suc ? "text copied" : "copying error occured!")
			has_error = false;
		} catch {
			alert("there was an error copying your text, please try again")
		}

		document.body.removeChild(b);
	}

	return !has_error;
}

// added from Maggy project on 27/02/2025 same day as these ^

function daysUntil(date) {
	let target = new Date(date).setHours(0, 0, 0, 0);
	let today = new Date().setHours(0, 0, 0, 0);
	let diff = (target - today) / (1000 * 60 * 60 * 24);

	if (diff < 0) return "passed";
	if (diff === 0) return "today";
	if (diff === 1) return "tomorrow";
	return `${diff} days remaining`;
}

// added from Shawn project on 27/02/2025 same day as these ^

function plural(wad,n) {
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
	} else if (wad.endsWith('_')){
		res = wad.substr(0,wad.length - 1);
	}else {
		res = wad + 's';
	}

	return res;
}

// added from betnare redesign on 27/02/2025 same day as these ^

function mekRandomString(charcount,useNums,useSymbols) {
	// setup defaults
	useNums = useNums == undefined ? true : useNums;
	useSymbols = useSymbols == undefined ? false : useSymbols;

	let letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&*()^~`";
	letters = useSymbols ? letters : letters.substr(0,26);

	let lettersLo = letters.toLowerCase();
	let nums = "1234578906";

	let options = letters + lettersLo;
	options += useNums ? nums : '';

	let res = '';
	let fin = [];

	for(let id = 0;id < charcount;id++){
		fin.push(options.split('')[Math.floor(Math.random() * options.length)]);
	};

	res = fin.join("");

	return res;
}

// added from diamondeng gig on 27/02/2025 same day as these ^

function startCountdown(targetDate,format,ifexpired,suffix) {
	ifexpired = ifexpired == undefined ? 'PASSED!' : ifexpired;
	suffix = suffix == undefined ? '' : suffix;
	format = format == undefined ? 0 : format;

	let tdate = Number(targetDate);

	const target = isNaN(tdate) ? new Date(targetDate) : new Date(tdate);

	// console.log(`target time: ${targetDate} -> `, target);
	// console.log(`new Date(${targetDate}) -> `, target);

	let updateCountdown = () => {
		const now = new Date().getTime();
		const timeLeft = target - now;
		let outxt = '';

		if (timeLeft <= 0) {
			return (typeof ifexpired == 'function') ? ifexpired() : ifexpired;
		}

		const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
		const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
		const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

		if(format == 0){
			outxt = days > 0 ? `${days} ${plural('day',days)}, ` : '';
			outxt +=
				`${String(hours).padStart(2, '0')} hours, ` +
				`${String(minutes).padStart(2, '0')} min ` +
				`${String(seconds).padStart(2, '0')} sec` +
				suffix;
		} else {
			outxt = days > 0 ? `${days}:` : '';
			outxt +=
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

function typetext(sel,duration,word) {
	let letr = 0,wad = "";
	let item = document.querySelector(sel);

	if(item != null){
		let myinter = setInterval(() => {
			letr += 1;
			wad = letr < word.length ? `${word.slice(0,letr)}_` : word;
			item.innerHTML = `${wad}`;

			if(letr >= word.length){
				clearInterval(myinter);
			}
		},(duration * 1000) / (word.length))
	} else {
		alert_danger('invalid selector for typetext');
	}
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
	console.log('whatsapp url: ', url);
	window.open(url, '_blank', 'noopener,noreferrer');
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

function animateCSSVariable(element, variable, from, to, duration) {
	const startTime = performance.now();

	function update() {
		const elapsedTime = performance.now() - startTime;
		const progress = Math.min(elapsedTime / duration, 1);
		const currentValue = from + (to - from) * progress;

		// Set the CSS variable
		element.style.setProperty(variable, `${currentValue}px`);

		if (progress < 1) {
		requestAnimationFrame(update);
		}
	}

	requestAnimationFrame(update);
}

// end of picked

// new from Nima

function toggleScrollable(selector) {
	let item = document.querySelector(selector);
	let factor = item.dataset.canscroll == undefined || item.dataset.canscroll == "" ? "yes" : item.dataset.canscroll;
	let b = factor.toLowerCase() == "yes";
	let scrollval = b ? "hidden" : "auto";
	let outval = b ? "no" : "yes";

	item.style.overflowY = scrollval;
	item.dataset.canscroll = outval;
}

function sendWhatsAppMessage(phoneNumber, message, newtab,delay) {
	// setup defaults
	newtab = newtab == undefined ? false : newtab;
	delay = delay == undefined ? false : delay;

	// Sanitize the phone number and encode the message for URL
	const sanitizedNumber = phoneNumber.replace(/\D/g, ''); // Remove non-digit characters using regex
	const encodedMessage = encodeURIComponent(message);
	const whatsappUrl = `https://wa.me/${sanitizedNumber}?text=${encodedMessage}`;

	setTimeout(el => {
		if(newtab){
			window.open(whatsappUrl, '_blank');
		} else {
			window.location.assign(whatsappUrl);
		}
	},delay);
}

function copycontent(start,destination,copyClasses,copytype,reqno){
	// this copies the contents of one element onto another with some extra features of course
	// copytype is either append,inner,outer (default is outer)

	if(start == undefined || destination == undefined){
		// the code needs a start and a destination so no dice
		let outlog = start == undefined && destination == undefined ? 'the code needs a start and a destination element' : (start == undefined ? 'this code needs a start element to copy from' : 'this code needs a destination element to copy to')
		if(debugmode)console.log(outlog);
		return;
	}

	copyClasses = copyClasses == undefined ? false : copyClasses;
	copytype = copytype == undefined ? "outer" : copytype.toLowerCase();
	reqno = reqno == undefined ? 0 : reqno;



	let startItem = reqno == 0 ? document.querySelector(start) : start;
	let destinationItem = reqno == 0 ? document.querySelector(destination) : destination;
	let toadd = destinationItem.dataset.appendclass;
	let toremove = destinationItem.dataset.removeclass;

	if(debugmode)console.log(copyClasses, copytype, reqno,start, destination, toadd, toremove);

	if(copytype == "append"){
		destinationItem.innerHTML += startItem.innerHTML;
	} else if(copytype == "inner") {
		destinationItem.innerHTML = startItem.innerHTML;
	} else {
		destinationItem.outerHTML = startItem.outerHTML;
	}

	if(copyClasses){
		destinationItem.className = startItem.className;
	}

	setTimeout(e => {
		// these only run if the destination has a data-appendclass or a data-removeclass
		if(toadd != undefined){

			let classes = toadd.split(",");

			classes.forEach(el => {
				if(debugmode)console.log(destinationItem.className,`adding ${el}`);
				destinationItem.classList.add(el);
				if(debugmode)console.log(destinationItem.className);
			})
		}

		if(toremove != undefined){
			if(debugmode)console.log('removing classes');

			let classes = toremove.split(",");

			classes.forEach(el => {
				destinationItem.classList.remove(el);
			})
		}
	},200);
}

// new from houseofjrm on 14/10/2025 around 229 days after these

function shufflelist(arr) {
	return arr
		.map(item => ({ item, sort: Math.random() }))
		.sort((a, b) => a.sort - b.sort)
		.map(({ item }) => item);
}

function add_to_storage(key, newObject) {
	try {
		const existingData = localStorage.getItem(key);
		const thelist = existingData ? JSON.parse(existingData) : [];

		// Validate that we have an array
		if (!Array.isArray(thelist)) {
			console.warn(`LocalStorage key "${key}" exists but is not an array. Overwriting with new array.`);
			thelist = [];
		}

		thelist.push(newObject);
		localStorage.setItem(key, JSON.stringify(thelist));

		return thelist;
	} catch (error) {
		console.error(`Error appending to localStorage key "${key}":`, error);
		throw error; // Re-throw to allow caller to handle
	}
}

function getpage(url) {
	let link = url.split('://')[1];
	let unquery = link.split('?')[0];
	let nodes = unquery.split('/');
	let anchors = nodes[nodes.length-1].split('#');
	let thefile = anchors[0];

	thefile = thefile == "" ? "index" : thefile;

	return thefile;
}

window['objtoquery'] = (obj, prefix = '') => {
	const pairs = [];

	for (const key in obj) {
		if (obj.hasOwnProperty(key)) {
			const fullKey = prefix ? `${prefix}[${key}]` : key;
			const value = obj[key];

			if (value !== null && typeof value === 'object' && !Array.isArray(value)) {
				pairs.push(objtoquery(value, fullKey));
			} else if (Array.isArray(value)) {
				value.forEach(item => {
					pairs.push(encodeURIComponent(fullKey + '[]') + '=' + encodeURIComponent(item));
				});
			} else {
				pairs.push(encodeURIComponent(fullKey) + '=' + encodeURIComponent(value));
			}
		}
	}

	return pairs.join('&');
}

// new from Jaymatt [19/03/26]
function reverseLerp(min,max,val) {
	return (val - min) / (max - min);
}
function getDateDiff(date1, date2, use_sec = true) {
	// console.log("date1: ",date1,typeof date1);
	// console.log("date2: ",date2,typeof date2);
	const time1 = new Date(date1);
	const time2 = new Date(date2);

	// console.log('di')
	const diffInMs = Math.abs(time2 - time1);
	const diffInSecs = Math.floor(diffInMs / 1000);
	const secs = diffInMs / 254879076.93333334;

	// console.log('oldtime: ',time1.getTime(),formatDate0(time1));
	// console.log('newtime: ',time2.getTime(),formatDate0(time2));
	// return 0;
	// alert_dark(`diff in ms: ${diffInMs}`,14);
	// alert_dark(`diff in secs: ${diffInSecs}`,14);

	return use_sec ? diffInSecs : diffInMs;
}


/**
 * Generates a QR code data URL from an encoded string
 *
 * requires qrcode.min.js [https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js]
 * activate the corian block to prevent errors if you dont have
 *
 * @param {string} encodedText - The text/URL to encode in the QR code
 * @param {number} size - Size of the QR code (default: 100)
 * @returns {Promise<string>} - Data URL of the QR code image
 */

// /*
function mekQRCode(encodedText, size = 100) {
    return new Promise((resolve, reject) => {
        try {
            // Create temporary container
            let tempDiv = document.createElement('div');
            tempDiv.style.display = 'none';
            document.body.appendChild(tempDiv);

            // Generate QR code
            let qrcode = new QRCode(tempDiv, {
                text: encodedText,
                width: size,
                height: size,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });

            // Wait for QR code generation to complete
            setTimeout(() => {
                let canvas = tempDiv.querySelector('canvas');
                if (canvas) {
                    let dataUrl = canvas.toDataURL('image/png');
                    document.body.removeChild(tempDiv);
                    resolve(dataUrl);
                } else {
                    document.body.removeChild(tempDiv);
                    reject(new Error('Failed to generate QR code'));
                }
            }, 100);
        } catch (error) {
            reject(error);
        }
    });
}
// */


// added from the timer project on [04/08/26] around 138 days after those
function formatDate(date) {
	if(!date){return '--';}

	return date.toLocaleDateString('en-US', {
		month: 'short',
		day: 'numeric',
		year: 'numeric'
	});
}
function formatDate0(date) {
	if(!date){return '--';}

	const d = new Date(date); // Ensures input is a Date object
	const day = String(d.getDate()).padStart(2, '0');
	const month = String(d.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
	const year = d.getFullYear();
	const hours = String(d.getHours()).padStart(2, '0');
	const minutes = String(d.getMinutes()).padStart(2, '0');
	const seconds = String(d.getSeconds()).padStart(2, '0');

	return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
}
function formatDate1(date,hastime = false,onlytime = false) {
	let now = new Date(date);
	let datepart = now.toLocaleDateString("en-US", { month: "short", day: "numeric" });
	let timepart = now.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
	let out = [datepart, timepart];

	if(hastime && !onlytime){
		return out.join(" , ");
	} else {
		if(!hastime){
			return datepart;
		} else {
			return timepart;
		}
	}
}
function formatNumber(n,dp = 0,locale = true) {
	if(Number(n) == NaN){
		alert_danger('invalid number passed for formatting');
		return 'NaN';
	} else {
		n = Number(n);
	}

	let parts = n.toString().split('.');
	let p1 = locale ? Number(parts[0]).toLocaleString() : Number(parts[0]);
	let p2 = parts[1] || '00';

	p2 = p2.slice(0,dp);
	res = dp == 0 || !(n.toString().includes('.')) ? p1 : `${p1}.${p2}`;

	return res;
}

// fileops
window['readTextFile'] = (toread,callback,isjson=false) => {
	let fr = new FileReader();
	fr.onload = (e) => {
		try{
			console.log('trying to read the file');
			console.log('reader input', e);
			if(isjson){
				callback(JSON.parse(e.target.result),e);
			} else {
				callback(e.target.result,e);
			}
		} catch(err){
			console.error(err);
		}
	};
	fr.readAsText(toread);
}

function killghost(who) {
	// kills ghost processes and functions
	// NOTE: this shit is OP as fukari so use it sparingly
	if(window[who] !== undefined){
		delete(window[who]);
	} else {
		// alert_danger('invalid killghost pass');
	}
}

function killghosts(stuff = []) {
	if(typeof stuff == 'object'){
		if(stuff.forEach === undefined){
			alert_silent('invalid killghosts pass')
			return;
		}
		stuff.forEach(s => {killghost(s);})
	} else {
		alert_silent('invalid killghosts pass');
	}
}

// added from a clanker on [07/09/26] around 34 days after those
// gets public ip address the world sees
async function getPublicIP() {
	try {
		const response = await fetch('https://api.ipify.org?format=json');
		const data = await response.json();
		return data.ip;
	} catch (error) {
		console.error('Failed to fetch public IP:', error);
		return 'Unknown';
	}
}