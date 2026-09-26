<div class="topnav slide-down">
	<div class="logo_part">
		<img src="./_assets/img/logo_txts.png">
	</div>

	<div class="title flowline overflow center" style="gap: 4px;">
		<b class="themetxt" data-role="branch_name">--</b>
		<span data-visibledata="0,1,1"> : </span>
		<span class="text-muted" id="curArea">--</span>
	</div>

	<div class="search-holder w3-hide">
		<div class="search-box">
			<input type="text" placeholder="Search clients, jobs and operations" id="globalSearch">
			<button><i class="fas fa-search"></i></button>
		</div>
	</div>

	<div class="navops">
		<button class="nav-icon w3-hide" data-notifications="3">
			<i class="fa fa-bell"></i>
		</button>

		<div class="dropdown open">
			<div class="user-profile" id="userOpsDropper" data-toggle="dropdown" aria-haspopup="true"
					aria-expanded="false">
				<img src="_assets/img/demos/demoimg2.jpg" class="w3-hide" loading="lazy" alt="Admin">
				<div class="imgstandin">
					<b class="fa fa-user-tie"></b>
				</div>
				<span class="d-none d-md-block" data-role="user_name_display">{{ auth()->user()->name }}</span>
				<i class="fa fa-caret-down"></i>
			</div>
			<div class="dropdown-menu dropdown-menu-right nopadding nooverflow w3-animate-opacity themeround realign themeshadow" aria-labelledby="userOpsDropper" style="margin-top: 12px;border: none;">
				<button class="dropdown-item spacy-tn flowline gap-tn centerline left" href="#" data-runme="view_profile">
					<i class="fa fa-user-tie"></i>
					<span>View profile</span>
				</button>
				<button class="dropdown-item spacy-tn flowline gap-tn centerline left" href="#" data-runme="reset_saved_password">
					<i class="fa fa-key"></i>
					<span>Clear password</span>
				</button>
				<button class="dropdown-item spacy-tn flowline gap-tn centerline left" href="#" data-submitme="#logoutform">
					<i class="fa fa-user-slash"></i>
					<span>Logout</span>
				</button>
			</div>
		</div>
	</div>
</div>
