<?php if($tweets !== array()) { ?>
<form id="tweetsListForm">
    <ul>
        <?php
		$i=0;
		foreach($tweets as $row) {
			$i++;
			?>
			<li onclick="Twitter.o.m.d.tweet('<?php echo $row['id']; ?>',this);">
                <a href="javascript:void(0);"<?php if($row['tweet_active'] === 1) { ?> class="select"<?php } ?>><span class="lisNum"><?php echo $i; ?></span> <?php echo Html::encode($row['tweet']); ?></a>
                <div style="display: none;">
                    <input id="_tweetsList_<?php echo $row['id']; ?>" type="checkbox" name="tweets[]" value="<?php echo $row['id']; ?>"<?php echo $row['tweet_active'] === 1?' checked="checked"':''; ?>>
                </div>
            </li>
		<?php } ?>
    </ul>
</form>
<?php } else { ?>
	<div style="text-align: center; padding: 5px;;">Не удалось загрузить твиты по вашему запросу</div>
<?php } ?>
