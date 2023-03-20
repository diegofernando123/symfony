<?php $view->extend("::layout.html.php") ?>
<?php $view['slots']->set("title", 'Messages') ?>
<?php $view['slots']->set("page_id", "messages") ?>
<?php $view['slots']->set("left_column", true) ?>
<?php $view['slots']->set("right_column", true) ?>
<?php $view['slots']->set('modals', true) ?>

<script>

	$(document).ready(function() {
		$("#contacts .mdl-list__item-primary-content,#chats .mdl-list__item-primary-content").click(function(e) {
			$(".chat-content.mdl-cell").attr('id', "chat-window-" + $(this).data('user')).html("<div class=\"mdl-list\" style=\"text-align: left; padding-bottom: 0; padding-top: 0; padding-right: 0; background-color: #fff; border-bottom: 1px solid #ddd;\">\
\
					  <div class=\"mdl-list__item mdl-list__item--two-line\" style=\"padding-right: 10px; padding-top: 10px; padding-bottom: 10px\">\
					    <a class=\"mdl-list__item-primary-content\">\
					       <span class=\"user-presense-" + $(this).data('user').substring(1) + " presense " + $(this).data('presense') + "\"></span>\
					       <img class=\"mdl-list__item-avatar\" src=\"/bundles/framework/images/" + ($(this).data('avatar').length > 0 ? "user/" + $(this).data('avatar') : "no_avatar.png") + "\">\
					       <span><strong>" + $(this).data('name') + "</strong></span>\
					       <span class=\"mdl-list__item-sub-title\"></span>\
					    </a>\
					    <div class=\"mdl-list__item-secondary-action\">\
\
						<button onclick=\"start_audio_call('" + $(this).data('user') + "')\" class=\"mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-color-text--blue-600\" style=\"box-shadow: none; background-color: transparent; \">\
		  					<i class=\"material-icons\">call</i>\
						</button>\
\
						<button onclick=\"start_video_call('" + $(this).data('user') + "')\" class=\"mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-color-text--blue-600\" style=\"box-shadow: none; background-color: transparent;\">\
		  					<i class=\"material-icons\">videocam</i>\
						</button>\
\
						</div>\
					</div>\
\
					</div><iframe allowfullscreen id=\"chat-content-" + $(this).data('user') + "-iframe\" allow=\"geolocation; microphone; camera; fullscreen\" style=\"height: 100%\" src=\"https://tradetoshare.com:8080/account/profile?layout=embedded\"></iframe>");
		});

		$("#search-box").on("keyup", function (e) {
			var inp = $("#search-box").val().toLowerCase();

			if(inp.length == 0) {
				$(".mdl-list__item").removeClass('hidden');
			} else {
				$(".mdl-list__item .mdl-list__item-primary-content").each(function() {
					var name = $(this).data('name').toLowerCase();
					var msg = $(this).find(".mdl-list__item-sub-title").text();

					if(name.indexOf(inp) === -1 && msg.indexOf(inp) === -1) {
						$(this).parent().addClass('hidden');
					} else {
						$(this).parent().removeClass('hidden');
					}
				});
			}
		});

<?php if($active_user): ?>
		$(".mdl-list__item-primary-content.user-presense-<?= substr($active_user, 1) ?>").click();
<?php endif ?>

	});

</script>

<h2 class="section_title">Messages</h2>

<div class="mdl-grid">

	<div class="mdl-cell mdl-cell--5-col">

<form action="#">
  <div class="mdl-textfield mdl-js-textfield" id="search-box-input">
	<i class="material-icons">search</i>
    <input class="mdl-textfield__input" type="text" id="search-box">
    <label class="mdl-textfield__label" for="search-box">Search for people....</label>
  </div>
</form>

<div class="mdl-tabs mdl-js-tabs mdl-js-ripple-effect">

  <div class="mdl-tabs__tab-bar">

      <a href="#contacts" class="mdl-tabs__tab<?= $active_tab == "contacts" ? " is-active" : ""?>">
	    <i class="material-icons">recent_actors</i>
      	Contacts
      </a>

<?php if(count($contacts) > 0): ?>

      <a href="#chats" class="mdl-tabs__tab<?= $active_tab == "chats" ? " is-active" : ""?>">
      	<i class="material-icons">chat</i>
      	Chats
      </a>

<?php endif ?>

  </div>

  <div class="mdl-tabs__panel<?= $active_tab == "contacts" ? " is-active" : ""?>" id="contacts" style="max-height: 100%; height: 500px; overflow: auto">
		<div class="mdl-list">

<?php if(count($contacts) == 0): ?>

			  <div class="mdl-list__item mdl-list__item--two-line">
			    <span class="mdl-list__item-primary-content">
			       <span class="mdl-list__item-sub-title">You don't have any contacts yet.</span>
			       <span class="mdl-list__item-sub-title">Please consider to add new one.</span>
			    </span>
			  </div>

<?php endif ?>

<?php foreach($contacts as &$contact): ?>

			  <div class="mdl-list__item mdl-list__item--two-line">
			    <a class="user-presense-<?= $contact['id'] ?> mdl-list__item-primary-content" href="#" data-name="<?= $view->escape($contact['name']) ?>" data-presense="<?= $contact['presense'] ?>" data-avatar="<?= $contact['avatar'] ?>" data-user="u<?= $contact['id'] ?>">
			       <span class="user-presense-<?= $contact['id'] ?> presense <?= $contact['presense'] ?>"></span>
			       <img class="mdl-list__item-avatar" src="/bundles/framework/images/<?= $contact['avatar'] ? "user/" . $contact['avatar'] : "no_avatar.png" ?>">
			       <span><?= $view->escape($contact['name']) ?></span>
			       <span class="mdl-list__item-sub-title"><?= $view->escape($contact['position']) ?></span>
			    </a>
			  </div>

<?php endforeach ?>

			</div>

		</div>

    <div class="mdl-tabs__panel<?= $active_tab == "chats" ? " is-active" : ""?>" id="chats" style="height: 500px">
		<div class="mdl-list">

<?php foreach($history as &$record): ?>

<?php if(isset($record->lastMessage)): ?>

			  <div class="mdl-list__item mdl-list__item--two-line">
			    <a class="user-presense-<?= $record->user['id'] ?> mdl-list__item-primary-content" href="#" data-name="<?= $view->escape($record->user['name']) ?>" data-presense="<?= $record->user['presense'] ?>" data-avatar="<?= $record->user['avatar'] ?>" data-user="u<?= $record->user['id'] ?>">
			       <span class="user-presense-<?= $record->user['id'] ?> presense <?= $record->user['presense'] ?>"></span>
			       <img class="mdl-list__item-avatar" src="/bundles/framework/images/<?= $record->user['avatar'] ? "user/" . $record->user['avatar'] : "no_avatar.png" ?>">
			       <span><?= $view->escape($record->user['name']) ?></span>
			       <span class="mdl-list__item-sub-title">
			       		<?= $view->escape($record->lastMessage->msg) ?>
			       </span>
			    </a>
			    <span class="mdl-list__item-secondary-content">
			       <?= $view['date']->show($record->lm) ?>
			    </span>
			  </div>

<?php endif ?>

<?php endforeach ?>

		</div>
	</div>

</div>

	</div>

	<div class="mdl-cell mdl-cell--7-col chat-content" style="text-align: center">

			<h2 class="section_title">Hello, <?= $view->escape($view['app']->getUser()->getName()) ?></h2>

			<div class="profile-photo">
                	<?php if($view['app']->getUser()->getAvatar()): ?>
                        <img src="/bundles/framework/images/user/<?= $view['app']->getUser()->getAvatar() ?>" alt=""/>
                 	<?php else: ?>
                        <img src="/bundles/framework/images/no_avatar.png" alt=""/>
                	<?php endif ?>
			</div>

<?php if(count($contacts) == 0): ?>
			<a class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" href="<?= $view['router']->path('network_index') ?>">
  				Add Contact
			</a>
<?php endif ?>

	</div>

</div>


