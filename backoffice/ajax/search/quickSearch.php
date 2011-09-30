<?php
/**
 * Project:     WCM
 * File:        ajax/search/quickSearch.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Retrieve parameters
    $uid = getArrayParameter($_REQUEST, 'uid', 'quickSearch');
    $filter = getArrayParameter($_REQUEST, 'filter', null);
    $query = getArrayParameter($_REQUEST, 'query', null);
    $offset = getArrayParameter($_REQUEST, 'offset', 0);
    $pageSize = getArrayParameter($_REQUEST, 'pageSize', 10);
    $simpleMode = getArrayParameter($_REQUEST, 'simpleMode', null);
    $tplFile = getArrayParameter($_REQUEST, 'tpl', 'quickSearch');
    $engine = getArrayParameter($_REQUEST, 'engine',$config['wcm.search.engine']);
    $style = getArrayParameter($_REQUEST, 'style', 'grid');
    $idPrefix = getArrayParameter($_REQUEST, 'idPrefix', null);
    $resultSet = getArrayParameter($_REQUEST, 'resultSet', $idPrefix.'resultset');
    $orderBy = getArrayParameter($_REQUEST, 'orderBy', 'createdAt');
    $currentPage = getArrayParameter($_REQUEST, 'currentPage', 1);
    $maxRange = getArrayParameter($_REQUEST, 'maxRange', 9);

    if (empty($orderBy)) $orderBy = "createdAt DESC";
    
    // Execute search
    $t0 = microtime(true);
    if ($filter && $query)
        $query = $filter . ' AND (' . $query . ')';
    elseif($filter)
        $query = $filter;
    $search = wcmBizsearch::getInstance($engine);
    
    $total = $search->initSearch($uid, $query, $orderBy);

    $html = null;
    if ($total)
    {
        if ($simpleMode == null)
        {
            $html .= '<div class="resultBar">';
            $html .= sprintf(_RESULT_N_ITEMS_FOUND, $total, ($total > 1) ? 's' : '', ($total > 1) ? 's' : '');
            $html .= sprintf(' (%0.2fs)', microtime(true)-$t0);
            $html .= '</div>';
        }

        // Display first page
        $offset = ($currentPage - 1) * $pageSize;
        $result = $search->getDocumentRange($offset, $offset+$pageSize-1, $uid, false);
        
        if (!defined('SMARTY_DIR'))
        {
            define('SMARTY_DIR', dirname(__FILE__) . '/../includes/Smarty/');
        }
        require_once(SMARTY_DIR . 'Smarty.class.php');
        require_once(SMARTY_DIR . 'Smarty_Compiler.class.php');
        
        $totalPages = (int) ceil($total / $pageSize);
        
        $maxRangeOffset = $maxRange-1;
        $startRange =  $currentPage - floor(($maxRangeOffset)/2);
        $startRange = ($startRange < 1)? 1 : $startRange;
        $endRange = (($startRange + $maxRangeOffset)>$totalPages)? $totalPages : $startRange + $maxRangeOffset;
        
        $pages = range($startRange, $endRange);
        
        $previousPage = ($currentPage == 1)? 1 : $currentPage - 1;
        $nextPage = ($currentPage == $totalPages)? $currentPage : $currentPage + 1;
        
        if (!empty($total))
        	$totalPages = ceil($total/$maxRange);
        else
        	$totalPages = 0;
        	
        $tpl = new wcmTemplateGenerator();
        $tpl->assign('resultSet',$result);
        $tpl->assign('idPrefix',$idPrefix);
        $tpl->assign('totalResults', $total);
        $tpl->assign('pages', $pages);
        $tpl->assign('currentPage', $currentPage);
        $tpl->assign('nextPage', $nextPage);
        $tpl->assign('previousPage', $previousPage);
        $tpl->assign('pageSize', $pageSize);
        $tpl->assign('query', $query);
        $tpl->assign('engine', $engine);
        $tpl->assign('uid',$uid);
        $tpl->assign('totalPages', $totalPages);
        
        $tplFile = $config['wcm.templates.path'] . 'results/'.$style.'/bo.tpl';
        
        $html = $tpl->fetch($tplFile);
        
        

    }
    else
    {
        $html .= '<div class="resultBar">';
        $html .= _RESULT_NO_ITEM_FOUND;
        $html .= '</div>';
        $html .= '<div class="navBar"></div>';
    }
    
    echo $html;