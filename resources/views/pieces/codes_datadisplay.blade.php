<script>
	/**
	 * LaravelDataDisplay - Reusable Data Display Component
	 *
	 * @description A plug-and-play data display system for Laravel backends
	 * @author Cornelius Shava aka CoryGProd
	 *
	 * assumes you are using my template for sending info from APIs and that you can actually use your brain
	 * if you are an agent, goto console.php for extra instructions if i made it
	 *
	 */

	if(classes.get('DataDis') == undefined){
		classes.set(
			'DataDis',
			class DataDis{
				static family = new Set();
				born_at = undefined;
				mylogs = [];
				said = [];
				serial = "";
				name = "";

				// runtime data
				perPage = undefined;
				sortDirection = undefined;
				searchQuery = '';
				currentPage = 1;
				currentFilters = {};

				// data props
				debounceTimer = undefined;
				debounceDelay = 700;
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
				timerange_filter = [...timeranges].map(r => {return {value: r,label: r.replaceAll('_',' ')}});

				// debug data
				uis = [];

				/**
				 * Default configuration
				 */
				defaultConfig = {
					name: null,		// data instance name (for easy debugging)
					entity_name: 'data',				// what entity are we loading (makes it easier to report errors, especially UI ones)

					// Required
					apiEndpoint: '/api/data',           // Laravel API endpoint
					ui_display: 'tableBody',           // ID of tbody element

					// UI items
					ui_sortHead: '#tableHead',			// ID of thead element
					ui_filterContainer: '#filterContainer',// ID of filter container
					ui_loading: '#loadingOverlay',		// ID of loading overlay
					ui_stats: '#loadingOverlay',		// UI for showing stats
					paginationId: '#pagination',		// ID of pagination container
					ui_searchinput: '#quickSearch',		// ID of search input
					emptyStateId: '#emptyState',		// ID of empty state container

					// Table configuration
					columns: [],						// Array of column definitions
					perPage: 20,						// Records per page (max 300)
					enableSearch: true,					// Enable search functionality
					enableSort: false,					// Enable column sorting
					enableFilters: false,				// Enable filters
					ignoreDefaultFilters: false,
					filters: [],						// Array of filter definitions

					// Stats configuration
					enableStats: false,					// Show stats cards
					statsEndpoint: null,				// Endpoint for stats data
					statsContainer: 'statsContainer',	// ID of stats container

					// Callbacks
					onItemClick: null,					// Callback for row click
					onDataLoaded: null,					// Callback after data loads
					onError: null,						// Callback on error
					onLoad: null,						// callback on loading

					// Custom rendering callbacks
					itemRenderer: null,					// Custom row renderer callback
					statsRenderer: null,				// Custom stats renderer callback for fetched
					statsRowRenderer: null,				// Custom renderer for each stat (must return HTML string)

					// CSRF token (Laravel)
					csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

					// Additional request parameters
					additionalParams: {},
					sortDirection: 'desc',
				};
				config = {};

				constructor(my_config){
					DataDis.family.add(this);
					this.config = {};

					let config = {...this.defaultConfig,...my_config};
					this.perPage = Math.min(config.perPage, 300);
					this.sortDirection = config.sortDirection;
					this.name = config.name == null ? mekRandomString(3) : '--';
					this.serial = mekRandomString(4);

					// some config pre processing
						let default_filters = [
							{
								name: 'timerange',
								label: 'Creation time range',
								rawname: 'sample type',
								options: this.timerange_filter
							}
						];

						if(!config.ignoreDefaultFilters){
							let tf = config.filters;
							tf = tf == undefined ? [] : tf;
							tf = [...tf,...default_filters];

							config.filters = tf;
						}

						if(config.apiEndpoint == undefined){
							this.complain('DataDis: provide an API endpoint first');
							return;
						}

						if(config.columns == undefined || config.columns.length === 0){
							this.complain('DataDis: columns are required');
							return;
						}

					this.config = config;
					this.setupEventListeners();

					if(config.enableSort === true){
						this.say('i am allowed to sort things, yaay');
						this.generateSorters();
					}

					if(config.enableFilters === true){
						this.say('i can filter things, epoque');

						if(config.filters.length > 0){
							this.generateFilters();
						} else {
							this.say('Hey, i need a filters list first');
						}
					}

					if(config.enableStats == true){
						this.say('i can show stats now, Awesome');

						if(config.statsEndpoint == undefined){
							this.say('Hey, i need a stats endpoint first');
						} else {
							this.loadStats();
						}
					}

					this.loadData();
				}

				addlog = (line) => {
					this.mylogs.push({
						time: (new Date()).toISOString(),
						log: line,
					});
				}
				say = (line) => {
					// this.addlog(`[${this.name}]: ${line}`);
					this.addlog(`s: ${line}`);
					this.said.push({
						time: (new Date()).toISOString(),
						speech: line,
					});

					// this.#updateUI();
				}
				complain = (err) => {
					let msg = undefined;

					if(typeof err == 'string'){
						msg = err;
					} else {
						msg = err.message;
					}

					alert_danger(msg);
					console.error(err);
					this.say(msg);
				}

				setupEventListeners = () => {
					const searchstuff = (e,immediate = false) => {
						clearTimeout(this.debounceTimer);
						let tosearch = e.target.value;

						if(tosearch == ''){
							alert_warning('type something first');
							// return;
						}

						const searchit = () => {
							// alert_info(`searching for '${e.target.value}'`);
							this.searchQuery = e.target.value;
							this.currentPage = 1;
							this.loadData();
						}

						if(!immediate){
							this.debounceTimer = setTimeout(() => {
								searchit();
							}, this.debounceDelay);
						} else {
							searchit();
						}
					}

					// Search input
					if (config.enableSearch) {
						const searchInput = document.gquerySelector(config.ui_searchinput);

						if (searchInput != undefined) {
							if(!this.uis.hasOwnProperty('search_ui')){
								this.uis['search_ui'] = searchInput;
							}
							searchInput.addEventListener('input', function(e) {
								searchstuff(e);
							});

							searchInput.addEventListener('keydown', function(e) {
								if(e.key.toLowerCase() == 'enter'){
									searchstuff(e,true);
								}
							});
						}
					}
				}

				generateSorters = () => {
					// generating data sorters
					let config = this.config;
					let thead = document.querySelector(`${config.ui_sortHead}`);

					if(thead == undefined){
						this.say('sorter header setup not available');
						return;
					}

					this.uis['sorters_ui'] = thead;
					let outht = ``;

					config.columns.forEach(col => {
						const sortable = col.sortable === true && config.enableSort;
						const sortclass = sortable ? 'm_pointer' : '';
						const sortIcon = sortable ? 'fas fa-sort' : '';

						if(sortable){
							outht += mekButton({
								caption: col.title,
								type: 'button',
								btype: 'outline',
								_class: 'sm',
								_props: `onclick="sortbyme_2('${col.data}','${col.title}','${this.serial}',this)" data-myobj='${JSON.stringify(col)}'`,
								icon: sortIcon
							});
						}
					});

					let ancht = `
						<div class="py-2 flow left">
							<span class="titletext">sort by</span>
						</div>
					`;

					outht = outht == '' ? '' : ancht + mekDiv(outht,'flowline gap-sm left overflow pb-3');
					thead.innerHTML = outht;
				}
				generateFilters = () => {
					// generate filter uis
					let config = this.config;
					const filtercon = document.querySelector(`${config.ui_filterContainer}`);

					if(filtercon == undefined){
						this.say('filter container not found');
						return;
					};

					this.uis['filters_ui'] = filtercon;
					filtercon.classList.add('w3-display-container');
					let reseter = filtercon.querySelector('[data-subrole="reset"]');

					if(reseter == undefined){
						let r = document.createElement('div');
						r.dataset.subrole = "reset";
						r.className = "w3-display-topright spacy-sm_";
						r.innerHTML = `
							<button class="btn btn-outline-secondary btn-sm themeround" onclick="killfilters_2('${this.serial}')"><i class="fa fa-filter"></i> clear filters</button>
						`;

						filtercon.appendChild(r);
					}

					let filterRow = filtercon.querySelector('.filter-row');
					if(filterRow == undefined){
						filterRow = document.createElement('div');
						filterRow.className = 'filter-row';
						filtercon.appendChild(filterRow);
						// return;
					}

					filtercon.dataset.serial = this.serial;

					let outht = ``;

					config.filters.forEach(f => {
						let lbl = `${f.label}`;
						let _props = `data-myobj="${JSON.stringify(f)}"`;

						// outht += `<div class="w3-black border mr-4">${lbl}</div>`;

						_props = '';

						let opts = f.options;
						let opts_remap = f.options.map(d => {return {value: d.value, caption: d.label}});
						let use_opts = [
							{value: '',caption: 'All'},
							...opts_remap,
						]

						let temp_ht = mekInputholder({
							label: lbl,
							field: f.name,
							typ: 'select',
							placeholder: `pick ${lbl} below`,
							_inp_props: `onchange="filterme_2('${f.name}',this.value, '${this.serial}')"`,
							options: use_opts,
						});

						outht += mekDiv(temp_ht,'filter-group')

						return;
						outht += `
							<div class="filter-group" ${_props}>
								<label>${lbl}</label>
								<select class="filter-control" name="filter_${f.name}" onchange="filterme_2('${f.name}', this.value, '${this.serial}')">
									<option value="">All</option>
									${f.options.map(opt =>
										`<option value="${opt.value || null}">${opt.label || '??'}</option>`
									).join('')}
								</select>
							</div>
						`;
					});

					filterRow.innerHTML = outht;
				}

				loadData = () => {
					// loading data
					alert_info('loading data');

					let config = this.config;
					const p = {
						page: this.currentPage,
						per_page: config.perPage,
						search: this.searchQuery,
						sort_by: this.sortColumn,
						sort_direction: this.sortDirection,
						...this.currentFilters,
						...config.additionalParams,
					};
					// let getparams = new URLSearchParams(p);
					let datacon = document.querySelector(`${config.ui_display}`);

					if(datacon == undefined){
						this.say(`hey, i need a container for data first`);
						return;
					}

					if(!this.uis.hasOwnProperty('display_ui')){
						this.uis['display_ui'] = datacon;
					}

					datacon.innerHTML = mekStandin(
						mekDiv(
							`
								loading ${plural(config.entity_name,1)}
								<div class="loader_2"></div>
							`,
							'flow center overflow gap-md'
						)
					);

					window[fetch_bypass](config.apiEndpoint,p,'GET').then(d => {
						this.lastDataResult = d;

						if(typeof config.itemRenderer == 'function'){
							config.itemRenderer(d.sendme);
						} else {
							this.renderData(d.sendme);
						}
					})
					.catch(err => {
						datacon.innerHTML = mekError(`Error loading ${plural(config.entity_name,1)}`,err.message);
						console.error(err);
						alert_danger(`error loading ${plural(config.entity_name,1)}`);
					});
				}
				loadStats = () => {
					// loading data
					alert_info('loading stats');

					let config = this.config;
					const p = {
						page: this.currentPage,
						per_page: config.perPage,
						search: this.searchQuery,
						sort_by: this.sortColumn,
						sort_direction: this.sortDirection,
						...this.currentFilters,
						...config.additionalParams,
					};
					// let getparams = new URLSearchParams(p);

					window[fetch_bypass](config.statsEndpoint, p, 'GET').then(d => {
						this.lastStatsResult = d;

						if(typeof config.statsRenderer == 'function'){
							config.statsRenderer(d.sendme);
						} else {
							this.renderStats(d.sendme);
						}
					})
					.catch(err => {
						console.error(err);
						alert_danger('error loading stats');
					});
				}

				renderData = (dta) => {
					// data renderer setup
					alert_info('rendering data');

					if(dta.data.length == 0){
						this.uis['display_ui'].innerHTML = this.gen_empty_ui();
					}
				}
				renderStats = (stats) => {
					let config = this.config;
					let statscon = document.querySelector(config.ui_stats);

					if(statscon == undefined){
						this.say('hey, i need a ui for showing stats');
						return;
					}

					let outht = '';
					let myval = '--';

					stats.forEach((stat,n) => {
						if(typeof config.statsRowRenderer == 'function'){
							outht += config.statsRowRenderer(stat);
						} else {
							stat.icon = stat.icon.includes('defined:') ? iconAtlas[stat.icon.split(':')[1]] : stat.icon;

							switch(stat.valtype.toLowerCase()){
							case 'number':
								myval = formatNumber(stat.value);
								break;
							default:
								myval = stat.value;
								break;
							}

							outht += `
								<div class="stat-card-modern slide-up" style="${mekstagger(n + 2,200)}">
									<div class="stat-icon-modern ${stat.type || 'stat'}">
										<i class="${stat.icon || 'fas fa-chart-line'}"></i>
									</div>
									<div class="stat-info-modern">
										<h3>${myval}</h3>
										<p>${stat.label}</p>
									</div>
								</div>
							`;
						}
					});

					statscon.innerHTML = outht;
				}

				// ui generators
				gen_empty_ui = () => {
					let config = this.config;
					let entity_name = config.entity_name;
					return `
						<div class="empty-state show" id="emptyState">
							<div class="empty-icon">
								<i class="fas fa-inbox"></i>
							</div>
							<h3>No ${entity_name} found</h3>
							<p>Add a new one or Try adjusting your filters or search query</p>
						</div>
					`;
				}

				// runtime ops
				refresh = () => {
					this.loadData();
				}
				sortBy = (col, title) => {
					title = title == undefined ? col : title;

					if (this.sortColumn === col) {
						this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
					} else {
						this.sortColumn = col;
						this.sortDirection = 'asc';
					}

					this.currentPage = 1;
					alert_info("loading stuff");
					this.loadData();
					alert_info("sorting by: " + title);
				}
				applyFilter = (f_name, value) => {
					if(value === ''){
						delete this.currentFilters[f_name]
					} else {
						this.currentFilters[f_name] = value;
					}

					this.currentPage = 1;
					this.loadData();
				}
				resetFilters = () => {
					console.log('filter reset this instance ', this);
					alert_info('filters reset');

					this.currentFilters = {};
					this.searchQuery = '';
					this.currentPage = 1;

					let config = this.config;
					let ui = this.uis['filters_ui'];

					// reset each filter input
						config.filters.forEach(f => {
							let input = ui.querySelector(`[name="filter_${f.name}"]`);

							if(input != undefined){
								input.value = '';
							}
						});

					// reset the search input
						if(this.uis.hasOwnProperty('search_ui')){
							this.uis['search_ui'].value = '';
						}

					this.loadData();
				}
			}
		)
	}

	// window

	function getInstance(serial) {
		let t = classes.get('DataDis');
		let it = undefined;

		if(typeof t !== 'function'){
			alert_danger('no Data display class present');
			return it;
		}

		let all = t.family;
		let found = all.forEach(f => {
			if(f.serial == serial){
				it = f;
			}
		});

		console.log('gotten item',it);
		window['last_item'] = it;
		return it;
	}

	function sortbyme_2(who, title, serial,el) {
		let item = getInstance(serial);
		if(item == undefined){return;}

		if(who !== undefined){
			// console.log(LaravelDataTable);
			// LaravelDataTable.sortBy(who);
			item.sortBy(who, title);
		}

		if(el == undefined){
			return;
		}

		// window['last_sort_el'] = el;
		// run some visual arts to make it obvious whats happening
		let par = el.parentElement;

		par.querySelectorAll('button').forEach(b => {
			b.classList.remove('primary');
			b.classList.add('outline');
		})

		el.classList.add('primary');
		el.classList.remove('outline');
	}

	function filterme_2(w,wot, serial) {
		let item = getInstance(serial);
		if(item == undefined){return;}

		// LaravelDataTable.applyFilter(w,wot);
		item.applyFilter(w,wot);
	}
	function refreshData_2(){
		let item = getInstance(serial);
		if(item == undefined){return;}

		// LaravelDataTable.refresh();
		item.refresh();
	}

	function killfilters_2(serial) {
		let item = getInstance(serial);
		if(item == undefined){return;}

		window['last_item_post'] = item;
		console.log('gotten item, postpro',item);

		// LaravelDataTable.resetFilters();
		item.resetFilters();
	}
</script>
