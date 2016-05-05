<div class="common-menu"><?php if(!empty($links)){foreach($links as $navlink){ ?>
<p><a href="<?php echo $navlink['url']; ?>" title="<?php echo $navlink['title']; ?>"><?php echo $navlink['name']; ?></a></p>
<?php } } else { ?>
<p>You can add a menu here by adding a menu.php file to your package's folder. See the Installer package for an example menu.php file.</p>
<?php } ?></div>