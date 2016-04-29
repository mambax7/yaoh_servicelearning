<?php
//  ------------------------------------------------------------------------ //
// 本模組由 Yaoh 製作
// 製作日期：2013-10-27
// sl_jsloader.inc.php
// 功能：前台引用管理
 //tad_tools引用
// if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
// redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, "需要 tadtools 模組，可至<a href='http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50' target='_blank'>Tad教材網</a>下載。");
// }
// include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";

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
	//引用編輯器
	public static function useFckeditor(){
		$SL_JS_FOLDER= YAOH_WWW_URL.'/fckeditor/';
		if(self::$useFckeditor==false){
			self::$useFckeditor = true;
			return "<script type='text/javascript' src='{$SL_JS_FOLDER}fckeditor.js'></script>";
		}
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
	// public static function useJquery($localhost=true){
		// $SL_JS_FOLDER= SL_JS_FOLDER;
		// if(self::$useJquery==false){
		// self::$useJquery=true;
			// if($localhost==true){
					// return "<script type='text/javascript' src='{$SL_JS_FOLDER}jquery.min.js'></script>";
			// }else{
					// return "<script src='http://code.jquery.com/jquery-1.9.1.js'></script>";
			// }
		// }
	// }
	//引用jquery
	public static function useJquery(){
		$SL_JS_FOLDER= SL_JS_FOLDER;
		$PRE_FIX=PRE_FIX;
		if(self::$useJquery==false){
		self::$useJquery=true;
		//js檢查
		$jquery=<<<useJqueryJS
         if(typeof jQuery == "undefined"){ 
         var {$PRE_FIX}js = document.createElement("script"); 
         {$PRE_FIX}js.type = "text/javascript";
         {$PRE_FIX}js.src = "{$SL_JS_FOLDER}jquery.min.js"; 
         document.getElementsByTagName("head")[0].appendChild({$PRE_FIX}js); 
        }
useJqueryJS;
		//$jquery=get_jquery($ui=false,$mode="local",$theme='base');
		//return $jquery;
		return self::addJava($jquery);
		}
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
		$SL_JS_FOLDER= SL_JS_FOLDER;
		$SL_CSS_FOLDER= SL_CSS_FOLDER;
		$return='';
		if(self::$useJqueryUI==false){
		self::$useJqueryUI=true;
		//載入jquery
		$return.=self::useJquery();
		if($localhost==true){
			$return.="<script type='text/javascript' src='{$SL_JS_FOLDER}jquery-ui.min.js'></script><link rel='stylesheet' href='{$SL_CSS_FOLDER}jquery-ui.css' />";
		}else{
			$return.="<script src='http://code.jquery.com/ui/1.10.3/jquery-ui.js'></script> <link rel='stylesheet' href='http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' />";
		}
		//return get_jquery($ui=true,$mode="local",$theme='base');
		 return $return;
		}
	}
//引用Bootstrap
	public static function useBootstrap(){
		$SL_JS_FOLDER= SL_JS_FOLDER;
		$SL_CSS_FOLDER= SL_CSS_FOLDER;
		$PRE_FIX=PRE_FIX;
		$return='';
		if(self::$useBootstrap==false){
			self::$useBootstrap=true;
			//載入jquery
			$return.=self::useJquery();
			//js檢查
$bootstrap=<<<useBootstrap
		 if(typeof bootstrap == "undefined"){ 
			var {$PRE_FIX}jsb = document.createElement("script");
			{$PRE_FIX}jsb.type = "text/javascript";
			{$PRE_FIX}jsb.src = "{$SL_JS_FOLDER}bootstrap.js"; 
			document.getElementsByTagName("head")[0].appendChild({$PRE_FIX}jsb); 
        }
useBootstrap;
		return "<link href='{$SL_CSS_FOLDER}bootstrap.min.css' rel='stylesheet' media='screen'>".
				self::addJava($bootstrap);
		}
	}
// //引用Bootstrap
	// public static function useBootstrap(){
		// $SL_JS_FOLDER= SL_JS_FOLDER;
		// $SL_CSS_FOLDER= SL_CSS_FOLDER;
		// $return='';
		// if(self::$useBootstrap==false){
			// self::$useBootstrap=true;
			// //載入jquery
			// $return.=self::useJquery();
			// $return.="<script type='text/javascript' src='{$SL_JS_FOLDER}bootstrap.min.js'></script><link rel='stylesheet' href='{$SL_CSS_FOLDER}bootstrap.min.css' />";
		// return $return;
		// }
	// }
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