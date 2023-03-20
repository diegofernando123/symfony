<?php $view['slots']->set("subject", 'Find out who voted for your page') ?>
<?php $view['slots']->start('text_body') ?>
Hello <?= $user->getName() ?>!

Congratulation!
<?= $voted_user->getName() ?> voted for your page.

Please visit your page at
https://tradetoshare.com/user/<?= $user->getId() ?>


Thank you.

TradeToShare Team.
<?php $view['slots']->stop('text_body') ?>

<html>
<head></head>
<body>

<br><br>

<center>

<table border="0" style="max-width: 100%" width="700">
<tr>
<td>
<a href="https://tradetoshare.com" target="_blank" style="text-decoration: none">
<img style="border: 1px solid #214a70; border-radius: 2px; vertical-align: middle; margin-right: 10px;" height="31" src="https://tradetoshare.com/bundles/framework/upload/smal_logo.jpg" alt="" />
<font color="#2a76bd" face="sans-serif" size="4">
	TradeToShare
</font>
</span>
</a>
<hr style="height: 0; border-top: 0; margin-top: 15px; margin-bottom: 30px;">
<strong><font face="sans-serif" size="3">Hello <?= $user->getName() ?>!</font></strong>

<table border="0" style="width:100%; margin-top: 20px">
<tr>
<td width="70">
<?php if ($voted_user->getAvatar() != null): ?>
	<a target="_blank" href="https://tradetoshare.com/user/<?= $voted_user->getId() ?>"><img height="60" src="https://tradetoshare.com/bundles/framework/images/user/<?= $voted_user->getAvatar() ?>" alt="" /></a>
<?php else: ?>
	<a target="_blank" href="https://tradetoshare.com/user/<?= $voted_user->getId() ?>"><img height="60" src="https://tradetoshare.com/bundles/framework/images/no_avatar.png" alt="" /></a>
<?php endif ?>
	
</td>
<td>
<font face="sans-serif" size="3"><?= $voted_user->getName() ?> voted for your page.</font>
</td>
</tr>
</table>

<br><br>

<a href="https://tradetoshare.com/user/<?= $user->getId() ?>" target="_blank" style="border: 1px solid #214a70; border-radius: 2px; background: #2a76bd; color: #fff; text-decoration: none; padding: 10px 20px">
	<font face="sans-serif" size="3">Visit your Page</font>
</a>

<br>
<br>
<p>
<font face="sans-serif" size="3">
	Thank you.<br>
	TradeToShare Team.
</font>
</p>

</td>
</tr>
</table>

</center>
</body>
</html>