        <div class="user_ico user_messages">
            <i class="fa fa-envelope" aria-hidden="true"></i><?php if($total_unread > 0): ?><div class="circle_num"><strong><?= $total_unread ?></strong></div><?php endif ?>
<?php if($total_unread > 0): ?>
            <div class="header_pop_up">
                <div class="header_pop_up_wrapper">
                    <div class="header_pop_up_wrapper_top">
                        <p><strong>Messages</strong></p>
                    </div>
                    <div class="header_pop_up_wrapper_body">
                        <ul>
<?php foreach($messages as $message): ?>
                            <li class="new" id="<?= $message->lastMessage->rid ?>">
                                <span class="img"><a href=""><img src="/user/avatar/<?= substr($message->lastMessage->u->username, 1) ?>" alt=""/></a></span>
                                <div class="text_wrapper">
                                    <a href="" class="user_name"><?= $view->escape($message->lastMessage->u->name) ?></a>
                                    <p class="text">
                                        <?= $view->escape($message->lastMessage->msg) ?>
                                    </p>
                                    <span class="time"><?= $view[date]->show($message->lastMessage->ts) ?></span>
                                    <a href="javascript:delete_hdr_message('<?= $message->lastMessage->rid ?>')" class="small_batton">Delete</a>
                                    <a href="<?= $view['router']->path('message_index') ?>?tab=chats&user=<?= $message->lastMessage->u->username ?>" class="small_batton">View</a>
                                </div>
                            </li>
<?php endforeach ?>
                        </ul>
                    </div>
                    <div class="header_pop_up_wrapper_bottom">
                        <a href="<?= $view['router']->path('message_index') ?>?tab=chats">See All Messages</a>
                    </div>
                </div>
             </div>
<?php endif ?>
        </div>
<script>
	function delete_hdr_message(rid) {
		$("#" + rid).hide(500);

		var cnt = $(".user_messages .circle_num strong").text();
		cnt--;

		if(cnt <= 0) {
			$(".user_messages .circle_num").remove();
		} else {
			$(".user_messages .circle_num strong").text(cnt);
		}

       	$.ajax({
           	url: '/message/markAsRead',
            data: {
             	rid: rid
            },
            type: "POST"
        });
	}
</script>
        