<?php
$path = __DIR__;
exec("nohup php $path/bot.php >> $path/icq.log &");
