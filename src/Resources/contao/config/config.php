<?php

/*H4aTabellen
*filesource
 */

$GLOBALS['TL_CTE']['handball']['h4a_spiele'] = Janborg\H4aTabellen\Elements\ContentH4aSpiele::class;
$GLOBALS['TL_CTE']['handball']['h4a_tabelle'] = Janborg\H4aTabellen\Elements\ContentH4aTabelle::class;


$GLOBALS['BE_MOD']['content']['calendar']['update_events'] = ['Janborg\H4aTabellen\Cron\H4aEventAutomator', 'updateEvents'];