
        <div class="colum_block user_friends">
            <div class="title_block">
                <a href="<?= $view['router']->path('tradeland_invite') ?>">Invite firends</a>
                <strong>People you may know</strong>
            </div>
            <div class="items">

<?php foreach($records as $user): ?>

                <div class="item">
                    <div class="img">
<?php if ($user['avatar'] != null): ?>
                        <img src="/bundles/framework/images/user/<?= $view->escape($user['avatar']) ?>" alt=""/>
<?php else: ?>
						<img src="https://tradetoshare.com/bundles/framework/images/no_avatar.png" alt="" />
<?php endif ?>
						<div class="actions">
                            <a href="<?= $view['router']->path('user_show', array('user' => $user['id'])) ?>"><i class="fa fa-eye" aria-hidden="true"></i><span>View</span></a>
<?php /*                    <a href="<?= $view['router']->path('network_connect', array('user' => $user['id'])) ?>"><i class="fa fa-plus" aria-hidden="true"></i><span>Connect</span></a>  */ ?>
                         </div>
                    </div>
                    <a href="/user/<?= $user['id'] ?>" class="friend_name"><?= $view->escape($user['name']) ?></a>
                </div>

<?php endforeach ?>

            </div>  <!-- items -->
        </div>    <!-- Recently Friends -->
