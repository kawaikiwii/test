<?php

/**
 * Project:     WCM
 * File:        _header.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */
?>
<body>
<div id="wrapper">
    <div id="header">
        <div id="banner">
            <h1><span>Nstein WCM</span></h1>
            <h2>Powering Online Publishing</h2>
            <form method="post" name="globalSearch" action="<?php echo $config['wcm.backOffice.url']; ?>index.php?paramPrefix=qs_">
                <input type="text" class="reg" id="search_string" name="qs_query" value="" size="55" />
                <input type="hidden" name="_wcmAction" value="business/search" />
                <input type="hidden" name="_wcmTodo" value="initSearch" />
                <input type="submit" class="reg submit" value="<?php echo _SEARCH; ?>" />
            </form>
        </div>
        <div id="navMenu"><?php wcmGUI::renderMainMenu();?></div>
        <div id="systemBar">
            <p class="signon">
                    <span>
                    <strong><?php echo _SIGNED_IN_AS; ?></strong> <?php echo $session->userName; ?>
                </span>
                    <a href="<?php echo $config['wcm.backOffice.url']; ?>index.php?_wcmAction=logout" class="signout"><span><span><?php echo _LOGOUT; ?></span></span></a>
            </p>
            <p class="breadcrumb">
                <?php wcmGUI::renderWorkingSite(); ?>
            </p>
            <div id="messagebox" onclick="$('sysmessage').hide();">
            <?php
                $kind = wcmMVC_Action::getMessageKind();
                $message = wcmMVC_Action::getMessage();
                if ($message)
                {
                    $className = ($kind == WCMLOG_ERROR) ? 'error' : ($kind == WCMLOG_WARNING) ? 'warning' : 'info';
                    echo '<div id="sysmessage" class="' . $className . '">' . $message . '</div>';
                    if($kind == WCMLOG_MESSAGE)
                    {
                        echo '<script type="text/javascript">if(wcmMessage) wcmMessage.timer = setTimeout(wcmMessage.hideMsg.bind(wcmMessage),3000);</script>';
                    }
                }
                else
                {
                    echo '<div id="sysmessage" style="display: none;">&nbsp;</div>';
                }
            ?>
            </div>
        </div>
    </div>
    
    <div id="modalWindow">
        <h2 id="modalTitle"></h2>
            <div id="modalDialog"></div>
    </div>
    <div id="modalBackground"></div>

    <div id="content-wrapper">
