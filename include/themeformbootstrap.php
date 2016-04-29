<?php

defined('XOOPS_ROOT_PATH') or die('Restricted access');
xoops_load('XoopsForm');
xoops_load('XoopsThemeForm');
class XoopsThemeFormBootstrap extends XoopsThemeForm
{
	private $insertbreakcheck=false;
    function render()
    {
        $ele_name = $this->getName();
        $ret = '<form name="' . $ele_name . '" id="' . $ele_name . '" action="' . $this->getAction() . '" method="' . $this->getMethod() . '" onsubmit="return xoopsFormValidate_' . $ele_name . '();"' . $this->getExtra() . '>
            <table class="table table-bordered table-striped">
            <tr><th colspan="2"><center><h3>' . $this->getTitle() . '</h3></center></th></tr>
        ';
        $hidden = '';
        foreach ($this->getElements() as $ele) {

            if (!is_object($ele)) {
                $ret .= $ele;
            } else if (!$ele->isHidden()) {
						$ret .= '<tr>';
					$ret .= '<td>';
                    if (($caption = $ele->getCaption()) != '') {
                        $ret .= '<div class="xoops-form-element-caption' . ($ele->isRequired() ? '-required' : '') . '">';
                        $ret .= '<span class="caption-text">' . $caption . '</span>';
                        $ret .= '<span class="caption-marker">*</span>';
                        $ret .= '</div>';
                    }
                    if (($desc = $ele->getDescription()) != '') {
                        $ret .= '<div class="xoops-form-element-help">' . $desc . '</div>';
                    }
                    $ret .= '</td><td>' . $ele->render() . '</td>';
					$ret .='</tr>';
            } else {
                $hidden .= $ele->render();
            }
        }
        $ret .= '</table>' . $hidden . '</form>';
        $ret .= $this->renderValidationJS(true);
        return $ret;
    }
	    function insertBreak($title = '', $class = '')
    {
		$insertbreakcheck=true;
        $class = ($class != '') ? " class='" . preg_replace('/[^A-Za-z0-9\s\s_-]/i', '', $class) . "'" : '';
        // Fix for $extra tag not showing
        if ($title) {
            $extra = '<tr><th colspan="4" ' . $class . '><center>' . $title . '</center></th></tr>';
            $this->addElement($extra);
        } else {
            $extra = '<tr><th colspan="4" ' . $class . '>&nbsp;</th></tr>';
            $this->addElement($extra);
        }
    }
}

?>