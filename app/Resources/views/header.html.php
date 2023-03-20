<!-- header -->
<header id="header" class="mdl-layout__header is-compact">
  <div>
    <div class="ts_masthead">
        <div class="masthead_batton"><i class="fa fa-bars" aria-hidden="true"></i><div class="circle_num"><strong>2</strong></div></div>
    </div>
    <div class="logo mdl-layout-title">
        <a <?php if ($view['app']->isLogged()): ?>href="<?= $view['router']->path('post_index', array('user' => $view['app']->getUserId())) ?>"<?php else: ?>href="/"<?php endif ?>>
            <img class="fool_logo" src="/bundles/framework/upload/logo.png" alt="Trade My Share"/>
            <img class="short_logo" src="/bundles/framework/upload/smal_logo.jpg" alt="Trade My Share"/>
        </a>
    </div>
    <div class="search_block">
        <form action="" class="search_form">
            <div class="form-item">
                <label></label>
                <input type="text" class="mail mandatory" name="mail" value="" placeholder="Search on TradeToShare" />
            </div>
            <div class="form-ection">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input type="submit" class="submit" value="" />
            </div>
        </form>
    </div>
    <?php if ($view['app']->isLogged()): ?>
    <nav>
        <div class="main_menu">
            <ul>
                <li class="first <?= $view['app']->getActiveMenu() == "post_index" ? " active" : "" ?>"><a href="<?= $view['router']->path('post_index', array('user' => $view['app']->getUserId())) ?>">Home</a></li>
                <li class="<?= $view['app']->getActiveMenu() == "network_index" ? " active" : "" ?>"><a href="<?= $view['router']->path('network_index') ?>">Network</a></li>
                <!--<li class="<?= $view['app']->getActiveMenu() == "post_index" ? " active" : "" ?>"><a href="<?= $view['router']->path('tradeland_new') ?>">Create a Tradeland</a></li>                                                   
                <li class="<?= $view['app']->getActiveMenu() == "post_index" ? " active" : "" ?>"><a href="<?= $view['router']->path('company_index') ?>">Companies</a></li>
                <li class="last <?= $view['app']->getActiveMenu() == "post_index" ? " active" : "" ?>"><a href="<?= $view['router']->path('article_index') ?>">Articles</a></li>-->
                <!--li class="last <?= $view['app']->getActiveMenu() == "post_index" ? " active" : "" ?>"><a href="<?= $view['router']->path('feedback_new') ?>">Contact us</a></li-->
            </ul>
        </div>
    </nav>
    <?php endif ?>
    <div class="header_right">
    	<?php if ($view['app']->isGranted("IS_AUTHENTICATED_REMEMBERED")): ?>
        <!-- Notifications -->
        <?= $view['actions']->render("/notification/") ?>
        <!-- End Notifications -->

        <?= $view['actions']->render("/message/header") ?>

        <div class="user_account">
            <div class="more_actions">
                <div class="us_pict">
                	<?php if ($view['app']->getUser()->getAvatar() != null): ?>
                        <span class="user_account_img"><img src="/bundles/framework/images/user/<?= $view['app']->getUser()->getAvatar() ?>" alt=""/></span>
                    <?php else: ?>
                        <span class="user_account_img"><img src="/bundles/framework/images/no_avatar.png" alt=""/></span>
                    <?php endif ?>
                    <span class="user_account_name"><?= $view->escape($view['app']->getUser()->getName()) ?></span>
                </div> <!--  link on user profile -->
                <i class="fa fa-caret-down" aria-hidden="true"></i>
                
                <div class="more_actions_wrapper">
                    <ul class="">
                        <li class=""><a href="<?= $view['router']->path('user_show', array('user' => $view['app']->getUserId())) ?>">My profile</a></li>
                        <li class=""><span class="line"></span></li>
                        <li class=""><a href="<?= $view['router']->path('user_edit') ?>">Edit</a></li>
                        <li class=""><a href="<?= $view['router']->path('tradeland_invite') ?>">Invite Friends</a></li>
                        <li class=""><a href="<?= $view['router']->path('user_network') ?>">My Network</a></li>
                        <li class=""><span class="line"></span></li>
                        <li class=""><a href="<?= $view['router']->path('fos_user_security_logout') ?>">Logout</a></li>
                    </ul>
                </div>
            </div>
            <div class="user-status">
            	<?php if ($view['app']->getUser()->getStatus() == 'away'): ?>
            		<?php $statusClass = 'fa-clock-o' ?>
            	<?php elseif( $view['app']->getUser()->getStatus() == 'busy' ): ?>
            		<?php $statusClass = 'fa-minus-circle' ?>
            	<?php elseif( $view['app']->getUser()->getStatus() == 'invisible' ): ?>
            		<?php $statusClass = 'fa-circle-o' ?>
            	<?php elseif( $view['app']->getUser()->getStatus() == 'online' ): ?>
            		<?php $statusClass = 'fa-check-circle' ?>
            	<?php endif ?>
                <div class="user-status_current"><i class="fa <?= $statusClass ?>" aria-hidden="true"></i></div>
                <div class="user-statuses">
                    <ul class="">
                        <li class="item_away <?php if ($view['app']->getUser()->getStatus() == 'away'): ?>activ<?php endif ?><i class="fa fa-clock-o" aria-hidden="true"></i><span data-status="away"> - Away</span></li>
                        <li class="item_online <?php if ($view['app']->getUser()->getStatus() == 'online'): ?>active<?php endif ?><i class="fa fa-check-circle" aria-hidden="true"></i><span data-status="online"> - Online</span></li>
                        <li class="item_invisible <?php if ($view['app']->getUser()->getStatus() == 'invisible'): ?>active<?php endif ?><i class="fa fa-circle-o" aria-hidden="true"></i><span data-status="invisible"> - Invisible</span></li>
                        <li class="item_busy <?php if ($view['app']->getUser()->getStatus() == 'busy'): ?>active<?php endif ?><i class="fa fa-minus-circle" aria-hidden="true"></i><span data-status="busy"> - Busy</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php else: ?>
            <div class="user_account">
                <div class="more_actions">
                    <a href="<?= $view['router']->path('fos_user_security_login') ?>">{{ 'layout.login'|trans({}, 'FOSUserBundle') }}</a>
                </div>
            </div>
    	<?php endif ?>
    </div>
  </div>
</header><!-- end header -->

<!-- Navigation -->

<div class="mdl-layout__drawer">

	<span class="mdl-layout-title" style="padding-bottom: 13px;">Menu</span>
	<nav class="mdl-navigation">
<?php if ($view['app']->isLogged()): ?>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "post_index" ? " mdl-navigation__link--current" : "" ?>" href="<?= $view['router']->path('post_index', array('user' => $view['app']->getUserId())) ?>">

                <?php if($view['app']->getUser()->getAvatar() != null): ?>
                                <span class="img"><img src="/bundles/framework/images/user/<?= $view['app']->getUser()->getAvatar() ?>" alt=""/></span>
                <?php else: ?>
                                <i class="material-icons">account_circle</i>
                <?php endif ?> My Profile</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "news" ? " mdl-navigation__link--current" : "" ?>" href="#"><i class="material-icons">art_track</i> News</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "bookmarks" ? " mdl-navigation__link--current" : "" ?>" href="#"><i class="material-icons">bookmark</i> Bookmark</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "message_index" ? " mdl-navigation__link--current" : "" ?>" href="<?= $view['router']->path('message_index') ?>"><i class="material-icons">chat</i> Messages</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "network_index" ? " mdl-navigation__link--current" : "" ?>" href="<?= $view['router']->path('user_network') ?>"><i class="material-icons">people</i> Friends</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "tradeland" ? " mdl-navigation__link--current" : "" ?>" href="<?= $view['router']->path('tradeland_index') ?>"><i class="material-icons">share</i> Tradeland</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "photos" ? " mdl-navigation__link--current" : "" ?>" href="<?= $view['router']->path('photo_index') ?>"><i class="material-icons">panorama</i> Photos</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "music" ? " mdl-navigation__link--current" : "" ?>" href="#"><i class="material-icons">queue_music</i> Music</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "videos" ? " mdl-navigation__link--current" : "" ?>" href="<?= $view['router']->path('video_index') ?>"><i class="material-icons">local_movies</i> Videos</a>
		<a class="mdl-navigation__link<?= $view['app']->getActiveMenu() == "games" ? " mdl-navigation__link--current" : "" ?>" href="#"><i class="material-icons">videogame_asset</i> Games</a>

<?php $trade_posts = $view['app']->getUser()->getTrade_posts() ?>

<?php if(count($trade_posts) > 0): ?>
<div class="masthead-container" style="display: block; position: static;">
    <div class="masthead-container_wrapper">
        <div class="masthead-container_block user_tradeland">
            <div class="title_block">
                 <strong>My Tradeland</strong>
            </div>
            <ul>
                <?php foreach($trade_posts as $post): ?>
            	<li>
                	<a href="/tradeland/<?= $post['tradeland_id'] ?>">
                		<span class="img">
        <?php if($post['avatar'] != null): ?>
           <img src="/bundles/framework/images/user/<?= $post['avatar'] ?>"  alt="><?= $view->escape($post['name']) ?>">
        <?php else: ?>
           <img src="/bundles/framework/images/no_avatar.png" alt="><?= $view->escape(substr($post['name'], 100)) ?>">
        <?php endif ?>
                		</span>
                		<span class="link_name"><?= $view->escape($post['text']) ?>.</span>
                    </a>
                </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>
<?php endif ?>



<?php else: ?>
		<a class="mdl-navigation__link mdl-navigation__link--current" href="<?= $view['router']->path('login') ?>"><i class="material-icons">exit_to_app</i> Login</a>
		<a class="mdl-navigation__link" href="<?= $view['router']->path('feedback_new') ?>"><i class="material-icons">mail_outline</i> Contact Us</a>
<?php endif ?>
	</nav>

</div>