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
	</div>

	<div class="w3-hide">
		@auth
			<form action="./logout" method="post" id="logoutform" data-blockdefault="yes" data-onsubmit="sendform" data-onfail="logout_fail" data-callback="run_post_logout">@csrf</form>
		@endauth
	</div>

<div class="w3-hide">
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
