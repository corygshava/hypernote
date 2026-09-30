<style>
	.av{
		width:44px;
		height:44px;
		border-radius:50%;
		object-fit:cover;
		flex:none;
		background:var(--tint);
		margin-right:.8rem;
		box-shadow:0 0 0 2px var(--tint);
	}
	.av.sm{
		width:36px;
		height:36px;
		margin-right:.7rem;
	}
	.av.xs{
		width:28px;
		height:28px;
		margin-right:.5rem;
	}
	.av.flush{
		width:100%;
		height:100%;
		margin:0;
		box-shadow:none
	}
	.av-anon{
		display:inline-flex;
		align-items:center;
		justify-content:center;
		color:#fff;
		background:linear-gradient(135deg,var(--themecolor),var(--seccolor));
		font-size:1.1rem;
	}
	.av.xs.av-anon,.av.sm.av-anon{font-size:.85rem}
</style>

	<div>
		<button class="mybtn primary w3-hide" id="password_confo_trig" data-toggle="modal" data-target="#password_confo">
			<i class="fas fa-plus"></i>
			password_confo
		</button>

		<div class="modal fade" id="password_confo" tabindex="-1" role="dialog" aria-labelledby="password_confo_title" aria-hidden="true" style="z-index: 102;">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content themeround borderless">
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
						<button type="button" class="mybtn secondary" data-dismiss="modal" data-runme="password_confo_clearghost">Cancel</button>
						<button type="button" class="mybtn primary" data-submitme="#confo_password">Confirm Password</button>
					</div>
				</div>
			</div>
		</div>

		<button class="mybtn primary w3-hide" id="new_note_trig" data-toggle="modal" data-target="#newNote">
			<i class="fas fa-plus"></i>
			new note
		</button>

		<div class="modal fade" id="newNote" tabindex="-1" aria-labelledby='[data-subrole="new_note_title"]' aria-modal="true" role="dialog">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content themeround borderless panelbg">
					<div class="modal-header">
						<span class="modal-title h4" data-subrole="new_note_title">--</span>
						<button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="fa fa-times modetxt"></span></button>
					</div>
					<div class="modal-body">
						<form id="add_edit_note_form" data-onsubmit="add_edit_note" data-blockDefault="yes">
							<input type="hidden" name="cur_purpose" value="add">
							<input type="hidden" name="post_id" value="add">

							<div>
								<div class="inputholder" data-noprops>
									<label class="form-label" for="title">Title</label>
									<input class="form-control-custom no_additional_classes" type="text" name="title" id="title" placeholder="What do we call this one" oninput="inp_tags_process(this)"  value="" required>
								</div>
							</div>
							<div>
								<div class="inputholder" data-noprops>
									<label class="form-label" for="post_body">input </label>
									<textarea class="form-control-custom no_additional_classes"  name="post_body" id="post_body" placeholder="whats on your mind, Marc?" rows='7' maxlength="1000" required></textarea>
								</div>
							</div>
							<div>
								<!-- setup for special radio toggles -->
								<div class="inputholder">
									<div class="form-label">privacy status</div>
									<div class="radio_group mt-3">
										<label class="radio_holder" for="vis0">
											<input type="radio" id="vis0" name="privacy_state" value="1" checked>
											<i class="fa fa-globe mr-1"></i>Public
										</label>
										<label class="radio_holder" for="vis1">
											<input type="radio" id="vis1" name="privacy_state" value="6">
											<i class="fa fa-user-secret mr-1"></i>Anonymous
										</label>
										<label class="radio_holder" for="vis2">
											<input type="radio" id="vis2" name="privacy_state" value="2">
											<i class="fa fa-lock mr-1"></i>Only me
										</label>
									</div>
								</div>
							</div>
							<div class="collapser onshow_border_ border spacy-tn themeround">
								<div class="flowline spread in_fullwidth spacy-tn topper" data-toggle="collapse" target2="[data-subrole='advanced_options']" data-target="#adv_o">
									<b>advanced options</b>
									<div>
										<i class="myicon fa fa-chevron-down"></i>
									</div>
								</div>
								<div class="collapse" data-subrole='advanced_options' id="adv_o">
									<div>
										<!-- setup for special tags inputs -->
										<div class="inputholder" data-noprops>
											<label class="form-label" for="tags_prepro">tags (optional)</label>
											<input class="form-control-custom no_additional_classes" type="text" data-subrole="tags_prepro" name="tags_prepro" id="tags_prepro" placeholder="tags, separated by commas" oninput='new_note_handletags()'  value="">
											<input type="hidden" name="tags_actual" data-subrole="tags_input">
											<div class="flowline left gap-tn tagholder py-2 overflow" data-subrole="tags_display">
												<small><i class="text-muted"><b>tags show here</b></i></small>
											</div>
										</div>
									</div>
									<div class="inputholder">
										<div class="form-label">Document Type</div>
										<div class="radio_group mt-3">
											<label class="radio_holder" for="doctype1">
												<input type="radio" id="doctype1" name="doctype" value="text" checked>
												<i class="fa fa-file mr-1"></i>Text
											</label>
											<label class="radio_holder" for="doctype2">
												<input type="radio" id="doctype2" name="doctype" value="markdown">
												<i class="fas fa-file-code mr-1"></i>Markdown
											</label>
											<label class="radio_holder" for="doctype3">
												<input type="radio" id="doctype3" name="doctype" value="code">
												<i class="fa fa-code mr-1"></i>Code
											</label>
										</div>
									</div>
									<div class="border-top py-2 mt-2">
										<button class="mybtn danger sm" type="reset" onclick="new_note_reset()">clear details</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<!-- <span class="text-muted small mr-auto"><span id="nCount">5000</span> characters left</span> -->
						<button class="mybtn trans" data-dismiss="modal">Cancel</button>
						<button class="mybtn primary" data-submitme="#add_edit_note_form">Post note <i class="fa fa-paper-plane"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- the mobile menu -->
	<div class="thecontrols flow gap-tn right spacy-sm slide-l" data-role="applet_controls" data-visibledata="1,0,0">
		<!-- <button class="btn circle_btn lg" data-myclass="btn circle_btn" data-runme="toggle_ui_mode" data-role="mode_indicator"><i class="fa fa-plus"></i></button> -->
		<button class="btn circle_btn lg themegrad" data-runme="add_new_note"><i class="fa fa-plus"></i></button>
		<!-- <button class="btn circle_btn" data-runme="clear_timers"><i class="fa fa-trash"></i></button> -->
	</div>

	<div class="w3-hide">
		@auth
			<form action="./logout" method="post" id="logoutform" data-blockdefault="yes" data-onsubmit="sendform" data-onfail="logout_fail" data-callback="run_post_logout">@csrf</form>
		@endauth
	</div>

<div class="w3-hide">
@auth
	<script>
		window['note_uis'] = {};
		window['add_note_serial'] = mekRandomString(3);
		window['init_note_uis'] = () => {
			let note_modal = document.querySelector('#newNote');
			let note_modal_title = note_modal.querySelector('[data-subrole="new_note_title"]');
			let add_edit_form = note_modal.querySelector('#add_edit_note_form');
			let note_modal_trig = document.querySelector('#new_note_trig');

			note_uis = {
				note_modal,
				add_edit_form,
				note_modal_title,
				note_modal_trig
			};
		}
		window['add_new_note'] = () => {
			note_uis.note_modal_title.innerHTML = `New Note`;

			note_uis.add_edit_form.cur_purpose.value = "add";
			note_uis.add_edit_form.post_id.value = "";
			note_uis.note_modal_trig.click();
		}
		window['new_note_reset'] = () => {
			note_uis.add_edit_form.reset()
			inp_tags_reset(add_note_serial);
			new_note_reset_tags_stuff();
			new_note_handletags();
		}
		window['new_note_reset_tags_stuff'] = () => {
			document.querySelector(`[data-subrole="tags_display"]`).innerHTML = '';
			document.querySelector(`[data-subrole="tags_input"]`).value = '';
		}
		window['new_note_handletags'] = () => {
			inp_tags_process(`[data-subrole="tags_prepro"]`,`[data-subrole="tags_display"]`,`[data-subrole="tags_input"]`,add_note_serial);
		}
		window['new_note_postpro'] = () => {
			$('#newNote').modal('hide');
			add_edit_note_form.reset();
		}

		window['add_edit_note'] = (el) => {
			console.log('passed', el);
			// console.log('passed', el)
			let fdata = getFormdata(el);
			console.log('formdata', fdata);

			window[fetch_bypass_fyls](routeAtlas['add_edit_note'],fdata,'POST',true,true).then(d => {
				responseHandler(d,new_note_postpro,d);
			}).catch(err => {
				alert_danger('error adding your note');
			})
		}

		init_note_uis();
	</script>
@endauth

	{{-- session flashes --}}
		@if ($errors->any())
			<script>
				@foreach ($errors->all() as $err)
					alert_danger(`{{$err}}`,10);
				@endforeach
			</script>
		@endif

		@if (session()->has('message'))
			<script>alert_success(`{{session('message')}}`,15);</script>
		@endif
	{{-- end of session flashes --}}
</div>
