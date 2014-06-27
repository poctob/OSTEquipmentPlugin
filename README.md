OSTEquipmentPlugin
=================
0. Make sure that you have downloaded correct version matching your OSTicket installation.
from here https://github.com/poctob/OSTEquipmentPlugin/releases

1. Installation - Crash course

 a. Unzip the archive into the following directory:
    <OSTicket root>/include/plugins

 b. Merge everything from  <Plugin Root>/scp directory into <OSTicket root>/scp directory.
 
 c. Merge everything from  <Plugin Root>Images directory into <OSTicket root>/Images directory.
 
 d. Merge everything from  <Plugin Root>/assets directory into <OSTicket root>/assets directory.
 
 e. Login into the backend admin panel.  Go to Manage->Plugins click Add New Plugin.
 
 f. Click Install under Equipment Manager.
 
 g. Check Enable check box, Save.
 
 h. Click on Equipment Manager.  Check Enable Backend.
 
 i. If you want front end.  
     Check Enable Frontend. 
     Copy  <Plugin Root>/Equipment_Front directory into <OSTicket root>.
     Backup up <OSTicket root>/include/class.nav.php file.
     Copy <OSTicket root>/include/class.nav.php with one from <Plugin Root>/ost_core/include directory.
 
 j. Go to Manage->Help Topics. Select a topic that you would like to use equipment for (for example Report a Problem).  Under Custom Form select Equipment.  Click Save.

 k. Go to staff panel and explore equipment tab. You will need to add equipment 
 category and status before you can add equipment item.

For more information visit Wiki page:
https://github.com/poctob/OSTEqupmentPlugin/wiki

email: alexp@xpresstek.net for problems
