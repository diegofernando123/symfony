
                    <!-- media_list -->
                    <div class="colum_block trending_media">
                        <div class="title_block">
<?php if($user->hasMedia()): ?>
                            <a href="#">View All</a>
<?php endif ?>
                            <strong>Photos and Videos<?php if($user->hasMedia()): ?> <span>(<?= $user->getNumMedia() ?>)</span><?php endif ?></strong>
                        </div>
                        <div class="items tranding_photo_slider">
<?php if(!$user->hasMedia()): ?>
							<p>This user has no photos or videos.</p>
<?php endif ?>
<?php foreach($user->getPhotosAndVideos() as $record): ?>
                            <div class="item view_<?= $record['type'] ?>">
                                <div class="img"><img src="<?= $record['type'] == "photo" ? ("/bundles/framework/images/photo/" . $record['image']) : ("https://i.ytimg.com/vi/" . $record['image'] . "/default.jpg") ?>" alt=""/></div>
                                <div class="item_action">
<?php if($record['type'] == 'photo'): ?>
                                    <p><i class="fa fa-eye" aria-hidden="true"></i> View</p>
<?php else: ?>
                                    <p><i class="fa fa-caret-square-o-right" aria-hidden="true"></i> Play</p>
<?php endif ?>
                                </div>
                            </div>
<?php endforeach ?>

                         </div><!-- items -->
                    </div>    <!-- media_list -->
