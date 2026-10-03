<script>
	// universal data
	privacy_state_atlas = [
		'draft',
		'public',
		'private',
		'unlisted',
		'temporary',
		'shadowed',
		'anonymous',
	];
</script>

<script>
	app_prefix = "hypernote_app_";
	pref_prefix = app_prefix;
	enableCache = true;

	let dyna_holder = undefined;
	let dyna_ui = undefined;
	let dyna_js = undefined;
	let dyna_loader = undefined;
	let min_loadtime = 400;

	// history system
	let browseHistory = [];
	let fullHistory = [];
	let crumbs = [];
	const crumbDepth = 4;

	routeAtlas = {
		profile : './ui/profile',
		my_notes : './ui/my_notes',
		my_feed : './ui/my_feed',
		my_following : './ui/my_following',

		// data getters
		get_my_notes: './data/my_notes',

		// operations
		add_edit_note: './op/add_edit_note',
	};
	pageNames = {
		profile : 'Your profile',
		my_notes : 'Your notes',
		my_feed : 'Your feed',
		my_following : 'recent posts',
	};
	iconAtlas = {
		profile: 'fas fa-user',
		my_notes: 'fas fa-pen-fancy',
		my_feed: 'fas fa-heart',
		my_following: 'fas fa-users',

		// post types
		public: 'fas fa-globe',
		private: 'fa fa-lock',
		unlisted: 'fas fa-unlink',
		anonymous: 'fa fa-user-secret',
	};
	timeranges = [
		"today",
		"yesterday",
		// "juzi",
		"this_week",
		"this_month",
		"this_year",
		"last_week",
		"last_month",
		"last_year",
		"lifetime"
	];

	window['classes'] = new Map();

	// initialisers
		function init_stuff() {
			updateSessionUI();
			init_ui();
			loadPage({template: './ui/my_notes', sidenote: 'my Notes'});
		}

		function init_ui() {
			if(dyna_ui == undefined){dyna_ui = document.querySelector('#dynamic_ui');}
			if(dyna_js == undefined){dyna_js = document.querySelector('#dynamic_js');}
			if(dyna_loader == undefined){dyna_loader = document.querySelector('#loader_ui');}
		}

		function updateSessionUI(p = undefined){
			console.log('updating session UI');

			// update user info displays
				let sels = {
					// '[data-role="user_name_display"]' : 	cur_user.name == undefined ? '--' : cur_user.name,
					// '[data-role="user_initials_display"]' :	cur_user.name == undefined ? '--' : cur_user.name.split(" ").map(s => {return s.slice(0,1);}).splice(0,2).join(""),
					// '[data-role="user_role_display"]' : 	cur_user.role_name == undefined ? '--' : cur_user.role_name,
					'[data-role="date_display"]' : 	formatDate(new Date()),
				};

				Object.keys(sels).forEach(k => {
					let uis = document.querySelectorAll(k);

					uis.forEach(ui => {
						ui.innerHTML = sels[k];
					});
				})

			// setup all icons where needed
				let _icons = document.querySelectorAll('[data-myicon]');
				_icons.forEach(_i => {
					let iclass = iconAtlas[_i.dataset.myicon.toLowerCase()] || 'fa fa-question';
					// let iclass = iconAtlas[_i.dataset.myicon.toLowerCase()] || 'fa-question';

					if(iclass == undefined){
						return;
					}

					_i.classList.add('fa');
					_i.classList.add(iclass.split(" ")[1]);
				});

				if(p == undefined){
					return;
				}

			// render all pagenames where needed
				let pnames = document.querySelectorAll('[data-role="pagename"]');
				pnames.forEach(pn => {pn.innerHTML = p.sidenote;});

			// reset session_csrf
				session_CSRF_token = document.querySelector('meta[name="csrf-token"]').content;

			// alert_warning('updating user');
			// console.log(cur_user);
		}

	// SPA ops
		function renderCrumbs() {
			console.log('rendering breadcrumbs');
		}
		window['loadPage'] = (p, after) => {
			let dft = {
				template: './ui/error',
				sidenote: 'new page',
				afterfx: undefined
			}
			p = {...dft,...p};

			// alert_dark(p.template);

			// run current UI after effects
			let wot = 'runOnLeave';
			if(typeof window[wot] == 'function'){
				window[wot]();
				killghost(wot);
			}

			wot = 'killOnLeave';
			if(window[wot] !== undefined){
				killghosts(window[wot]);
				delete(window[wot]);
			}

			// alert_info(`loading ${p.sidenote}`);
			// activateSidebar(true);
			// toggleSidenav(null, false);

			// anim data
			let tymin = {duration: 300,easing: 'ease-out', fill:'forwards'};
			// dyna_ui.innerHTML = "i c changes";
			dyna_ui.innerHTML = "";
			dyna_ui.animate([...fadeout],tymin);
			dyna_ui.style.pointerEvents = 'none';

			let dl = dyna_loader;
			dl.classList.remove('w3-hide');
			dl.animate([...floatin],{...tymin, duration: 700});

			document.body.classList.remove('modal-open');
			// let areaName = document.querySelector('#curArea');
			// areaName.innerHTML = 'loading...';

			function rundataverify() {
				alert_silent('verifying recieved data');
			}

			function load_the_ui() {
				let where = p.template;
				let who = p.sidenote;

				dyna_ui.innerHTML = '...';

				window[fetch_ui](p.template, {}, 'get').then(dt => {
					// alert_dark(`${who} loaded`);
					window['runOnAwake'] = () => {};

					dyna_ui.innerHTML = dt;
					// areaName.innerHTML = who;
					dyna_ui.style.pointerEvents = 'all';
					dyna_ui.animate([...fadein], tymin);
					dl.animate([...floatin].reverse(),{...tymin, duration: 700});

					fullHistory.push(p);
					browseHistory.push(p);

					compileJS(dyna_ui);
					updateSessionUI(p);
					renderCrumbs();
					refreshUI(300);

					setTimeout(() => {
						rundataverify();
						clear_alerts();
						alert_success('loaded');
					}, min_loadtime + 0.200);

				})
			}

			setTimeout(() => {
				load_the_ui();
			}, 400);
		}
		window['load_page'] = (el,topass) => {
			// let fun = el.dataset.afterfx !== undefined ? window[el.dataset.afterfx] : undefined;
			let passme = {
				template: el.dataset.link || './upi/error',
				sidenote: el.dataset.label || 'error',
				afterfx: el.dataset.afterfx || undefined,
			};

			if(passme.template.includes('defined:')){
				let i = passme.template.split(":")[1];
				passme.template = routeAtlas[i];
			}
			if(passme.sidenote.includes('defined:')){
				let i = passme.sidenote.split(":")[1];
				passme.sidenote = pageNames[i] || i;
			}

			// updateSidebar(passme);
			window['loadPage'](passme);
		}

		window['compileJS'] = (el) => {
			dyna_js.innerHTML = '';
			let scripts = el.querySelectorAll('script');
			console.log('codes: ',scripts);

			let d_ui = document.createElement('div');
			d_ui.dataset.ddx = mekRandomString(4);

			if(scripts.length > 0){
				scripts.forEach(s => {
					let coder = document.createElement('script');
					coder.textContent = s.innerHTML;
					s.innerHTML = '';

					d_ui.appendChild(coder);
				});

				// console.log('codes: ',scripts);
			}

			dyna_js.appendChild(d_ui);

			// running initializer operations on a separate thread
			// so that kaa kuna shida kwa code it wont stop the loadpage procedure
			setTimeout(() => {
				// run the awaken function
				if(typeof window['runOnAwake'] == 'function'){
					window['runOnAwake']();
				}
			}, min_loadtime);
		}

	// awlknawdnawk
		function sendform(m,callback,onfail,tosend = undefined) {
			// mekwark
			let hasencrypted = false;
			let ps = '';

			if(m.dataset.submitting === true){
				alert_danger('already submitting...');
				return;
			} else {
				alert_info('submitting form...')
			}

			// callback mgt
			if(callback == undefined){
				if(m.dataset.callback){
					console.log('callback: ',m.dataset.callback);
					// alert_dark('callback: ' + m.dataset.callback);

					callback = window[m.dataset.callback.trim()];
				} else {
					console.log('no fallback to the callback');
					// alert_dark('no fallback to the callback');
				}
			} else {
				if(typeof callback != "function"){
					console.log('trying to read from a window variable');
					// alert_dark('trying to read from a window variable');
					callback = window[callback];
				}
			}

			if(typeof callback == "function"){
				console.log('valid callback')
				// alert_dark('valid callback')
			} else {
				callback = (a) => {
					alert_success('process successful');
					console.log('default callback results: ',a);
				};
				console.log('not a function')
				// alert_dark('not a function')
			}

			// onfail mgt
			if(onfail == undefined){
				if(m.dataset.onfail){
					console.log('onfail: ',m.dataset.onfail);
					// alert_dark('onfail: ' + m.dataset.onfail);

					onfail = window[m.dataset.onfail.trim()];
				} else {
					console.log('no fallback to the onfail');
					// alert_dark('no fallback to the onfail');
				}
			} else {
				if(typeof onfail != "function"){
					console.log('trying to read from a window variable');
					// alert_dark('trying to read from a window variable');
					onfail = window[onfail];
				}
			}

			if(typeof onfail == "function"){
				console.log('valid onfail')
				// alert_dark('valid onfail')
			} else {
				onfail = (a) => {alert_danger('process failed')};
				console.log('not a function')
				// alert_dark('not a function')
			}

			// return;

			// callback = callback == undefined ? (m.dataset.callback ? window[m.dataset.callback] : undefined) : callback;

			callback = typeof callback == 'function' ? callback : () => {alert_info('invalid callback passed to sendform')};

			m.style.pointerEvents = "none";
			m.style.opacity = 0.2;
			m.dataset.submitting = true;

			if(m.admin_password !== undefined){
				hasencrypted = true;
				ps = m.admin_password.value;
				m.admin_password.value = encryptme(ps,encsetup.offset,encsetup.salt,encsetup.chunks);
			}

			let fdata = tosend == undefined ? getFormdata(m) : tosend;
			const sender = tosend == undefined ? window[fetch_bypass_fyls] : window[fetch_bypass];
			const as_is = tosend == undefined;
			let myres = undefined;
			let myaction = m.action;
			// console.log('fdata: ',fdata,'use as is: ',as_is);

			if(myaction.includes('defined:')){
				myaction = linkAtlas[myaction.split(':')[1]];
			}

			// add_edit_form.style.pointerEvents = "all";
			sender(myaction,fdata,'POST',true,as_is).then(d => {
				myres = d;

				// console.log(callback);
				// return;
				responseHandler(d,callback,{'el': m,'data': d});

				if(!d.success || !d.result){
					onfail(d);
				}
				// console.log("finally, the result is: ",myres);
				// callback(m);
			})
			.catch(err => {
				alert_danger(err.message);
				console.error(err);

				if(onfail !== undefined){
					onfail(err);
				}
			})
			.finally(() => {
				m.style.pointerEvents = "all";
				m.style.opacity = 1;
				m.dataset.submitting = false;

				if(hasencrypted){
					m.admin_password.value = ps;
				}
			})
		}

	// intent mgt
		function handle_intent() {
			// requires UI to have an intent map setup
			if(window['intent_map'] == undefined){
				return;
			}

			let intent = getpage_intent();
			intent = (intent == undefined) ? 'default' : intent;
			intent_map[intent]();
		}
		function getpage_intent() {
			let link = window.location.href;
			let intent = link.split('#');

			if(intent.length < 2){
				return undefined;
			}

			return intent[1];
		}
</script>
