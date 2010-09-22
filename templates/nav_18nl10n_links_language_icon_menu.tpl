<?php
/**
 Menu for switching between languages of a page.
 */
 
?>

<?php foreach ($this->items as $item): ?>
<a href="<?php 
    echo $this->generateFrontendUrl($item,'/language/'.$item['language']);
    ?>"
    <?php if ($item['isActive']) {echo ' class="active"';} ?>
    title="<?php echo $this->languages[$item['language']];?>"
    ><img src="<?php 
echo 'system/modules/i18nl10n/html/flag_icons/png/'.$item['language'].'.png';?>"
title="<?php echo $this->languages[$item['language']];?>"
alt="<?php echo $this->languages[$item['language']];?>"
/></a>
<?php endforeach; ?>

