<?php $view->extend("::layout.html.php") ?>

<div>

<form method="post" action="<?= $view['router']->path('user_edit_experience') ?>" class="mdl-labels-on-left" enctype="multipart/form-data">

<?php if($experience->getId() != null): ?>

	<input name="id" type="hidden" value="<?= $experience->getId() ?>"/>

<?php endif ?>

<?= $view['form']->widget($form['_token']) ?>

<fieldset>

<h1 class="mdl-typography--display-3 mdl-color-text--blue-900">Company</h1>

<div>
	<div class="mdl-textfield mdl-js-textfield<?php if(array_key_exists('company', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">business</i>
		<label class="mdl-textfield__label" for="appbundle_experience_company">Company Name<span>*</span>:</label>
		<?= $view['form']->widget($form['company'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">Company Name is required</span>
	</div>
</div>

<div>
	<div class="mdl-textfield mdl-js-textfield<?php if(array_key_exists('title', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">work_outline</i>
		<label class="mdl-textfield__label" for="appbundle_experience_title">Job Title<span>*</span>:</label>
		<?= $view['form']->widget($form['title'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">Job Title is required</span>
	</div>
</div>

<div>
	<div class="mdl-textfield mdl-js-textfield<?php if(array_key_exists('industry', $errors)): ?> is-invalid<?php endif ?>" style="padding-right: 33px;">
		<i class="material-icons">category</i>
		<label class="mdl-textfield__label" for="appbundle_experience_industry" style="width: calc(100% - 33px)">Industry<span>*</span>:</label>
		<?= $view['form']->widget($form['industry'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">Industry is required</span>
	</div>
</div>

<div>
	<div class="mdl-textfield mdl-js-textfield<?php if(array_key_exists('location', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">room</i>
		<label class="mdl-textfield__label" for="appbundle_experience_location">Location<span>*</span>:</label>
		<?= $view['form']->widget($form['location'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">Location is required</span>
	</div>
</div>

<div>
	<div class="mdl-textfield mdl-js-textfield<?php if(array_key_exists('specialisation', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">group_work</i>
		<label class="mdl-textfield__label" for="appbundle_experience_specialisation">Specialization:</label>
		<?= $view['form']->widget($form['specialisation'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		<span class="mdl-textfield__error">Specialisation is required</span>
	</div>
</div>

<div>
	<div class="mdl-textfield mdl-js-textfield<?php if(array_key_exists('start', $errors) || array_key_exists('end', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">access_time</i>
		<label class="mdl-textfield__label" for="appbundle_experience_startYear">Time period<span>*</span>:</label>

		<span style="width: 75px; display: inline-block; margin-left: 35%">
			<?= $view['form']->widget($form['startMonth'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		</span>
		<span style="width: 100px; display: inline-block;">
			<?= $view['form']->widget($form['startYear'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
		</span>
		
		<span id="timeperiod-right-side">

			-

			<span style="width: 75px; display: inline-block;">
				<?= $view['form']->widget($form['endMonth'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
			</span>
			<span style="width: 100px; display: inline-block;">
				<?= $view['form']->widget($form['endYear'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
			</span>
		
		</span>

		<span class="mdl-textfield__error">
<?php if(array_key_exists('start', $errors)): ?>
	<?= $errors['start'] ?>
<?php elseif(array_key_exists('end', $errors)): ?>
	<?= $errors['end'] ?>
<?php else: ?>
	Time period is required
<?php endif ?>
		</span>
	</div>
</div>

<div>
	<div class="mdl-textfield <?php if(array_key_exists('isCurrent', $errors)): ?> is-invalid<?php endif ?>">
		<label class="mdl-textfield__label" for="appbundle_experience_isCurrent">I currently work here:</label>
		<?= $view['form']->widget($form['isCurrent'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
	</div>
</div>

<div>
		
<?php if($experience->getLogo() != null): ?>

		<img src="/bundles/framework/images/company/<?= $experience->getLogo() ?>">

<?php endif ?>
	<div class="mdl-textfield mdl-js-textfield<?php if(array_key_exists('logo', $errors)): ?> is-invalid<?php endif ?>">
		<i class="material-icons">attach_file</i>
		
		
		<label class="mdl-textfield__label" for="appbundle_experience_logo">Upload/Change Company Logo:</label>
		<?= $view['form']->widget($form['logo'], array('attr' => array('class' => 'mdl-textfield__input'))) ?>
	</div>
</div>

<br/>

<div>

	<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" type="submit">Submit</button>
	<a href="<?= $view['router']->path('user_show', array('user' => $user->getId())) ?>" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" type="button">Cancel</a>

</div>

</fieldset>

</form>

</div>

<script>

	$(".mdl-textfield__input[required=required]").each(function() {
		$(this).removeAttr("required").attr("data-required", "true");
	});

	$(document).ready(function() {
		toggleTimePeriod(document.getElementById('appbundle_experience_isCurrent'));
	});

	$(window).load(function() {
		setTimeout(function() {
			$("input[data-required=true]").each(function() {
				$(this).attr("required", "required").removeAttr("data-required");
			});
			$("select[data-required=true]").each(function() {
				$(this).attr("required", "required").removeAttr("data-required");
			});
		}, 200);
	});

	function toggleTimePeriod(e) {
		document.getElementById('timeperiod-right-side').style.visibility = e.checked ? 'hidden' : 'visible';
	}

    $(document).ready(function() {
		$("#appbundle_experience_location" ).autocomplete({
      		source: "/place/autocomplete",
      		minLength: 2,
      		select: function( event, ui ) {
      		}
		});
    });
    </script>
