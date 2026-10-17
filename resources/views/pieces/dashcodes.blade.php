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

	// localstorage prefs
	window['pref_lastlogin'] = `${app_prefix}_last_login_data`;
	window['pref_authdata'] = `${app_prefix}_authdata`;
	pref_auth = `${app_prefix}_authorizer_key`;

	let dyna_holder = undefined;
	let dyna_ui = undefined;
	let dyna_js = undefined;
	let dyna_loader = undefined;
	let min_loadtime = 400;

	// history system
	let browseHistory = [];
	let fullHistory = [];
	let crumbs = [];

	const _enc = {
		// o: 13,
		// s: 'hypnte_app',
	    "o": 21,
	    "s": "JaGGasax{",
	    "c": 7
	}
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

		// api actions
		api_login: './api/auth/login',
		get_cur_user: './api/auth/cur_user',
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
		"juzi",
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
			check_session_setup();
		}

		function init_ui() {
			if(dyna_ui == undefined){dyna_ui = document.querySelector('#dynamic_ui');}
			if(dyna_js == undefined){dyna_js = document.querySelector('#dynamic_js');}
			if(dyna_loader == undefined){dyna_loader = document.querySelector('#loader_ui');}
		}

		function check_session_setup() {
			let authdata = {};
			let auth_key = '';

			const log_me_out = () => {
				alert_info('log in first');

				setTimeout(() => {
					lform = document.querySelector('[data-submitme="#logoutform"]');
					if(lform != undefined){
						lform.click();
					} else {
						location.assign('./login');
					}
				}, 3000);
			}

			const step0 = () => {
				let d = localStorage.getItem(pref_authdata);
				authdata = d == null ? undefined : JSON.parse(d);

				if(authdata === undefined){
					step1();
				} else {
					step4();
				}
			}

			// checks for login residuals from login page
			const step1 = () => {
				let last_login = JSON.parse(localStorage.getItem(pref_lastlogin));

				if(last_login == undefined){
					log_me_out();
				} else {
					step2(last_login)
				}
			}

			// does stateless login
			const step2 = (l) => {
				// alert_info('starting your session',12);
				alert_info('starting your session');

				let dta = {
					"email": decryptme(l['des'],_enc.o,_enc.s),
					"password": l['code'],
				};

				console.log('data being sent',dta);
				// return;

				window[fetch_bypass](routeAtlas['api_login'], dta, 'POST').then(d => {
					responseHandler(d, step3, d);
				}).catch(err => {
					alert_danger(err.message());
					alert_info('retrying...');

					setTimeout(() => {
						step2();
					}, 1000)
				})
			}

			// sets the recieved token from the server
			const step3 = (d) => {
				alert_info('recieved result from server');
				console.log("the result recieved",d);

				let authobj = {
					value: d.sendme.token,
					...d.sendme
				};

				localStorage.setItem(pref_authdata, JSON.stringify(d.sendme));
				localStorage.setItem(pref_auth, JSON.stringify(authobj));
				localStorage.removeItem(pref_lastlogin);

				step4();
			}

			// gets the current user
			const step4 = () => {
				window[fetch_bypass](routeAtlas['get_cur_user'],{},'POST').then((u) => {
					if(u.success){
						responseHandler(u,step5,u);
					} else {
						log_me_out();
					}
				}).catch(err => {
					alert_danger(err.message());
					alert_info('retrying...');

					setTimeout(() => {
						step4();
					}, 1000)
				})
			}

			// sets the current user
			const step5 = (d) => {
				window['cur_user'] = d.sendme;
				continue_the_stuff();
			}

			const continue_the_stuff = () => {
				remove_big_loader();
				init_ui();
				loadPage({template: routeAtlas['my_notes'], sidenote: 'my Notes'});
			}

			step0();
		}

		function updateSessionUI(p = undefined){
			console.log('updating session UI');
			if(window['cur_user'] == undefined){
				window['cur_user'] = {};
			}

			// update user info displays
				// vars
				let u_initials = cur_user.name == undefined ? '--' : cur_user.name.split(" ").map(s => {return s.slice(0,1);}).splice(0,2).join("");
				let u_img = cur_user.profile_image == undefined ?
					`https://ui-avatars.com/api/?name=${u_initials.split('').join('+')}&color=FFFFFF&background=${getRandomColor(false,true).toUpperCase().substr(1)}`
					 : (cur_user.profile_processed ? cur_user.profile_image : `./media/profiles/${cur_user.profile_image}`);

				if(cur_user.profile_image == undefined){
					cur_user.profile_image = u_img;
					cur_user.profile_processed = true;
				}

				let sels = {
					'[data-role="user_name_display"]' : 	cur_user.name == undefined ? '--' : cur_user.name,
					'[data-role="u-name"]' : 	cur_user.name == undefined ? '--' : cur_user.name,
					'[data-role="user_initials_display"]' :	u_initials,
					'[data-role="u-name-short"]' : 			cur_user.name == undefined ? '--' : cur_user.name.split(" ")[0],
					// '[data-role="user_role_display"]' : 	cur_user.role_name == undefined ? '--' : cur_user.role_name,
					'[data-role="date_display"]' : 	formatDate(new Date()),
					'[data-role="u-avatar"]' : `${u_img}|src`
				};

				Object.keys(sels).forEach(k => {
					let uis = document.querySelectorAll(k);

					uis.forEach(ui => {
						let parts = sels[k].split("|");
						let prop = parts.length > 1 ? parts[1] : 'innerHTML';

						ui[prop] = parts[0];
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
