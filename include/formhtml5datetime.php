<?php
defined('XOOPS_ROOT_PATH') or die('Restricted access');
xoops_load('XoopsFormElement');
class XoopsFormHtml5DateTime extends XoopsFormElement
{
    var $_size;
    var $_maxlength;
    var $_value;
    function XoopsFormHtml5DateTime($caption, $name, $value = '')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->setValue($value);
    }
	function setValue($value)
    {
        $this->_value = $value;
    }
    function getValue($encode = false)
    {
        return $encode ? htmlspecialchars($this->_value, ENT_QUOTES) : $this->_value;
    }
    function render()
    {
		$datetime=$this->_value;
		if($datetime<>null){
			$date=date('Y-m-d', strtotime($datetime));
			$time=date('H:m', strtotime($datetime)).":00";
		}else{
			$date=date('Y-m-d');
			$time=date('H:m').":00";
		}
        $return = "<input type='date' name='" . $this->getName() . "[date]' title='" . $this->getTitle() . "' id='" . $this->getName() . "[date]' value='" . $date . "'" . $this->getExtra() . " />";
		$return.=  "<input type='time' name='" . $this->getName() . "[time]' title='" . $this->getTitle() . "' id='" . $this->getName() . "[time]' value='" . $time . "'" . $this->getExtra() . " />";
		return $return;
	}
}

?>
