@if(false)
	<script>
		let boxes = undefined;
		let mdl = undefined;

		window.addEventListener('load', () => {
			boxes = document.querySelectorAll('.postbox');
			mdl = document.querySelector('[data-role="postmodal"]');

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

					toggleShowB('[data-role="postmodal"]','flex','none');

					let themsg = el.dataset.msg;
					let richidentifier = '[richtext]';
					let isrich = themsg.startsWith(richidentifier);

					themsg = isrich ? themsg.replace(richidentifier,'') : themsg;

					let ui_creator = mdl.querySelector('[data-subrole="creator"]');
					let ui_title = mdl.querySelector('[data-subrole="mytitle"]');
					let ui_mybody = mdl.querySelector('[data-subrole="mybody"]');
					let ui_timestamps = mdl.querySelector('[data-subrole="timestamps"]');

					ui_creator.innerHTML = `<span class="text-gld">by <b class="themetxt">${el.dataset.creator}</b></span>`;
					ui_title.innerText = `${el.dataset.title}`;

					if(isrich) {
						ui_mybody.innerHTML = `${themsg.replaceAll('\n','<br>')}`;
					} else {
						ui_mybody.innerText = `${themsg}`;
					}

					ui_timestamps.innerHTML = `
						<span class="text-muted text-gld w3-block">item created on <b class="themetxt">${el.dataset.datemade}</b></span>
						<span class="text-muted text-gld w3-block">last update <b class="themetxt">${el.dataset.dateedit}</b></span>
					`;

					console.log(el.dataset.creator, el.dataset.title, el.dataset.msg, el.dataset.timestamps);
				})
			});
		}
	</script>
@endif