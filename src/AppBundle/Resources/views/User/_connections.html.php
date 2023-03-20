                    <!-- connections_list -->
                    <div class="colum_block trending_media">
                        <div class="title_block">
<?php if($user->hasConnections()): ?>
                            <a href="<?= $view['router']->path('user_connections', array('user' => $user->getId())) ?>">View All</a>
<?php endif ?>
                            <strong><?= $user->getName() ?> Connections<?php if($user->hasConnections()): ?><span>(<?= $user->getNumConnections() ?>)</span><?php endif ?></strong>
                        </div>
                        <div class="items tranding_photo_slider">
<?php if(!$user->hasConnections()): ?>
	<p>This user has no connections</p>
<?php endif ?>
<?php foreach($user->getFriends() as $user): ?>
							<div class="item">
                    			<div class="img">
<?php if(strlen($user['avatar']) > 0): ?>
                        			<img src="/bundles/framework/images/user/<?= $user['avatar'] ?>" alt=""/>
<?php else: ?>
                        			<img src="/bundles/framework/images/user/no_avatar.png" alt=""/>
<?php endif ?>
                        			<div class="friend_name" style="margin-top: 5px"><?= $user['name'] ?></div>
                    			</div>
                       			<div class="item_action" style="text-align: center;">
                            		<a href="<?= $view['router']->path('user_show', array('user' => $user['id'])) ?>"><i class="fa fa-eye" aria-hidden="true"></i> View</a>
                        		</div>
                			</div>
<?php endforeach ?>
                        </div><!-- items -->
                    </div>
                    <!-- connections_list -->
