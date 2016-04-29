<?php

$i = 1;

$YaohSL_frontEndAdminMenu[$i]['title'] = _MD_MENU_YAOH_SL_BACK_HOME;//回服務學習首頁
$YaohSL_frontEndAdminMenu[$i]['link'] = "index.php#YaohSL_home";
$YaohSL_frontEndAdminMenu[$i]['desc'] = _MD_MENU_YAOH_SL_BACK_HOME_DESC;//回到服務學習公告系統首頁
$YaohSL_frontEndAdminMenu[$i]['icon'] = 'images/home.png' ;
$i++;
$YaohSL_frontEndAdminMenu[$i]['title'] = _MD_MENU_YAOH_LIST_ALL_TASK;//列出所有服務學習
$YaohSL_frontEndAdminMenu[$i]['link'] = "index.php?op=list_all#YaohSL_home";
$YaohSL_frontEndAdminMenu[$i]['desc'] = _MD_MENU_YAOH_LIST_ALL_TASK_DESC;//列出所有任務列表
$YaohSL_frontEndAdminMenu[$i]['icon'] = 'images/list.png' ;
if(YaohSL_isManageAble()){
$i++;
$YaohSL_frontEndAdminMenu[$i]['title'] = _MD_MENU_YAOH_MY_TASK_LIST;//我的任務列表
$YaohSL_frontEndAdminMenu[$i]['link'] = "index.php?op=list_my_task#YaohSL_home";
$YaohSL_frontEndAdminMenu[$i]['desc'] = _MD_MENU_YAOH_MY_TASK_LIST_DESC;//列出您所管理的任務列表
$YaohSL_frontEndAdminMenu[$i]['icon'] = 'images/my_list.png' ;
$i++;
$YaohSL_frontEndAdminMenu[$i]['title'] =_MD_MENU_YAOH_SL_ADD_TASK;//新增任務
$YaohSL_frontEndAdminMenu[$i]['link'] = "index.php?op=add&type=task#YaohSL_home";
$YaohSL_frontEndAdminMenu[$i]['desc'] = _MD_MENU_YAOH_SL_ADD_TASK_DESC ;//添加新的服務學習任務
$YaohSL_frontEndAdminMenu[$i]['icon'] = 'images/add.png';
}
if(YaohSL_gpermCheck('1')){
$i++;
$YaohSL_frontEndAdminMenu[$i]['title'] =_MD_MENU_YAOH_MY_REG_LIST;//我的推薦清單
$YaohSL_frontEndAdminMenu[$i]['link'] = "index.php?op=history#YaohSL_home";
$YaohSL_frontEndAdminMenu[$i]['desc'] = _MD_MENU_YAOH_MY_REG_LIST_DESC ;//列出自己推薦的同學清單
$YaohSL_frontEndAdminMenu[$i]['icon'] = 'images/history.png' ;
}
if(YaohSL_isViewAble()){
$i++;
$YaohSL_frontEndAdminMenu[$i]['title'] =_MD_MENU_YAOH_TEACHER_REG_RANK;//教師推薦數量排名
$YaohSL_frontEndAdminMenu[$i]['link'] = "index.php?op=regteacherstatic#YaohSL_home";
$YaohSL_frontEndAdminMenu[$i]['desc'] = _MD_MENU_YAOH_TEACHER_REG_RANK_DESC ;//推薦排名
$YaohSL_frontEndAdminMenu[$i]['icon'] = 'images/group.png' ;

$i++;
$YaohSL_frontEndAdminMenu[$i]['title'] =_MD_MENU_YAOH_STUDENT_REG_RANK;//學生時數排名
$YaohSL_frontEndAdminMenu[$i]['link'] = "index.php?op=regstudentstatic#YaohSL_home";
$YaohSL_frontEndAdminMenu[$i]['desc'] = _MD_MENU_YAOH_STUDENT_REG_RANK_DESC ;//列出前幾名的學生
$YaohSL_frontEndAdminMenu[$i]['icon'] = 'images/child.png' ;
}
?>
