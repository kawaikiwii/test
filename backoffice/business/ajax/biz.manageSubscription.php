<?php
require_once dirname(__FILE__).'/../../initWebApp.php';

// Get current project
$project = wcmProject::getInstance();
$session = wcmSession::getInstance();
$connector = $project->datalayer->getConnectorByReference("biz");
$db = $connector->getBusinessDatabase();
global $db, $project, $session;
// What we need:
// div id
// id to check for subscriptions
// webuser/bizclass/custom
// if webuser: webuser id
// if bizclass: bizclass name

header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );
header("Content-Type: text/html");
switch ($_REQUEST['func'])
{
    case "delete":
        $query = 'DELETE FROM biz_subscription WHERE id=?';
        $db->executeStatement($query, array($_REQUEST['id']));
        break;
}

createSubscriptionList();

function createSubscriptionList()
{
    global $db, $project, $session;
    switch ($_REQUEST['searchType'])
    {
        case 'bizclass':
            // Search for all subscriptions matching all instances of bizclass
            $query = 'SELECT * FROM biz_subscription WHERE bizclass=?';
            $params[] = $_REQUEST['bizclass'];
            break;
        case 'bizobject':
            // Search for all subscription matching a single bizclass
            $query = 'SELECT * FROM biz_subscription WHERE bizclass=? AND subscribedId=?';
            $params[] = $_REQUEST['bizclass'];
            $params[] = $_REQUEST['subscribedId'];
            break;
        case 'webuser':
            // Search for all subscriptiosn for a particular webuser
            $query = 'SELECT * FROM biz_subscription WHERE webuserId=?';
            $params[] = $_REQUEST['webuser'];
            break;
        case 'specificSubscription':
            // Search for a specific subscription by id
            $query = 'SELECT * FROM biz_subscription WHERE id=?';
            $params[] = $_REQUEST['id'];
            break;
    }
    
    switch ($_REQUEST['func'])
    {
        case 'save':
            
            $sub = new subscription($project);
            if (isset($_REQUEST['id'])) $sub->refresh($_REQUEST['id']);
            $sub->checkIn($_REQUEST);
        case 'list':
        default:
            $res = $db->executeQuery($query, $params);
            $i = 0;
            foreach ($res as $row)
            {
                $i++;
                if ($row['subscribedClass'])
                {
                    $bo = new $row['subscribedClass']($project);
                    $bo->refresh($row['subscribedId']);
                    $title = $bo->title;
                    $id = $bo->id;
                } else {
                    $title = $row['subscribedId'];
                }
                $icon = ($row['subscribedClass'])? $row['subscribedClass'] : 'fiche';
                ?>
                    <table width="100%" height="20" bgcolor="#c0c0c0" cellspacing="1">
                        <tr bgcolor="#f4f4f4" height="50%">
                            <td width="30" align="center" class="position" rowspan="2"> 
                                <?php echo $i;?>
                            </td>
                            <td width="*">
                                <table>
                                    <tr>
                                        <td>
                                            <img src='img/icons/<?php echo $icon;?>.gif' alt='' border='0' hspace='2'>
                                        </td>
                                        <td>
                                            <?php if ($row['subscribedClass']): ?>
                                                <div style="cursor:pointer" onClick="onSelectItem('<?php echo $row['subscribedClass'];?>', <?php echo $row['subscribedId']; ?>);">
                                                    <strong><?php echoH8(getConst($project->bizlogic->getBizClassByClassName($row['subscribedClass'])->title));?>:</strong> <?php echo $title; ?>
                                                </div> 
                                            <?php else: ?>
                                                <div>
                                                    <strong><?php echo _BIZ_CUSTOMIZED_SUBSCRIPTION; ?></strong>: <?php echo $title; ?>
                                                </div>
                                            <?php endif; ?>                                
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <?php if (isset($_REQUEST['locked']) && !$_REQUEST['locked']): ?><<<<<<< .working
                            <td align="center" rowspan="2" style="width: 20px"> <img style="cursor:pointer" src="img/delete.gif" alt="<?php echo _BIZ_DELETE; ?>" title="<?php echo _BIZ_DELETE; ?>" onClick="deleteSubscription('<?php echo $row['id']; ?>','<?php echo $_REQUEST['listId'];?>');"/> </td>
                            <?php endif; ?>
                        </tr>
                        <tr bgcolor="#f4f4f4">
                            <td>
                                <?php if ($row['subscriptionStart']): ?>
                                <strong><?php echo _BIZ_START_DATE; ?>:</strong> <?php echo $row['subscriptionStart']; ?>
                                <?php endif; ?>
                                <?php if ($row['subscriptionEnd']): ?>
                                <span style="font: 10px tahoma; font-weight: bold">::</span> <strong><?php echo _BIZ_END_DATE; ?>:</strong> <?php echo $row['subscriptionEnd']; ?>
                                <?php endif; ?>
                                (<?php echo _BIZ_SUBSCRIPTION_ID; ?>: <?php echo $row['id']; ?>)
                            </td>
                        </tr>
                                
                    </table>
                <?php
            }            
    }
}
?>