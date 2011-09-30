<script type="text/javascript">
toggleCheckboxes = function(target)
{
    var myCheckboxes = $('mainForm').getElements('checkbox');
    var targetLength = target.length;

    myCheckboxes.each(function(elem)
        {
            if (target.indexOf('_') != -1)
            {
                var elemNames = elem.value.split('*');
                if (elemNames[0] == target)
                    elem.checked = (elem.checked == true) ? false : true;
            }
            else if (elem.value.substring(2, targetLength + 2) == target)
            {
                elem.checked = (elem.checked == true) ? false : true;
            }
            else if (elem.value.substring(0, targetLength) == target)
            {
                elem.checked = (elem.checked == true) ? false : true;
            }
        }
    );
}
</script>