const fetch_bypass = "f_bps_" + mekRandomString(5);
const fetch_bypass_fyls = "f_bps_f_" + mekRandomString(5);
const fetch_ui = "f_ui_" + mekRandomString(5);
const simple_fetch = "s_fetch_" + mekRandomString(5);
const fetch_bypass_blob = "fbp_blob_" + mekRandomString(16);
const fetch_identifier = "viaFetch";
let curfun = "rnvar_" + mekRandomString(3);
let session_CSRF_token = "csrf_" + mekRandomString(12);

let enableCache = true;
let cache_lifetime_in_secs = 1200

// localstorage stuff
let app_prefix = 'coryg_app';
let pref_prefix = app_prefix;
let pref_auth = `${app_prefix}_authorizer_key`;

// for the confirmer mech
const confirm_callback = "rnvar_" + mekRandomString(12);
const confirm_canceller = "rnvar_" + mekRandomString(10);

let variableAtlas = {
	'get_input_inter': mekRandomString(12),
};

// runtime ui items
var the_modal = undefined;
var the_modal_toggler = undefined;
var confirm_modal = undefined;
var confirm_toggler = undefined;

// fetch utils
	curfun = fetch_bypass;
	if(window[curfun] == undefined){
		window[curfun] = async (p,dta = {},mt = 'POST',skipappend = false,use_as_is=false,is_quiet = false) => {
			try{
				alert_silent({to: p,data: dta,method: mt});

				mt = mt.toUpperCase();

				if(!skipappend && !use_as_is){
					dta[fetch_identifier] = 'yes';
					dta['source'] = 'makwldnnalwkndajkdnajwdn_testenviron';
				}
				// alert_dark(JSON.stringify(dta));

				let headers = {
					'X-Requested-With' : 'XMLHttpRequest',
					'X-CSRF-TOKEN' : session_CSRF_token,
					'Accept' : 'application/json',
					'Content-Type' : 'application/json',
				};

				const prff = JSON.parse(localStorage.getItem(pref_auth));

				// alert(prff);

				if (prff !== null) {
					headers['Authorization'] = `Bearer ${prff.value}`
				}

				if(mt == 'GET'){
					let pl = objtoquery(dta);
					if(p.includes('?')){
						p += `&${pl}`;
					} else {
						p += `?${pl}`;
					}
				}

				let body = mt == "GET" || mt == 'HEAD' ? null : (use_as_is ? dta : JSON.stringify(dta));

				// await alert_dark('sending info');

				// alert_info(JSON.stringify(headers));
				// console.log(`[${fetch_bypass}] passed_data: `,dta);
				// console.log(`[${fetch_bypass}] headers: `,headers);
				// console.log(`[${fetch_bypass}] request body: `,body);

				let s_data = {
					method: mt.toUpperCase(),
					headers: headers,
					// credentials: 'same-origin',
				};

				if(!(mt == "GET" || mt == 'HEAD')){
					s_data.body = body;
				}

				let req = await fetch(p,s_data);

				if(!req.ok){
					if(req.status == 422){
						throw new Error(`[${req.status}] -> some required fields are missing`);
					} else if(req.status == 419){
						throw new Error(`[${req.status}] -> Your session has expired, reload the page to continue`);
					}

					throw new Error(`[${req.status}] -> ${req.statusText}`);
				}

				return await req.json();
			} catch (error){
				if(!is_quiet){
					alert_danger(error);
				}

				console.error(error);
				throw new Error(error);
			}
		}
	}

	curfun = fetch_bypass_fyls;
	if(window[curfun] == undefined){
		window[curfun] = async (p,dta = {},mt = 'POST',skipappend = false,use_as_is=false) => {
			try{
				alert_silent({to: p,data: dta,method: mt});

				if(!skipappend && !use_as_is){
					dta[fetch_identifier] = 'yes';
				}
				// alert_dark(JSON.stringify(dta));

				let headers = {
					'X-Requested-With' : 'XMLHttpRequest',
					'X-CSRF-TOKEN' : session_CSRF_token,
					'Accept' : 'application/json',
					// 'Content-Type' : 'multipart/form-data',
				};
				const prff = JSON.parse(localStorage.getItem(pref_auth));
				if (prff !== null) {
					headers['Authorization'] = `Bearer ${prff.value}`
				}

				let body = mt == "GET" || mt == 'HEAD' ? null : (use_as_is ? dta : JSON.stringify(dta));

				// alert_info(JSON.stringify(headers));
				// console.log(`${fetch_bypass}: `,dta);

				let req = await fetch(p,{
					method: mt.toUpperCase(),
					headers: headers,
					// credentials: 'same-origin',
					body: body,
				});

				if(!req.ok){
					if(req.status == 422){
						throw new Error(`[${req.status}] -> some required fields are missing`);
					} else if(req.status == 419){
						throw new Error(`[${req.status}] -> Your session has expired, reload the page to continue`);
					}

					throw new Error(`[${req.status}] -> ${req.statusText}`);
				}

				return await req.json();
			} catch (error){
				alert_danger(error);
				throw new Error(error);
			}
		}
	}

	curfun = simple_fetch;
	if(window[curfun] == undefined){
		window[curfun] = async (p,dta = {},mt = 'POST') => {
			try{
				alert_silent({to: p,data: dta,method: mt});

				dta[fetch_identifier] = 'yes';
				// alert_dark(JSON.stringify(dta));

				mt = mt.toUpperCase();
				let body = mt == "GET" || mt == 'HEAD' ? null : JSON.stringify(dta);

				// alert_info(JSON.stringify(headers));
				// console.log(`${fetch_bypass}: `,dta);

				let req = await fetch(p,{
					method: mt,
					// credentials: 'same-origin',
					body: body,
				});

				if(!req.ok){
					throw new Error(`[${req.status}] -> ${req.statusText}`);
				}

				return await req.json();
			} catch (error){
				alert_danger(error);
				throw error;
			}
		}
	}

	curfun = "simple_fetcher";
	if(window[curfun] == undefined){
		window[curfun] = async (p,dta = {},mt = 'POST') => {
			try{
				alert_silent({to: p,data: dta,method: mt});

				dta['viaFetch'] = 'yes';
				// alert_dark(JSON.stringify(dta));

				my_CSRF_token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

				let headers = {
					'X-Requested-With' : 'XMLHttpRequest',
					'X-CSRF-TOKEN' : my_CSRF_token,
					'Accept' : 'application/json',
					'Content-Type' : 'application/json',
				};

				mt = mt.toUpperCase();
				let body = mt == "GET" || mt == 'HEAD' ? null : JSON.stringify(dta);

				// alert_info(JSON.stringify(headers));
				// console.log(`${fetch_bypass}: `,dta);

				let req = await fetch(p,{
					method: mt,
					// credentials: 'same-origin',
					headers: headers,
					body: body,
				});

				if(!req.ok){
					throw new Error(`[${req.status}] -> ${req.statusText}`);
				}

				return await req.json();
			} catch (error){
				alert_danger(error);
				throw error;
			}
		}
	}

	curfun = fetch_ui;
	if(window[curfun] == undefined){
		window[curfun] = async (p,dta = {},mt = 'POST',skipappend = false,use_as_is=false) => {
			try{
				let now = new Date();
				let mkey = `${pref_prefix}[${p}]`;
				let pkey = `${pref_prefix}mypermissions`;
				let local_cache_version = localStorage.getItem(mkey);
				let saved_perms = localStorage.getItem(pkey);

				let cache_saved = local_cache_version !== undefined && local_cache_version !== null;
				let perms_saved = saved_perms !== undefined && saved_perms !== null;

				// perms_saved = true;

				if(cache_saved && perms_saved && enableCache){
					let local_cache = JSON.parse(local_cache_version);
					// let myperms = getitempermissions(p);

					// if(mypermissions.includes(myperms) || myperms == undefined){
					if(true){
						let cache_age = getDateDiff(now, local_cache.saved_at);
						let usecache = (cache_age <= cache_lifetime_in_secs);
						// let isvalid = saved_perms.toLowerCase() == JSON.stringify(mypermissions).toLowerCase();
						let isvalid = true;

						if(usecache && isvalid){
							alert_dark('loading from cache');
							return local_cache.html;
						} else {
							if(!usecache){
								// alert_dark(`cache is old [${cache_age} / ${cache_lifetime_in_secs}s old], disposing`,12);
								localStorage.removeItem(mkey);
							} else {
								// alert_dark('cache is invalid');
							}
						}
					} else {
						// alert_dark('cache found but you dont have permissions');
						// alert_dark(`permissions required: ${myperms}`);
					}
				} else {
					// alert_dark('no cache found');
				}

				alert_silent({to: p,data: dta,method: mt});
				alert_info('loading from server, please wait');

				const prff = JSON.parse(localStorage.getItem(pref_auth));

				let headers = {
					'X-Requested-With' : 'XMLHttpRequest',
					'X-CSRF-TOKEN' : session_CSRF_token,
					'Accept' : 'application/json',
					'Content-Type' : 'application/json',
				};

				// alert(prff);

				if (prff !== null) {
					headers['Authorization'] = `Bearer ${prff.value}`
				}

				let s_data = {
					method: mt.toUpperCase(),
					headers: headers,
					// credentials: 'same-origin',
				};

				let req = await fetch(p,s_data);
				let final = await req.text();
				let cache = {
					saved_at: (new Date()).toISOString(),
					html: final
				};
				let cache_data = JSON.stringify(cache);

				// save cache for fast loading later
				localStorage.setItem(mkey,cache_data);
				// localStorage.setItem(pkey,JSON.stringify(mypermissions));

				return final;
			} catch (error){
				alert_danger(error);
				throw new Error(error);
			}
		}
	}

	curfun = fetch_bypass_blob;
	if(window[curfun] == undefined){
		window[curfun] = async (p,dta = {},mt = 'POST',skipappend = false,use_as_is=false) => {
			try{
				alert_silent(`fetching blob from [${p}]`);

				if(!skipappend && !use_as_is){
					dta[fetch_identifier] = 'yes';
				}
				// alert_dark(JSON.stringify(dta));

				let headers = {
					'X-Requested-With' : 'XMLHttpRequest',
					'X-CSRF-TOKEN' : session_CSRF_token,
					'Accept' : 'application/json',
					// 'Content-Type' : 'multipart/form-data',
				};
				const prff = JSON.parse(localStorage.getItem(pref_auth));
				if (prff !== null) {
					headers['Authorization'] = `Bearer ${prff.value}`
				}

				let body = mt == "GET" || mt == 'HEAD' ? null : (use_as_is ? dta : JSON.stringify(dta));

				// alert_info(JSON.stringify(headers));
				// console.log(`${fetch_bypass}: `,dta);

				let req = await fetch(p,{
					method: mt.toUpperCase(),
					headers: headers,
					// credentials: 'same-origin',
					body: body,
				});

				if(!req.ok){
					if(req.status == 422){
						throw new Error(`[${req.status}] -> some required fields are missing`);
					} else if(req.status == 419){
						throw new Error(`[${req.status}] -> Your session has expired, reload the page to continue`);
					}

					throw new Error(`[${req.status}] -> ${req.statusText}`);
				}

				return req.blob();
			} catch(error){
				console.error(error);
				alert_danger('error fetching blob: ' + error.message);
				throw new Error(error);
			}
		}
	}

	curfun = "loadUI";
	if(window[curfun] == undefined){
		window[curfun] = async ({p='/',mt='get',holder=undefined,prerun = () => {alert_info('loading...')},postrun=() => {alert_info('loaded')}}) => {
			if(holder === undefined){
				holder = dft_holder;
			}

			if(typeof prerun == "function"){
				prerun();
			}

			showloader();

			window[fetch_ui](p,{},mt).then(d => {
				hideloader();
				holder.innerHTML = d;
				refreshUI(300)

				if(typeof postrun == "function"){
					postrun();
				}
			})
		}
	}

	curfun = "log_visit";
	if(window[curfun] == undefined){
		window[curfun] = async (p,dta = {},mt = 'POST') => {
			let mypage = undefined;
			let base = document.querySelector('base');
			let c_page = cpg(window.location.href);

			if(base != undefined){
				// alert_info(base.href);
				c_page = window.location.href.replace(base.href,'');
			}

			// alert_info(c_page);
			window['simple_fetcher'](`${mainhost}/api/log_visit`,{where: c_page}).then(w => {
				if(w){
					alert_silent('wirked');
				} else {
					alert_silent('not wirked');
				}
			});
		}
	}

	function getFormdata(formElement) {
		if (!(formElement instanceof HTMLFormElement)) {
			throw new Error('Input must be a <form> element');
		}

		if (!formElement.reportValidity()) {
			return null; // Validation failed
		}

		return new FormData(formElement);
	}

	function responseHandler(w,callback,args = undefined,quiet = false,skip_prepro = false) {
		console.log('response_handler: ',w);

		const runcallback = () => {
			if(callback != undefined || typeof callback === 'function'){
				args = args ?? w;
				callback(args);
			}
		}

		if(skip_prepro){
			runcallback();
		}

		if(w.success){
			if(w.result){
				if(!quiet) {alert_success(w.message);}

				runcallback();
			} else {
				if(!quiet) alert_warning(w.message);
			}
		} else {
			alert_warning('sorry, you arent logged in. lets fix that',12);
			setTimeout(() => {
				// window.location.reload();
				// window.location.assign('./login');
			}, 3000);
		}
	}

	function openModal(id){
		toggleShow(`#${id}`);
	}

	// fetch partner functions
		function clear_view_cache() {
			let tokill = [];

			for(let c = 0;c < localStorage.length;c++){
				let key = localStorage.key(c);
				if(key.includes(app_prefix)){
					tokill.push(key);
				}
			}

			tokill.forEach(tk => {
				localStorage.removeItem(tk);
			})

			alert_dark('all view cache cleared');
		}

// initialisers
	curfun = "mekXtras";
	if(window[curfun] == undefined){
		window[curfun] = (m) => {
			// alert_info('xtras made');
			console.log('made the xtras');
			dft_holder = document.querySelector('#app_ui');

			// alert_info('making extras');
			alert_silent('making extras');

			// make the confirmer modal
			if(true && confirm_modal === undefined){
				let conid = "confirmerBox_" + mekRandomString(3);
				let desig = `#${conid}`;

				let b = document.createElement('div');

				b.className = "modal";
				// b.style.display = "none";
				b.id = conid;
				b.dataset.shown = "0";
				b.innerHTML = `
					<div class="modal-dialog modal-sm" role="document">
						<div class="modal-content themeround borderless panelbg w3-animate-zoom">
							<div class="modal-header">
								<span class="h3 modal-title" id="password_confo_title">Confirm your Password</span>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close" data-runme="password_confo_clearghost"><span aria-hidden="true">&times;</span></button>
							</div>
							<div class="modal-body">
								<!-- Multiple inputs -->
								<form id="confo_password" action="./op/change_password" data-onsubmit="confirmPassword" data-blockdefault="yes" data-callback="form_confo_afterfx">
									<div class="input-group-custom">
										<div class="form-group">
											<label class="form-label" for="admin_password">Your Password</label>
											<input type="password" class="form-control-custom" id="admin_password" name="admin_password" placeholder="enter your password for authorization" required>
										</div>
									</div>
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="mybtn secondary" data-dismiss="modal" data-runme="password_confo_clearghost" data-subrole="cancel">Cancel</button>
								<button type="button" class="mybtn primary" data-submitme="#confo_password" data-subrole="continue">Continue</button>
							</div>
						</div>
					</div>
				`;

				let btn = document.createElement('button');
				btn.classList.add('w3-hide');
				// btn.dataset.toggler = desig;
				// btn.dataset.onshow = "flex";
				btn.dataset.toggle = "modal";
				btn.dataset.target = desig;

				document.body.appendChild(b);
				document.body.appendChild(btn);
				confirm_modal = b;
				confirm_toggler = btn;
			}

			// make the common modal
			conid = "modalBox_" + mekRandomString(3);
			desig = `#${conid}`;

			b = document.createElement('div');

			b.className = "modal _mymodal fade";
			b.style.display = "none";
			b.id = conid;
			b.dataset.shown = "0";
			b.innerHTML = `
				<div class="modal-dialog" role="document">
					<div class="modal-content panelbg modal-md borderless themeround">
						<div class="modal-header">
							<div>
								<span class="modal-title h4" id="d_mdl_title">Modal title</span>
								<span class="modal-subtitle" style="font-size: 0.9rem" id="d_mdl_subtitle">default modal subtitle</span>
							</div>
							<button type="button themetxt" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true" class="modetxt">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							default modal contents
						</div>
						<div class="modal-footer">
							<button type="button" class="mybtn secondary sm" data-dismiss="modal" data-subrole="cancel">cancel</button>
							<button type="button" class="mybtn primary sm" data-subrole="continue">continue &raquo;</button>
							<div class="xtra"></div>
						</div>
					</div>
				</div>
			`;

			let btnguy = document.createElement('div');
			btnguy.id = 'common_modal_toggler_holder';
			btnguy.classList.add('w3-hide');
			btnguy.innerHTML = `
				<button type="button" class="btn btn-primary btn-sm w3-hide" id="common_modal_toggler" data-toggle="modal" data-target="${desig}" data-role="toggleModal">Launch Modal</button>
			`;

			document.body.appendChild(b);
			document.body.appendChild(btnguy);

			// alert_info('med modal');
			alert_silent('med modal');
			setTimeout(() => {
				the_modal = b;
				the_modal_toggler = btnguy.querySelector('button');
			},300);

			// refresh UI when everything is done
			refreshUI(100);
		}
	}

	callOnDocLoad.push({act: window[curfun]});

// html generators
	function mekstagger(delay,count) {
		let del = count * delay;
		return `animation-delay: ${del}ms;animation-fill-mode: forwards;opacity: 0;`;
	}
	function mekdisplay(what,startclasses = '',template='[val]') {
		let out = '';
		let trydate = new Date(what);

		if(what == null ) return 'null';
		if(what == undefined ) return 'undefined';
		if(trydate != 'Invalid Date' && (typeof what != 'object') && (("" + what).includes('-') || ("" + what).includes('/'))){
			return trydate.toLocaleDateString('en-US', {
						year: 'numeric',
						month: 'numeric',
						day: 'numeric',
						hour: 'numeric',
						minute: 'numeric'
					});
		}

		if(typeof what == 'object'){
			out += `<div class="${startclasses}">`;
			Object.keys(what).forEach(k => {
				val = `<b>${k}</b> : ${mekdisplay(what[k])}<br>`;
				out += template.replaceAll('[val]',val);
			})
			out += '</div>';
		} else if(typeof what == "array"){
			what.forEach(k => {
				out += `<div>${k}</div>`;
			})
		} else if(typeof what == 'string'){
			if(what.includes('http:') || what.includes('https:')){
				out += `<a href="${what}">${what.substr(0,50)}</a>`;
			} else {
				out += what;
			}
		} else {
			out += template.replaceAll('[val]',what);
		}

		return out;
	}

	function mekError(hed= 'Error',demo='an error happened',serious = false){
		let sertxt = serious ? `<button class="mybtn primary sm" onclick="window.location.reload()">reload page</button>` : '';

		return `
			<div class="spacy-mg w3-center slide-up flow gap-sm">
				${mekHeading(hed,'h4')}
				<b><i class="w3-text-red">${demo}</i></b>
				<div>
					${sertxt}
				</div>
			</div>
		`;
	}
	function mekUiAlert(msg,typ='danger',icon='fa fa-info') {
		return `
			<article class="spacy-sm border-${typ} uialert alert-${typ} text-${typ} slide-up" style="border-left: 3px solid;">
				<i class="${icon} mr-2"></i><b>${msg}</b>
			</article>
		`;
	}
	function mekStandin(demo='an error happened',class_ovr = 'modetxt',cta_link = undefined,cta_text = undefined){
		let cta = '';

		cta = cta_link != undefined ?
			`<a href="${cta_link}" class="btn outline"><i class="fa fa-plus"></i> ${cta_text || 'go now'}</a>` :
			'';

		return `
			<div class="spacy-mg w3-center slide-up">
				<b><i class="${class_ovr}">${demo}</i></b>
				${cta}
			</div>
		`;
	}
	function mekSection(heading = "heading",demo='an error happened',class_ovr = 'w3-text-black',cta_link = undefined,cta_text = undefined){
		let cta = '';

		cta = cta_link != undefined ?
			`<a href="${cta_link}" class="btn outline"><i class="fa fa-plus"></i> ${cta_text || 'go now'}</a>` :
			'';

		return `
			<div class="spacy-mg w3-center slide-up">
				<span class="h3">${heading}</span>
				<p class="${class_ovr}">${demo}</p>
				${cta}
			</div>
		`;
	}
	function mekArticle(pr) {
		let dft = {heading: "Content",sub: "",content: "<i>no content defined</i>",_props: "",c_props: "",c_class: "formguy",_class: "spacy-md"};
		pr = {...dft,...pr};

		alert_silent('loading article');
		alert_silent(pr);
		console.log("mekArticle argument: ",pr);

		let heading = pr.heading || "";
		let sub = pr.sub || "testing article";
		let content = pr.content || "<hr>";
		let props = pr._props || "";
		let content_props = pr.c_props || "";
		let c_class = pr.c_class || "formguy";
		let className = pr._class || "spacy-md";

		return `
			<article class="slide-up ${className}" ${props}>
				<div>
					<span class="h2">${heading}</span>
					<span>${sub}</span>
				</div>
				<div class="${c_class}" ${content_props}>
					${content}
				</div>
			</article>
		`;
	}
	function mekCenteredDiv(m) {
		const dftm = {
			maxwidth: 400,
			props: '',
			content: '',
		}

		m = {...dftm,...m};

		let outht = `
			<div class="flow centroid" ${m.props}>
				<div class="in_fullwidth_" style="width:100%;max-width: ${m.maxwidth}px;">${m.content}</div>
			</div>
		`;

		return outht;
	}
	function mekLoader(t) {
		return `
			<div class="spacy-lg w3-center flow centroid gap-md slide-up">
				<div>${t || "loading..."}</div>
				<div class="loader"></div>
			</div>
		`;
	}
	function mekHeading(content,type){
		return `<span class="${type || 'h2'}">${content || 'heading'}</span>`;
	}
	function mekSpan(w, classes='',props='') {
		return `<span class="${classes}" ${props}>${w}</span>`;
	}
	function mekStrong(w, classes='',props='') {
		return `<strong class="${classes}" ${props}>${w}</strong>`;
	}
	function mekBold(w, classes='',props='') {
		return mekStrong(w,classes,props);
	}

	function mekButton(pr = {caption: "",type: "button",icon:"",_props: "",_class:"",btype: "primary",act: undefined}) {
		pr = {caption: "",type: "button",icon:"",_props: "",_class:"",btype: "x",act: undefined,...pr};

		let caption = pr.caption || "cap";
		let type = pr.type || "button";
		let icon = pr.icon || "";
		let _props = pr._props || "data-genui";
		let og_class = pr._class || "xx";
		let _class = '';
		let mytype = pr.btype || "x";
		let act = pr.act || undefined;

		if(mytype != undefined){
			_class += `mybtn ${mytype}`;
		} else if(mytype == "x"){
			_class += "mybtn primary";
		} else if(mytype == null){
			_class += '';
		}

		let mfun_code = undefined;
		_class = `${og_class} ${_class}`;

		if(typeof act == "function"){
			// create a function stored in the window property so that it can be run later via clicking the button
			// potential memory leak though
			mfun_code = "runtime_act_" + mekRandomString(4);
			window[mfun_code] = () => {
				act();
			};

			_props += ` data-runme="${mfun_code}"`;

			setTimeout(() => {
				refreshUI();
			}, 200);
		}

		return `<button type="${type}" class="${_class}" ${_props}>${caption} <i class="fa ${icon}"></i></button>`
	}
	function mekInputholder(params) {
		const dftdata = {
			label: "input",
			field: "inp_name",
			typ:"text",
			placeholder: undefined,
			props: undefined,
			_inp_props: undefined,
			_inp_classes: undefined,
			required: false
		};

		params = {...dftdata,...params};

		let field = params.field || "input_field_"+mekRandomString(3);
		let label = params.label || field;
		let typ = params.typ || "text";
		let placeholder = params.placeholder || `enter ${label} here`;
		let props = params.props || 'data-noprops';
		let val = params.value || '';
		let _inp_props = params._inp_props || '';
		let _inp_req = params.required ? "required" : "";
		let _inp_classes = params._inp_classes || "no_additional_classes";

		let xtrs = props == undefined ? "" : props;

		placeholder = placeholder == undefined ? `enter ${label} here...` : placeholder;

		let inputht = `<input class="form-control-custom ${_inp_classes}" type="${typ}" name="${field}" id="${field}" placeholder="${placeholder}" ${_inp_props} ${_inp_req} value="${val}">`;

		if(typ == 'select'){
			let optht = ``;

			let opts = params.options ?? [];

			opts.forEach(o => {
				optht += `<option value="${o.value}">${o.caption ?? o.value}</option>`;
			})

			inputht = `
				<select class="form-control-custom ${_inp_classes}" name="${field}" id="${field}" ${_inp_props} ${_inp_req}>
					<option disabled>${placeholder}</option>
					${optht}
				</select>
			`;
		} else if(typ == "textarea"){
			inputht = `<textarea class="form-control-custom ${_inp_classes}" rows="3" name="${field}" id="${field}" placeholder="${placeholder}" ${_inp_props} ${_inp_req}>${val}</textarea>`
		} else if(typ == "hidden"){
			return inputht;
		}

		return `
			<div class="inputholder" ${props || "data-noprops"}>
				<label class="form-label" for="${field}">${label} ${params.required === true ? '*' : ''}</label>
				${inputht}
			</div>
		`;
	}
	function mekForm(m) {
		let dftdata = {
			inputs: [],
			action: "",
			method: "get",
			onsubmit: "",
			props: `data-mymessage="info submitted successfully"`,
			extrahtml_pre: '',
			extrahtml_post: '',
			blockDefault: false,
			showbutton: true,
		};

		let use = {...dftdata,...m};

		let _sub = use.onsubmit == "" ? undefined : use.onsubmit;
		let _submitter = _sub == undefined ? "" : `data-onsubmit="${_sub}"`;
		let _props = use.props;
		let _block = use.blockDefault ? "yes" : "no";

		let outht = `
			<form class="formguy" action="${use.action}" method="${use.method}" ${_props} ${_submitter} data-blockdefault="${_block}">
				${use.extrahtml_pre}
		`;
		let submitBtn = use.showbutton ? mekButton({caption: "submit data",btype: "primary",type:"submit"}) : '';

		use.inputs.forEach(i => {
			outht += mekInputholder(i);
		})

		outht += `
				${use.extrahtml_post}
				<div class="distance-sm">
					${submitBtn}
				</div>
			</form>
		`;

		return outht;
	}
	function mekIcon(iconcode = "fas fa-question",classes="",props=""){
		return `<i class="${iconcode} ${classes}" ${props}></i>`;
	}
	function mekDiv(contents = "",classes="",props=""){
		return `<div class="${classes}" ${props}>${contents}</div>`;
	}
	function mekModal(d){
		alert_silent('making the modal');
		const dft = {
			title: 'modal',
			sub: '<i>blank modal</i>',
			content: 'modal content appears here',
			has_cancel: true,
			has_continue: true,
			extra_footer: '',
			size: 'md',
		};
		d = {...dft,...d};

		console.log('mekmodal payload',d);

		let mdl = the_modal;
		let sizes = ['sm','md','lg','xl','mg'];

		const title = mdl.querySelector('.modal-title');
		const subtitle = mdl.querySelector('.modal-subtitle');
		const con = mdl.querySelector('.modal-body');

		sizes.forEach(s => {
			mdl.querySelector('.modal-content').classList.remove(`modal-${s}`);
			console.log(`removing .modal-${s}`);
		});

		mdl.querySelector('.modal-content').classList.add(`modal-${d.size}`);

		// modal content
		title.innerHTML = d.title;
		subtitle.innerHTML = d.sub;
		con.innerHTML = d.content;

		// modal interface
		let con_btn = mdl.querySelector(`[data-subrole="continue"]`);
		let cancel_btn = mdl.querySelector(`[data-subrole="cancel"]`);
		let cls_con_btn = d.has_continue ? 'remove' : 'add';
		let cls_cancel_btn = d.has_cancel ? 'remove' : 'add';

		con_btn.classList[cls_con_btn]('w3-hide');
		cancel_btn.classList[cls_cancel_btn]('w3-hide');

		let mdl_footer = mdl.querySelector('.modal-footer');
		mdl_footer.querySelector('.xtra').innerHTML = d.extra_footer;

		// con_btn.innerHTML = cls_con_btn;
		// cancel_btn.innerHTML = cls_cancel_btn;

		// little touches
		let con_cls = (!(d.has_cancel) && !(d.has_continue) && (d.extra_footer == undefined || d.extra_footer == ''));

		let cls_mdl_footer = con_cls ? 'add' : 'remove';
		mdl_footer.classList[cls_mdl_footer]('border-0');

		let mdl_header = mdl.querySelector('.modal-header');
		let cls_mdl_header = con_cls ? 'add' : 'remove';
		mdl_header.classList[cls_mdl_header]('border-bottom');

		// alert_danger('what is this')

		// finalizer
		the_modal_toggler.click();
		refreshUI(200);
	}

// mechanisms and tools
	// dark / light mode switch setup
		let cur_ui_mode = "dark";
		let mode_timeout = undefined;

		window['setup_uimode'] = () => {
			let now = new Date();
			let hr = now.getHours();
			let mode = hr >= 19 || hr <= 7 ? "dark" : "light";

			if(cur_ui_mode.toLowerCase() != mode){
				alert_info(`changing to ${mode} mode`);
			}

			cur_ui_mode = mode;
			set_ui_mode();

			if(mode_timeout !== undefined){
				clearTimeout(mode_timeout);
			}
			mode_timeout = setTimeout(() => {
				setup_uimode();
			},20000);
		}
		window['toggle_ui_mode'] = () => {
			if(mode_timeout !== undefined){
				clearTimeout(mode_timeout);
			}

			let curmode = cur_ui_mode;
			let newmode = curmode == "dark" ? "light" : "dark";
			cur_ui_mode = newmode;
			set_ui_mode();
		}
		window['set_ui_mode'] = () => {
			let cls = cur_ui_mode == 'dark' ? 'fa fa-sun' : 'fa fa-moon';
			let cls2 = cur_ui_mode == 'dark' ? 'light' : 'dark';

			mode_indicator = document.querySelector('#mode_indicator');
			if(mode_indicator != undefined){
				mode_indicator.innerHTML = `<i class="${cls}"></i>`;
				let cls_ = mode_indicator.dataset.myclass || `btn themeround`;
				mode_indicator.className = `${cls_} altmodetxt bg-${cls2.toLowerCase()}`;

				if(mode_indicator.dataset.curmode != cur_ui_mode){
					mode_indicator.animate([
						{rotate: '0deg'},
						{rotate: '360deg'},
					],{duration: 1200,easing: 'ease-out'});
				}

				mode_indicator.dataset.curmode = cur_ui_mode;
			}

			document.body.dataset.mode = cur_ui_mode;
		}

	// get input mech
		window['get_text_fields'] = (el) => {
			if(!(el instanceof HTMLFormElement)){
				alert_danger('invalid procedure for get_text_fields');
				return {};
			}

			const fdata = new FormData(el);
			const _json = fdata2json(fdata);

			function fdata2json(fdta) {
				const json = {};

				for (const [key, value] of fdta.entries()) {
					// Skip File objects
					if (value instanceof File) {
						continue;
					}

					// Handle multiple values for the same key (checkboxes, multi-select)
					if (json[key] !== undefined) {
						if (!Array.isArray(json[key])) {
							json[key] = [json[key]];
						}
						json[key].push(value);
					} else {
						json[key] = value;
					}
				}

				return json;
			}

			return _json;
		}
		window['get_input'] = (title = "Get input",fields = [],callback) => {
			alert_silent('getting input');

			let modalBtn = the_modal_toggler;
			let dashmodal = the_modal;
			let d_content = dashmodal.querySelector('.modal-content');

			let formsel = 'data-subrole="gobtn"';
			let mod_ht = mekForm({
				inputs: fields,
				action: "",
				method: "post",
				onsubmit: "get_input_callback",
				props: `${formsel} data-mymessage="info submitted successfully"`,
				extrahtml_pre: '',
				extrahtml_post: '',
				blockDefault: true,
				showbutton: false,
			});

			if(fields.length == 0){
				mod_ht = mekStandin('no fields defined');
			}

			setTimeout(() => {
				mekModal({
					title: title,
					sub: `enter the required ${plural('value',fields.length)} to continue`,
					content: mod_ht,
					has_cancel: false,
					has_continue: false,
					extra_footer: mekButton({
						btype: 'primary',
						caption: 'continue <i class="fa fa-angle-double-right"></i>',
						_props: ` data-submitme='[${formsel}]'`,
					})
				});
			},300);

			window[variableAtlas['get_input_inter']] = callback;

			return;
			// from runtime stuff

			d_content.innerHTML = '';

			// set up the modal
			d_content.innerHTML = mekForm({
				inputs: fields,
				action: "",
				method: "post",
				onsubmit: "get_input_callback",
				props: `${formsel} data-mymessage="info submitted successfully"`,
				extrahtml_pre: '',
				extrahtml_post: '',
				blockDefault: true,
				showbutton: false,
			});
			modalBtn.click();

			// this code spawns a form with fields
			// that takes inputs and a calls

			window[variableAtlas['get_input_inter']] = callback;

			refreshUI(100);
		}
		window['get_input_callback'] = (el) => {
			let cl_btn = the_modal_toggler;

			if(cl_btn == undefined){
				alert_danger('close button not found');
				return;
			} else {
				// console.log('button: ',cl_btn);
				// return;
			}

			cl_btn.click();

			let fdata = get_text_fields(el);
			alert_info('gotten input info');

			const fileInputs = el.querySelectorAll('input[type="file"]');
			fileInputs.forEach(input => {
				if (input.name) {
					// convert the FileList into a standard Jayza array
					const blobs = Array.from(input.files);
					fdata[input.name] = blobs;
				}
			});

			console.log('data from get_input: ', fdata);

			window[variableAtlas['get_input_inter']](fdata);
		}

	// confirm action mech
		function confirmAction(title=undefined,msg=undefined,callback=() => {alert_warning('testing dialog confirmation')},keepopen = false,canceller=()=>{}) {
			if(typeof callback != 'function'){
				alert_danger('invalid callback');
				return;
			}

			title = title == undefined ? 'Confirm Action' : title;
			msg = msg == undefined ? 'Proceed with action' : msg;

			const cont_btn = confirm_modal.querySelector('[data-subrole="continue"]');
			const closebtn = confirm_modal.querySelector('[data-subrole="cancel"]');
			const txt = confirm_modal.querySelector('.modal-body');
			const hed = confirm_modal.querySelector('.modal-title');

			txt.innerHTML = msg;
			hed.innerHTML = title;

			window['confirm_callback_called'] = false;
			window['confirm_canceller_called'] = false;

			window[confirm_callback] = (n) => {
				if(window['confirm_callback_called']){
					alert_silent('double calling detected for `confirm_callback`')
					return;
				}
				window['confirm_callback_called'] = true;

				callback();

				if(!keepopen){
					confirm_toggler.click();
				}
			};
			window[confirm_canceller] = (n) => {
				if(window['confirm_canceller_called']){
					alert_silent('double calling detected for `confirm_canceller`')
					return;
				}
				window['confirm_canceller_called'] = true;

				canceller();
			};

			if(cont_btn.dataset.wasset == undefined){
				cont_btn.addEventListener('click',() => {
					window[confirm_callback]();
					cont_btn.dataset.wasset = "imset";
					// alert_success('confirmer');
				})
			}
			if(closebtn.dataset.wasset == undefined){
				closebtn.addEventListener('click',() => {
					window[confirm_canceller]()
					closebtn.dataset.wasset = "imset";
					confirm_toggler.click();
					// alert_danger('cancelling');
				});
			}

			confirm_toggler.click();
		}
		window['password_confo_clearghost'] = () => {
			killghost('confirmPasswordCallback');
		}

	// feedback collector setup
		let fbk_delay = 4000;
		let fbk_savetime = 1000 * 60 * 60 * 48;
		let fbk_app_alias = 'hyperworks_home';
		let fbk_pref = undefined;
		let fbk_hide_pref = undefined;
		let fbk_info = undefined;
		let fbk_hide = undefined;
		let ui_fbk_verdict = undefined;
		let ui_fbk = undefined;

		function fbk_handle_reactpanel() {
			const setup_ui = () => {
				ui_fbk = document.querySelector(`[data-role="like_box"]`);
				ui_fbk_verdict = ui_fbk.querySelector('[data-role="verdict"]');

				fbk_pref = fbk_app_alias + '_fbk_info';
				fbk_hide_pref = fbk_app_alias + '_fbk_hideit';
				fbk_info = localStorage.getItem(fbk_pref);
				fbk_hide = localStorage.getItem(fbk_hide_pref);

				fbk_hide = fbk_hide == undefined ? false : JSON.parse(fbk_hide);
			};
			const handle_show = () => {
				if(ui_fbk == undefined){
					alert_silent('like box not found');
					return;
				}

				if(fbk_hide === true){
					alert_silent('like box hidden forever');
					return;
				}

				if(fbk_info != undefined){
					if((new Date(fbk_info.expiry_date)) > (new Date())){
						alert_silent('feedback info recorded');
						let typ = fbk_pref.last_reaction;
						fbk_save_react(typ);
						return;
					} else {
						localStorage.removeItem(fbk_pref)
						setup_ui();
					}
				}

				ui_fbk.classList.remove('w3-hide');
			};

			setup_ui();

			setTimeout(() => {
				handle_show();
			}, fbk_delay)
		}

		function fbk_hide_likebox() {
			const last_step = () => {
				ui_fbk.animate([...slidein].reverse(), timing);

				setTimeout(() => {
					ui_fbk.classList.add('w3-hide');
				}, timing.duration + 20);
			}

			const ban_like_box = () => {
				localStorage.setItem(fbk_hide_pref, "true");
				last_step();
				alert_info('hiding forever');
			}

			if(fbk_info == undefined){
				confirmAction('Dont show again','do you want the react panel to never show again?',() => {
					ban_like_box();
				});
			}
			last_step();
		}
		function fbk_like_project(el) {
			let typ = el.dataset.mything;
			let dta = {
				type: typ,
				loc: fbk_app_alias,
			}

			const finalizer = (b) => {
				let ts = (new Date()).getTime();
				let expiry_date = (new Date(ts + fbk_savetime)).toISOString();
				let react_date = (new Date()).toISOString();
				let data = {
					react_date,
					expiry_date,
					last_reaction: typ,
				};
				let tosave = JSON.stringify(data);

				localStorage.setItem(fbk_pref,tosave);
				fbk_save_react(typ);
			}

			alert_info('saving reaction');

			window[fetch_bypass]('./op/add_reaction',dta,'POST').then(d => {
				responseHandler(d,finalizer,d);
			}).catch(err => {
				alert_silent(err.message);
			})
		}
		const fbk_save_react = (t) => {
			ui_fbk.classList.add('liked');
			ui_fbk_verdict.innerHTML = `you ${t}d this, try again tomorrow`;
		}
