<?php
include '../../../include/cp_header.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsform/grouppermform.php';

//取得本模組編號
$module_id = $xoopsModule->getVar('mid');

//權限項目陣列
$item_list = array(
    '1' => _AM_YAOHSL_POWER_REG, //推薦學生
    '2' => _AM_YAOHSL_POWER_VIEW_REG, //檢視推薦清單
    '3' => _AM_YAOHSL_POWER_MANAGEABLE //管理與新增任務權限
);

//頁面標題
$title_of_form = _AM_YAOHSL_TITLE;//服務學習系統細部權限設定

//權限名稱
$perm_name = 'yaoh_servicelearning';

//權限描述
$perm_desc = _AM_YAOHSL_DESC;//請勾選欲開放給群組使用的權限：<br>

//建立XOOPS權限表單
$formi = new XoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);

//將權限項目設進表單中
foreach ($item_list as $item_id => $item_name) {
	$formi->addItem($item_id, $item_name);
}

xoops_cp_header();
//loadModuleAdminMenu(2);
echo $formi->render();
xoops_cp_footer();
?>
