<?php
namespace filters\decorators;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VApparatuses extends VDecorator {
	public function __construct(){
		parent::__construct('apparatuses');
		parent::setDefaultField('name','');
		parent::setDefaultField('type','');
		parent::setDefaultField('country','');
	}

	public function getParams(){
		return parent::getParams();
	}

	public function getParam($sKey){
		return parent::getParam($sKey);
	}
}