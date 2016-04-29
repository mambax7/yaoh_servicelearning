<?php
include_once "../../mainfile.php";
/*-----------設定--------------------*/
$redirect_target=XOOPS_URL."/modules/yaoh_servicelearning/index.php";
/*-----------引入檔案區--------------*/
// $myts =& MyTextSanitizer::getInstance();//過濾問題字串物件實體化
if(!defined('MY_INC_PATH'))define('MY_INC_PATH',XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning/include/');
include_once(MY_INC_PATH.'functions.php');
include_once(MY_INC_PATH.'jsloader.inc.php');
include_once(MY_INC_PATH.'tab.inc.php');
/*-----------JS區塊-----------------*/
echo sl_jsloader::useBootStrap();
echo sl_jsloader::useUTF8();
/*-----------function區--------------*/
function YaohSL_manageMenu(){
	global $xoopsUser;
	include XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning/menu.php';
	$menu="";
	$menu.="<div class='alert alert-success' id='YaohSL_home'><h3>".
	_MD_INDEX_YAOH_SL_MANAGE_MENU_TITLE.//服務學習公告
	"</h3>".
	_MD_INDEX_YAOH_SL_MANAGE_MENU_YOUR_POWER.//您的權限：
	YaohSL_PowerList()."</div>";
	$menu.="<div class='alert alert-success' style='margin-top:-20px'>".
	_MD_INDEX_YAOH_SL_MANAGE_MENU_MENU_TITLE;//管理選單
	
	foreach($YaohSL_frontEndAdminMenu as $a){
		$menu.="<a class='btn btn-mini'  href='{$a['link']}' title='{$a['desc']}'><img src='{$a['icon']}'><br>{$a['title']}</a>";
	}
	$menu.="</div>";
	return $menu;
}
function YaohSL_listServiceLearningTask($activeTaskId=null){
  global $xoopsDB;
	$res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_task") . " where (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(`task_dead_line`) <= 0) and `task_enable`=1 order by `task_id` desc");
	$tab=new opmytab();
	if($xoopsDB->getRowsNum($res)>0){
	while($row=$xoopsDB->fetchArray($res)){
		$res_s=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_recoder") . " where `task_id`={$row['task_id']}");
		//info
		$info='';
		//目前推薦數
		$regNum=$xoopsDB->getRowsNum($res_s);
		$info.="(<font color=red>{$regNum}</font>/{$row['task_maxnum']})";
		if($regNum>=$row['task_maxnum']){
			$info.="<span class='label label-important'>".
			"額滿".//額滿
			"</span>";
			$isFull=true;
		}else{
			$isFull=false;
		}
		if($row['task_enable']<>1 or strtotime($row['task_dead_line']) < time())$info.="<span class='label label-warning'>".
		_MD_INDEX_YAOH_SL_TASK_STATUS_IS_TIMEUP. //截止
		"</span>";
		
		if((strtotime($row['task_dead_line']) < time())){
			$info.= date('Y-m-d',strtotime($row['task_start_line']))."<span class='label'>".
			_MD_INDEX_YAOH_SL_TASK_STATUS_IN_PREPARATION. //準備中
			"</span>";
		}elseif($isFull==false){
			$info.="<span class='label label-info'>".
			_MD_INDEX_YAOH_SL_TASK_STATUS_IS_ENROLLING. //報名中
			"</span>";
		}
		$nav=$row['task_title'].$info;
		

		
		$content=YaohSL_transTaskInfo($row,'index');
		if(YaohSL_isViewAble($row['task_id']))$content.=YaohSL_RegList($row['task_id']);
		if($activeTaskId<>null and $row['task_id']==$activeTaskId){
			$actBollSet=true;		
		}else{
			$actBollSet=false;
		}
		$tab->addNav("slFrontEnd_{$row['task_id']}",$nav,$actBollSet);
		$tab->setContent("slFrontEnd_{$row['task_id']}",$content,$actBollSet);
			}
			$return=$tab->render();
			}else{
			$return=_MD_INDEX_YAOH_SL_TASK_STATUS_NO_TASK;//目前尚無服務學習之需求
			}
	return $return;
}
/*-----------執行動作判斷區----------*/
$op=empty($_REQUEST['op'])?"":$_REQUEST['op'];
$type=empty($_REQUEST['type'])?"":$_REQUEST['type'];
$task_id=empty($_REQUEST['task_id'])?"":(int)$_REQUEST['task_id'];
$id=empty($_REQUEST['id'])?"":(int)$_REQUEST['id'];
if(!preg_match("/^[A-Za-z0-9_]*$/", $op))exit;
if(!preg_match("/^[A-Za-z0-9_]*$/", $type))exit;
if($task_id)$redirect_target_local=$redirect_target."?task_id=".$task_id."#YaohSL_home";
switch($op){
	case 'add':
		switch($type){
			case 'task':
				YaohSL_guestDeny();
				$main= YaohSL_TaskEditForm();
			break;
			case 'reg':
				if(!YaohSL_isRegAble($task_id))exit(_MD_INDEX_YAOH_SL_TASK_ALERT_NO_POWER);//很抱歉您尚無權限
				echo YaohSL_RegEditForm($task_id,null);
				 exit;
			break;
			default:
			break;
		}
	break;
// 編輯
	case 'edit':
		switch($type){
			case 'task':
				YaohSL_guestDeny();
				$main= YaohSL_TaskEditForm($id);
			break;
			case 'reg':
				YaohSL_guestDeny();
				$main=YaohSL_RegEditForm($task_id,$id);
			break;
		}
	break;
	case 'add_do':

		switch($type){
			case 'task':
				YaohSL_guestDeny();
				if(YaohSL_TaskEditDo((isset($id))?$id:null))redirect_header($redirect_target_local,3);
				exit;
			break;
			case 'reg':
				if(!YaohSL_isRegAble($task_id) and ($id==null)){
					redirect_header($redirect_target_local,3,_MD_INDEX_YAOH_SL_TASK_ALERT_STOP_REG);//很抱歉，目前無法進行推薦
               		exit;
				}else{
					if(YaohSL_RegEditDo((isset($id))?$id:null)){
						redirect_header($redirect_target_local,3,_MD_INDEX_YAOH_SL_TASK_ALERT_REG_SUCCESS);//推薦成功
					}else{
						redirect_header($redirect_target_local,3,_MD_INDEX_YAOH_SL_TASK_ALERT_REG_ERROR);//推薦失敗，請聯絡管理員協助
					}
				}
			break;
			default:
			break;
		}
	break;
	case 'del':
		YaohSL_guestDeny();
		include MY_INC_PATH.'YaohSL_del.inc.php';
	break;
	case 'list_my_task':
		YaohSL_guestDeny();
		$main="<div id='list_my_task'></div>".YaohSL_TaskList($xoopsUser->uid());
	break;
	case 'list_all':
		 $main="<div id='list_all'></div>".YaohSL_TaskList();
	break;
	case 'list_task':
		$main= YaohSL_TaskList($_GET['UserID']);
	break;
	case 'history':
		YaohSL_guestDeny();
		$main=YaohSL_RegHistory();
	break;
	case 'regteacherstatic':
		$main=YaohSL_RegTeacherStatic();
	break;
	case 'regstudentstatic':
		$main= YaohSL_RegStudentStatic();
	break;
	case 'task_detail':
		$main=YaohSL_TaskDetail($id);
	break;
	case 'get_rss':
	         header("Content-type:text/xml");
             header("Cache-Control:no-cache");
		echo YaohSL_genRSS();
		exit;
	break;
	case 'task2csv':
		YaohSL_transTaskToCSV($id);
		exit;
	break;
	case 'task2xls':
		YaohSL_transTaskToXLS($id);
	exit;
	break;
	default:
		$main=YaohSL_listServiceLearningTask($task_id);
	break;
}

/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH."/header.php";
if($xoopsUser){
	echo YaohSL_manageMenu();
}else{
	echo "<div class='alert alert-success' id='YaohSL_home'><h3>".
	_MD_INDEX_YAOH_SL_MANAGE_MENU_TITLE.//服務學習公告
	"</h3></div>";
}
echo $main;
include_once XOOPS_ROOT_PATH.'/footer.php';
?>