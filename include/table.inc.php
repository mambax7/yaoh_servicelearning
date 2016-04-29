<?php
class opmytable{
	private $table_str='';
	
	public function __construct($class_name='table',$style=''){
		$style_txt=($style<>'')?" style='{$style}' ":"";
		$this->table_str.="<table class='{$class_name}'{$style_txt}>";
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
	//colspan讀取欄位來決定
	//type:array
	//總數要等於最大欄位數
	//例如：共有4欄則可設定為array(1,1,1,1);array(2,2);array(3,1);array(1,3)等等
	public function addTitle($array=array(),$style='',$colspan=array(),$attr_ar = array()){
		$attr_txt = ($attr_ar)?$this->addAttributes($attr_ar):'';
		$return='';
		$style_txt=($style<>'')?" style='{$style}' ":"";
		if(count($array)>0){
			$return.="<tr{$style_txt} {$attr_txt}>";
			$i=0;
			foreach($array as $a){
				if(isset($colspan[$i]) and $colspan[$i]>1){
					$return.="<th colspan={$colspan[$i]}>{$a}</th>";
				}else{
					$return.="<th>$a</th>";
				}
				$i++;
			}
			$return.="</tr>";
		}
		$this->table_str.=$return;
	}
	public function addRow($array=array(),$style='',$colspan=array(),$attr_ar = array()){
		$attr_txt = ($attr_ar)?$this->addAttributes($attr_ar):'';
		$return='';
		$style_txt=($style<>'')?" style='{$style}' ":"";
		if(count($array)>0){
			$return.="<tr{$style_txt} {$attr_txt}>";
			$i=0;
			foreach($array as $a){
				if(isset($colspan[$i]) and $colspan[$i]>1){
					$return.="<td colspan={$colspan[$i]}>{$a}</td>";
				}else{
					$return.="<td>$a</td>";
				}
				$i++;
			}
			$return.="</tr>";
		}
		$this->table_str.=$return;
	}
	public function render() { 
			$this->table_str.="</table>"; 
			return $this->table_str;
		}
}
?>