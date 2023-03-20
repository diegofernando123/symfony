<div class="colum_left">
    <div class="colum_wrapper" id="left_colum_wrapper" style="">
        <div class="colum_block user_profile">
            <div class="user_profile_wrapper">
                <div class="user_photo view_photo ">
                	<?php if($view['app']->getUser()->getAvatar()): ?>
                        <span class="user_account_img"><img src="/bundles/framework/images/user/<?= $view['app']->getUser()->getAvatar() ?>" alt=""/></span>
                 	<?php else: ?>
                        <span class="user_account_img"><img src="/bundles/framework/images/no_avatar.png" alt=""/></span>
                	<?php endif ?>
                </div>
                <div class="edit_user_profile">
                    <a href="#" class="refresh-photo"><i class="fa fa-camera" aria-hidden="true"></i><span class="link_name">Refresh photo</span></a>
                    <a href="<?= $view['router']->path('user_edit') ?>"><i class="fa fa-cog" aria-hidden="true"></i><span class="link_name">Edit profile</span></a>
                </div>
            </div>
            <div class="user_profile_socials">
                <div class="title_block">
                    <strong>Social Media</strong>
                </div>
                    <ul class="networks-list" style="height: auto;">
                    	<?php foreach($view['app']->getUser()->getProviders() as $provider): ?>
                        <li class="<?= $provider->getName() ?>  " data-provider="<?= $provider->getName() ?>" title="<?= $provider->getName() ?>">
                            <span></span>
                            <input class="provider_current" type="hidden" value="<?= $provider->getName() ?>">
                            <div class="media_actions">                                
                                <div class="item Delete"><a href="#" class="social action_item" data-action="remove">Delete</a></div>
                                <div class="item get_post"><a href="#" class="social action_item" data-action="reload">Get Post</a></div>
                                <div class="item invite_friends"><a class="social" href="<?= $view['router']->path('tradeland_invite') ?>">Invite Friends</a></div>
                            </div>
                        </li>
                        <?php endforeach ?>
                    </ul>
                <div class="add_new_network">
                    <span class="batton">Add new network</span>
                </div>
            </div>
        </div>
    </div>
</div><!-- colum_left -->