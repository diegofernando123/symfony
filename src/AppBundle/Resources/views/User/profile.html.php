<?php $view->extend("::layout.html.php") ?>

<?php $view['slots']->set("title", $user->getName()) ?>
<?php $view['slots']->set("page_id", "profile_page") ?>
<?php $view['slots']->set('left_column', true) ?>
<?php $view['slots']->set('right_column', true) ?>
<?php $view['slots']->set('modals', true) ?>

<?php if($view['app']->isMe($user->getId())): ?>

<script>
	$(document).ready(function() {
		$(".delete-link").click(function() {

			$(this).addClass("selected");

  			var dialog = document.getElementById('delete-confirmation');
  			dialogPolyfill.registerDialog(dialog);
   			dialog.querySelector('.yes').addEventListener('click', function() {
      			dialog.close();
      			window.location.href = '<?= $view['router']->path('user_delete_experience') ?>?id=' + $(".delete-link.selected").attr('data-id');
    		});
  			dialog.querySelector('.close').addEventListener('click', function() {
      			dialog.close();
    		});
   			dialog.showModal();
   			return false;
		});
	});
</script>

 <dialog class="mdl-dialog" id="delete-confirmation" style="min-width: 30%">
    <h4 class="mdl-dialog__title">Confirmation Delete</h4>
    <div class="mdl-dialog__content">
      <p>
        Are you sure to delete this record?
      </p>
    </div>
    <div class="mdl-dialog__actions">
      <button type="button" class="mdl-button close">Cancel</button>
      <button type="button" class="mdl-button yes">Yes</button>
    </div>
</dialog>
<?php endif ?>

<script>

	function sendMessage(user_id, name) {

		var uname = "u" + user_id;
		var dialog = document.getElementById("chat-window-" + uname);

		if(typeof(dialog) == 'undefined' || dialog == null) {

			var el = $("<dialog/>");
			el.attr("id", "chat-window-" + uname);
			el.addClass("mdl-dialog");
			el.attr("style", "width: 600px; height: 600px; max-width: 100%; max-height: 100%;");

			$("body").append(el);

			dialog = document.getElementById("chat-window-" + uname);
			dialogPolyfill.registerDialog(dialog);

		} else {
			if(typeof(dialog.showModal) == 'function') {
				if(!dialog.hasAttribute('open')) {
					dialog.showModal();
				}
			}

			return;
		}

		dialog.showModal();
		dialog.addEventListener('click', function (event) {
    		var rect = dialog.getBoundingClientRect();
    		var isInDialog=(rect.top <= event.clientY && event.clientY <= rect.top + rect.height && rect.left <= event.clientX && event.clientX <= rect.left + rect.width);
    		if (!isInDialog) {
        		dialog.close();
    		}
		});
		dialog.addEventListener('close', function (event) {
         	$(dialog).remove();
 		});

		$(dialog).addClass('chat-content').html("<div class=\"mdl-list\" style=\"text-align: left; padding-bottom: 0; padding-top: 0; padding-right: 0; background-color: #fff; border-bottom: 1px solid #ddd;\">\
\
					  <div class=\"mdl-list__item mdl-list__item--two-line\" style=\"padding-right: 10px; padding-top: 10px; padding-bottom: 10px\">\
					    <a class=\"mdl-list__item-primary-content\">\
					       <span class=\"user-presense-" + uname.substring(1) + " presense\"></span>\
					       <img class=\"mdl-list__item-avatar\" src=\"/user/avatar/" + uname.substring(1) + "\">\
					       <span><strong>" + name + "</strong></span>\
					       <span class=\"mdl-list__item-sub-title\"></span>\
					    </a>\
					    <div class=\"mdl-list__item-secondary-action\">\
\
							<button onclick=\"start_audio_call('" + uname + "')\" class=\"mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-color-text--blue-600\" style=\"box-shadow: none; background-color: transparent; \">\
			  					<i class=\"material-icons\">call</i>\
							</button>\
\
							<button onclick=\"start_video_call('" + uname + "')\" class=\"mdl-button mdl-js-button mdl-button--fab mdl-js-ripple-effect mdl-color-text--blue-600\" style=\"box-shadow: none; background-color: transparent;\">\
			  					<i class=\"material-icons\">videocam</i>\
							</button>\
\						</div>\
					</div>\
\
					</div><iframe data-room_id=\"" + uname + "\" allowfullscreen id=\"chat-content-" + uname + "-iframe\" allow=\"geolocation; microphone; camera; fullscreen\" style=\"height: calc(100% - 73px)\" src=\"https://tradetoshare.com:8080/account/profile?layout=embedded\"></iframe>");

       	$.ajax({
           	url: '/user/presense',
            data: {
             	id: uname.substring(1)
            },
            type: "POST",
            success: function(data) {
				$("span.user-presense-" + uname.substring(1)).removeClass('offline').removeClass('busy').removeClass('away').removeClass('online').removeClass('invisible').addClass(data.status);
				$("a.user-presense-" + uname.substring(1)).attr("data-presense", data.status);
            }
        });

	}

	function unVote(el, user_id) {
        $.ajax({
            url: '/user/unvote',
            data: {
             	id: user_id
            },
            type: "POST",
            success: function (data) {
               	if(data.status == 'OK') {
              		$(el).addClass('hidden').find('span').html("Voted <strong>" + data.votes + "</strong>");
               		$("#vote-btn").removeClass('hidden').find('span').html("Vote <strong>" + data.votes + "</strong>");

              		var notification = document.querySelector('.mdl-js-snackbar');
					notification.MaterialSnackbar.showSnackbar(
  						{
    						message: 'You unvoted for this user!'
  						}
					);

					$(".ms_item_postes .more_actions").remove();

					if(data.votes > 0) {
						var info = $("<div class=\"more_actions\" style=\"left: auto; right: 15px; width: 150px\"/>");
						var ul = $("<ul/>");
						info.append(ul);
						ul.append("<li>&nbsp;&nbsp;&nbsp;&nbsp;Voted users:</li>");

						for(var i in data.users) {
							ul.append("<li><a href=\"/user/" + data.users[i].id + "\">" + data.users[i].name + "</a></li>");
						}

						$(".ms_item_postes").append(info);
					}
             	}
            }, error: function (data) {
                console.log('error');
            }
        });
	}

	function doVote(el, user_id) {
        $.ajax({
            url: '/user/vote',
            data: {
             	id: user_id
            },
            type: "POST",
            success: function (data) {
               	if(data.status == 'OK') {
              		$(el).addClass('hidden').find('span').html("Vote <strong>" + data.votes + "</strong>");
               		$("#unvote-btn").removeClass('hidden').find('span').html("Voted <strong>" + data.votes + "</strong>");

              		var notification = document.querySelector('.mdl-js-snackbar');
					notification.MaterialSnackbar.showSnackbar(
  						{
    						message: 'You voted for this user!'
  						}
					);

					$(".ms_item_postes .more_actions").remove();

					if(data.votes > 0) {
						var info = $("<div class=\"more_actions\" style=\"left: auto; right: 15px; width: 150px\"/>");
						var ul = $("<ul/>");
						info.append(ul);
						ul.append("<li>&nbsp;&nbsp;&nbsp;&nbsp;Voted users:</li>");

						for(var i in data.users) {
							ul.append("<li><a href=\"/user/" + data.users[i].id + "\">" + data.users[i].name + "</a></li>");
						}

						$(".ms_item_postes").append(info);
					}
             	}
            }, error: function (data) {
                console.log('error');
            }
        });
	}

	$(document).ready(function() {

        $(document).on('click', '.subscribe_block .message', function (e) {
            e.preventDefault();

            let msg = {
  				msg: 'changed',
  				collection: 'stream-room-messages',
  				id: String(messagesCount++),
  				fields: {
  					args: [
  						[
  							{
  								u: {
  									username: 'u<?= $view->escape($user->getId()) ?>',
  									name: '<?= $view->escape($user->getName()) ?>'
  								},
  								rid: 'u<?= $view->escape($user->getId()) ?>'
  							}
  						]
  					]
  				}
			};

            socket.onmessage({
            	data: JSON.stringify(msg)
            });
        });

        $(document).on('click', '.subscribe_block .connect', function (e) {
            e.preventDefault();
            var el = $(this),
                connect = el.data('connect'),
                url = '<?= $view['router']->path('network_connect', array('user' => 'user_id')) ?>';

            var statusEl = $(this).parent().parent().parent().find('.user-status_current');            

            url = url.replace("user_id", connect);

            $.ajax({
                url: url,
                type: "POST",
                success: function (data) {
                    if (data.message) {
                        if (data.message === 'Pending') {
                            el.html('Cancel request');                                                       
                        } else if (data.message === 'Connect') {
                            el.html('Connect');                                                        
                        }
                    }
                }, error: function (data) {
                    console.log('error');
                }
            });
        });
    });

</script>

                    <div class="colum_block company_page">
                        <div class="page_top_front">
                            <div class="photo user_profile_wrapper">
<?php if(strlen($user->getCoverImage()) > 0): ?>
                                <img alt="" src="/bundles/framework/images/cover/<?= $user->getCoverImage() ?>">

<?php if($view['app']->isMe($user->getId())): ?>
                    			<a><p class="subheader"><button><i class="fa fa-camera" aria-hidden="true"></i> &nbsp; <span class="link_name">Change Cover Image</span></button></a></p>
<?php endif ?>

<?php else: ?>
                                <img alt="" src="/bundles/framework/css/imgs/bg2.jpg">
                                <p class="subheader"><span>TradeToShare lets you connect with</span> <span>all social medias in one place</span>

<?php if($view['app']->isMe($user->getId())): ?>
                    			<a><button><i class="fa fa-camera" aria-hidden="true"></i><span class="link_name">Change Cover Image</span></button></a>
<?php endif ?>

                                </p>
 <?php endif ?>
                            </div>

                            <div class="title_block">
                                <strong></strong>
                            </div>
                            <div class="slogan_block">
                                <strong></strong>
                            </div>
                            <div class="subscribe_block">
<?php if(!$view['app']->isMe($user->getId())): ?>
                                <a href="#" class="connect batton" data-connect="<?= $user->getId() ?>">&nbsp;&nbsp;<?= $view['app']->isConnected($user->getId()) ? ($view['app']->isFriend($user->getId()) ? "Remove Connection" : "Cancel Request") : "Connect" ?>&nbsp;&nbsp;</a>
                                <a href="#" class="message batton">&nbsp;&nbsp;Message&nbsp;&nbsp;</a>
<?php endif ?>
                            </div>
                        </div>
                        <div class="company_wrapper">                           
                           <div class="company_logo">

                           		<div class="img">
<?php if($user->getAvatar()): ?>
                        			<img src="/bundles/framework/images/user/<?= $user->getAvatar() ?>" alt=""/>
<?php else: ?>
                        			<img src="/bundles/framework/images/no_avatar.png" alt=""/>
<?php endif ?>
									<div style="margin-top: 10px;"><strong><?= $view->escape($user->getName()) ?></strong></div>
                                </div>
                                
                            	<div class="company_action">
                                	<div class="company_action_wrapper">
                                    	<div class="item item_share_tts">
                                        	<i class="fa fa-share" aria-hidden="true"></i>
                                        	<span>Share</span>
                                    	</div>
                                    	<div class="item item_share_netwokr">                                   
                                        	<i class="fa fa-share-alt" aria-hidden="true"></i>
                                        	<span>Share Network</span>
                                    	</div>
                                	</div>
                            	</div>
                                
                                
                            </div>
                            <div class="company_text">

<?php if(strlen($user->getOrganization()) > 0): ?>
                                <div class="company_text_field"><strong>Organisation:</strong> <span><?= $view->escape($user->getOrganization()) ?></span></div>
<?php endif ?>

<?php if(strlen($user->getPosition()) > 0): ?>
                                <div class="company_text_field">
                                    <div class="location"><strong>Position:</strong> <span><?= $view->escape($user->getPosition()) ?></span></div>
                                </div>
<?php endif ?>

<?php if(strlen($user->getIndustry()) > 0): ?>
                                <div class="company_text_field"><strong>Industry:</strong> <span><?= $view->escape($user->getIndustry()) ?></span></div>
<?php endif ?>

<?php if(strlen($user->getLastDiploma()) > 0): ?>
                                <div class="company_text_field industry">
                                    <div class="location"><strong>Last Diploma:</strong> <span><?= $view->escape($user->getLastDiploma()) ?></span></div>
                                </div>
<?php endif ?>
   
<?php if(strlen($user->getEducationYear()) > 0): ?>
                               <div class="company_text_field">
                                    <div class="location"><strong>Last year of education:</strong> <span><?= $view->escape($user->getEducationYear()) ?></span></div>
                               </div>
<?php endif ?>
 
<?php if(strlen($user->getLocation()) > 0): ?>
                                <div class="company_text_field locations">
                                    <div class="location"><strong>Location:</strong> <span><?= $view->escape($user->getLocation()) ?></span></div>
                                </div>
<?php endif ?>
                                
                                <br/>
 
<?php if(strlen($user->getLink()) > 0): ?>
                                <div class="company_text_field">
                                    <div class="location"><strong>Website:</strong> <span><a target="_blank" href="<?= $view->escape($user->getLink()) ?>"><?= $view->escape($user->getLink()) ?></a></span></div>
                                </div>
<?php endif ?>

                                <br/>

<?php if(strlen($user->getAbout()) > 0): ?>
                                <div class="company_text_field">
                                    <strong>About me:</strong>
                                </div>

                                <div class="short_description">
                                	<?= $view->escape($user->getAbout()) ?>
                                </div>
<?php endif ?>
                                <br/>
<?php if($view['app']->isMe($user->getId()) || count($experiences) > 0): ?> 
<?php if($view['app']->isMe($user->getId())): ?>
                                <a href="<?= $view['router']->path('user_edit_experience') ?>" style="float: right" class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" type="submit">Add Company</a>
<?php endif ?>
                                <div class="company_text_field">
                                    <strong>Experience:</strong>
                                </div>

                                <div class="short_description"<?php if(count($experiences) > 0): ?> style="padding-left: 0; padding-bottom: 0;"<?php endif ?>>

<?php if(count($experiences) == 0): ?>
	no information
<?php endif ?>


<table class="mdl-data-table mdl-js-data-table experience-section">
  <tbody>
<?php foreach($experiences as $experience): ?>
    <tr>
      <td>

<?php if(strlen($experience['logo']) > 0): ?>
      	<img style="max-width: 140px; margin-bottom: 10px;" src="/bundles/framework/images/company/<?= $view->escape($experience['logo']) ?>"><br/>
<?php endif ?>
      	<span><?= $view->escape($experience['company']) ?></span>
      </td>
      <td>

       	<span><?= $view->escape($experience['title']) ?></span>
<?php if(strlen($experience['specialisation']) > 0): ?>
        <span><?= $view->escape($experience['specialisation']) ?></span>
<?php endif ?>
<?php if(strlen($experience['location']) > 0): ?>
       	<span><?= $view->escape($experience['location']) ?></span>
<?php endif ?>

      </td>
      <td>
<?php if(strlen($experience['industry']) > 0): ?>
       	<span><?= $view->escape($experience['industry']) ?></span>
<?php endif ?>
      	<span><?= $view->escape($experience['duration']) ?></span>

      	<?= $view->escape($experience['start_text']) ?>
      	-
      	<?= $view->escape($experience['end_text']) ?>

      </td>
<?php if($view['app']->isMe($user->getId())): ?>
      <td style="padding-right: 0; padding-top: 0">

		<form action="<?= $view['router']->path('user_edit_experience') ?>" method="get">

			<input type="hidden" name="id" value="<?= $experience['id'] ?>"/>

      		<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect" type="submit">
      			<i class="material-icons">edit</i>
      		</button>

      		<br/>

      		<button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored mdl-js-ripple-effect delete-link" style="margin-top: 4px;" data-id="<?= $experience['id'] ?>">
      			<i class="material-icons">delete</i>
      		</button>

      	</form>

      </td>
<?php endif ?>
    </tr>
<?php endforeach ?>
  </tbody>
</table>

                                </div>


<?php endif ?>


                            </div>
                            <div class="company_action">
                                <div class="company_action_wrapper ">

                                	<div class="ms_item_postes" style="position: static">

                                    	<a id="unvote-btn" class="active <?php if(!$user->isVotedBy($view['app']->getUserId())): ?>hidden<?php endif ?> item item_vote_tts" onclick="unVote(this, <?= $user->getId() ?>)">
                                        	<i class="fa fa-check" aria-hidden="true"></i>
                                        	<span>Voted <strong><?= $user->getNumVotes() ?></strong></span>
                                   		</a>

<div class="mdl-tooltip mdl-tooltip--large mdl-tooltip--top" for="unvote-btn">
	Please click to unvote
</div>

                                    	<a id="vote-btn" class="<?php if($user->isVotedBy($view['app']->getUserId())): ?>hidden<?php endif ?> item item_vote_tts" onclick="doVote(this, <?= $user->getId() ?>)">
                                        	<i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                        	<span>Vote <strong><?= $user->getNumVotes() ?></strong></span>
                                   		</a>


<?php if($user->getNumVotes() > 0): ?>
            <div class="more_actions" style="left: auto; right: 15px; width: 150px">
                <ul>
                	<li>&nbsp;&nbsp;&nbsp;&nbsp;Voted users:</li>
<?php foreach($user->getVoteList() as $voted_user): ?>
                    <li><a href="/user/<?= $voted_user['id'] ?>"><?= $voted_user['name'] ?></a></li>
<?php endforeach ?>
                </ul>
            </div>
<?php endif ?>

            						</div>

                                </div>
                            </div>

                            <div class="clear"></div>
                         </div><!-- company_wrapper -->
                    </div>

<?= $view->render("AppBundle:User:_connections.html.php", array('user' => $user)) ?>
<?= $view->render("AppBundle:Photo:_photos_and_videos.html.php", array('user' => $user)) ?>

<?php /*
                    <div id="tabs">
                          <ul>
                            <li><a href="#tabs-1">Recommendations</a></li>
                            <li><a href="#tabs-2">Reviews</a></li>
                          </ul>
                        <div id="tabs-1">

<?php if($view['app']->getUserId() != $user->getId()): ?>

                            <div class="colum_block add_post">
                                <div class="title_block">
                                    <strong>Add new recommendation</strong>
                                </div>
                                <div class="add_post_wrapper">
                                    <form action="">
                                        <div class="add_post_content">
                                            <div class="form-item">
                                                <label></label>
                                                <textarea cols="" rows=""  placeholder="Write a recommendation..."></textarea> 
                                                <span class="user_account_img">

<?php if($view['app']->getUser()->getAvatar()): ?>
                        			<img src="/bundles/framework/images/user/<?= $view['app']->getUser()->getAvatar() ?>" alt=""/>
<?php else: ?>
                        			<img src="/bundles/framework/images/no_avatar.png" alt=""/>
<?php endif ?>

                                                </span>            
                                            </div>
                                            <div class="form_bottom">  
                                                <div class="also_post">
                                                    <p>Also post to:</p>
                                                    <ul class="networks-list" style="height: auto;">
                                                        <li class="facebook  active " data-provider="facebook" title="facebook">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="google " data-provider="google" title="google">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="twitter " data-provider="twitter" title="twitter">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="vkontakte " data-provider="vkontakte" title="vkontakte">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="tumblr " data-provider="tumblr" title="tumblr">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="instagram " data-provider="instagram" title="instagram">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="flickr " data-provider="flickr" title="flickr">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="foursquare " data-provider="foursquare" title="foursquare">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="pinterest " data-provider="pinterest" title="pinterest">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="imgur " data-provider="imgur" title="imgur">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                     </ul>
                                                </div>                                  
                                                <div class="media_selector">                                        
                                                    <a class="ms_item_photo" tabindex="0" data-title="Photo" aria-label="Photo" role="link"><i class="fa fa-camera" aria-hidden="true"></i><span class="blind_label">Photo</span></a>
                                                    <a class="ms_item_video" tabindex="0" data-title="Video" aria-label="Video" role="link"><i class="fa fa-video-camera" aria-hidden="true"></i><span class="blind_label">Video</span></a>
                                                    <a class="ms_item_gift" tabindex="0" data-title="Gift" aria-label="Gift" role="link"><i class="fa fa-gift" aria-hidden="true"></i><span class="blind_label">Gift</span></a>                                            
                                                    <a class="ms_item_smile" tabindex="0" data-title="Smile" aria-label="Smile" role="link"><i class="fa fa-smile-o" aria-hidden="true"></i><span class="blind_label">Smile</span></a>
                                                    <div class="ms_items_more_wrap">
                                                        <div class="ms_item_more">
                                                            <span>More</span><i class="fa fa-angle-down" aria-hidden="true"></i>                                                    
                                                        </div>
                                                        <div class="more_actions">
                                                            <a class="ms_item_gif" tabindex="0" data-title="Gif" aria-label="Gif" role="link"><i class="fa fa-puzzle-piece" aria-hidden="true"></i><span class="blind_label">Gif</span></a>
                                                            <a class="ms_item_audio" tabindex="0" data-title="Audio" aria-label="Audio" role="link"><i class="fa fa-music" aria-hidden="true"></i><span class="blind_label">Audio</span></a>
                                                            <a class="ms_item_doc" tabindex="0" data-title="Document" aria-label="Document" role="link"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="blind_label">Document</span></a>
                                                            <a class="ms_item_map" tabindex="0" data-title="Map" aria-label="Map" role="link"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="blind_label">Map</span></a>                                                        
                                                        </div>
                                                    </div>                                       
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-ection">                                                                            
                                            <input type="submit" class="submit" value="Post" />
                                            <span class="schedule_batton batton">Schedule</span>                                    
                                        </div>
                                    </form>
                                </div>
                            </div>    <!-- add post -->
                            
<?php endif ?>



                            <div class="colum_block post_block">                        
                                <div class="post_top">
                                    <div class="post_user">
                                        <div class="post_control">
                                            <div class="post_control_wrapp">...</div>
                                            <div class="more_actions">
                                                <span class="delete">Delete Post</span>
                                                <span class="pin_post">Pin Post</span>
                                                <span class="unpin_post">Hide Post</span>                                                        
                                            </div>
                                        </div>
                                        <a href="#" class="user_post_img"><img src="/bundles/framework/images/files/upload/tom_kruse.png" alt=""></a>
                                        <div class="user_post_name_wrapp">
                                            <div class="post_from">
                                                <span>Post from</span>
                                                <img src="/bundles/framework/images/files/upload/facebook_ico.png" alt="">
                                            </div>
                                            <a href="#" class="user_post_name">Cameron Diaz</a>                                
                                        </div>
                                        <span class="post_date">23 Mar at 10:37 pm</span>
                                    </div>
                                </div>
                                <div class="post_body">
                                    <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin 
                                        literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney 
                                        College in Virginia, looked up one of the more obscure Latin words, consectetur
                                        Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin 
                                        literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney 
                                        </p>
                                </div>
                                <div class="post_bottom">
                                    <div class="post_action">                                
                                        <div class="post_action_wrapper">
                                            <div class="item item_vote_tts">
                                                <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                                <span>Vote <strong>18</strong></span>
                                            </div>
                                            <div class="item item_comments_tts">
                                                <i class="fa fa-comments" aria-hidden="true"></i>
                                                <span>Comment</span>
                                            </div>
                                            <div class="item item_share_tts">
                                                <i class="fa fa-share" aria-hidden="true"></i>
                                                <span>Share</span>
                                            </div>
                                            <div class="item item_share_netwokr">                                   
                                                <i class="fa fa-share-alt" aria-hidden="true"></i>
                                                <span>Share Network</span>
                                            </div>
                                        </div>      
                                    </div>
                                    <div class="post_comments">                                
                                        <div class="post_comments_form">
                                             <form action="">
                                                <div class="add_post_content">
                                                    <div class="form-item">                        
                                                        <label></label> 
                                                        <textarea cols="" rows=""  placeholder="leave a comment"></textarea> 
                                                        <span class="user_account_img">
<?php if($view['app']->getUser()->getAvatar()): ?>
                        			<img src="/bundles/framework/images/user/<?= $view['app']->getUser()->getAvatar() ?>" alt=""/>
<?php else: ?>
                        			<img src="/bundles/framework/images/no_avatar.png" alt=""/>
<?php endif ?>
                                                        </span>
                                                    </div>
                                                    <div class="form_bottom">  
                                                        <div class="form-ection">                                                                            
                                                            <input type="submit" class="submit" value="Post" />                                                                                     
                                                        </div>                                                                                 
                                                        <div class="media_selector">                                        
                                                            <a class="ms_item_photo" tabindex="0" data-title="Photo" aria-label="Photo" role="link"><i class="fa fa-camera" aria-hidden="true"></i><span class="blind_label">Photo</span></a>
                                                            <a class="ms_item_video" tabindex="0" data-title="Video" aria-label="Video" role="link"><i class="fa fa-video-camera" aria-hidden="true"></i><span class="blind_label">Video</span></a>
                                                            <a class="ms_item_gift" tabindex="0" data-title="Gift" aria-label="Gift" role="link"><i class="fa fa-gift" aria-hidden="true"></i><span class="blind_label">Gift</span></a>                                                    
                                                            <a class="ms_item_smile" tabindex="0" data-title="Smile" aria-label="Smile" role="link"><i class="fa fa-smile-o" aria-hidden="true"></i><span class="blind_label">Smile</span></a>
                                                            <div class="ms_items_more_wrap">
                                                                <div class="ms_item_more">
                                                                    <span>More</span><i class="fa fa-angle-down" aria-hidden="true"></i>
                                                                </div>
                                                                <div class="more_actions">
                                                                    <a class="ms_item_gif" tabindex="0" data-title="Gif" aria-label="Gif" role="link"><i class="fa fa-puzzle-piece" aria-hidden="true"></i><span class="blind_label">Gif</span></a>
                                                                    <a class="ms_item_audio" tabindex="0" data-title="Audio" aria-label="Audio" role="link"><i class="fa fa-music" aria-hidden="true"></i><span class="blind_label">Audio</span></a>
                                                                    <a class="ms_item_doc" tabindex="0" data-title="Document" aria-label="Document" role="link"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="blind_label">Document</span></a>
                                                                    <a class="ms_item_map" tabindex="0" data-title="Map" aria-label="Map" role="link"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="blind_label">Map</span></a>                                                        
                                                                </div>
                                                            </div>                                       
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </form>    
                                        </div>
                                    </div>
                                </div>
                            </div><!-- post -->
                        </div><!---  tab news -->  
                        <!----------------------------------------------------------------------------------------------------------------->
                         <div id="tabs-2">

<?php if($view['app']->getUserId() != $user->getId()): ?>

                            <div class="colum_block add_post">
                                <div class="title_block">                          
                                    <strong>Add new review</strong>
                                </div>
                                <div class="add_post_wrapper">
                                    <form action="">
                                        <div class="add_post_content">
                                            <div class="form-item">                        
                                                <label></label> 
                                                <textarea cols="" rows=""  placeholder="Write a review"></textarea>

                                                <span class="user_account_img">

<?php if($view['app']->getUser()->getAvatar()): ?>
                        			<img src="/bundles/framework/images/user/<?= $view['app']->getUser()->getAvatar() ?>" alt=""/>
<?php else: ?>
                        			<img src="/bundles/framework/images/no_avatar.png" alt=""/>
<?php endif ?>
												</span>

                                            </div>
                                            <div class="form_bottom">  
                                                <div class="also_post">
                                                    <p>Also post to:</p>
                                                    <ul class="networks-list" style="height: auto;">
                                                        <li class="facebook  active " data-provider="facebook" title="facebook">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="google " data-provider="google" title="google">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="twitter " data-provider="twitter" title="twitter">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="vkontakte " data-provider="vkontakte" title="vkontakte">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="tumblr " data-provider="tumblr" title="tumblr">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="instagram " data-provider="instagram" title="instagram">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="flickr " data-provider="flickr" title="flickr">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="foursquare " data-provider="foursquare" title="foursquare">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="pinterest " data-provider="pinterest" title="pinterest">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                        <li class="imgur " data-provider="imgur" title="imgur">
                                                            <i class="fa fa-check" aria-hidden="true"></i>
                                                            <span></span>
                                                        </li>
                                                     </ul>
                                                </div>                                  
                                                <div class="media_selector">                                        
                                                    <a class="ms_item_photo" tabindex="0" data-title="Photo" aria-label="Photo" role="link"><i class="fa fa-camera" aria-hidden="true"></i><span class="blind_label">Photo</span></a>
                                                    <a class="ms_item_video" tabindex="0" data-title="Video" aria-label="Video" role="link"><i class="fa fa-video-camera" aria-hidden="true"></i><span class="blind_label">Video</span></a>
                                                    <a class="ms_item_gift" tabindex="0" data-title="Gift" aria-label="Gift" role="link"><i class="fa fa-gift" aria-hidden="true"></i><span class="blind_label">Gift</span></a>                                            
                                                    <a class="ms_item_smile" tabindex="0" data-title="Smile" aria-label="Smile" role="link"><i class="fa fa-smile-o" aria-hidden="true"></i><span class="blind_label">Smile</span></a>
                                                    <div class="ms_items_more_wrap">
                                                        <div class="ms_item_more">
                                                            <span>More</span><i class="fa fa-angle-down" aria-hidden="true"></i>                                                    
                                                        </div>
                                                        <div class="more_actions">
                                                            <a class="ms_item_gif" tabindex="0" data-title="Gif" aria-label="Gif" role="link"><i class="fa fa-puzzle-piece" aria-hidden="true"></i><span class="blind_label">Gif</span></a>
                                                            <a class="ms_item_audio" tabindex="0" data-title="Audio" aria-label="Audio" role="link"><i class="fa fa-music" aria-hidden="true"></i><span class="blind_label">Audio</span></a>
                                                            <a class="ms_item_doc" tabindex="0" data-title="Document" aria-label="Document" role="link"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="blind_label">Document</span></a>
                                                            <a class="ms_item_map" tabindex="0" data-title="Map" aria-label="Map" role="link"><i class="fa fa-map-marker" aria-hidden="true"></i><span class="blind_label">Map</span></a>                                                        
                                                        </div>
                                                    </div>                                       
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-ection">                                                                            
                                            <input type="submit" class="submit" value="Post" />
                                            <span class="schedule_batton batton">Schedule</span>                                    
                                        </div>
                                    </form>
                                </div>
                            </div>    <!-- add post -->

<?php endif ?>
<?php */ ?>
                            <div class="colum_block post_block">                        
                                <div class="post_top">                            
                                    <div class="post_user">
                                        <div class="post_control">
                                            <div class="post_control_wrapp">...</div>
                                            <div class="more_actions">
                                                <span class="delete">Delete review</span>
                                                <span class="pin_post">Pin review</span>
                                                <span class="unpin_post">Hide review</span>                                                        
                                            </div>
                                        </div>
                                        <a href="#" class="user_post_img"><img src="/bundles/framework/upload/user_account_photo.jpg" alt=""></a>
                                        <div class="user_post_name_wrapp">                                            
                                            <a href="#" class="user_post_name">Cameron Diaz</a>                                
                                        </div>
                                        <span class="post_date">23 Mar at 10:37 pm</span>
                                    </div>
                                </div>
                                <div class="post_body">
                                    <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin 
                                        literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney 
                                        College in Virginia, looked up one of the more obscure Latin words, consectetur
                                        Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin 
                                        literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney 
                                        </p>
                                </div>
                                <div class="post_bottom">
                                    <div class="post_action">                                
                                        <div class="post_action_wrapper">
                                            <div class="item item_vote_tts">
                                                <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                                                <span>Vote <strong>18</strong></span>
                                            </div>
                                            <div class="item item_comments_tts">
                                                <i class="fa fa-comments" aria-hidden="true"></i>
                                                <span>Comment</span>
                                            </div>
                                            <div class="item item_share_tts">
                                                <i class="fa fa-share" aria-hidden="true"></i>
                                                <span>Share</span>
                                            </div>
                                            <div class="item item_share_netwokr">                                   
                                                <i class="fa fa-share-alt" aria-hidden="true"></i>
                                                <span>Share Network</span>
                                            </div>
                                        </div>      
                                    </div>                                   
                                </div>
                            </div><!-- post -->
                        </div><!---  tab reviews --> 
                    </div><!---  tabs -->  


                    <div class="clear"></div>

