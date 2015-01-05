<div class="account-container">
	
	<div class="content clearfix">
		
		{{ Form::open(array('url'=>'users/signin', 'class'=>'form-signin', 'role'=>'form')) }}
		
			<h1>{{ $heading_title_login }}</h1>		
			
			<div class="login-fields">
				
				<p>{{ $heading_subtitle_login }}</p>
				
				<div class="field">
					<label for="username">{{ $entry_username }}</label>
					{{ Form::text('username', null, array('class'=>'login username-field', 'placeholder'=>'Username', 'autocomplete'=>'off')) }}
				</div> <!-- /field -->
				
				<div class="field">
					<label for="password">{{ $entry_password }}</label>
					{{ Form::password('password', array('class'=>'login password-field', 'placeholder'=>'Password', 'autocomplete'=>'off')) }}
				</div> <!-- /password -->
				
			</div> <!-- /login-fields -->
			
			<div class="login-actions">
				<!--
				<span class="login-checkbox">
					<input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4">
					<label class="choice" for="Field">Keep me signed in</label>
				</span>
				-->					
				{{ Form::submit($button_signin, array('class'=>'button btn btn-success btn-large'))}}
				
			</div> <!-- .actions -->
		{{ Form::close() }}	
			
			
		</form>
		
	</div> <!-- /content -->
	
</div>