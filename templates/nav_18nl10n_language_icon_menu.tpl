<?php
/**
 Menu for switching between languages of a page.
 */
 
?>

<form name="<?php echo $this->type;?>" method="post"  style="display:inline"
><?php foreach ($this->items as $item): ?><input 
    class="language" type="radio" name="language" 
    id="language_<?php echo $item['language'];?>" 
    onchange="this.form.submit();"
    value="<?php echo $item['language'];?>" <?php 
if ($item['isActive']) {echo ' class="active" checked="checked"';} ?>  />
<label for="language_<?php echo $item['language'];?>" <?php 
    if ($item['isActive']) {echo ' class="active"';} ?>><img src="<?php 
echo 'system/modules/i18nl10n/html/flag_icons/png/'.$item['language'].'.png';?>"
title="<?php echo $this->languages[$item['language']];?>"
alt="<?php echo $this->languages[$item['language']];?>"
/></label><?php endforeach; ?></form>