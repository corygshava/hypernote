let boxes = undefined;
let mymdl = undefined;

window.addEventListener('load', () => {
	boxes = document.querySelectorAll('.postbox.card');
	mymdl = document.querySelector('[data-role="postmodal_"]');

	init_boxes();
});

function init_boxes() {
	boxes.forEach((el,m) => {
		el.addEventListener('click',(e) => {
			console.log('click registered',e);
			if(e.target.className.includes('fa') || e.target.className.includes('btn')){
				return;
			}

			// alert_warning('warkin', 8 * Math.random());
			toggleShowB('[data-role="postmodal_"]','flex','none');

			let ui_creator = mymdl.querySelector('[data-subrole="creator"]');
			let ui_title = mymdl.querySelector('[data-subrole="mytitle"]');
			let ui_mybody = mymdl.querySelector('[data-subrole="mybody"]');
			let ui_timestamps = mymdl.querySelector('[data-subrole="timestamps"]');

			ui_creator.innerHTML = `<span class="text-gld">by <b class="themetxt">${el.dataset.creator}</b></span>`;
			ui_title.innerText = `${el.dataset.title}`;
			ui_mybody.innerText = `${el.dataset.msg}`;
			ui_timestamps.innerHTML = `
				<span class="text-muted text-gld w3-block">item created on <b class="themetxt">${el.dataset.datemade}</b></span>
				<span class="text-muted text-gld w3-block">last update <b class="themetxt">${el.dataset.dateedit}</b></span>
			`;

			console.log(el.dataset.creator, el.dataset.title, el.dataset.msg, el.dataset.timestamps);
		})
	});
}

function toggletab(group,n,hideall = false) {
	if(tg_contents[group] == undefined){
		alert_danger(`tab group [${group}] has no contents`);
		return;
	}

	if(tg_btns[group] == undefined){
		alert_danger(`tab button for group [${group}] not found`);
		return;
	}

	if(hideall){
		hidetabs();
		return;
	}

	let wg = `${group}_${n}`;

	tg_contents[group].forEach(wh => {
		wh.dataset.shown="0";
		
		if(wh.dataset.myid == wg){
			wh.dataset.shown = "1";
		}
	});

	// highlight the current button
	tg_btns[group].forEach(wh => {
		wh.classList.remove('active');
	});

	let btns = tg_btns[group];
	let btn = [...btns].filter(el => {return el.dataset.myid == wg});

	console.log(btns,btn);
	btn[0].classList.add('active');
}

function hidetabs() {
	Object.keys(tg_btns).forEach(el => {
		if(tg_contents[el] != undefined){
			tg_contents[el].forEach(it => {
				it.dataset.shown = "0";
			})
			alert_silent(`hidetabs -> contents for [${el}] found`);
		} else {
			alert_danger(`hidetabs -> no corresponding contents for [${el}]`);
		}
	})
}

function showfirsttab(){
	let wntd = [];

	Object.keys(tg_btns).forEach(grup => {
		tg_btns[grup].forEach((btn,m) => {
			if(!wntd.includes(btn.dataset.tabgroup)){
				wntd.push(btn.dataset.tabgroup);
			}
		});
	})

	hidetabs();

	wntd.forEach(el => {
		toggletab(el,0);
	})
}