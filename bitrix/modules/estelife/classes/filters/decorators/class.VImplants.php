<?php
namespace filters\decorators;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VImplants extends VDecorator {
	public function __construct(){

		parent::__construct('implants', array('ptype'));
		parent::setDefaultField('name','');
		parent::setDefaultField('company_name','');
		parent::setDefaultField('type','');
		parent::setDefaultField('country','');
		parent::setDefaultField('ptype','3');
	}

	public function getParams(){
		return parent::getParams();
	}

	public function getParam($sKey){
		return parent::getParam($sKey);
	}
}