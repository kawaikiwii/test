<?php
    set_time_limit(7200);

    include('includes/header.php');

?>
    <div id="assetbar">
        <h3><?php echo _BIZ_SEMANTIC_DATA;?></h3>
    </div>
    <div id="sysinfo">
    </div>
    <div class="genericForm">
    <form>

<?php

    if (getArrayParameter($_REQUEST, 'todo') == 'mine')
    {
    $t0 = microtime(true);
    //mineObjects('article');
    mineObjects('photo');
    mineObjects('slideshow');
	mineObjects('news');
	mineObjects('video');
	mineObjects('event');
	mineObjects('location');
    //mineObjects('forum');
    mineObjects('contribution', array('NSentiment'));

    echo '<h4>' . _BIZ_TOTAL_COMPUT_TIME . sprintf('%0.2f sec', microtime(true) - $t0) . '</h4>';
    }
    else
    {
        echo '<fieldset>';
        echo '<legend>'._BIZ_UPDATE_SEMANTIC_DATA.'</legend>';
        echo '<ul>';
        echo '<li>'._BIZ_UPDATE_SEMANTIC_DATA_ALERT.'</li>';
        echo '<li><a href="?todo=mine" class="action">'._BIZ_EXECUTE_PROCESS.'</a></li>';
        echo '</ul>';
        echo '</fieldset>';
    }

    function mineObjects($className, $methods = null)
    {
        $tme = wcmSemanticServer::getInstance();

        $bo = new $className;
        $bo->beginEnum();
        echo '<h2>' . _BIZ_UPDATING_SEMANTIC_DATA_FOR . $className . '</h2>';
        echo '<ul>';
        while ($bo->nextEnum())
        {
            try
            {
                echo '<li>' . $bo->title;
                $bo->semanticData = $tme->mineObject($bo, $methods);
                $bo->save();
                echo '</li>';            
            }
            catch(Exception $e)
            {
                echo ' : error => ' . $e->getMessage() . '</li>';
            }
        }
        $bo->endEnum();
        echo '</ul>';
        echo '<br/>';
    }
    ?>
    </form>
    </div>
    <?php
    include('includes/footer.php');
?>
