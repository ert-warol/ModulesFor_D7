<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

	<?php //print $user_picture; ?>
    
    <?php if (!$page): ?>
    <div class="node-title">
    
		<?php if ($display_submitted): ?>
        <h4 class="date"><?php print format_date($node->created, 'custom', 'j M Y'); ?></h4>
        <?php endif;?>
        
    
    </div>
    <?php endif; ?>

    <div class="clearfix copy content"<?php print $content_attributes; ?>>
		<?php
        // We hide the comments and links now so that we can render them later.
        hide($content['comments']);
        hide($content['links']);
        print render($content);
		print render($content['links']);
        ?>
    </div>
    
    <?php print render($content['comments']); ?>

</div>
