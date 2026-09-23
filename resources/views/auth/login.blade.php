<x-layouts.mainlayout>
	<div class="flowline gap-sm spacy-tn" style="position: fixed;bottom: 0;left: 0;z-index: 3;">
		<button class="btn circle_btn altmodetxt bg-dark" data-myclass="btn circle_btn" data-runme="toggle_ui_mode" id="mode_indicator"><i class="fa fa-moon"></i></button>
	</div>

    <div class="flow center fullheight" style="justify-content: flex-start;padding-top: 80px;">
		<div class="flow center topnav">
			<a href="#" class="nav-logo" aria-label="_hyperworks home">
				<span>hypernote</span>
			</a>
		</div>
		<div class="flowline center spacy-md tabbtns">
			<button class="mybtn primary sm" onclick="switch_tab(0)">log in</button>
			<button class="mybtn outline sm" onclick="switch_tab(1)">sign up</button>
			<button class="mybtn outline sm" onclick="switch_tab(2)">burner Account</button>
		</div>

		<div class="spacy-sm border_ themeround formholder slide-up mytab active">
			<div class="w3-center border-bottom">
				<span class="h3">User Login</span>
                <p>
                    enter your details to log in
                </p>
			</div>

			<form class="t1" action="./op/login" method="post">
				<div class="w3-hide">@csrf</div>
				<div class="inputholder" data-noprops="">
					<label class="form-label" for="email">your email</label>
					<input class="form-control-custom no_additional_classes" type="email" name="email" id="email" placeholder="enter email here" value="">
				</div>
				<div class="inputholder" data-noprops="">
					<label class="form-label" for="password">password</label>
					<input class="form-control-custom no_additional_classes" type="password" name="password" id="password" placeholder="enter password here" value="">
				</div>
				<button type="submit" class="mybtn2 primary flowline center">
					Try logging in
					<i class="fa fa-paper-plane"></i>
				</button>
				<div class="spacy-tn w3-center">
					<a href="#signup" onclick="switch_tab(1)"><i>dont have an account, <b class="themetxt">register now</b></i></a>
				</div>
			</form>
		</div>

		<div class="spacy-sm border_ themeround formholder slide-up mytab">
			<div class="w3-center border-bottom">
				<span class="h3">Create account</span>
                <p>
                    this creates a new account
                </p>
			</div>

			<form class="t1" action="./op/login" method="post">
				<div class="w3-hide">@csrf</div>
				<div class="inputholder">
					<label class="form-label" for="username">your username</label>
					<input class="form-control-custom no_additional_classes" type="text" name="username" id="username" placeholder="enter username here" value="">
				</div>
				<div class="inputholder">
					<label class="form-label" for="email">your email</label>
					<input class="form-control-custom no_additional_classes" type="email" name="email" id="email" placeholder="enter email here" value="">
				</div>
				<div class="inputholder">
					<label class="form-label" for="password">password</label>
					<input class="form-control-custom no_additional_classes" type="password" name="password" id="password" placeholder="enter password here" value="">
				</div>
				<div class="inputholder">
					<label class="form-label" for="confirm_password">confirm password</label>
					<input class="form-control-custom no_additional_classes" type="confirm_password" name="confirm_password" id="confirm_password" placeholder="confirm password here" value="">
				</div>
				<button type="submit" class="mybtn2 primary flowline center">
					Register
					<i class="fa fa-paper-plane"></i>
				</button>
				<div class="spacy-tn w3-center">
					<a href="#signup" onclick="switch_tab(0)"><i>I already have an account, <b class="themetxt">login now</b></i></a>
				</div>
			</form>
		</div>

		<div class="spacy-sm border_ themeround formholder slide-up mytab">
			<div class="w3-center border-bottom">
				<span class="h3">Create burner account</span>
                <p>
                    this creates an account that will be auto archived after 2 days, note that only <b>pro</b> members can make dissapearing posts
                </p>
			</div>

			<div class="spacy-lg flow center w3-center">
				<b>work in progress</b>
				<i class="text-muted">not supported yet</i>
			</div>

			<form class="t1 w3-hide" action="./op/login" method="post">
				<div class="w3-hide">@csrf</div>
				<div class="inputholder">
					<label class="form-label" for="username">your username</label>
					<input class="form-control-custom no_additional_classes" type="text" name="username" id="username" placeholder="enter username here" value="">
				</div>
				<div class="inputholder">
					<label class="form-label" for="password">password</label>
					<input class="form-control-custom no_additional_classes" type="password" name="password" id="password" placeholder="enter password here" value="">
				</div>
				<div class="inputholder">
					<label class="form-label" for="confirm_password">confirm password</label>
					<input class="form-control-custom no_additional_classes" type="confirm_password" name="confirm_password" id="confirm_password" placeholder="confirm password here" value="">
				</div>
				<button type="submit" class="mybtn2 primary flowline center">
					begin
					<i class="fa fa-paper-plane"></i>
				</button>
				<div class="spacy-tn w3-center">
					<a href="#signup" onclick="switch_tab(1)"><i>I want a normal account, <b class="themetxt">register now</b></i></a>
				</div>
			</form>
		</div>
	</div>

	<script>
		function switch_tab(n) {
			tabSwitch(n, '.mytab', 'inactive', 'active', 'classswitch');
			tabSwitch(n, '.tabbtns button', 'outline', 'primary', 'classswitch');
		}

		callOnLoad.push({act: setup_uimode})
	</script>
</x-layouts.mainlayout>
