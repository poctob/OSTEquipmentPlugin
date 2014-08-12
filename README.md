OSTEquipmentPlugin
=================
0. Make sure that you have downloaded correct version matching your OSTicket installation.
from here https://github.com/poctob/OSTEquipmentPlugin/releases

1. Installation - Crash course

 a. Unzip the archive into the following directory:
    [OSTicket root]/include/plugins

 b. Copy [OSTicket root]/scp/apps/dispatcher.php to [OSTicket root]/scp/dispatcher.php.
 
 c. Login into the backend admin panel.  Go to Manage->Plugins click Add New Plugin.
 
 d. Click Install under Equipment Manager.
 
 e. Check Enable check box, Save.
 
 f. Click on Equipment Manager.  Check Enable Backend.
 
 g. If you want front end.  
     Check Enable Frontend. 
     Copy  [Plugin Root]/Equipment_Front directory into [OSTicket root].
     Backup up [OSTicket root]/include/class.nav.php file.
     Replace [OSTicket root]/include/class.nav.php with one from [Plugin Root]/ost_core/include directory.
 
 h. Go to Manage->Help Topics. Select a topic that you would like to use equipment for (for example Report a Problem).  Under Custom Form select Equipment.  Click Save.

 i. Go to staff panel and explore applications->equipment tab. You will need to add equipment 
 category and status before you can add equipment item.

2. Upgrading

 a. Delete your current Equipment plugin directory from [OSTicket root]/include/plugins.
 
 b. Unzip the archive into the following directory:
    [OSTicket root]/include/plugins

 c. Copy [OSTicket root]/scp/apps/dispatcher.php to [OSTicket root]/scp/dispatcher.php.

You may see a database error on upgrade, it should go away on the next reload.

For more information visit Wiki page:
https://github.com/poctob/OSTEquipmentPlugin/wiki

email: alexp@xpresstek.net for problems
