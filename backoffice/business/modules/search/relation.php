<?php
$filter = (count($params) > 0) ? array_shift($params) : '';
?>
<div class="search">
    <div class="query">
        <input type="text" value=""/><a href="#" onclick="$('relation_resultset).innerHTML='<div class="wait"><?php echo _BIZ_WAO; ?></div>';javascript:launchSearch(this.previousSibling.value,'<?php echo $filter;?>'); return false;" class="search"><span><?php echo _SEARCH; ?></span></a>
    </div>

    <div class="resultset" id="relation_resultset">
    </div>

    <div class="options">
        <div class="pagination">
            <a class="previous" href="#" onclick="return false;"><?php echo _PREVIOUS;?></a>
            <a class="next" href="#" onclick="return false;"><?php echo _NEXT;?></a>
        </div>
    </div>
</div>
