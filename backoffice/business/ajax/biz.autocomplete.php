<?php
/**
 * Parse taxonomy.xml to display in autocomplete
 */

// Initialize the system
require_once dirname(__FILE__) . '/../../initWebApp.php';


$autoCompleteName = getArrayParameter($_REQUEST, 'autoCompleteName', null);
$autoCompleteValue = getArrayParameter($_REQUEST, $autoCompleteName, null);

switch ($type)
{
    default:
        $taxonomy = simplexml_load_file(WCM_DIR.'/business/xml/taxonomies/default.xml');
        echo '<ul>';
        foreach ($taxonomy->common->type as $type)
        {
            // $list array is an assoc array. The key is the name and the value is the (xml) path
            $list = array();
            $category = $type;
            $categoryName = $category->attributes();
            $name = (string) $categoryName['name'];
            
            // get matching categories
            if (strtolower($autoCompleteValue) == strtolower(substr($name, 0, $autoLength)))
            {
                $list[$name] = $name;
            }
            // get matching tags
            foreach ($category as $tag)
            {
                if (strtolower($autoCompleteValue) == strtolower(substr($tag, 0, $autoLength)))
                {
                    $list[(string) $tag] = $name . '/' . (string) $tag;
                }
            }
            // render resulting array
            foreach ($list as $name => $path)
            {
                echo '<li>' . $path . '</li>';
            }
        }
        echo '</ul>';
        break;
}