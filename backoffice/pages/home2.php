<?php
/*
 * Project:     WCM
 * File:        home.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

    // Execute action
    wcmMVC_Action::execute('home');

    // Include header
    include('includes/header.php');
    // Render user dashboard
    $dashboard = new wcmDashboard();
    echo $dashboard->render();

    include('includes/footer.php');