<?php
//  ------------------------------------------------------------------------ //
// 本模組由 Yaoh 製作
// 製作日期：2013-10-27
// sl_jsloader.inc.php
// 功能：前台引用管理
 //tad_tools引用
if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, "需要 tadtools 模組，可至<a href='http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50' target='_blank'>Tad教材網</a>下載。");
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";

//  ------------------------------------------------------------------------ //
define("SL_JS_FOLDER",XOOPS_URL. "/modules/yaoh_servicelearning/js/");
define("SL_CSS_FOLDER",XOOPS_URL. "/modules/yaoh_servicelearning/css/");
define("PRE_FIX","YAOH_SERVICE_LEARNING");
class sl_jsloader{
	private static $useMyjs=false;
	private static $useJquery=false;
	private static $useJqueryUI=false;
	private static $useBootstrap=false;
	private static $useJqueryTools=false;
	private static $useColorPicker=false;
	private static $useFckeditor=false;
	//使用utf8
	public static function useUTF8(){
	  return "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
	}	
	//引用自訂的js
	public static function useMyjs(){
		$SL_JS_FOLDER= SL_JS_FOLDER;
		if(self::$useMyjs==false){
			self::$useMyjs = true;
			return "<script type='text/javascript' src='{$SL_JS_FOLDER}myjs.js'></script>";
		}
	}
	//引用jquery
	public static function useJquery(){
		$jquery=get_jquery($ui=false,$mode="local",$theme='base');
		return $jquery;
	}
	
//引用JqueryTools
	public static function useJqueryTools(){
		$SL_JS_FOLDER= SL_JS_FOLDER;
		$return='';
		if(self::$useJqueryTools==false){
		self::$useJqueryTools=true;
		//載入jquery
		$return.=self::useJquery();
		$return.="<script type='text/javascript' src='{$SL_JS_FOLDER}jquery.tools.min.js'></script>";
		return $return;
		}
	}
//引用JqueryUI
	public static function useJqueryUI($localhost=true){
		return get_jquery($ui=true,$mode="local",$theme='base');
	}
//引用Bootstrap
	public static function useBootstrap(){
		return get_bootstrap();
	}
//引用ColorPicker
	public static function useColorPicker(){
		$SL_JS_FOLDER= SL_JS_FOLDER;
		$SL_CSS_FOLDER= SL_CSS_FOLDER;
		$return='';
		if(self::$useColorPicker==false){
			self::$useColorPicker=true;
			//載入jquery
			$return.=self::useJquery();
			$return.="<script type='text/javascript' src='{$SL_JS_FOLDER}spectrum.js'></script><link rel='stylesheet' href='{$SL_CSS_FOLDER}spectrum.css' />";
		return $return;
		}
	}
//其他小工具
public function addCss($css){
$return="<style type='text/css'>
$css
</style>";
return $return;
}
public function addJquery($code){
	return "<script type='text/javascript'>$(function(){".$code."})</script>"; 
}
public function addJava($content,$src=null){
	return "<script type='text/javascript'".(($src<>null)?"src=".$src:'').">".$content."</script>"; 
}
//將東西包到某div中
public function addDiv($content,$divId=null,$divClass=null,$style=null,$attr_ar = array()){
	return "<div ".
		(($divId<>null)?" id=$divId ":'').
		(($divClass<>null)?" class=$divClass ":'').
		(($style<>null)?" style=$style ":'').
		(($attr_ar)?self::addAttributes($attr_ar):'').
		">".$content."</div>";
}
//增加屬性用工具
function addAttributes( $attr_ar ) {
        $str = ''; 
        // check minimized attributes 
        $min_atts = array('checked', 'disabled', 'readonly', 'multiple');
        foreach( $attr_ar as $key=>$val ) { 
            if ( in_array($key, $min_atts) ) { 
                if ( !empty($val) ) {  
                    $str .= " $key=\"$key\""; 
                } 
            } else { 
                $str .= " $key=\"$val\""; 
            } 
        }
        return $str; 
    }

}
?>