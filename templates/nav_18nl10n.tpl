<?php
/**
    The ModuleNavigation inherits from Module where menu items a re actually generated
    TTTOOO complex, so we modify the collected items here and propose to the 
    site developer to copy this template and style it.
 */
 $this->import('I18nL10nFrontend','I18n');
 
?>
<ul class="<?php echo $this->level; ?>">
<?php foreach ($this->I18n->i18nl10nNavItems($this->items) as $item): ?>
<?php if ($item['isActive']): ?>
<li class="active<?php if ($item['class']): ?> <?php echo $item['class']; ?><?php endif; ?>"><span class="active<?php if ($item['class']): ?> <?php echo $item['class']; ?><?php endif; ?>"><?php echo $item['link']; ?></span><?php echo $item['subitems']; ?></li>
<?php else: ?>
<li<?php if ($item['class']): ?> class="<?php echo $item['class']; ?>"<?php endif; ?>><a href="<?php echo $item['href']; ?>" title="<?php echo $item['pageTitle'] ? $item['pageTitle'] : $item['title']; ?>"<?php if ($item['class']): ?> class="<?php echo $item['class']; ?>"<?php endif; ?><?php if ($item['accesskey'] != ''): ?> accesskey="<?php echo $item['accesskey']; ?>"<?php endif; ?><?php if ($item['tabindex']): ?> tabindex="<?php echo $item['tabindex']; ?>"<?php endif; ?><?php if ($item['nofollow']): ?> rel="nofollow"<?php endif; ?><?php echo $item['target']; ?>><?php echo $item['link']; ?></a><?php echo $item['subitems']; ?></li>
<?php endif; ?>
<?php endforeach; ?>
</ul>
