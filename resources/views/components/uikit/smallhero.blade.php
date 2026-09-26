		<section class="smallhero">
			<div class="inner">
				<span class="hero-title h3">hi there <b data-role="u-name-short" class="themetxt">Marc</b></span>
				<p class="hero-subtitle">
					In case no one told you today, I'm glad you woke up
				</p>
				<div class="flowline gap-sm overflow mt-4 w3-hide">
					<span class="hero-badge"><i class="fa fa-globe"></i>Available worldwide</span>
					<span class="hero-badge"><i class="fa fa-user-secret"></i>Anonymous by choice</span>
					<span class="hero-badge"><i class="fa fa-bolt"></i>Zero censorship</span>
				</div>
				<div class="flowline gap-sm overflow mt-4 w3-hide_" data-role="tabbtns">
					<button class="mybtn2 sm secondary" data-runme="menu_op" data-myid="0" data-link="defined:profile" data-label="defined:profile">
						<i data-myicon="profile"></i>
						<span>my profile</span>
					</button>
					<button class="mybtn2 sm primary" data-runme="menu_op" data-myid="1" data-link="defined:my_notes" data-label="defined:my_notes">
						<i data-myicon="my_notes"></i>
						<span>my notes</span>
					</button>
					<button class="mybtn2 sm secondary" data-runme="menu_op" data-myid="2" data-link="defined:my_feed" data-label="defined:my_feed">
						<i data-myicon="my_feed"></i>
						<span>for you</span>
					</button>
					<button class="mybtn2 sm secondary" data-runme="menu_op" data-myid="3" data-link="defined:my_following" data-label="defined:my_following">
						<i data-myicon="my_following"></i>
						<span>following</span>
					</button>
				</div>
			</div>
		</section>

		<script>
			window['menu_op'] = (el) => {
				let n = Number(el.dataset.myid);

				load_page(el);
				tabSwitch(n, '[data-role="tabbtns"] [data-runme="menu_op"]','secondary','primary','classswitch');
			}
		</script>
