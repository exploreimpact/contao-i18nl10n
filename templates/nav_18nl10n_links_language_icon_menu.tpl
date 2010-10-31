<?php
/**
 Menu for switching between languages of a page.
 */
 
?>

<?php 
foreach ($this->items as $item): 
    $url = '';
    if($GLOBALS['TL_CONFIG']['i18nl10n_alias_suffix']):
        $url = $this->generateFrontendUrl($item);
    else:
        $url = $this->generateFrontendUrl($item,'/language/'.$item['language']);
    endif;

?>

<a href="<?php echo $url; ?>"
    <?php if ($item['isActive']) {echo ' class="active"';} ?>
    title="<?php echo $this->languages[$item['language']];?>"
    ><img src="<?php 
        echo 'system/modules/i18nl10n/html/flag_icons/png/'.$item['language'].'.png';?>"
        title="<?php echo $this->languages[$item['language']];?>"
        alt="<?php echo $this->languages[$item['language']];?>"
/></a>
<?php 
endforeach; ?>

