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
								_props: `onclick="sortbyme_2('${col.data}','${col.title}','${this.serial}')" data-myobj='${JSON.stringify(col)}'`,
								icon: sortIcon
							});
						}
					});

					outht = outht == '' ? '' : mekDiv(outht,'flow left overflow');
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
				}
				renderStats = (stats) => {
					let config = this.config;
					let statscon = document.querySelector(config.ui_stats);

					if(statscon == undefined){
						this.say('hey, i need a ui for showing stats');
						return;
					}

					let outht = '--';
					let myval = '--';

					switch(stat.valtype.toLowerCase()){
					case 'number':
						myval = formatNumber(stat.value);
						break;
					default:
						myval = stat.value;
						break;
					}

					stats.forEach((s,n) => {
						if(typeof config.statsRowRenderer == 'function'){
							outht += config.statsRowRenderer(s);
						} else {
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

	// window['laravelDataDisplay'] = new DataDis();

	window['LaravelDataTable_ref'] = (function() {
		// Private variables
			let config = {};
			let currentPage = 1;
			let perPage = 5;
			let totalRecords = 0;
			let currentFilters = {};
			let searchQuery = '';
			let sortColumn = '';
			let sortDirection = 'desc';
			let debounceTimer = null;
			let timeranges = [
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
			let timerange_filter = [...timeranges].map(r => {return {value: r,label: r.replaceAll('_',' ')}});

		/**
		 * Default configuration
		 */
		const defaultConfig = {
			// Required
			apiEndpoint: '/api/data',           // Laravel API endpoint
			tableBodyId: 'tableBody',           // ID of tbody element

			// Optional
			tableHeadId: 'tableHead',           // ID of thead element
			paginationId: 'pagination',         // ID of pagination container
			ui_searchinput: 'quickSearch',       // ID of search input
			loadingOverlayId: 'loadingOverlay', // ID of loading overlay
			emptyStateId: 'emptyState',         // ID of empty state container

			// Table configuration
			columns: [],                        // Array of column definitions
			perPage: 20,                        // Records per page (max 300)
			enableSearch: true,                 // Enable search functionality
			enableSort: true,                   // Enable column sorting
			enableFilters: false,               // Enable filters
			ignoreDefaultFilters: false,		//
			filters: [],                        // Array of filter definitions

			// Stats configuration
			enableStats: false,                 // Show stats cards
			statsEndpoint: null,                // Endpoint for stats data
			statsContainer: 'statsContainer',   // ID of stats container

			// Callbacks
			onRowClick: null,                   // Callback for row click
			onDataLoaded: null,                 // Callback after data loads
			onError: null,                      // Callback on error

			// Row rendering
			rowRenderer: null,                  // Custom row renderer function

			// CSRF token (Laravel)
			csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

			// Additional request parameters
			additionalParams: {},
			sortDirection: 'desc',
		};

		/**
		 *
		 * Initialize the data table
		 * @param {Object} userConfig - User configuration
		 *
		 */
		function init(userConfig) {
			alert_info('loading information, please wait...');

			config = { ...defaultConfig, ...userConfig };
			perPage = Math.min(config.perPage, 300);
			sortDirection = config.sortDirection;

			console.log('lrvl_dt config: ',config);

			// pre processing
				// set up the must have filters
				let default_filters = [
					{
						name: 'timerange',
						label: 'Creation time range',
						rawname: 'sample type',
						options: timerange_filter
					}
				];

				if(!config.ignoreDefaultFilters){
					let the_filters = config.filters;
					the_filters = the_filters == undefined ? [] : the_filters;
					the_filters = [...the_filters,...default_filters];

					config.filters = the_filters;
				}

				// Validate required config
				if (!config.apiEndpoint) {
					console.error('LaravelDataTable: apiEndpoint is required');
					return;
				}

				if (!config.columns || config.columns.length === 0) {
					console.error('LaravelDataTable: columns configuration is required');
					return;
				}

			// Setup event listeners
			setupEventListeners();

			// Generate table headers
			generateSorters();

			// Generate filters if enabled
			if (config.enableFilters && config.filters.length > 0) {
				generateFilters();
			}

			// Load stats if enabled
			if (config.enableStats && config.statsEndpoint) {
				loadStats();
			}

			// Load initial data
			loadData();
		}

		/**
		 * Setup event listeners
		 */
		function setupEventListeners() {
			const searchstuff = (e,immediate = false) => {
				clearTimeout(debounceTimer);

				let tosearch = e.target.value;

				if(tosearch == ''){
					alert_warning('type something first');
					return;
				}

				const searchit = () => {
					// alert_info(`searching for '${e.target.value}'`);
					searchQuery = e.target.value;
					currentPage = 1;
					loadData();
				}

				if(!immediate){
					debounceTimer = setTimeout(() => {
						searchit();
					}, 700); // debounce to prevent too many requests
				} else {
					searchit();
				}
			}

			// Search input
			if (config.enableSearch) {
				const searchInput = document.getElementById(config.ui_searchinput);
				if (searchInput) {
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

		/**
		 * Generate table headers
		 */
		function generateSorters() {
			const thead = document.getElementById(config.tableHeadId);
			if (!thead) return;

			let headerHTML = '<tr>';

			config.columns.forEach(column => {
				const sortable = column.sortable !== false && config.enableSort;
				const sortClass = sortable ? 'cursor-pointer' : '';
				const sortIcon = sortable ? '<i class="fas fa-sort"></i>' : '';

				headerHTML += `
					<th class="${sortClass}"
						${sortable ? `onclick="sortbyme_2('${column.data}','${column.title}')"` : ''}
						${column.width ? `width="${column.width}"` : ''}>
						${column.title} ${sortIcon}
					</th>
				`;
			});

			headerHTML += '</tr>';
			thead.innerHTML = headerHTML;
		}

		/**
		 * Generate filters
		 */
		function generateFilters() {
			// /*
				const filterContainer = document.getElementById('filterContainer');
				if (!filterContainer) return;

				filterContainer.classList.add('w3-display-container');

				let reseter = filterContainer.querySelector('[data-subrole="reset"]');

				if(reseter == undefined){
					let r = document.createElement('div');
					r.dataset.subrole = "reset";
					r.className = "w3-display-topright spacy-sm";
					r.innerHTML = `
						<button class="btn btn-outline-secondary btn-sm themeround" onclick="killfilters_2('${this.serial}')"><i class="fa fa-filter"></i> clear filters</button>
					`;

					filterContainer.appendChild(r);
				}

				const filterRow = filterContainer.querySelector('.filter-row');
				if (!filterRow) return;

				let filtersHTML = '';

				config.filters.forEach(filter => {
					let lbl = `${filter.label}`;
					let _props = `data-myobj="${JSON.stringify(filter)}"`;

					_props = '';
					filtersHTML += `
						<div class="filter-group" ${_props}>
							<label>${lbl}</label>
							<select class="filter-control" id="filter_${filter.name}" onchange="filterme_2('${filter.name}', this.value, '${this.serial}')">
								<option value="">All</option>
								${filter.options.map(opt =>
									`<option value="${opt.value || null}">${opt.label || '??'}</option>`
								).join('')}
							</select>
						</div>
					`;
				});

				filterRow.innerHTML = filtersHTML;
			// */
		}

		/**
		 * Load data from Laravel backend
		 */
		function loadData() {
			showLoading(true);

			// Build request parameters
			const params = {
				page: currentPage,
				per_page: config.perPage,
				search: searchQuery,
				sort_by: sortColumn,
				sort_direction: sortDirection,
				...currentFilters,
				...config.additionalParams
			};

			console.log('lrvl_dt load data params: ',params);

			// Make AJAX request
			fetch(`${config.apiEndpoint}?${new URLSearchParams(params)}`, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': config.csrfToken,
					'Accept': 'application/json'
				}
			})
			.then(response => {
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				return response.json();
			})
			.then(data => {
				handleDataResponse(data);
				showLoading(false);

				// Call onDataLoaded callback
				if (config.onDataLoaded && typeof config.onDataLoaded === 'function') {
					config.onDataLoaded(data.sendme);
				}

				refreshUI(300);
			})
			.catch(error => {
				console.error('LaravelDataTable Error:', error);
				showLoading(false);
				showToast('error', 'Error', 'Failed to load data');

				// Call onError callback
				if (config.onError && typeof config.onError === 'function') {
					config.onError(error);
				}
			});

			// update stats if allowed
			if (config.enableStats) {
				loadStats();
			}
		}

		/**
		 * Handle data response from Laravel
		 * Expected format: { data: [], total: 0, per_page: 10, current_page: 1 }
		 */
		function handleDataResponse(gotten) {
			if(!gotten.success){
				alert_danger('access denied!');
				return;
			}

			if(!gotten.result){
				alert_danger('there was an error processing your request',12);
				return;
			}

			const response = gotten.sendme;
			const data = response.data || [];
			totalRecords = response.total || 0;
			currentPage = response.current_page || 1;

			// Render table rows
			renderTable(data);

			// Update pagination
			updatePagination(response);

			// Show/hide empty state
			toggleEmptyState(data.length === 0);
		}

		/**
		 * Render table rows
		 */
		function renderTable(data) {
			const tbody = document.getElementById(config.tableBodyId);
			if (!tbody) return;

			if (data.length === 0) {
				tbody.innerHTML = '';
				return;
			}

			let rowsHTML = '';

			data.forEach((row, index) => {
				if (config.rowRenderer && typeof config.rowRenderer === 'function') {
					// Use custom row renderer
					rowsHTML += config.rowRenderer(row, index);
				} else {
					// Default row renderer
					rowsHTML += renderDefaultRow(row, index);
				}
			});

			tbody.innerHTML = rowsHTML;

			// Attach row click events if configured
			if (config.onRowClick && typeof config.onRowClick === 'function') {
				tbody.querySelectorAll('tr').forEach((tr, index) => {
					tr.style.cursor = 'pointer';
					// passes the row, id and event data
					tr.addEventListener('click', (c) => config.onRowClick(data[index], index, c));
				});
			}
		}

		/**
		 * Default row renderer
		 */
		function renderDefaultRow(row, index) {
			let rowHTML = '<tr>';

			config.columns.forEach(column => {
				let cellValue = getNestedValue(row, column.data);

				// Apply cell renderer if provided
				if (column.render && typeof column.render === 'function') {
					cellValue = column.render(cellValue, row, index);
				}

				rowHTML += `<td>${cellValue !== null && cellValue !== undefined ? cellValue : '-'}</td>`;
			});

			rowHTML += '</tr>';
			return rowHTML;
		}

		/**
		 * Get nested object value
		 */
		function getNestedValue(obj, path) {
			return path.split('.').reduce((prev, curr) => {
				return prev ? prev[curr] : null;
			}, obj);
		}

		/**
		 * Update pagination
		 */
		function updatePagination(response) {
			const lastPage = response.last_page || 1;
			const from = response.from || 0;
			const to = response.to || 0;

			// Update pagination info
			document.getElementById('showingStart').textContent = from;
			document.getElementById('showingEnd').textContent = to;
			document.getElementById('totalItems').textContent = totalRecords;

			// Generate pagination buttons
			const paginationContainer = document.getElementById(config.paginationId);
			if (!paginationContainer) return;

			let paginationHTML = '';

			// Previous button
			paginationHTML += `
				<li class="page-item">
					<button class="page-link"
							onclick="LaravelDataTable.changePage(${currentPage - 1})"
							${currentPage === 1 ? 'disabled' : ''}>
						<i class="fas fa-chevron-left"></i>
					</button>
				</li>
			`;

			// Page numbers (show 5 pages at a time)
			const startPage = Math.max(1, currentPage - 2);
			const endPage = Math.min(lastPage, currentPage + 2);

			for (let i = startPage; i <= endPage; i++) {
				paginationHTML += `
					<li class="page-item">
						<button class="page-link ${i === currentPage ? 'active' : ''}"
								onclick="LaravelDataTable.changePage(${i})">
							${i}
						</button>
					</li>
				`;
			}

			// Next button
			paginationHTML += `
				<li class="page-item">
					<button class="page-link"
							onclick="LaravelDataTable.changePage(${currentPage + 1})"
							${currentPage === lastPage ? 'disabled' : ''}>
						<i class="fas fa-chevron-right"></i>
					</button>
				</li>
			`;

			paginationContainer.innerHTML = paginationHTML;
		}

		/**
		 * Change page
		 */
		function changePage(page) {
			currentPage = page;
			loadData();
		}

		/**
		 * Sort by column
		 */
		function sortBy(column,title) {
			// alert_info("chosen col: " + column);
			// return;

			title = title == undefined ? column : title;

			if (sortColumn === column) {
				sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
			} else {
				sortColumn = column;
				sortDirection = 'asc';
			}
			currentPage = 1;
			loadData();

			alert_info("sorting by: " + title);
		}

		/**
		 * Apply filter
		 */
		function applyFilter(filterName, value) {
			if (value === '') {
				delete currentFilters[filterName];
			} else {
				currentFilters[filterName] = value;
			}
			currentPage = 1;
			loadData();
		}

		/**
		 * Show/hide loading overlay
		 */
		function showLoading(show) {
			const overlay = document.getElementById(config.loadingOverlayId);
			if (overlay) {
				overlay.classList.toggle('show', show);
			}
		}

		/**
		 * Toggle empty state
		 */
		function toggleEmptyState(show) {
			const emptyState = document.getElementById(config.emptyStateId);
			if (emptyState) {
				emptyState.classList.toggle('show', show);
			}
		}

		/**
		 * Load stats data
		 */
		function loadStats() {
			const params = {
				page: currentPage,
				per_page: config.perPage,
				search: searchQuery,
				sort_by: sortColumn,
				sort_direction: sortDirection,
				...currentFilters,
				...config.additionalParams
			};
			let getparams = new URLSearchParams(params);

			fetch(`${config.statsEndpoint}?${getparams}`, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': config.csrfToken,
					'Accept': 'application/json'
				}
			})
			.then(response => response.json())
			.then(data => {
				renderStats(data.sendme);
			})
			.catch(error => {
				console.error('Stats loading error:', error);
				alert_danger('error loading stats');
			});
		}

		/**
		 * Render stats cards
		 */
		function renderStats(stats) {
			const container = document.getElementById(config.statsContainer);
			if (!container || !stats) return;

			let statsHTML = '';


			stats.forEach((stat,n) => {
				stat.icon = stat.icon.includes('defined:') ? iconAtlas[stat.icon.split(':')[1]] : stat.icon;

				let myval = '--';

				switch(stat.valtype.toLowerCase()){
				case 'number':
					myval = formatNumber(stat.value);
					break;
				default:
					myval = stat.value;
					break;
				}

				statsHTML += `
					<div class="stat-card-modern slide-up" style="${mekstagger(n + 2,200)}">
						<div class="stat-icon-modern ${stat.type || 'total'}">
							<i class="${stat.icon || 'fas fa-chart-line'}"></i>
						</div>
						<div class="stat-info-modern">
							<h3>${myval}</h3>
							<p>${stat.label}</p>
						</div>
					</div>
				`;
			});

			container.innerHTML = statsHTML;
		}

		/**
		 * Refresh data
		 */
		function refresh() {
			loadData();
		}

		/**
		 * Reset filters
		 */
		function resetFilters() {
			this.currentFilters = {};
			this.searchQuery = '';
			this.currentPage = 1;

			// Reset filter inputs
			config.filters.forEach(filter => {
				const input = document.getElementById(`filter_${filter.name}`);
				if (input) input.value = '';
			});

			// Reset search input
			const searchInput = document.getElementById(config.ui_searchinput);
			if (searchInput) searchInput.value = '';

			loadData();
		}

		// Public API
		return {
			init: init,
			loadData: loadData,
			changePage: changePage,
			sortBy: sortBy,
			applyFilter: applyFilter,
			refresh: refresh,
			resetFilters: resetFilters,
			getConfig: () => config,
			getCurrentPage: () => currentPage,
			getTotalRecords: () => totalRecords
		};
	})();

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

	function sortbyme_2(who, title, serial) {
		let item = getInstance(serial);
		if(item == undefined){return;}

		if(who !== undefined){
			// console.log(LaravelDataTable);
			// LaravelDataTable.sortBy(who);
			item.sortBy(who, title);
		}
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
