<?php 
$bizobject = wcmMVC_Action::getContext();
$config = wcmConfig::getInstance();

$formats = $bizobject->getFormats();
?>
<div class="zone">
	<?php 
    foreach ($formats["square"] as $format) {
        
    ?>
    <div style="padding:5px 10px 15px 5px;">
    	<h3><?php echo $format["width"] ?>x<?php echo $format["height"] ?> (<?php echo $format["weight"] ?>)</h3>
        <a href="<?php echo $format["fileurl"] ?>" target="_blank"><img src="<?php echo $format["fileurl"] ?>" alt="<?php echo $format["width"] ?>x<?php echo $format["height"] ?>" width="<?php echo $format["width"] ?>" height="<?php echo $format["height"] ?>"/></a>
    </div>
    <?php 
    }
    ?>
    <?php 
    foreach ($formats["height"] as $format) {
        
    ?>
    <div style="padding:5px 10px 15px 5px;">
    	<h3><?php echo $format["width"] ?>x<?php echo $format["height"] ?> (<?php echo $format["weight"] ?>)</h3>
        <a href="<?php echo $format["fileurl"] ?>" target="_blank"><img src="<?php echo $format["fileurl"] ?>" alt="<?php echo $format["width"] ?>x<?php echo $format["height"] ?>" width="<?php echo $format["width"] ?>" height="<?php echo $format["height"] ?>"/></a>
    </div>
    <?php 
    }
    ?>
	<?php 
    foreach ($formats["width"] as $format) {
        
    ?>
    <div style="padding:5px 10px 15px 5px;">
    	<h3><?php echo $format["width"] ?>x<?php echo $format["height"] ?> (<?php echo $format["weight"] ?>)</h3>
        <a href="<?php echo $format["fileurl"] ?>" target="_blank"><img src="<?php echo $format["fileurl"] ?>" alt="<?php echo $format["width"] ?>x<?php echo $format["height"] ?>" width="<?php echo $format["width"] ?>" height="<?php echo $format["height"] ?>"/></a>
    </div>
    <?php 
    }
    ?>
</div>