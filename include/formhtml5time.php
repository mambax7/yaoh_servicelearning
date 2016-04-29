<?php
defined('XOOPS_ROOT_PATH') or die('Restricted access');
xoops_load('XoopsFormElement');
class XoopsFormHtml5Time extends XoopsFormElement
{
    var $_size;
    var $_maxlength;
    var $_value;
    function XoopsFormHtml5Time($caption, $name, $value = '')
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
        return "<input type='time' name='" . $this->getName() . "' title='" . $this->getTitle() . "' id='" . $this->getName() . "' value='" . $this->getValue() . "'" . $this->getExtra() . " />";
    }
}

?>
