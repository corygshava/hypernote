/*
	* Author: Cornelius Shava
	* Organisation: Haosel Kenya
	* Date: 27/07/2025
	* last modified: 17/08/2025
	* Time: 2:00
	* Email: corygprod@duck.com
	* File: coryG_UIOps.js
*/

/*
	* NOTE:
		DONT, and i repeat DONT CHANGE ANYTHING unless you know what youre doing and you've been given recorded permission to do so.
		this code relies heavily on coryG_UIOps.css and w3.css to display certain items correctly
		if you want to modify how the items look you should do so via the css file and only modify here if no other way is convenient
		note that this file is mostly for runtime behaviour rather than how something looks
		also its alot easier to change via css than over here so no need to go through the trouble
*/

// selectors
let partselector = '.part';		// the selector that defines a section of a page

// code data
let afterfx_delay = 700;
let pageloaded = false;

// UI components
let scrollers = [];
let togglers = [];
let pageparts = [];
let heightguy = undefined;

// runtime data
let windowHeight = 0;
let default_offset = 60;		// the default % of the item scrolled in before a scrolltrigger is activated
let current_item = undefined	// the current item in view

// event data
let latest_ScrollEvent = undefined;

// Startup functions - these must run before everything else

	// makes reference elements to be used by the Ops
	function createextras() {
		let m = undefined;
		let container = document.body;

		// height reference
		m = document.createElement('div');
		m.className = "heightguy w3-red";
		m.style.height = "100vh";
		m.style.width = "100vw";
		m.style.pointerEvents = "none";
		container.appendChild(m);
		console.log("created height reference",m);

		// back to top guy
		m = undefined;
		m = document.createElement('div');
		m.className = 'upbtn';
		// data-scrollstart="0%" data-scrollend="100%" data-classtoggle="showme" data-scroller
		m.dataset.scrollstart = '0';
		m.dataset.scrollend = '90%';
		m.dataset.classtoggle = 'showme';
		m.dataset.scroller = '';
		m.innerHTML = `<div class="cap w3-black w3-btn">Go to top</div>
			<button class="w3-btn w3-black w3-text-white themehover" onclick="window.location.assign('#')"><i class="fa fa-arrow-up"></i></button>
		`;
		container.appendChild(m);
		console.log("created the back to top anchor");
	}

// Ops - these do stuff

	// initialiser
	function uis_init() {
		console.log("running initialiser");

		// get UX critical parts

		// scrollers
		scrollers = document.querySelectorAll('[data-scroller]');
		scrollers.forEach(el => {el.dataset['picker'] = 'scrollers';});

		// page parts
		pageparts = document.querySelectorAll(partselector);

		// height reference
		heightguy = document.querySelector('.heightguy');
		windowHeight = heightguy.offsetHeight;

		// UI aftereffects (sanitisers)
		let items = undefined;

		// togglers
		togglers = document.querySelectorAll('[data-toggler]');
		togglers.forEach((el,id) => {
			el.dataset.togglerid = id;

			el.addEventListener('click',() => {
				let a = el.dataset.onshow || "block";
				let b = el.dataset.onhide || "none";
				toggleShowB(el.dataset.toggler,a,b);
			})
		})
		items = togglers;
		console.log(`done with togglers, found ${items.length} ${plural("item",items.length)}`);

		// setup visibledata
		// let items: Array = undefined; // for better intellisense, disable before running

		items = document.querySelectorAll('[data-visibledata]');

		items.forEach((el, id) => {
			// assumes the data is in the form 0,0,1
			let vizdata = el.dataset.visibledata.split(","),screen = ["small","medium","large"];
			let mid = 0,xclass = new Array();

			for (let x = 0; x < vizdata.length; x++) {
				let me = vizdata[x];
				let afix = parseInt(me) == 0 ? "hide" : "show";
				let wot = `w3-${afix}-${screen[x]}`;
				xclass.push(wot);
			}

			xclass.forEach(a => {
				el.classList.add(a);
			});
		})

		console.log(`done with visibledata, found ${items.length} ${plural("item",items.length)}`);

		items = scrollers;
		items.forEach((el,id) => {
			/* note that
				* ton% are relative to the object
				* n% are absolute
			*/
			let startat = el.dataset.scrollstart !== undefined ? el.dataset.scrollstart : 'me';
			let endat = el.dataset.scrollend !== undefined ? el.dataset.scrollend : '0%';
			let mytop = el.offsetTop;
			let s_height = el.scrollHeight;
			let scrollspace = mytop + s_height;

			el.dataset.debugdata = `${startat} / ${windowHeight}`;
			// console.log("why the fak")

			// sanitises scrollstart
			if(startat == "me"){
				startat = mytop;
			} else if(startat.includes('to')){
				// conclude later
				// object relative scrollstart
				let tmp_str = startat.substr(2,startat.length);
				el.dataset.debugdata += ` | ${startat} -> ${tmp_str}`;
				startat = getval(tmp_str,s_height) + mytop;
				el.dataset.debugdata += ` | ${startat} -> ${getval('12%',100)}`;
			} else if(startat.includes('%')){
				el.dataset.debugdata += ` | ${startat}`;
				startat = getval(startat,windowHeight);
				el.dataset.debugdata += ` | ${startat} -> ${getval('12%',100)}`;
			}

			// fix to make it consistent with what i said earlier
			// sanitises scrollend
			if(endat == "me"){
				endat = scrollspace;
			} else if(endat.includes('to')){
				// conclude later
				// object relative scrollend
				let tmp_str = startat.substr(2,startat.length);
				el.dataset.debugdata += ` [ender] : ${endat} -> ${tmp_str}`;
				endat = getval(tmp_str,scrollspace);
				el.dataset.debugdata += ` | ${endat}`;
			} else if(endat.includes('%')){
				el.dataset.debugdata += ` [ender] : ${endat}`;
				endat = getval(endat,(mytop + windowHeight));
				el.dataset.debugdata += ` | ${endat}`;
			}

			el.dataset.mytop = mytop;
			el.dataset.scrollstart = startat;
			el.dataset.scrollend = endat;
		});


		// content cloners
		let copyguys = document.querySelectorAll('[data-copyme]');
		copyguys.forEach(el => {
			el.dataset['picker'] = 'copyguy';

			let tk = document.querySelector(el.dataset.copyme);
			let dt = tk.innerHTML;

			el.innerHTML = dt;
		});
	}

	function uis_afterfx() {
		// after effects once the uis are all initiated

		// make the sidenav links close the menu modal
		let sdnavs = document.querySelectorAll('.sidebar');

		if(sdnavs.length > 0){
			sdnavs.forEach((sdnav,id) => {
				if(sdnav.dataset.ignoredefault != undefined && sdnav.dataset.ignoredefault != "no"){
					return;
				}
				let lnks = sdnav.querySelectorAll('a');
				let mlk = "sdlink_" + id;
				sdnav.dataset.mlk = mlk;

				let sel = sdnav.dataset.myparent == undefined ? `[data-mlk=${mlk}]` : sdnav.dataset.myparent;

				lnks.forEach((el,id) => {
					el.addEventListener('click',() => {
						toggleShowB(sel,'flex','none');
					})
				})
			});
		}
	}

	// handles scroller functionality
	function handle_scrollers(e) {
		// the idea is to run through all scrollers find out what to do once they are chosen and do it

		// get current window scroll
		let scrollAmt = window.scrollY;

		windowHeight = heightguy.offsetHeight;

		let absolute_sfactor = (scrollAmt / windowHeight);
		// console.log(`${scrollAmt} / ${windowHeight} = ${absolute_sfactor}`);

		// handle logic for page parts
		pageparts.forEach(el => {
			let mytop = el.offsetTop;
			let offset = default_offset;

			if(item.dataset.scrolloffset){
				offset = Number(item.dataset.scrolloffset);
			}

			if(scrollAmt >= sectiontop - Number(offset)){
				current_item = el;

				if(item.dataset.scrollalive != null && item.dataset.function != null){
					if(item.dataset.hasrun == null || item.dataset.alwaysrun == "yes"){
						let tm = item.dataset.function || "none";
						item.dataset.hasrun = "yes";

						// integrate later
						// handlecode(tm,item);
					}
				}
			}
		});

		// for the scrollbys
		scrollers.forEach(el => {
			let logger = el.querySelector('.logger');

			let s_start = Number(el.dataset.scrollstart);
			let s_end = Number(el.dataset.scrollend);
			let s_classes = el.dataset.classdata;

			let isvalid = (scrollAmt < s_end) && (scrollAmt >= s_start)
			let s_prg = 0;
			let theclass = "ddr5";
			let otherclass = "ddr3";

			if(isvalid){
				s_prg = scrollAmt / s_end;
			}

			if(el.dataset.classtoggle != undefined && isvalid){
				// el.classList.add(el.dataset.classtoggle);
			}

			if(s_classes != undefined){
				let theid = isvalid ? 0 : 1;
				let otherid = (theid + 1) % 2;
				let tmpcls = s_classes.includes(",") ? s_classes.split(",") : [s_classes,""];
				theclass = tmpcls[theid];
				otherclass = tmpcls[otherid];

				if(el.className.includes(otherclass)){
					el.classList.remove(otherclass);
				}
				if(!el.className.includes(theclass)){
					if(el.className == ""){
						el.className = theclass;
					} else {
						el.classList.add(theclass);
					}
				}
			}

			if(logger){
				logger.innerHTML = `[${isvalid}] ${s_prg * 100}% | ${s_classes} -> ${theclass} / ${otherclass} [${el.className}]`;
			}
		})
	}

// runtime events and helpers

	// start the functions as soon as the page is loaded
	window.addEventListener('load',() => {
		createextras();
		uis_init();
		setTimeout(() => {
			uis_afterfx();
		},afterfx_delay);

		pageloaded = true;
	})

	window.addEventListener('scroll',event => {
		// for inspection purposes
		latest_ScrollEvent = event;

		if(pageloaded){
			handle_scrollers(event);
		}
	})

	function refreshUI(){
		uis_init();
	}


// reusables (add to toappend.js later)
	function getval(tx,n) {
		// basically tx% of n
		let res = 0;
		let amt = Number(tx.substr(0,tx.length - 1));

		// basically tx% of n
		if(tx.includes('%')){
			res = n * (amt / 100);
		}

		// console.log(`getval : ${amt} * ${n} -> ${res}`);

		return res;
	}

	function openlink(link) {
		window.open(link,"_blank")
	}

/* TODO
	* any shit to do goes here
*/