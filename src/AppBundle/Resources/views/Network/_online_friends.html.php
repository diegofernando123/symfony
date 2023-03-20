       <div class="colum_block user_friends">
            <div class="title_block">
                <a href="<?= $view['router']->path('network_index') ?>">All friends</a>
                <strong>Friends Online<?php if(count($records) > 0): ?> <span>(<?= count($records) ?>)</span><?php endif ?></strong>
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
                            <a href="/user/<?= $user['id'] ?>"><i class="fa fa-eye" aria-hidden="true"></i><span>View</span></a>
                            <a class="send-message" href="#" style="white-space: nowrap"><i class="fa fa-pencil" aria-hidden="true"></i><span data-user-id="<?= $user['id'] ?>" data-user-name="<?= $view->escape($user['name']) ?>" >Message</span></a>
                        </div>
                    </div>
                    <a href="/user/<?= $user['id'] ?>" class="friend_name"><?= $view->escape($user['name']) ?></a>
                </div>

<?php endforeach ?>
 
            </div>  <!-- items -->
        </div>    <!-- Friends Online -->