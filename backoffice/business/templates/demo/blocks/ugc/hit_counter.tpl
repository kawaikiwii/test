<div id="hitCounter"></div>
<?php if (!isset($_REQUEST['preview'])): ?>
<script type="text/javascript"> bizHitCounter("{$bizobject.className}", {$bizobject.id}); </script>
<?php endif; ?>
