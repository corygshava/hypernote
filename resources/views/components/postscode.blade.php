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

	<button type="button" class="btn btn-primary btn-sm w3-hide" data-toggle="modal" data-target="#confirmerBox" data-role="toggleConfirmModal">Launch Dialog</button>

	<div class="modal fade" style="display: none;" id="confirmerBox" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content modebg">
				<div class="modal-header borderless">
					<h5 class="modal-title">Confirm Action</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					do you want to delete this
				</div>
				<div class="modal-footer borderless">
					<button type="button" class="btn btn-secondary except" data-dismiss="modal" data-subrole="cancel">cancel</button>
					<button class="btn btn-primary except" data-subrole="continue">continue &raquo;</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		const confirm_modal = document.querySelector(`#confirmerBox`);
		const confirm_toggler = document.querySelector(`[data-role="toggleConfirmModal"]`);

		function confirmAction(title=undefined,msg=undefined,callback=() => {alert_warning('testing dialog confirmation')},keepopen = false) {
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

			cont_btn.onclick = () => {
				callback();

				if(!keepopen){
					closebtn.click();
				}
			};

			confirm_toggler.click();
		}
	</script>

	<form action="./delete-post/0" data-role="happimod" method="post">
		@csrf
		@method('DELETE')
	</form>