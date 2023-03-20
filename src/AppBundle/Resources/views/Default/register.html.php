<?php $view->extend("::layout.html.php") ?>

<div>

<form method="post" action="<?= $view['router']->path('register') ?>">

<fieldset>

<h1 class="mdl-typography--display-3 mdl-color-text--blue-900">Join Network</h1>

<div>

	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(array_key_exists('first_name', $errors)): ?> is-invalid<?php endif ?>" style="width: 248px">
		<label class="mdl-textfield__label" for="login_form_email">First Name:</label>
		<?= $view['form']->widget($form['first_name'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">First Name is required</span>
	</div>

	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(array_key_exists('last_name', $errors)): ?> is-invalid<?php endif ?>" style="width: 248px">
		<i class="material-icons">person_outline</i>
		<label class="mdl-textfield__label" for="login_form_email">Last Name:</label>
		<?= $view['form']->widget($form['last_name'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">Last Name is required</span>
	</div>

	</div>


	<div>

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

<?php /*
	<div>

	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
		<i class="material-icons">call</i>
		<?= $view['form']->errors($form['phone'], array('attr' => array('class' => 'mdl-textfield__error'))) ?>
		<label class="mdl-textfield__label" for="login_form_password">Phone:</label>
		<?= $view['form']->widget($form['phone'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">Digits and spaces only</span>
	</div>

	</div>

*/ ?>

	<div>

	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(array_key_exists('password', $errors)): ?> is-invalid<?php endif ?>" style="width: 248px">
		<?= $view['form']->errors($form['password'], array('attr' => array('class' => 'mdl-textfield__error'))) ?>
		<label class="mdl-textfield__label" for="login_form_password">Password:</label>
		<?= $view['form']->widget($form['password'], array('attr' => array('pattern' => '.{6,}',  'class' => 'mdl-textfield__input'))) ?>
<?php if(array_key_exists('password', $errors)): ?>
		<span class="mdl-textfield__error"><?= $errors['password'] ?></span>
<?php else: ?>
		<span class="mdl-textfield__error">Password should contain at least 6 characters</span>
<?php endif ?>
	</div>

	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label<?php if(array_key_exists('password_confirm', $errors)): ?> is-invalid<?php endif ?>" style="width: 248px">
		<i class="material-icons">lock_outline</i>
		<?= $view['form']->errors($form['password_confirm'], array('attr' => array('class' => 'mdl-textfield__error'))) ?>
		<label class="mdl-textfield__label" for="login_form_password">Password (repeat):</label>
		<?= $view['form']->widget($form['password_confirm'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
<?php if(array_key_exists('password_confirm', $errors)): ?>
		<span class="mdl-textfield__error"><?= $errors['password_confirm'] ?></span>
<?php else: ?>
		<span class="mdl-textfield__error">Passwords should match each other</span>
<?php endif ?>
	</div>

	</div>
	<br/>
	<div class="">
		<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" type="submit">Sign Up</button>
	</div>


            <div class="additional_buttons">
                <ul>
                    <li>
                        <a href="<?= $view['router']->path('login') ?>">Login</a>
                    </li>
                    <li>
                        <a href="#">Lost Password</a>
                    </li>
                </ul>
            </div>


</fieldset>

</form>

</div>