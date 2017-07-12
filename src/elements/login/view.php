
<div class="panel panel-primary login-panel">
	<div class="panel-heading">Login</div>
	<div class="panel-body">

		<form id="login" action="<?= request::build_url(['https', 'path' => '/login']) ?>" method="post" onsubmit="return validate_login(this);">
			<input type="hidden" name="challenge" id="login_challenge" value="<?= $_SESSION['challenge'] ?>" />
			<input type="hidden" name="next" id="login_next" value="<?= htmlentities(get('next')) ?>" />

			<div class="form-group has-feedback">
				<input type="text" class="form-control" id="login_email" name="email" placeholder="Email address" />
				<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			</div>
			<div class="form-group has-feedback">
				<input type="password" class="form-control" id="login_password" name="password" placeholder="Password" />
				<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			</div>

			<div class="g-recaptcha" data-sitekey="<?= env::get('google')['recaptcha-site'] ?>"></div>

			<br />

			<div class="row">
				<div class="col-xs-8">
					<div class="checkbox icheck">
						<label><input type="checkbox" name="persistent" value="1" /> Keep me logged in</label>
					</div>
				</div>
				<!-- /.col -->
				<div class="col-xs-4">
					<button type="submit" class="btn btn-primary btn-block btn-flat" id="login_submit">Login</button>
				</div>
				<!-- /.col -->
			</div>
			<span class="ajax-loading hide" id="login_loading"><img src="indicator.gif" alt="Loading" /> Loading...</span>
		</form>

		<br />
		<p>By logging in you are agreeing to our <a>Terms of Use</a> and <a>Privacy Policy</a>.</p>

		<hr />
		<a href="forgot-password">Forgot your password?</a><br />
		<a href="resend-verification">Resend Email Verification</a><br />

		<hr />
		<p>Not a member?</p>
		<a href="register" class="btn btn-default btn-block">Create an account</a>

	</div>
</div>

<script type="text/javascript">
function validate_login(form)
{
	if (form.login_email.value && form.login_password.value)
	{
		//$("login_loading").className = "ajax-loading";
		$("login_loading").removeClass("hide");
		return true;
	}
	if (form.login_email.value == "")
		alert("Please enter your email address!");
	else
		alert("Please enter your password!");
	return false;
}

</script>
