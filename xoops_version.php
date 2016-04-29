<?php
/*--------------------------
//author:DengYuan
//last modified:2014/1/13
--------------------------*/
$modversion = array();

//---模組基本資訊---//
$modversion['name'] = _MI_XV_YAOH_SL_MODE_NAME;
$modversion['version'] = 1.0;
$modversion['description'] = _MI_XV_YAOH_SL_MODE_NAME_DESC;
$modversion['author'] = 'DengYuan';
$modversion['credits'] = '';
$modversion['help'] = 'page=help';
$modversion['license'] = 'GNU GPL 2.0';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['image'] = 'images/logo.png';
$modversion['dirname'] = basename(dirname(__FILE__));


//---模組狀態資訊---//
$modversion['status_version'] = 'RC10';
$modversion['release_date'] = '2014/2/19';
$modversion['module_website_url'] = 'https://sites.google.com/a/dcjh.tn.edu.tw/mis/';
$modversion['module_website_name'] = "DengYuan's working space";
$modversion['module_status'] = 'RC';
$modversion['author_website_url'] = 'https://sites.google.com/a/dcjh.tn.edu.tw/mis/';
$modversion['author_website_name'] = 'DengYuan';
$modversion['min_php']=5.2;
$modversion['min_xoops']='2.5';


//---後台使用系統選單---//
$modversion['system_menu'] = 1;


//---模組資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][0] = 'sl_task';
$modversion['tables'][1] = 'sl_recoder';

//---後台管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';


//---前台主選單設定---//
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name']=_MI_XV_YAOH_SL_LIST;
$modversion['sub'][1]['url']='index.php?max=10';
//---偏好設定---//
$modversion['config'] = array();

//---搜尋---//
$modversion['hasSearch'] = 0;

$modversion['blocks'] = array();
$modversion['blocks'][1]['file'] = "block.php";
$modversion['blocks'][1]['name'] = _MI_XV_YAOH_SL_MODE_NAME;
$modversion['blocks'][1]['description'] = _MI_XV_YAOH_SL_MODE_NAME_DESC;
$modversion['blocks'][1]['show_func'] = "listServiceLearningTask";
$modversion['blocks'][1]['template'] = "yaoh_sl_block_tpl.html";
//$modversion['blocks'][1]['edit_func'] = "editServiceLearningTask";
$modversion['blocks'][1]['options'] = "10";


//---偏好設定---//
$modversion['config'][0]['name']  = 'input_mode';
$modversion['config'][0]['title'] = '_MI_XV_YAOH_SL_INPUT_MODE';
$modversion['config'][0]['description'] = '_MI_XV_YAOH_SL_INPUT_MODE_DESC';
$modversion['config'][0]['formtype']  = 'yesno';
$modversion['config'][0]['valuetype'] = 'int';
$modversion['config'][0]['default'] = 1;

$modversion['config'][1]['name']  = 'class_name';
$modversion['config'][1]['title'] = '_MI_XV_YAOH_SL_INPUT_CLASS_NAME';
$modversion['config'][1]['description'] = '_MI_XV_YAOH_SL_INPUT_CLASS_NAME';
$modversion['config'][1]['formtype']  = 'textarea';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = '101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,301,302,303,304,305,306,307,308,309,310,311,312,313,314,315,316,317,318,319,320';

$modversion['config'][2]['name']  = 'class_number';
$modversion['config'][2]['title'] = '_MI_XV_YAOH_SL_INPUT_CLASS_NUMBER';
$modversion['config'][2]['description'] = '_MI_XV_YAOH_SL_INPUT_CLASS_NUMBER';
$modversion['config'][2]['formtype']  = 'textarea';
$modversion['config'][2]['valuetype'] = 'text';
$modversion['config'][2]['default'] = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40';

$modversion['hasComments'] = 0;
?>
