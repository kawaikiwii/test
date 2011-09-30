<table border='0' cellspacing='5' cellpadding='5'>
{php}
$binid = $this->get_template_vars('binid');

$bin = new wcmBin($this->project);
if ($bin->beginEnum("id=".$binid))
{
    while ($bin->nextEnum())
    {
        $contentArray = explode('/', $bin->content);
        if ($contentArray)
        {
            foreach ($contentArray as $content)
            {
                if ($content)
                {
                    list($objectClass, $objectId) = explode('_', $content, 2);
                    if ($objectClass && $objectId)
                    {
                        $bizobject = new $objectClass($this->project);
                        if ($bizobject->refresh($objectId))
                        {
                            echo "<tr>";
                            echo "<td>".$objectClass."</td>";
                            echo "<td>(id :".$objectId.")</td>";
                            echo "<td><label>label : <b>".getObjectLabel($bizobject)."</b></label></td>";
                            echo "</tr>";                  
                        }
                    }
                }
            }
        }
    }
}
{/php}

</table>