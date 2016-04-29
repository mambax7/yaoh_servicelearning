<?php
//  ------------------------------------------------------------------------ //
// 本模組由 Yaoh 製作
// 製作日期：2013-10-27
// 檔案名稱：functions.php
// 功能：前後台共用功能
//  ------------------------------------------------------------------------ //
//include
include_once XOOPS_ROOT_PATH."/mainfile.php";
if(!defined('MY_INC_PATH'))define('MY_INC_PATH',XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning/include/');
if(!defined('MY_IMG_URL'))define('MY_IMG_URL',XOOPS_URL.'/modules/yaoh_servicelearning/images/');
date_default_timezone_set("Etc/GMT-8");
/*-----------function 區-------------*/
//checker
function yaohSL_isRecoderOwner($reg_id){//檢查recoder所屬的擁有者是不是本人或者管理員，訪客無法判斷
  global $xoopsDB,$xoopsUser;
  YaohSL_guestDeny();
  $res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_recoder") . " left join " .$xoopsDB->prefix("sl_task") . " using(`task_id`) where " .$xoopsDB->prefix("sl_recoder") . ".`r_id` = '{$reg_id}'
     and
    (" .$xoopsDB->prefix("sl_recoder") . ".`UserID`='{$xoopsUser->uid()}' or " .$xoopsDB->prefix("sl_task") . ".`UserID`='{$xoopsUser->uid()}')");
  if($xoopsDB->getRowsNum($res)>0){
    return true;
  }else{
    return false;
  }
}
function yaohSL_isTaskOwner($task_id){//檢查task所屬的擁有者是不是本人
  global $xoopsDB,$xoopsUser;
  if(!$xoopsUser)return false;
  $res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_task") . " where `task_id`='{$task_id}' and `UserID`='{$xoopsUser->uid()}'");
  if($xoopsDB->getRowsNum($res)>0){
    return true;
  }else{
    return false;
  }
}
function yaohSL_isOverReg($task_id){//檢查登入者在該任務的推薦數是否已經超過限制
  global $xoopsDB,$xoopsUser;
  if($xoopsUser and isset($task_id)){
  //先取得task的教師推薦人數
    $sql1="select `task_maxnum_each` from " .$xoopsDB->prefix("sl_task") . " where `task_id`='{$task_id}'";
    $res1=$xoopsDB->query($sql1);
    $row1=$xoopsDB->fetchArray($res1);
    $task_maxnum_each=$row1['task_maxnum_each'];
  //取得目前已經推薦人數
    $sql2="select * from " .$xoopsDB->prefix("sl_recoder") . " where `task_id`='{$task_id}' and `UserID`='{$xoopsUser->uid()}'";
    $res2=$xoopsDB->query($sql2);
    $task_maxnum_each_now=$xoopsDB->getRowsNum($res2);
    if($task_maxnum_each_now>=$task_maxnum_each and $task_maxnum_each<>0 and $task_maxnum_each<>null){
      return true;
      }else{
      return false;
    }
  }else{
    return false;
  }
}
function YaohSL_isRegAble($task_id){//確認可不可以新增推薦
  global $xoopsDB;
  $sql="select * from " .$xoopsDB->prefix("sl_task") . " where `task_id`='{$task_id}'";
  $res=$xoopsDB->query($sql);
  $row=$xoopsDB->fetchArray($res);
  $res_student=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_recoder") . " where `task_id`='{$task_id}'");

  $YaohSL_RegStudentStatic=$xoopsDB->getRowsNum($res_student);
  return (
     ($row['task_maxnum_enable']<>1
       or
       $YaohSL_RegStudentStatic<$row['task_maxnum'])//沒限制人數或者目前推薦少於限制人數才能推薦
    and $row['task_enable']==1  //任務設定為開啟
    and (YaohSL_isInTime($task_id)) //期限內
    and !yaohSL_isOverReg($task_id) //未超過教師個人推薦上限才能推薦
    and YaohSL_gpermCheck('1') //細部權限設定要設定『開放推薦』的群組才能推薦
    or yaohSL_isTaskOwner($task_id) //只要是任務擁有者可以無限制的推薦
    )?true:false;
}
function YaohSL_isViewAble($task_id){//確認可不可以檢視推薦
  //global $xoopsUser;
  return (
  //  $xoopsUser //要登入才能檢視
  //  and
    YaohSL_gpermCheck('2') //細部權限設定要設定『開放推薦』的群組才能推薦
    or yaohSL_isTaskOwner($task_id) //只要是任務擁有者可以無限制的推薦
    )?true:false;
}
function YaohSL_isManageAble($task_id=null){//確認可不可以管理
  //global $xoopsUser;
  if($task_id<>null){
  return (
    //$xoopsUser //要登入才能
    //and
    YaohSL_gpermCheck('3') //細部權限設定要設定『開放管理』的群組
    and yaohSL_isTaskOwner($task_id) //任務擁有者
    )?true:false;
    }else{
    return (
    //$xoopsUser //要登入才能
    //and
    YaohSL_gpermCheck('3')
    )?true:false;
    }
}
function YaohSL_isInTime($task_id){//檢查是否在規定時間內
  global $xoopsDB;
  $sql="select * from " .$xoopsDB->prefix("sl_task") . " where `task_id`='{$task_id}'";
  $res=$xoopsDB->query($sql);
  $row=$xoopsDB->fetchArray($res);
  $s=strtotime($row['task_start_line']);
  $d=strtotime($row['task_dead_line']);
  $n=time();
  if($d > $n and $n > $s){
    return true;
  }else{
    return false;
  }
}
function YaohSL_gpermCheck($gperm_itemid){//細部權限設定檢查，有使用權傳回true
  global $xoopsUser;
  $perm_name = 'yaoh_servicelearning';
  $perm_itemid = intval($gperm_itemid);
  if($xoopsUser)
  {
    $groups = $xoopsUser->getGroups();
  }else{
    $groups = XOOPS_GROUP_ANONYMOUS;
  }
  $module_handler =& xoops_gethandler('module');
  $xoopsModule =& $module_handler->getByDirname($perm_name);
  $module_id = $xoopsModule->getVar('mid');
  $gperm_handler =& xoops_gethandler('groupperm');
  if($gperm_handler->checkRight($perm_name, $perm_itemid, $groups, $module_id))
  {
    return true;
  }else{
    return false;
  }
}
//task
function YaohSL_TaskEditForm($task_id=null){

  global $xoopsDB,$xoopsUser;

  //有給id視為編輯
  if(isset($task_id)){
    $res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_task") . " where `task_id` = {$task_id}");
    $row=$xoopsDB->fetchArray($res);
  }else{
    $row=null;
  }
  //form
  include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
  include_once XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning/include/themeformbootstrap.php';
  include_once XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning/include/formhtml5datetime.php';

  $form=new XoopsThemeFormBootstrap((($task_id)?_MD_FUNCTION_YAOH_SL_TASK_FORM_EDIT:_MD_FUNCTION_YAOH_SL_TASK_FORM_ADD)._MD_FUNCTION_YAOH_SL_TASK_FORM_TITLE, //編輯,新增,服務學習需求
                'YaohSL_TaskEditForm',
                "{$_SERVER['PHP_SELF']}?op=add_do&type=task",
                'post');
  $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_TITLE,"task_title",50,50,$row['task_title']),true);//任務標題
  $form->addElement(new XoopsFormTextArea(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_DESC,"task_desc",$row['task_desc'],3,50));//詳細說明
  $form->addElement(new XoopsFormTextArea(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_WORKTIME,"task_worktime",$row['task_worktime'],3,50));//服務時間
  $form->addElement(new XoopsFormTextArea(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_LIMIT,"task_limit",$row['task_limit'],3,50));//條件限制
  $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_REGTIME,"task_regtime",50,50,$row['task_regtime']));//可登錄時數
  $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_MAXNUM,"task_maxnum",50,50,$row['task_maxnum']));//需求人數
    $task_maxnum_enable_default=($row['task_maxnum_enable']<>'')?$row['task_maxnum_enable']:1;
  $form->addElement(new XoopsFormRadioYN(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_MAXNUM_ENABLE,"task_maxnum_enable",$task_maxnum_enable_default,_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_MAXNUM_ENABLE_TRUE,_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_MAXNUM_ENABLE_FALSE));//是否超額錄取,限制,不限制
  $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_MAXNUM_EACH,"task_maxnum_each",50,50,$row['task_maxnum_each']));//每位教師可推薦人數(空白代表不限制)
  date_default_timezone_set("Asia/Taipei");
   $form->addElement(new XoopsFormHtml5DateTime(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_START_LINE, "start_date", $row['task_start_line']));
   $form->addElement(new XoopsFormHtml5DateTime(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_DEAD_LINE, "end_date", $row['task_dead_line']));
  $form->addElement(new XoopsFormTextArea(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_GATHER,"task_gather",$row['task_gather'],3,50));//集合資訊(含要攜帶的物品)
  $form->addElement(new XoopsFormRadioYN(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_ENABLE,"task_enable",(($row['task_enable']<>'')?$row['task_enable']:1),_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_ENABLE_TRUE,_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_ENABLE_FALSE));//募集中或停止募集
  $form->addElement(new XoopsFormTextArea(_MD_FUNCTION_YAOH_SL_TASK_FORM_NOTE,"note",$row['note'],3,50));//備註(含聯絡方式)

  if(isset($task_id) and $task_id <>null)$form->addElement(new XoopsFormHidden('id',$task_id));
  //表單安全檢查
  $form->addElement(new XoopsFormHiddenToken());
  $form->addElement(new XoopsFormButton("","",_MD_FUNCTION_YAOH_SL_TASK_FORM_SEND,"submit"));//送出
  $return=$form->render();
  return $return;
}
function YaohSL_TaskEditDo($task_id=null){
  global $xoopsDB,$xoopsUser;
  if($_POST['task_title']<>null){
  //data pretreatment
  if(!$xoopsUser){
    $UserID='0';
    $UserName=XOOPS_GROUP_ANONYMOUS;
    $Email='';
  }else{
    $UserID=$xoopsUser->uid();
    $UserName=$xoopsUser->name();
    $Email=$xoopsUser->email();
  }
  $task_title=YaohSL_XssProtect($_POST['task_title']);
  $task_desc=YaohSL_XssProtect($_POST['task_desc']);
  $task_worktime=YaohSL_XssProtect($_POST['task_worktime']);
  $task_limit=YaohSL_XssProtect($_POST['task_limit']);
  $task_gather=YaohSL_XssProtect($_POST['task_gather']);
  $task_regtime=(int)$_POST['task_regtime'];
  $task_maxnum=(int)$_POST['task_maxnum'];
  $task_maxnum_enable=(bool)$_POST['task_maxnum_enable'];
  $task_maxnum_each=(int)$_POST['task_maxnum_each'];
  $task_start_line=$_POST["start_date"]['date']." ".$_POST["start_date"]['time'].":00";
  $task_start_line=$task_start_line;
  $task_dead_line=$_POST["end_date"]['date']." ".$_POST["end_date"]['time'].":00";
  $task_dead_line=$task_dead_line;
  $task_enable=(bool)$_POST['task_enable'];
  $note=YaohSL_XssProtect($_POST['note']);
  $teacher_enable=(bool)$_POST['teacher_enable'];
  //檢查是否安全
  if(!$GLOBALS['xoopsSecurity']->check()){
  $error=implode("<br />" , $GLOBALS['xoopsSecurity']->getErrors());
  redirect_header($_SERVER['PHP_SELF'],3, $error);
  }
  $sql=($task_id==null)?"
    INSERT INTO " .$xoopsDB->prefix("sl_task") . "
      (`task_title`, `task_desc`, `task_worktime`,
      `task_limit`, `task_gather`, `task_regtime`, `task_maxnum`, `task_maxnum_enable`, `task_maxnum_each`,
      `task_start_line`, `task_dead_line`, `UserID`, `UserName`, `Email`, `task_enable`, `note`, `teacher_enable`)
        VALUES
        (
      '{$task_title}' ,'{$task_desc}', '{$task_worktime}',
      '{$task_limit}', '{$task_gather}', '{$task_regtime}',
      '{$task_maxnum}', '{$task_maxnum_enable}', '{$task_maxnum_each}',
      '{$task_start_line}','{$task_dead_line}',
      '{$UserID}',
      '{$UserName}', '{$Email}',
      '{$task_enable}', '{$note}', '{$teacher_enable}'
        );
        ":"
      UPDATE " .$xoopsDB->prefix("sl_task") . " SET
        `task_title`='{$task_title}',`task_desc`='{$task_desc}',
        `task_worktime`='{$task_worktime}',
        `task_limit`='{$task_limit}', `task_gather`='{$task_gather}',
        `task_regtime`='{$task_regtime}',`task_maxnum`='{$task_maxnum}',
        `task_maxnum_enable`='{$task_maxnum_enable}',
        `task_maxnum_each`='{$task_maxnum_each}',
        `task_start_line`='{$task_start_line}',
        `task_dead_line`='{$task_dead_line}',
        `UserID`='{$UserID}',`UserName`='{$UserName}',
        `Email`='{$Email}',
        `task_enable`='{$task_enable}', `note`='{$note}', `teacher_enable`='{$teacher_enable}'
        WHERE `task_id`={$task_id} ;
        ";

    if($xoopsDB->queryF($sql)){
      redirect_header($_SERVER['PHP_SELF'],3,_MD_FUNCTION_YAOH_SL_TASK_EDIT_DO_ALERT_ADD_SUCCESS);//任務新增成功
    }else{
      redirect_header($_SERVER['PHP_SELF'],3,_MD_FUNCTION_YAOH_SL_TASK_EDIT_DO_ALERT_ADD_ERROR);//任務新增失敗
    }
  }
    redirect_header($_SERVER['PHP_SELF'],3,_MD_FUNCTION_YAOH_SL_TASK_EDIT_DO_ALERT_NO_BLANK);//標題不能空白

}
function YaohSL_getTaskInfoFromTaskId($task_id,$col_name=null){
  global $xoopsDB;
  $sql="select * from " .$xoopsDB->prefix("sl_task") . " where `task_id`='{$task_id}'";
  $res=$xoopsDB->query($sql);
  $row=$xoopsDB->fetchArray($res);
  if($col_name<>null && isset($row[$col_name])){
    return $row[$col_name];
  }else{
    return $row;
  }
}
function YaohSL_TaskList($UserID=null){
  global $xoopsDB,$xoopsUser;
  //sql建構
  $return="<table class='table'><tr><th>"._MD_FUNCTION_YAOH_SL_TASK_LIST_TITLE."</span></th></tr></table>";
  if($UserID<>null){
    $where_sql_str=" WHERE `UserID`='{$UserID}' ";
    }else{
    $where_sql_str='';
    }

  $list_task_sql="select * from " .$xoopsDB->prefix("sl_task") . " {$where_sql_str} order by task_id desc";
  //query
  $res=$xoopsDB->query($list_task_sql);
  if($xoopsDB->getRowsNum($res)>0){
  //show
  include_once(MY_INC_PATH.'table.inc.php');
  $table=new opmytable();
  $table->addTitle(array(
              _MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_ID,//服務學習編號
              _MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_USER_NAME,//承辦人
              _MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_TITLE,//服務學習主題
              _MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_REGTIME,//可認證時數
              _MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_START_END,//招募期間(起->迄)
              _MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_INFO_STATUS));//目前招募狀況
  while($row=$xoopsDB->fetchArray($res)){
    $table->addRow(array(
              $row['task_id'],
              $row['UserName'],
              "<a class='btn btn-block btn-mini' title='{$row['task_desc']}'
                href='{$_SERVER['PHP_SELF']}?op=task_detail&id={$row['task_id']}'>
                {$row['task_title']}</a>".  //標題
              ((yaohSL_isTaskOwner($row['task_id']))?"<br><font color=red>"._MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_OWNER_NOTICE."</font>":""),//您的專案檢查
              $row['task_regtime'],//可登錄時數
              _MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_REGTIME_FROM.$row['task_start_line'].
              "<br />"._MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_REGTIME_TO.$row['task_dead_line']
                ,
              YaohSL_transTaskInfoStatus($row)//狀況
              ));
      }
  $return.=$table->render();
  }else{
  $return.=_MD_FUNCTION_YAOH_SL_TASK_LIST_NO_TASK_NOW;
  }
  return $return;
}
function YaohSL_showTaskBtn($task_id){
global $xoopsUser;
if($xoopsUser->uid()<>null and YaohSL_isManageAble($task_id)){
  return "<a title='"._MD_FUNCTION_YAOH_SL_SHOW_TASK_BTN_OUTPUT_CSV."' href='{$_SERVER['PHP_SELF']}?op=task2csv&id={$task_id}'>
  <img width=36 height=36 src='".MY_IMG_URL."csv.png'></a>
  <a title='"._MD_FUNCTION_YAOH_SL_SHOW_TASK_BTN_OUTPUT_XLS."' href='{$_SERVER['PHP_SELF']}?op=task2xls&id={$task_id}'>
  <img width=36 height=36 src='".MY_IMG_URL."csv.png'></a>
  <a title='"._MD_FUNCTION_YAOH_SL_SHOW_TASK_BTN_EDIT_TASK."' href='{$_SERVER['PHP_SELF']}?op=edit&type=task&id={$task_id}'>
  <img width=36 height=36 src='".MY_IMG_URL."edit.png'></a>
  <a title='"._MD_FUNCTION_YAOH_SL_SHOW_TASK_BTN_DEL_TASK."' href='{$_SERVER['PHP_SELF']}?op=del&type=task&id={$task_id}'>
  <img width=36 height=36 src='".MY_IMG_URL."del.png'></a>";
  }
}
function YaohSL_PowerList(){
  global $xoopsUser;
  //if(!$xoopsUser)return '';
  $return='';
  if(YaohSL_gpermCheck('2')){
    $return.="<img src='".MY_IMG_URL."on.png'>"._MD_FUNCTION_YAOH_SL_POWER_LIST_VIEW;
  }else{
    $return.="<img src='".MY_IMG_URL."off.png'>"._MD_FUNCTION_YAOH_SL_POWER_LIST_VIEW;
  }
  if(YaohSL_gpermCheck('1')){
    $return.="<img src='".MY_IMG_URL."on.png'>"._MD_FUNCTION_YAOH_SL_POWER_LIST_REG;
  }else{
    $return.="<img src='".MY_IMG_URL."off.png'>"._MD_FUNCTION_YAOH_SL_POWER_LIST_REG;
  }
  if(YaohSL_gpermCheck('3')){
    $return.="<img src='".MY_IMG_URL."on.png'>"._MD_FUNCTION_YAOH_SL_POWER_LIST_MANAGE;
  }else{
    $return.="<img src='".MY_IMG_URL."off.png'>"._MD_FUNCTION_YAOH_SL_POWER_LIST_MANAGE;
  }
  return $return;
}
function YaohSL_TaskDetail($id){
  global $xoopsDB;
  $return="<div>";
  $res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_task") . " where `task_id`={$id}");
  $row=$xoopsDB->fetchArray($res);
  $res2=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_recoder") . " where `task_id`={$id}");
  $YaohSL_RegStudentStatic=$xoopsDB->getRowsNum($res2);
  $return.=YaohSL_transTaskInfo($row);
  if(YaohSL_isViewAble($row['task_id'])){
    $return.=YaohSL_RegList($row['task_id']);
  }
    $return.="</div>";
// //待增加列出參與學生資料的功能
  return $return;
}
function YaohSL_transTaskInfo(Array $row,$prefix='YaohSL_'){
  global $xoopsUser;
  include_once(MY_INC_PATH.'table.inc.php');
  $table=new opmytable('table table-bordered');
  $table->addTitle(array("<h3>"._MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_TASK_TITLE_HEAD."{$row['task_title']}</h3>".(($xoopsUser)?YaohSL_showTaskBtn($row['task_id']):'')),'',array(4));//服務學習主題:
  $table->addRow(array(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_DESC,"<div class='alert alert-error'>".$row['task_desc']."</div>"),'',array(1,3));//詳細說明
  //時間提示字串
    date_default_timezone_set('Asia/Taipei');
    if(YaohSL_isRegAble($row['task_id'])){
      $reg_btn=sl_jsloader::useMyjs();
      $reg_btn.="<br><a class='btn btn-mini' title='".
      _MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_I_WANT_TO_REG.//我要推薦學生
      "' onclick=sjgn('".XOOPS_URL."/modules/yaoh_servicelearning/index.php?op=add&type=reg&task_id={$row['task_id']}','{$prefix}add_reg_zone{$row['task_id']}')><img width=36 height=36 src='".MY_IMG_URL."add_v.png'></a>"._MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_I_WANT_TO_REG;//我要推薦學生
      if(yaohSL_isTaskOwner($row['task_id']))$reg_btn.="<font color='red'>".
      _MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_OWNER_REG_NOTICE.//(您是此專案擁有者，可以無條件新增推薦喔)
      "</font>";
      $reg_btn.="<div id='{$prefix}add_reg_zone{$row['task_id']}'></div>";
    }
  $table->addRow(array(_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_START_END,_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_REGTIME_FROM.$row['task_start_line']._MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_REGTIME_TO.$row['task_dead_line'].YaohSL_transTaskInfoStatus($row).$reg_btn),'',array(1,3));
  $table->addRow(array(_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_WORKTIME,$row['task_worktime'],_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_LIMIT,$row['task_limit']),'',array(1,1,1,1));
  $table->addRow(array(_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_REGTIME,$row['task_regtime'],_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_MAXNUM,$row['task_maxnum'].(($row['task_maxnum_enable']==true)?'':_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_TASK_MAXNUM_ENABLE_NOTICE).(($row['task_maxnum_each']>0)?"(".
  _MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_EACH_REGABLE_HEAD.//每位可推薦
  "{$row['task_maxnum_each']}".
  _MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_EACH_REGABLE_TAIL.//位
  ")":"")),'',array(1,1,1,1));
  $table->addRow(array(_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_GATHER,"<div class='alert'>".$row['task_gather']."</div>"),'',array(1,3));
  // $table->addRow(array('報名方式',(($row['teacher_enable']==1)?'全校教職員處皆可報名':'請洽承辦人報名')),'',array(1,3));
  $table->addRow(array(_MD_FUNCTION_YAOH_SL_TASK_LIST_TABLE_USER_NAME,$row['UserName']),'',array(1,3));
  $table->addRow(array(_MD_FUNCTION_YAOH_SL_TASK_FORM_NOTE,$row['note']),'',array(1,3));
  $content="<div style='border:2px dashed gray;padding:10px;'>".$table->render()."</div><font color=gray>"._MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_NOW.//現在時刻：
  date('Y-m-d H:i',time())."</font>";
  return $content;
}
function YaohSL_transTaskInfoStatus($row){
  global $xoopsDB;
  $res_s=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_recoder") . " where `task_id`={$row['task_id']}");
  $regNum=$xoopsDB->getRowsNum($res_s);
  //info
  $info='';
  $info.="(<font color=red>{$regNum}</font>/{$row['task_maxnum']})";
    if($regNum>=$row['task_maxnum']){
      $info.="<span class='label label-important'>".
      _MD_INDEX_YAOH_SL_TASK_STATUS_IS_FULL.//"額滿"
      "</span>";
      $isFull=true;
    }else{
      $isFull=false;
    }
    if($row['task_enable']<>1 or strtotime($row['task_dead_line']) < time())$info.="<span class='label label-warning'>"._MD_INDEX_YAOH_SL_TASK_STATUS_IS_TIMEUP."</span>";

    if((strtotime($row['task_start_line']) > time())){
      $info.= date('Y-m-d',strtotime($row['task_start_line']))."<span class='label'>"._MD_INDEX_YAOH_SL_TASK_STATUS_IN_PREPARATION."</span>";
    }elseif($isFull==false){
      $info.="<span class='label label-info'>"._MD_INDEX_YAOH_SL_TASK_STATUS_IS_ENROLLING."</span>";
    }
    return $info;

}
//reg
function YaohSL_RegEditForm($task_id,$id=null){
  global $xoopsDB,$xoopsUser,$xoopsModuleConfig;
  //有給id視為編輯
  if(isset($id)){
    $res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_recoder") . " where `r_id` = {$id}");
    $row=$xoopsDB->fetchArray($res);
  }
  //form
  include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
  include_once XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning/include/themeformbootstrap.php';

  $form=new XoopsThemeFormBootstrap((($id)?_MD_FUNCTION_YAOH_SL_TASK_FORM_EDIT:_MD_FUNCTION_YAOH_SL_TASK_FORM_ADD)._MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_REG_NOTICE,
                'YaohSL_RegEditForm',
                "{$_SERVER['PHP_SELF']}?op=add_do&type=reg",
                'post');
  if($xoopsModuleConfig['input_mode']==1){
    $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS,"class",50,50,$row['class']),false);
    $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS_NUMBER,"class_number",50,50,$row['class_number']),false);
  }else{
    $formElementClassRadio=new XoopsFormSelect(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS,"class",$row['class']);
    $formElementClassRadio->addOptionArray(explode(',',$xoopsModuleConfig['class_name']));
    $form->addElement($formElementClassRadio);
    $formElementNumberRadio=new XoopsFormSelect(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS_NUMBER,"class_number",$row['class_number']);
    $formElementNumberRadio->addOptionArray(explode(',',$xoopsModuleConfig['class_number']));
    $form->addElement($formElementNumberRadio);
  }
  $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_NAME,"name",50,50,$row['name']),true);
  $form->addElement(new XoopsFormText(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_STUDENT_ID,"student_id",50,50,$row['student_id']),true);
  $form->addElement(new XoopsFormTextArea(_MD_FUNCTION_YAOH_SL_REG_TABLE_REG_REASON,"desc",$row['desc'],3,50));
  $form->addElement(new XoopsFormTextArea(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_R_NOTE,"r_note",$row['r_note'],3,50));
  $form->addElement(new XoopsFormHidden('task_id',$_GET['task_id']));
  if(isset($id) and $id <>null)$form->addElement(new XoopsFormHidden('id',$id));
  //表單安全檢查
  $form->addElement(new XoopsFormHiddenToken());
  $form->addElement(new XoopsFormButton(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_REG,"",_MD_FUNCTION_YAOH_SL_TASK_FORM_SEND,"submit"));
  $return=$form->render();

  return $return;
}
function YaohSL_RegEditDo($id=null){
  global $xoopsDB,$xoopsUser;
  //data pretreatment
  $task_id=(int)$_POST['task_id'];
  if($xoopsUser){
    $uid=$xoopsUser->uid();
    $uname=$xoopsUser->name();
    $uemail=$xoopsUser->email();
  }else{
    $uid=null;
    $uname=_MD_FUNCTION_YAOH_SL_REG_EDIT_DO_GUEST;//訪客
    $uemail='';//未來可以讓使用者輸入
  }
  $student_id=(int)$_POST['student_id'];
  $name=YaohSL_XssProtect($_POST['name']);
  $desc=YaohSL_XssProtect($_POST['desc']);
  $class=YaohSL_XssProtect($_POST['class']);
  $class_number=(int)$_POST['class_number'];
  $r_note=YaohSL_XssProtect($_POST['r_note']);
  //檢查是否安全
  if(!$GLOBALS['xoopsSecurity']->check()){
  $error=implode("<br />" , $GLOBALS['xoopsSecurity']->getErrors());
  redirect_header($_SERVER['PHP_SELF'],3, $error);
  }
  //sql
  if($_POST['name']<>null and $_POST['student_id']<>null){
    $sql=($id==null)?"
      INSERT INTO " .$xoopsDB->prefix("sl_recoder") . "
        (`task_id`, `UserID`, `UserName`,
        `Email`, `student_id`, `name`,
        `desc`, `class`, `class_number`, `r_note`, `r_time`
        ) VALUES
        ('{$task_id}','{$uid}','{$uname}',
        '{$uemail}','{$student_id}','{$name}',
        '{$desc}','{$class}', '{$class_number}', '{$r_note}'
        , NOW() );
        ":"
        UPDATE " .$xoopsDB->prefix("sl_recoder") . " SET ".
    " `student_id`='{$student_id}',`name`='{$name}',`desc`='{$desc}',
        `class`='{$class}', `class_number`='{$class_number}', `r_note`='{$r_note}' WHERE `r_id`='{$id}' and `task_id`='{$task_id}';
        ";

  if($xoopsDB->queryF($sql)){
    return true;
  }else{
    return false;
  }
  }
    YaohSL_goBack('-1',_MD_FUNCTION_YAOH_SL_REG_EDIT_DO_ID_NAME_NOTICE);//姓名和學號都不能空白喔，以利服務學習時數之登錄
}
function YaohSL_RegHistory(){
  global $xoopsDB,$xoopsUser;
  $res=$xoopsDB->query("SELECT  * FROM  " .$xoopsDB->prefix("sl_recoder") . " LEFT JOIN  ( select `task_id`,`task_title`,`task_regtime` From " .$xoopsDB->prefix("sl_task") . ") as `b` USING (`task_id`) WHERE  " .$xoopsDB->prefix("sl_recoder") . ".`UserID` = '{$xoopsUser->uid()}'");
  if($xoopsDB->getRowsNum($res)>0){
  $return="<font color='red'>"._MD_FUNCTION_YAOH_SL_REG_HISTORY_NOTICE."</font>";
  include_once MY_INC_PATH.'table.inc.php';
  $table=new opmytable();
  $table->addTitle(array(_MD_FUNCTION_YAOH_SL_REG_TABLE_OPERATION,_MD_FUNCTION_YAOH_SL_REG_TABLE_REG_TIME,_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_TITLE,_MD_FUNCTION_YAOH_SL_TASK_FORM_TASK_REGTIME,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS_NUMBER,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_NAME,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_STUDENT_ID,_MD_FUNCTION_YAOH_SL_REG_TABLE_REG_TEACHER,_MD_FUNCTION_YAOH_SL_REG_TABLE_REG_REASON,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_R_NOTE));



  while($row=$xoopsDB->fetchArray($res)){
    $table->addRow(array(
      (($xoopsUser->uid()==$row['UserID'])?"
        <a href='{$_SERVER['PHP_SELF']}?op=del&type=reg&id={$row['r_id']}'>
        <img src='".MY_IMG_URL."del.png'></a>
        <a href='{$_SERVER['PHP_SELF']}?op=edit&type=reg&task_id={$row['task_id']}&id={$row['r_id']}'>
        <img src='".MY_IMG_URL."edit.png'></a>
        ":"--")
        ,$row['r_time'],$row['task_title'],$row['task_regtime'],$row['class'],$row['class_number'],$row['name'],$row['student_id'],
        (($xoopsUser->name()==$row['UserName'])?"<span style='background-color:yellow'>{$row['UserName']}</span>":"{$row['UserName']}"),$row['desc'],$row['r_note']));
    }


  $return.=$table->render();
  }else{
  $return=_MD_FUNCTION_YAOH_SL_REG_TABLE_NO_REG_NOTICE;
  }
  return $return;
}
function YaohSL_RegTeacherStatic(){
  global $xoopsDB,$xoopsUser;
  $return="<h2>"._MD_FUNCTION_YAOH_SL_REG_TEACHER_STATIC_TITLE
  ."</h2>";
  $res=$xoopsDB->query("SELECT  *,count(`UserID`) as `reg_teacher_static` FROM  " .$xoopsDB->prefix("sl_recoder") . "
        LEFT JOIN  ( select `task_id`,`task_title` From " .$xoopsDB->prefix("sl_task") . ")
        as `b` USING (`task_id`)  GROUP BY `UserID`
        ORDER BY `reg_teacher_static` desc
        ");
  $row=$xoopsDB->fetchArray($res);
  include_once MY_INC_PATH.'table.inc.php';
  $table=new opmytable();
  $table->addTitle(array(_MD_FUNCTION_YAOH_SL_REG_TEACHER_STATIC_TEACHER_NAME,_MD_FUNCTION_YAOH_SL_REG_TEACHER_STATIC_STATIC));
  $max_YaohSL_RegTeacherStatic=(isset($row[0]['reg_teacher_static']) and $row[0]['reg_teacher_static']>0)?$row[0]['reg_teacher_static']:1;
  do{
    $table->addRow(array($row['UserName'],$row['reg_teacher_static']."<div style='background-color:black;height:20px;width:".($row['reg_teacher_static']/$max_YaohSL_RegTeacherStatic*100)."px' />"));
  }while($row=$xoopsDB->fetchArray($res));
      $return.=$table->render();
  return $return;
}
function YaohSL_RegStudentStatic(){
  global $xoopsDB,$xoopsUser;
  $return="<h2>"._MD_FUNCTION_YAOH_SL_REG_TEACHER_STATIC_TITLE."</h2><font color=red>"._MD_FUNCTION_YAOH_SL_REG_TEACHER_STATIC_DESC."</font>";
  $res=$xoopsDB->query("SELECT  *,sum(`task_regtime`) as `reg_student_static`,
        group_concat(concat(`task_title`,'(',`UserName`,',',`task_regtime`,')')) as `concat_title`
         FROM  " .$xoopsDB->prefix("sl_recoder") . "
         LEFT JOIN  ( select `task_id`,`task_title`,`task_regtime` From " .$xoopsDB->prefix("sl_task") . ")
        as `b` USING (`task_id`)
  WHERE `student_id`<>''
GROUP BY `student_id`
        ORDER BY `reg_student_static` desc

        ");

  $row=$xoopsDB->fetchArray($res);
  include_once MY_INC_PATH.'table.inc.php';
  $table=new opmytable();
  $table->addTitle(array(_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_STUDENT_ID,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_NAME,_MD_FUNCTION_YAOH_SL_REG_TEACHER_STATIC_HOUR_COUNT,_MD_FUNCTION_YAOH_SL_REG_TEACHER_STATIC_RECODER));
  $max_YaohSL_RegStudentStatic=(isset($row[0]['reg_student_static']) and $row[0]['reg_student_static']>0)?$row[0]['reg_student_static']:1;
  do{
    $table->addRow(array($row['student_id'],$row['name'],$row['reg_student_static'],
      //"<div style='background-color:black;height:20px;width:".
      //($row['reg_student_static']/$max_YaohSL_RegStudentStatic*100).
      //"px' />".
      $row['concat_title']));
  }while($row=$xoopsDB->fetchArray($res));
    $return.=$table->render();
  return $return;
}
function YaohSL_RegList($task_id){
  global $xoopsDB;
  $sql="select * from " .$xoopsDB->prefix("sl_recoder") . " where `task_id`='{$task_id}' order by r_id desc";
  return YaohSL_RegListSQL($sql);
}
function YaohSL_RegListSQL($sql){
    global $xoopsDB,$xoopsUser;
    $return='';
    $res=$xoopsDB->query($sql);
    if($xoopsDB->getRowsNum($res)){
    include_once MY_INC_PATH.'table.inc.php';
    $table=new opmytable('table');
    $table->addTitle(array(_MD_FUNCTION_YAOH_SL_REG_TABLE_OPERATION,_MD_FUNCTION_YAOH_SL_REG_TABLE_REG_TIME,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_CLASS_NUMBER,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_NAME,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_STUDENT_ID,_MD_FUNCTION_YAOH_SL_REG_TABLE_REG_TEACHER,_MD_FUNCTION_YAOH_SL_REG_TABLE_REG_REASON,_MD_FUNCTION_YAOH_SL_TRANS_TASK_INFO_R_NOTE));
    if($xoopsDB->getRowsNum($res)==0){
      $table->addRow(array(_MD_FUNCTION_YAOH_SL_REG_LIST_SQL_NO_REG),'',array(9));
    }else{
      while($row=$xoopsDB->fetchArray($res)){
          if($xoopsUser){
          $controler=($xoopsUser->uid()==$row['UserID'] or $xoopsUser->uid()==YaohSL_getTaskInfoFromTaskId($row['task_id'],'UserID'))?"
            <a href='{$_SERVER['PHP_SELF']}?op=del&type=reg&id={$row['r_id']}'>
            <img src='".MY_IMG_URL."del.png'></a>
            <a href='{$_SERVER['PHP_SELF']}?op=edit&type=reg&task_id={$row['task_id']}&id={$row['r_id']}'>
            <img src='".MY_IMG_URL."edit.png'></a>
            ":"--";
            $uname=$xoopsUser->name();
          }else{
            $controler='--';
            $uname=_MD_FUNCTION_YAOH_SL_REG_EDIT_DO_GUEST;
          }
          $table->addRow(array(
            $controler,$row['r_time'],$row['class'],$row['class_number'],
            $row['name'],$row['student_id'],
            $uname,$row['desc'],$row['r_note']));
        }
      }
      $return.=$table->render();
      }else{
      $return.=_MD_FUNCTION_YAOH_SL_REG_LIST_SQL_NO_REG;
      }
      return "<div id='reglist'></div>".$return;
  }
//function

function YaohSL_genRSS($add=''){  //RSS種子產生器
  function get_RSS_time($the_time){
    return date('r',strtotime(str_replace("+"," +",str_replace("T"," ",preg_replace("([+].*)","\\1",date("c",$the_time))))));
  }
    //rss頭
      $return="";
      $return.="<?xml version='1.0' encoding='utf-8'?>";
      $return.="<rss version='2.0'>";
      $return.="<channel>";
      $return.="<title><![CDATA["._MD_INDEX_YAOH_SL_MANAGE_MENU_TITLE."]]></title>";
      $return.="<link><![CDATA[".XOOPS_URL."/modules/yaoh_servicelearning/"."]]></link>";
      $return.="<description><![CDATA["._MD_INDEX_YAOH_SL_MANAGE_MENU_TITLE."]]></description>";
      $return.="<language>zh-TW</language>";
      $return.="<ttl>10</ttl>";

  global $xoopsDB,$xoopsUser;
  $res=$xoopsDB->query("select * from " .$xoopsDB->prefix("sl_task") . " where ((TO_DAYS(NOW()) - TO_DAYS(task_dead_line) <= 2) or `hidden_at_dead_line`<>1) and `task_enable`=1 order by `task_id` desc ".$limit_str);
  while($row=$xoopsDB->fetchArray($res)){
      $title=strip_tags($row['Roles'].":『".$row['task_title'].'』');
      $desc=$row['task_desc'];
    $return.="<item>";
    $return.="<title><![CDATA[".$title."]]></title>";
    $return.="<link><![CDATA[".XOOPS_URL."/modules/yaoh_servicelearning/"."]]></link>";
    $return.="<description><![CDATA[".$desc."]]></description>";
    $return.="<pubDate>".get_RSS_time(strtotime($row['task_start_line']))."</pubDate>";
    $return.="</item>";
  }

    //rss尾巴
    $return.="</channel>";
    $return.="</rss>";
  return $return;
}
function YaohSL_transTaskToCSV($id){
      global $xoopsDB,$xoopsUser;
      //編碼轉換
      function iconv2big5($arr)
      {
        return iconv('utf-8','big5',$arr);
      }
      $crlf="\r\n";
      echo iconv2big5(_MD_FUNCTION_YAOH_SL_TRANS_TASK_TO_CSV_TITLE_ARRAY).$crlf;
      $order_sql_str=' order by `r_id` desc, `class` asc ';
      $sql="select * from " .$xoopsDB->prefix("sl_recoder") . " left join " .$xoopsDB->prefix("sl_task") . " using(`task_id`) where `task_id`='{$id}' {$order_sql_str}";
      $res=$xoopsDB->query($sql);
      $rows=$xoopsDB->fetchArray($res);
      header("Content-type: application/file; charset=utf-8");
      header("Content-Disposition: attachment; filename="._MD_FUNCTION_YAOH_SL_TRANS_TASK_TO_CSV_FILE_NAME_HEADER."{$id}.csv");
      while($row=$xoopsDB->fetchArray($res)){
        $row = array_map("iconv2big5", $row);
        //加入後台設定功能或自動判斷機制或由使用者輸入
        echo  "102,2,".substr($row["class"],0,1) . "," .substr($row["class"],1,2) . "," . $row["class_number"] . "," .$row["student_id"] . ",".$row["name"].",".$row['task_regtime'].",".$row['task_title'];
        echo  $crlf;
      }
}
function YaohSL_transTaskToXLS($id){
  header("Content-Disposition: attachment; filename="._MD_FUNCTION_YAOH_SL_TRANS_TASK_TO_CSV_FILE_NAME_HEADER."{$id}.xls;");
      //編碼轉換
      function iconv2big5($arr)
      {
        return iconv('utf-8','big5',$arr);
      }
echo '<HTML xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">'."\n";
echo '<head><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"></head>'."\n";
echo "<body><table class='table'>\n";
//標題
echo "<tr>";
  $i=1;
  $title_array=explode(',',_MD_FUNCTION_YAOH_SL_TRANS_TASK_TO_CSV_TITLE_ARRAY);
  foreach($title_array as $t){
    echo "<td>".$t."</td>";
    $i++;
  }
echo "</tr>";

      global $xoopsDB,$xoopsUser;
      $order_sql_str=' order by `r_id` desc, `class` asc ';
      $sql="select * from " .$xoopsDB->prefix("sl_recoder") . " left join " .$xoopsDB->prefix("sl_task") . " using(`task_id`) where `task_id`='{$id}' {$order_sql_str}";
      $res=$xoopsDB->query($sql);
    //第二列開始
      $i=2;
      while($row=$xoopsDB->fetchArray($res)){
        //$row = array_map("iconv2big5", $row);
        $array=array(101,2,substr($row["class"],0,1),substr($row["class"],1,2),$row["class_number"],$row["student_id"],$row["name"],$row['task_regtime'],$row['task_title']);
        $j=1;
        echo "<tr>";
        foreach($array as $a){
          echo "<td>".$a."</td>";
          $j++;
        }
        echo "</tr>";
        $i++;
      }

echo '</table></body></html>';

}
function YaohSL_goBack($to=-1,$str=null,$waiting=null){
  echo "<script language=javascript>";
  if($str<>null){
    echo "alert('{$str}');";
  };
  if($waiting<>null){
    echo "sleep($waiting);";
  }
    echo "history.go({$to});";
  echo "</script>";
    exit;
}
function YaohSL_Alert($str){
  echo "<script language=javascript>
        alert('{$str}');
    </script>";
}
function YaohSL_XssProtect($data) {
  $pattern=array();
  //pattern 參考自http://www.thinkstudio.cc/blog/?post=110
  $pattern[]="/<(\\/?)(script|iframe|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU";
  $pattern[]="/\\s+/";
  $pattern[]="/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU";
   return preg_replace($pattern,"",$data);

}
// function YaohSL_XssProtect($data, $strip_tags = false, $allowed_tags = "") {//有些狀況會將資料轉為亂碼
    // if($strip_tags) {
        // $data = strip_tags($data, $allowed_tags . "<b>");
    // }

    // if(stripos($data, "script") !== false) {
        // $result = str_replace("script","scr<b></b>ipt", htmlentities($data, ENT_QUOTES));
    // } else {
        // $result = htmlentities($data, ENT_QUOTES);
    // }

    // return $result;
// }
function YaohSL_guestDeny(){
  global $xoopsUser;
  if(!$xoopsUser)redirect_header($_SERVER['PHP_SELF'],3,_MD_FUNCTION_YAOH_SL_GUEST_DENY_NOTICE);
}
?>
