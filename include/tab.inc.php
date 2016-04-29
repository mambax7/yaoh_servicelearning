<?php
/*
Auth:yaoh@dcjh.tn.edu.tw
Date:2013/10/9
Function:quickly build a bootstrap based tab
public function:
addNav:add a nav
setContent:after add a nav ,we can set the content of the nav
render: output the tab string

*/
class opmytab{
	private $bootstrap_str;
	private $nav='';
	private $content='';
	private $tab_id='';
	public function __construct(){
		$this->tab_id='tab'.time();
		$this->bootstrap_str=sl_jsloader::useJquery().sl_jsloader::useBootstrap().sl_jsloader::addCss(".nopoint{list-style-type:none;}").sl_jsloader::addJquery("
		  $(function () {
		  $('#myTab{$this->tab_id} li').addClass('nopoint');
		  $('#myTab{$this->tab_id} a').click(function (e) {
			  e.preventDefault();
			  $(this).tab('show');
			})
			$('#myTab{$this->tab_id} a:first').tab('show');
		  })
	");

	}
	public function addNav($tab_id='',$tab_name='',$active=false){
		if($tab_id=='')$tab_id='無標籤';
		if($tab_name=='')$tab_name='無標籤';
		$this->nav.="<li".(($active)?" class='active'":"")."><a href='#{$tab_id}' data-toggle='tab'>{$tab_name}</a></li>";
	}
	public function setContent($tab_id='',$tab_content='',$active=false){
		if($tab_id=='')$tab_id='無標籤';
		$this->content.="<div class='tab-pane".(($active)?" active":"")."' id='{$tab_id}'>";
		$this->content.=$tab_content;
		$this->content.="</div>";
	}
	public function render(){
		$return=$this->bootstrap_str;
		$return.="<div class='tabbable'><ul class='nav nav-tabs' id ='".$this->tab_id."'>";
		$return.=$this->nav;
		$return.="</ul>
		<div class='tab-content'>";
		$return.=$this->content;
		$return.="</div>
					</div>";
		return $return;
	}
}
?>