<div class="mymodal" id="newpostmodal" data-shown="0">
	<div class="modal-content slide-in-bottom mycon">
		<button class="w3-btn w3-display-topright" data-toggler="#newpostmodal"><i class="fa fa-times"></i></button>
		<span class="h3">Add post</span>
		<form action="./mek-post" method="post" class="s-autoform-v">
			@csrf

			<select name="privacy_s" id="privacy_s">
				<option value="private">private (only you can see it)</option>
				<option value="public" selected>public (anyone can see it)</option>
				<option value="unlisted">unlisted (anyone can see it if they have the link)</option>
			</select>
			<input type="text" name="post_title" id="post_title" value="{{old('post_title')}}" placeholder="what do we call this adventure" autofocus>
			<textarea name="post_message" id="post_message" rows="11" placeholder="what's on your mind">{{old('post_message')}}</textarea>
			<div>
				<button class="btn primary"><i class="fa fa-plus"></i> add post</button>
			</div>
		</form>
	</div>
</div>