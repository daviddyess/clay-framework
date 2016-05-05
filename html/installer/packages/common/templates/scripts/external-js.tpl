<?php foreach(self::$map['scripts.'.$position] as $file){ ?>
<script type="text/javascript" src="<?php echo $file['src']; ?>"></script>
<?php } ?>