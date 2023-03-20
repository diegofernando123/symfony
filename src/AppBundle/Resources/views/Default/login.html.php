<?php $view->extend("::layout.html.php") ?>

<div>

<form method="post" action="<?= $view['router']->path('login') ?>">

<fieldset>

<h1 class="mdl-typography--display-3 mdl-color-text--blue-900">Login</h1>

<p style="text-align: center; margin-bottom: 0; margin-top: 20px;">
	Please login using Social Media:
</p>

<div class="user_profile_socials">
                                <ul class="networks-list" style="height: auto;">
                                    <li class="facebook  " data-provider="facebook" title="facebook">
                                        <a href="/login/facebook"><span></span></a>
                                    </li>
                                    <li class="google " data-provider="google" title="google">
                                        <a href="/login/google"><span></span></a>
                                    </li>
                                    <li class="twitter " data-provider="twitter" title="twitter">
                                        <a href="/login/twitter"><span></span></a>
                                    </li>
                                    <li class="vkontakte " data-provider="vkontakte" title="vkontakte">
                                        <a href="/login/vkontakte"><span></span></a>
                                    </li>
                                    <li class="tumblr " data-provider="tumblr" title="tumblr">
                                        <a href="/login/tumblr"><span></span></a>
                                    </li>
                                    <li class="instagram " data-provider="instagram" title="instagram">
                                        <a href="/login/instagram"><span></span></a>
                                    </li>
                                    <li class="flickr " data-provider="flickr" title="flickr">
                                        <a href="/login/flickr"><span></span></a>
                                    </li>
                                    <li class="foursquare " data-provider="foursquare" title="foursquare">
                                        <a href="/login/foursquare"><span></span></a>
                                    </li>
                                    <li class="yahoo " data-provider="yahoo" title="yahoo">
                                        <a href="/login/yahoo"><span></span></a>
                                    </li>
                                    <li class="imgur " data-provider="imgur" title="imgur">
                                        <a href="/login/imgur2"><span></span></a>
                                    </li>
                                    <li class="pinterest " data-provider="pinterest" title="pinterest">
                                        <a href="/login/pinterest"><span></span></a>
                                    </li>
                                    <li class="ok " data-provider="ok" title="ok">
                                        <a href="/login/odnoklassniki"><span></span></a>
                                    </li>
                                </ul>
                            </div>

<br/><br/><br/>
<center style="position: relative;">

<label style="position: absolute; margin-top: -8px; left: 50%; background: #fff; width: 100px; margin-left: -50px;">OR</label>

<hr/>
	
</center>


	<div>
	

<p style="text-align: center; margin-bottom: 20px; margin-top: 40px;">
	Use your login details:
</p>

	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(array_key_exists('email', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">mail_outline</i>
		<label class="mdl-textfield__label" for="login_form_password">Email:</label>
		<?= $view['form']->widget($form['email'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
<?php if(array_key_exists('email', $errors)): ?>
		<span class="mdl-textfield__error"><?= $errors['email'] ?></span>
<?php else: ?>
		<span class="mdl-textfield__error">Please provide correct email address</span>
<?php endif ?>
	</div>

	</div>

	<div>

	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(array_key_exists('password', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">lock_outline</i>
		<?= $view['form']->errors($form['password'], array('attr' => array('class' => 'mdl-textfield__error'))) ?>
		<label class="mdl-textfield__label" for="login_form_password">Password:</label>
		<?= $view['form']->widget($form['password'], array('attr' => array('pattern' => '.{6,}',  'class' => 'mdl-textfield__input'))) ?>
<?php if(array_key_exists('password', $errors)): ?>
		<span class="mdl-textfield__error"><?= $errors['password'] ?></span>
<?php else: ?>
		<span class="mdl-textfield__error">Password should contain at least 6 characters</span>
<?php endif ?>
	</div>

	</div>
	<br/>
	<div class="">
		<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" type="submit">Submit</button>
	</div>

            <div class="additional_buttons" style="margin-bottom: 20px">
                <ul>
                    <li>
                        <a href="<?= $view['router']->path('fos_user_resetting_request') ?>">Lost Password?</a>
                    </li>
                </ul>
            </div>
	
<br/>
<center style="position: relative;">

<label style="position: absolute; margin-top: -8px; left: 50%; background: #fff; width: 100px; margin-left: -50px;">OR</label>

<hr/>
	
</center>

<br/>

<center>

		<a href="<?= $view['router']->path('register') ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" type="submit">Create Account</a>


</center>


	<div class="">
	</div>
            
</fieldset>

</form>


</div>

