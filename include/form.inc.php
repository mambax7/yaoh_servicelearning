<?php
if(!defined('YAOH_SL_PATH'))define('YAOH_SL_PATH',XOOPS_ROOT_PATH.'/modules/yaoh_servicelearning');
 //tad_tools引用
if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, "需要 tadtools 模組，可至<a href='http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50' target='_blank'>Tad教材網</a>下載。");
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";

class myform{
//設定月份
public static $MONTHS_LONG = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 
        'August', 'September', 'October', 'November', 'December'); 

//開頭字串產生器，若空白表示要用ajax傳遞，讓action=javascript:void%200
	public function startForm($action = '', $method = 'post', $id = NULL, $attr_ar = array('enctype'=>'multipart/form-data')) {
		$action=($action=='')?'javascript:void%200':$action;
        $str = "<form action=\"$action\" method=\"$method\""; 
        if ( isset($id) ) { 
            $str .= " id=\"$id\""; 
        } 
        $str .= $attr_ar? $this->addAttributes( $attr_ar ) . '>': '>'; 
        return $str;
    }
//結尾字串產生器
    public function endForm() { 
        return "</form>"; 
    }
//增加屬性用工具
private function addAttributes( $attr_ar ) { 
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
private function addCss($css){
	$return="<style type='text/css'>
	$css
	</style>";
	return $return;
}
private function addJquery($code){
	return "<script type='text/javascript'>$(function(){".$code."})</script>"; 
}
private function addJava($content,$src=null){
	return "<script type='text/javascript'".(($src<>null)?"src=".$src:'').">".$content."</script>"; 
}
//inputBox
    public function addInput($type, $name, $value, $attr_ar = array() ) {
        $str = "<input type=\"$type\" name=\"$name\" value=\"$value\""; 
        if ($attr_ar) { 
            $str .= $this->addAttributes( $attr_ar ); 
        } 
        $str .= ' />'; 
        return $str; 
    } 
//addTextarea
    public function addTextarea($name, $rows = 4, $cols = 30, $value = '', $attr_ar = array() ) {
        $str = "<textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\""; 
        if ($attr_ar) { 
            $str .= $this->addAttributes( $attr_ar ); 
        } 
        $str .= ">$value</textarea>"; 
        return $str; 
    } 
    // for attribute refers to id of associated form element 
    public function addLabelFor($forID, $text, $attr_ar = array() ) {
        $str = "<label for=\"$forID\""; 
        if ($attr_ar) { 
            $str .= $this->addAttributes( $attr_ar ); 
        } 
        $str .= ">$text</label>"; 
        return $str; 
    }
    // from parallel arrays for option values and text 
    public function addSelectListArrays($name, $val_list, $txt_list, $selected_value = NULL, $header = NULL, $attr_ar = array() ) {
        $option_list = array_combine( $val_list, $txt_list ); 
        $str = $this->addSelectList($name, $option_list, true, $selected_value, $header, $attr_ar );
        return $str; 
    }
    // option values and text come from one array (can be assoc) 
    // $bVal false if text serves as value (no value attr) 
    public function addSelectList($name, $option_list, $bVal = true, $selected_value = NULL, $header = NULL, $attr_ar = array() ) {
        $str = "<select name=\"$name\""; 
        if ($attr_ar) { 
            $str .= $this->addAttributes( $attr_ar ); 
        } 
        $str .= ">\n"; 
        if ( isset($header) ) { 
            $str .= "  <option value=\"\">$header</option>\n"; 
        } 
        foreach ( $option_list as $val => $text ) { 
            $str .= $bVal? "  <option value=\"$val\"": "  <option"; 
            if ( isset($selected_value) && ( $selected_value === $val || $selected_value === $text) ) {
                $str .= ' selected="selected"'; 
            } 
            $str .= ">$text</option>\n"; 
        } 
        $str .= "</select>"; 
        return $str; 
    } 


//由array輸出各種選單
//input:數值的array,select name
function addSelectboxFromArray($array,$name,$default=''){
	if(is_array($array)){
		$return= "<select name='".$name."' size='1'>";
		foreach($array as $a){
			$return.= "<option value='".$a."'";
			if($a==$default) 
			$return.=" selected";
			$return.=">".$a."</option>";
		}
		$return.= "</select>";
		return $return;
	}else{
		return false;
	}
}
//由sql輸出各種選單
//輸入：選單的sql SQL語法id_field及list_field 及 name以便post到目標程式
//若有預設值則放到最後
function addSelectboxFromSql($sql,$id_field,$list_field,$name,$default=''){
	if($sql){
		$conne=new opmysql();
		$return= "<select name='{$name}' size=1>";
		//空白option
			$return.="<option value='' ></option>";
		while($row=$conne->getRow($sql)){
			$return.= "<option value=".$row[$id_field];
			
			$return.=($row[$id_field]==$default)?" selected=selected":"";
			
			$return.=">".$row[$list_field]."</option>";
		}
		$return.= "</select>";
		return $return;
	}else{
		return false;
	}
}
//由逗號分隔字串輸入
//input:字串,select name
function addSelectboxFromText($text,$name,$default=''){
	$array=explode(',',$text);
	if(is_array($array)){
		$return= "<select name='".$name."' size='1'>";
		foreach($array as $a){
			$return.= "<option value='".$a."'";
			if($a==$default) 
			$return.=" selected";
			$return.=">".$a."</option>";
		}
		$return.= "</select>";
		return $return;
	}else{
		return false;
	}
}
//由array輸出各種checkbox
//input:數值的array,radio name

function addRadioFromArray($array,$name,$default='',$attr_ar = array()){
	$return='';
	$attr=($attr_ar)? $this->addAttributes( $attr_ar ):''; 
	if(is_array($array)){
		foreach($array as $a=>$b){
			$default_selected=($a==$default)?" checked":"";
			$return.= "<input type='radio' name='".$name."' value='".$a."' ".$default_selected.' '.$attr.">".$b;
		}
		return $return;
	}else{
		return false;
	}
}
function addRadioFromText($text,$name,$default='',$attr_ar = array()){
	$return='';
	$attr=($attr_ar)? $this->addAttributes( $attr_ar ):''; 

	$array=explode(',',$text);
	if(is_array($array)){
		foreach($array as $a){
			$default_selected=($a==$default)?" checked=checked":"";
			$return.= "<input type='radio' name='".$name."' value='".$a."' ".$default_selected.' '.$attr.">".$a;
		}
		return $return;
	}else{
		return false;
	}
}
//輸出各種checkbox
function addCheckboxFromArray($array,$name,$default='',$attr_ar = array()){
	$return='';
	$attr=($attr_ar)? $this->addAttributes( $attr_ar ):''; 
	if(is_array($array)){
		foreach($array as $a=>$b){
			$default_selected=($a==$default)?" checked":"";
			$return.= "<input type='checkbox' name='".$name."[]' value='".$a."' ".$default_selected.' '.$attr.">".$b;
		}
		return $return;
	}else{
		return false;
	}
}

//輸出各種checkbox由文字$default也是切成array
function addCheckboxFromText($text,$name,$default='',$attr_ar = array()){
	$return='';
	$attr=($attr_ar)? $this->addAttributes( $attr_ar ):''; 
	$array=explode(',',$text);
	$default=explode(',',$default);
	//將$default轉換成array
	if(is_array($default)){
		foreach($default as $b){
			$default2[$b]=$b;		
		}
	}
	if(is_array($array)){
		foreach($array as $a){
			$default_selected=($a==@$default2[$a])?" checked":"";
			$return.= "<input type='checkbox' id='".$name."' name='".$name."[]' value='".$a."' ".$default_selected.' '.$attr.">".$a."</input>";
		}
		return $return;
	}else{
		return false;
	}
}

//addHidden alian
function input_hidden($name,$value){
return $this->addHidden($name,$value);
}
//由array輸入各種隱藏參數
//input:數值的array(key->value)
function addHidden($name,$value){
	if($name && $value){
			$return= "<input type='hidden' name='".$name."' value='".$value."' >";
		return $return;
	}else{
		return false;
	}
}
//html5 輸入數字
function addInputNumber($name,$min,$max,$default=''){
	$default_str=($default<>'')?" value='{$default}' ":"";
	$return="<input style='height:30px' type='number' name='{$name}' min='{$min}' max='{$max}' {$default_str} >";
	return $return;
}
//html5 輸入日期
//加上若無支援html5之date使用jqueryUIdatePicker
function addInputDate($name,$default='',$attr_ar=array()){
	$return='';
	$return.=get_jquery(true);
    $attr_add=($attr_ar)?$this->addAttributes($attr_ar):'';
	// $return.=$this->addJava('',YAOH_SL_PATH."/js/ui/jquery.ui.core.js");
	// $return.=$this->addJava('',YAOH_SL_PATH."/js/ui/jquery.ui.widget.js");
	// $return.=$this->addJava('',YAOH_SL_PATH."/js/ui/jquery.ui.datepicker.js");
	// $return.=$this->addJava('',YAOH_SL_PATH."/js/ui/jquery.ui.datepicker-zh-TW.js");
	$default_str=($default<>'')?" value='{$default}' ":" value='".date('Y-m-d', strtotime(date('Y/m/d')))."'";
	//$return.=$this->addCss("",YAOH_SL_PATH."/css/base/jquery.ui.all.css");
	$return.=$this->addJquery("
	$('.datepicker').click().datepicker({numberOfMonths: 3,showButtonPanel: true});
	$('.datepicker').datepicker('option',$.datepicker.regional['zh-TW']);
	$('.datepicker').datepicker('option', 'dateFormat','yy-mm-dd');
	").$this->addCss(".datepicker{background:pink;}.ui-datepicker{background:pink;display:none;}")."<input type='text' id='{$name}' class='datepicker' name='{$name}' {$default_str} {$attr_add}>";
	return $return;
}
//html5 輸入時間
//加上若無支援html5之time使用jquery.clockpick.1.2.9
function addInputTime($name,$default='',$attr_ar=array()){
	$return='';
    $attr_add=($attr_ar)?$this->addAttributes($attr_ar):'';
	$return.=$this->addJava('',YAOH_SL_PATH."/js/ui/jquery.clockpick.1.2.9.js");
	//$default_str=($default<>'')?" value='{$default}' ":" value='".date('Y-m-d', strtotime(date('Y/m/d')))."'";
	$return.=$this->addCss("",YAOH_SL_PATH."/css/base/jquery.clockpick.1.2.9.css");
	$return.=$this->addJava("
	var obj={
	starthour:0,
	endhour:23,
	showminutes:true,
	minutedivisions:12,
	military:true,
	event:'click',//focus click mouseover
	layout:'horizontal',//vertical horizontal
	valuefield:null,
	hoursopacity:0.8,
	minutesopacity:0.8
	};
	$(document).ready(function(){
	$('.clockpick').clockpick(obj,clockcallback);
});

function clockcallback(){};
	");
	$return.=$this->addCss(".clockpick{background:lightYellow;}")."<input type='time' id='{$name}' class='clockpick' name='{$name}' {$default_str} {$attr_add}>";
	return $return;
}
//html5 datetime
// function addInputDateTime($name,$default){
	// $default_str=($default<>'')?" value='{$default}' ":" value='".date('Y-m-d', strtotime(date('Y/m/d')))."'";
	// $return="<input type='datetime' name='{$name}' {$default_str} >";
	// return $return;
// }
//輸出提交
function addSubmit($value='送出'){
	return "<input type='submit' name='submit' id='submit' value='{$value}'>";

}
//輸出重設
function addRest($value){
	return "<input type='reset' name='{$value}'>";
}

//檔案上傳
public function addFile($name, $value, $attr_ar = array() ) {
        $str = "<input type='file' name=\"$name\" value=\"$value\"";
        if ($attr_ar) { 
            $str .= $this->addAttributes( $attr_ar ); 
        } 
        $str .= ' />'; 
        return $str; 
    }

//萬用新增格式(無預設值設定)
//目前支援類型有 input,textarea,selectbox,checkbox,radio,date,time,number
var $typestr="input,textarea,selectbox,checkbox,radio,date,time,number,file";
function add($item_type,$item_id,$item_c=null,$default=null,$attr_ar=array()){
	$add='';
	global $fill_count;
		switch($item_type){
			case 'input':
				return $this->addInput('text', $item_id, $item_c, $attr_ar = array('name'=>$item_id));
			break;
			case 'textarea':
				$add.=$this->addJava("   
						$(document).ready(function($) {  
						$('textarea').css('overflow','hidden').bind('keydown keyup', function(){  
							$(this).height('0px').height($(this).prop('scrollHeight')+'px');  
						}).keydown();
						});");
				return $add.$this->addTextarea($item_id, 2,100, $item_c, $attr_ar = array('name'=>$item_id));
			break;
			case 'selectbox':
				return $this->addSelectboxFromText($item_c, $item_id, $default, $attr_ar = array('name'=>$item_id));
			break;
			case 'checkbox':
				return $this->addCheckboxFromText($item_c, $item_id, $default, $attr_ar = array('name'=>$item_id));
			break;
			case 'radio':
				return $this->addRadioFromText($item_c, $item_id, $default, $attr_ar = array('name'=>$item_id));
			break;
			case 'date':
				return $this->addInputDate($item_id, $item_c,  $attr_ar = array('name'=>$item_id));
			break;
			case 'time':
				return $this->addInputTime($item_id, $item_c,  $attr_ar = array('name'=>$item_id));
			break;
			case 'number':
				$MinMax=explode(',',$item_c);
				return $this->addInputNumber($item_id, @$MinMax[0], @$MinMax[1],$default);
			break;
			case 'file':
				return $this->addFile($item_id,$item_c,array('name'=>$item_id));
			break;
			// case 'datetime':
				// //return $this->addInputDateTime($item_id, $item_c,  $attr_ar = array('name'=>$item_id));
				// return useJqueryUI().$this->addJquery("$('.datepicker').datepicker();").$this->addInput('text', $item_id, $item_c, $attr_ar = array('name'=>$item_id,'class'=>'datepicker'));
// testing;
			// break;
			default:
				return false;
			break;		
		}
		}
}
?>
