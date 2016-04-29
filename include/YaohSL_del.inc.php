<?php
			switch($type){
				case 'task':
						if(!yaohSL_isTaskOwner($id))exit;
						$sql="DELETE FROM " .$xoopsDB->prefix("sl_task") . " WHERE `task_id`={$id}";
						$sql2 = "DELETE FROM " .$xoopsDB->prefix("sl_recoder") . " WHERE `task_id`={$id}";
						$action_str='task';
				break;
				case 'reg':
						if(yaohSL_isRecoderOwner($id)){
							$sql="DELETE FROM " .$xoopsDB->prefix("sl_recoder") . " WHERE `r_id`={$id}";
						}else{
							$sql = "DELETE FROM " .$xoopsDB->prefix("sl_recoder") . " WHERE `r_id`={$id} and `UserID`='{$xoopsUser->uid()}'";
						}
						$action_str='reg';
				break;
				}
				if(isset($_GET['del_check']) and $_GET['del_check']=='del'){
					if(isset($sql2)){
						if($xoopsDB->queryF($sql2) && $xoopsDB->queryF($sql))redirect_header($redirect_target,3,'刪除成功');
					}else{
						if($xoopsDB->queryF($sql))redirect_header($redirect_target,3,'刪除成功');
					}
				}elseif(isset($_GET['del_check'])){
						YaohSL_goBack('-2','要刪除要輸入del');
				}else{
					$main =  "<h2>為避免不小心刪除重要資料，請再三確認您的動作</h2>
					請輸入[del]確認刪除
					<form name=form1 method=get action='{$_SERVER['PHP_SELF']}?op=del&type={$action_str}'>
					<input type=hidden name=op value=del>
					<input type=hidden name=type value={$action_str}>
					<input type=hidden name=id value={$id}>
					<input type=text id=del_check name=del_check>
					<input type=submit value=刪除 /></form>";
				}
?>