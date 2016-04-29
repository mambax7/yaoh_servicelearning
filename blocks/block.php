<?php
/*
 *版本：2.0 2013/11/2
 *作者：yaoh
 *功能：服務學習推薦管理系統區塊
 *@para：$options array為設定用陣列
 *		$options[0]顯示幾則
 *@return:區塊顯示的內容
 */
//
if(!defined('MY_INC_PATH'))define('MY_INC_PATH',XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning/include/');
include_once(MY_INC_PATH.'functions.php');
include_once(MY_INC_PATH.'jsloader.inc.php');
include_once(MY_INC_PATH.'tab.inc.php');
function listServiceLearningTask(){
  global $xoopsDB,$xoopsModuleConfig,$xoopsUser;
	$res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_task") . " where (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`task_dead_line`) <= 0) and `task_enable`=1 order by `task_id` desc");
	if($xoopsDB->getRowsNum($res)>0){
			$nav='';
	while($row=$xoopsDB->fetchArray($res)){
		$nav.="<a class='btn' title='{$row['task_desc']}' href='".XOOPS_URL."/modules/yaoh_servicelearning/index.php?task_id={$row['task_id']}#YaohSL_home' >".$row['task_title'].YaohSL_transTaskInfoStatus($row)."</a>";
	}
			$return=$nav;
			}else{
			$return=_MB_YAOH_SL_NO_TASK_REQUIRE;
			}
	return $return;
}
?>
