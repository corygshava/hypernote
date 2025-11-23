/*
	* Author: Cornelius Shava
	* Organisation: Haosel Kenya
	* Date: 27/07/2025
	* last modified: 08/10/2025
	* Time: 15:14
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
let navbar = undefined;

// runtime data
let windowHeight = 0;
let default_offset = 60;		// the default % of the item scrolled in before a scrolltrigger is activated
let current_item = undefined;	// the current item in view
let observer_started = false;	// for the liveclone

// event data
let latest_ScrollEvent = undefined;
let observer = undefined;
let c_inters = [];

let tg_btns = {};
let tg_contents = {};

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

	if(window['cpg'] == undefined){
		window['cpg'] = (url = window.location.href) => {
			let link = url.split('://')[1];
			let unquery = link.split('?')[0];
			let nodes = unquery.split('/');
			let anchors = nodes[nodes.length-1].split('#');
			let thefile = anchors[0];

			thefile = thefile == "" ? "index" : thefile;

			return thefile;
		};
	}

	if(window['specialcloze'] == undefined){
		window['specialcloze'] = (m,clb = undefined,dur = 300) => {
			m.animate([
					{opacity: 1},
					{opacity: 0}
				],
				{duration: dur + 1}
			);
			
			setTimeout(() => {
				if(typeof clb == 'function'){
					clb();
				}

				m.dataset.shown = 0;
				m.style.display = 'none';
			}, dur + 0);
		}
	}

// Ops - these do stuff
	// function poolers
	function uis_init() {
		console.log("running initialiser");
		alert_silent("running initialiser");
		// alert_info("running initialiser");

		// get UX critical parts
		// navbar links
		init_navbar();

		// alerters
		init_alerters();

		// content cloners
		init_copiers();

		// height reference
		init_measureRefs();

		// page parts
		init_pageparts();

		// scrollers
		init_scrollers();

		// UI aftereffects (sanitisers)
		let items = undefined;
		// let items: Array = undefined; // for better intellisense, disable before running

		// togglers
		items = init_togglers();
		console.log(`done with togglers, found ${items.length} ${plural("item",items.length)}`);

		// setup visibledata
		items = init_visibledata();
		console.log(`done with visibledata, found ${items.length} ${plural("item",items.length)}`);

		// auto links (gotos)
		init_gotos();

		// tabs
		init_tabs();
		init_tabs_legacy();

		// submitters (for forms)
		init_submitters();

		// onsubmits (attach to forms)
		init_onsubmits();

		// reseters (also for forms)
		init_reseters();

		// autorunners
		init_runmes();

		// livecloners
		init_cloners();

		// livecountdown
		init_countdowns();
	}

	function uis_afterfx() {
		// after effects once the uis are all initiated

		// make the sidenav links close the menu modal
		afterfx_sidenav();
	}

	// initialisers
	function init_measureRefs() {
		heightguy = document.querySelector('.heightguy');
		windowHeight = heightguy.offsetHeight;
	}

	function init_navbar() {
		navbar = document.querySelector('nav') || document.querySelector('[data-role="navbar"]');

		if(navbar != undefined){
			let naver = navbar.querySelector('#sitelinks');
			let navlinks = naver != undefined ? naver.querySelectorAll('a') : undefined;

			if(navlinks == undefined){
				alert_danger('sitelinks not found');
				console.log('sitelinks not found');
				return;
			}

			// alert_warning(`navlinks has [${navlinks.length}] links`);

			if(navlinks.length > 0){
				let curpage = window['pagename'] ?? cpg(window.location.href);
				let theid = -1,href = "";
				curpage = `./${curpage}`;

				navlinks.forEach((el,id) => {
					href = `./${cpg(el.href)}`;
					el.classList.remove("active");
					// el.innerHTML += ' -> nvr'; // test if it works
					el.dataset.curpage = `${el.href.toLowerCase()} | ${curpage} | ${id} | ${theid}`;

					if(href.toLowerCase() === curpage.toLowerCase()){
						// alert_dark("found one");
						theid = id;
					}
				})

				if(theid >= 0){
					navlinks[theid].classList.add('active');
					// alert_dark(curpage);
				}
			}
		}
	}

	function init_scrollers() {
		scrollers = document.querySelectorAll('[data-scroller]');
		const picker = "scrollers";

		let items = scrollers;
		items.forEach((el,id) => {
			/* note that
				* ton% are relative to the object
				* n% are absolute
			*/

			if(el.dataset.picker == undefined || el.dataset.picker !== picker){
				el.dataset.picker = picker;

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
			}
		});
	}

	function init_pageparts() {
		pageparts = document.querySelectorAll(partselector);
	}

	function init_togglers() {
		let items = undefined;
		const picker = "togglers";

		togglers = document.querySelectorAll('[data-toggler]');

		const runtoggler = (c,tog,dta) => {
			if(c){
				let tg = document.querySelector(tog);

				if(tg.dataset.shown == "1"){
					specialcloze(tg);
				} else {
					toggleShowB(tog,dta[0],dta[1]);
				}
			}else {
				toggleShowB(tog,dta[0],dta[1]);
			}
		}

		togglers.forEach((el,id) => {
			if(el.dataset.picker == undefined || el.dataset.picker !== picker){
				el.dataset.picker = picker;
				el.dataset.togglerid = id;
				let _toggle = el.dataset.toggler;
				let _onshow = el.dataset.onshow || "block";
				let _onhide = el.dataset.onhide || "none";
				let _special = el.dataset.special || 'no';
				
				// alert_success('addin toggler');
				
				el.addEventListener('click',() => {
					let a = _onshow;
					let b = _onhide;
					let c = _special == "yes";

					// alert_info(`running toggler: ${a}_${b}`);
					alert_silent(`runing toggler: ${id}`);

					if(!_toggle.includes(",")){
						runtoggler(c,_toggle,[a,b]);
						return;
					}

					let toToggle = _toggle.split(",");

					toToggle.forEach((tog, n) => {
						a = a.split(",")[0];
						b = b.split(",")[0];

						let _a = _onshow.split(",")[n] || a;
						let _b = _onhide.split(",")[n] || b;

						runtoggler(c,tog,[_a,_b]);
					})
				});
			}
		})

		items = togglers;
		return items;
	}

	function init_visibledata() {
		let items = document.querySelectorAll('[data-visibledata]');
		const picker = "visibledata";

		items.forEach((el, id) => {
			if(el.dataset.picker == undefined || el.dataset.picker !== picker){
				el.dataset.picker = picker;
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
			}
		});

		return items;
	}

	function init_copiers() {
		let copyguys = document.querySelectorAll('[data-copyme]');
		const picker = "copyguy";

		copyguys.forEach(el => {
			if(el.dataset.picker == undefined || el.dataset.picker !== picker){
				let tk = document.querySelector(el.dataset.copyme);
				
				if(tk != undefined){
					el.dataset.picker = picker;
					let dt = tk.innerHTML;

					el.innerHTML = dt;
				} else {
					alert_danger(`copy reference not found: ${el.dataset.copyme}`);
					console.log('init_copiers error: ',el);
				}
			}
		});
	}

	function init_gotos() {
		const picker = 'autolinks';
		let gotos = document.querySelectorAll('[data-goto]');

		gotos.forEach(el => {
			if(el.dataset.picker == undefined || el.dataset.picker !== picker){
				el.dataset.picker = picker;

				el.addEventListener('click',() => {
					let where = el.dataset.goto;
					let target = el.dataset.target == undefined ? "" : el.dataset.target;
					let features = el.dataset.features == undefined ? "" : el.dataset.features;
					let delay = el.dataset.delay == undefined ? 1 : Number(el.dataset.delay);

					setTimeout(() => {
						if(el.dataset.newwindow != undefined && el.dataset.newwindow == "yes"){
							window.open(where,target,features);
						} else {
							window.location.assign(where);
						}
					},delay);
				});
			}
		});
	}

	function init_tabs() {
		// tabs
		const tabs = document.querySelectorAll('[data-role="tab"]');
		const contents = document.querySelectorAll('[data-role="tab-content"]');

		tabs.forEach(tab=>{
			tab.addEventListener('click', ()=>{
				tabs.forEach(t=>t.classList.remove('active'));
				contents.forEach(c=>{
					c.classList.remove('active');
					// c.classList.add("slide-in-bottom");
				});
				tab.classList.add('active');
				document.getElementById(tab.dataset.tab).classList.add('active');
			});
		});
	}

	function init_tabs_legacy() {
		// tab inits
		const tabs = document.querySelectorAll('[data-role="tab-btn"]');
		const contents = document.querySelectorAll('[data-role="tab-content"]');

		let tg_dft = mekRandomString(4);

		// group tab buttons
		tabs.forEach((tab,id)=>{
			tab.dataset.picker = "tab handler";
			let tg = tab.dataset.tabgroup == undefined ? tg_dft : tab.dataset.tabgroup;
			
			if(tg_btns[tg] == undefined){
				tg_btns[tg] = [];
			}

			tab.dataset.tabgroup = tg;
			tg_btns[tg].push(tab);
		});
		
		contents.forEach((con,id)=>{
			con.dataset.picker = "tab handler";
			let tg = con.dataset.tabgroup == undefined ? tg_dft : con.dataset.tabgroup;

			if(tg_contents[tg] == undefined){
				tg_contents[tg] = [];
			}

			con.dataset.tabgroup = tg;
			tg_contents[tg].push(con);
		});

		// console.log(tg_btns,tg_contents);

		Object.keys(tg_btns).forEach((el,id) => {
			if(tg_contents[el] == undefined){
				alert_danger(`no tab contents found for group [${el}]`);
				return;
			} else {
				alert_silent(`tab contents found for group [${el}]`);
			}
			
			tg_contents[el].forEach((tgc,n) => {
				if(tgc.dataset.myid == undefined){
					tgc.dataset.myid = `${el}_${n}`;
				}
			})

			tg_btns[el].forEach((tgb,n) => {
				if(tgb.dataset.myid == undefined){
					tgb.dataset.myid = `${el}_${n}`;
				}

				tgb.addEventListener('click',() => {
					toggletab(el,n);
				})
			});
		});
	}

	function init_submitters() {
		let subs = document.querySelectorAll('[data-submitme]');
		const picker = "submitters";

		subs.forEach(el => {
			if(el.dataset.picked == undefined || el.dataset.picked !== picker){
				let sel = el.dataset.submitme;
				let notice = el.dataset.notice;
				let force = el.dataset.forceit;
				let force_con = force !== undefined && (force == "yes" || force == "true")
				let maform = document.querySelector(sel);

				if(maform != undefined){
					el.dataset.picked = picker;
					let btn = maform.querySelector('button[type="submit"]');

					if(btn == undefined){
						btn = document.createElement('button');
						btn.className = "w3-hide";
						btn.setAttribute('type','submit');

						maform.appendChild(btn);
					}

					el.addEventListener('click', () => {
						let _btn = maform.querySelector('button[type="submit"]');

						if(force_con){
							maform.submit();
						} else {
							console.log(_btn);
							// alert(_btn == undefined ? "we bad" : "we good");
							// return;
							maform.requestSubmit(_btn);
						}

						if(notice != undefined){
							alert_primary(notice);
						}
					});
				} else {
					alert_warning(`form: ${sel}, doesnt exist`);
				}
			}
		})
	}

	function init_onsubmits() {
		let subz = document.querySelectorAll('form[data-onsubmit]');
		const picker = "onsubmitters";

		subz.forEach((el,id) => {
			if(el.dataset.picker == undefined || el.dataset.picker !== picker){
				el.dataset.picker = picker;

				let fun = el.dataset.onsubmit;
				let _block = el.dataset.blockdefault;
				let nodefault = _block !== undefined && (_block == "yes" || _block == "true");

				el.addEventListener('submit',(e) => {
					// alert(`block: ${_block} -> ndft: ${nodefault ? 'yes' : 'no'}`);
					
					if(nodefault){
						e.preventDefault();
					}

					if(window[fun] != undefined){
						window[fun](el);
					} else {
						alert_danger('invalid submitter function');
					}
				})
			}
		})
	}

	function init_reseters() {
		let subs = document.querySelectorAll('[data-resetme]');
		const picker = 'reseters';

		subs.forEach(el => {
			if(el.dataset.picked == undefined || el.dataset.picked !== picker){
				let sel = el.dataset.resetme;

				let maform = document.querySelector(sel);

				if(maform != undefined){
					el.dataset.picked = picker;

					el.addEventListener('click', () => {
						maform.reset();
					});
				}
			}
		});
	}

	function init_runmes() {
		const picker = "autorunmes";
		const runs = document.querySelectorAll('[data-runme]');

		runs.forEach((el,id) => {
			if(el.dataset.picked == undefined || el.dataset.picked !== picker){
				let who = el.dataset.runme;
				const load_stats = 'alert_danger';

				if(who.includes('defined:')){
					// whatever follows MUST be defined by var
					let con = who.split(":")[1];
					let item = getconstval(con);
					let val = item;
					who = val;
					// alert_warning(`window[${con}] -> ${val}`);
					// return;
				}

				el.dataset.picked = window[who] == undefined ? undefined : picker;

				el.addEventListener('click', () => {
					if(window[who] == undefined){
						if(el.dataset.errorset == undefined){
							alert_danger(`function not found`);
							el.dataset.errorset = true;
						}
					} else {
						// alert_primary(`function [${who}] found`);
						window[who](el);
					}
				});
			}
		})
	}

	function init_cloners() {
		const picker = "livecloners";
		const cloners = document.querySelectorAll('[data-liveclone]');
		// const input_cloners = document.querySelector('[data-liveclone][data-clonemode="input"]');

		const updateTarget_input = (m,t) => {
			let tmp_par = t instanceof HTMLInputElement || t instanceof HTMLTextAreaElement;
			let t_par = tmp_par ? 'value' : 'innerHTML';

			tmp_par = m instanceof HTMLInputElement || m instanceof HTMLTextAreaElement;
			let m_par = tmp_par ? 'value' : 'innerHTML';

			t[t_par] = m[m_par];
		}

		const updateTarget_html = (dt) => {
			let m = dt[0];
			let ts = dt[1];

			if(m == undefined){
				alert_danger('caller isnt defined');
				return;
			} else if(ts == undefined || ts.length == 0){
				alert_danger('targets are undefined');
				return;
			}

			ts.forEach(t => {
				let tmp_par = t instanceof HTMLInputElement || t instanceof HTMLTextAreaElement;
				let t_par = tmp_par ? 'value' : 'innerHTML';

				tmp_par = m instanceof HTMLInputElement || m instanceof HTMLTextAreaElement;
				let m_par = tmp_par ? 'value' : 'innerHTML';

				t[t_par] = m[m_par];
			});
		}

		cloners.forEach((el,id) => {
			if(el.dataset.picked == undefined || el.dataset.picked !== picker){
				let targets = document.querySelectorAll(el.dataset.liveclone);

				if(targets.length > 0){
					el.dataset.picked = picker;
					el.dataset.clonemode = el.dataset.clonemode ?? 'html';

					if(el.dataset.clonemode.toLowerCase() == "input"){
						el.addEventListener('input',(e) => {
							targets.forEach(tt => {
								updateTarget_input(el,tt);
							});
						});

						// alert_info('listener set');
					} else {
						// change HTML when shit changes
						if(!observer_started){
							init_Observer(updateTarget_html,[el,targets]);
						}

						observer.observe(el, {
							childList: true,    // watch added/removed nodes
							subtree: true,      // watch all descendants
							attributes: true,   // watch attribute changes
							characterData: true // watch text content changes
						});
					}
				} else {
					console.log('liveclone targets: ',targets)
					el.dataset.error = "invalid targets";
				}
			}
		});
	}

	function init_countdowns() {
		const picker = "countdowns";
		const counters = document.querySelectorAll('[data-countdown]');

		counters.forEach((el,id) => {
			if(el.dataset.picked == undefined || el.dataset.picked !== picker){
				let contre = el.dataset.countdown;
				let items = [],suffixes = [],actions = [];
				let tmp_el = el;
				let now = new Date();

				if(!contre.includes(',')){
					tmp_el = contre == "this" || contre == "" ? tmp_el : document.querySelector(contre);
					if(tmp_el != undefined){
						items.push(tmp_el);
						suffixes.push(tmp_el.dataset.suffix);
						actions.push(tmp_el.dataset.endaction);
					} else {
						alert_danger('countdown element: [' + contre + '] is missing');
					}
				} else {
					contre.split(",").forEach(me => {
						tmp_el = me == "this" || me == "" ? tmp_el : document.querySelector(me);

						if(tmp_el != undefined){
							items.push(tmp_el);
							suffixes.push(tmp_el.dataset.suffix);
						} else {
							alert_danger('countdown element: [' + me + '] is missing');
						}
					})
				}

				// alert_info(contre);

				// start intervals and render the times
				items.forEach((me,i) => {
					let myinter = undefined;
					let suffix = suffixes[i] || '';
					let finalact = actions[i];

					c_inters.push(myinter);

					const endme = () => {
						clearInterval(myinter);
						
						if(finalact !== undefined){
							if(typeof window[finalact] == 'function'){
								window[finalact](me);
							} else {
								console.log('countdown error for: ',me);
							}
						}

						return 'EXPIRED!';
					}

					myinter = setInterval(() => {
						let end = me.dataset.targettime;
						// let endtym = new Date(end);
						me.innerHTML = startCountdown(end,1,endme,suffix);
					}, 1000);
				})
			}
		});
	}

	function init_alerters() {
		const picker = "alerters";
		const alerters = document.querySelectorAll('[data-alertme]');

		alerters.forEach((el,id) => {
			if(el.dataset.picked == undefined || el.dataset.picked !== picker){
				if(el.dataset.notme != undefined){
					el.dataset.skipped = "yes";
					return;
				}

				// alert_info("found an alerter")
				// let aldata = el.dataset.alertme.split(",");
				let toshow = el.dataset.alertme;
				let typ = el.dataset.type || (el.dataset.typ || 'info');
				let dur = el.dataset.duration || (el.dataset.dur || 5);

				// in case i ever implement an abbreviation dictionary
				// aldata[0] = getlonger(aldata[0]);

				el.addEventListener('click',() => {
					// showAlert(toshow,dur,thetype);
					window[`alert_${typ}`](toshow,dur);
				})
			}
		})
	}

	// aftereffects
	function afterfx_sidenav() {
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

// runtime functions
	// handles scroller and pageparts functionality
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

			if(el.dataset.scrolloffset){
				offset = Number(el.dataset.scrolloffset);
			}

			if(scrollAmt >= mytop - Number(offset)){
				current_item = el;

				if(el.dataset.scrollalive != null && el.dataset.function != null){
					if(el.dataset.hasrun == null || el.dataset.alwaysrun == "yes"){
						let tm = el.dataset.function || "none";
						el.dataset.hasrun = "yes";

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

// runtime events and utilities
	// start the functions as soon as the page is loaded
	window.addEventListener('load',() => {
		createextras();
		uis_init();

		setTimeout(() => {
			uis_afterfx();
		},afterfx_delay);

		pageloaded = true;
	})

	// run the scroll handler whenever there is a scroll event on the document
	window.addEventListener('scroll',event => {
		// for inspection purposes
		latest_ScrollEvent = event;

		if(pageloaded){
			handle_scrollers(event);
		}
	})

	function refreshUI(delay){
		if(delay == undefined || delay == 0 || typeof delay != 'number'){
			uis_init();
		} else {
			setTimeout(() => {
				uis_init();
			}, delay);
		}
	}

	function init_Observer(callme,args = undefined) {
		// destroy any current observer instance
		if(observer != undefined){observer.disconnect();}

		observer = new MutationObserver((changes) => {
			changes.forEach(change => {
				// alert_success('change occured');

				if(callme != undefined && typeof callme == "function"){
					callme(args);
				}
			})
		});

		// alert_warning('observer started');

		observer_started = true;
	}

// reusables (to be added to toappend.js later)
	function getval(tx,n) {
		// basically tx% of n
		let res = 0;
		// let amt = Number(tx.substr(0,tx.length - 1));
		let amt = Number(tx.split("%")[0]);

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

	function getconstval(name) {
		if(typeof name !== 'string'){return false;}
		try{
			let res = name in globalThis ? globalThis[name] : 'not found';
			return res;
		} catch {
			return false;
		}
	}

/** 
	 * TODO
	 * any shit to do goes here
	 *
	 * make the page parts thing in scrollers actually work
	 * 
*/