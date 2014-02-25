<?php
namespace filters\decorators;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VProfessionals extends VDecorator {
	public function __construct(){
		parent::__construct('professionals');
		parent::setDefaultField('country','');
		parent::setDefaultField('name','');
	}

	public function getParams(){
		return parent::getParams();
	}

	public function getParam($sKey){
		return parent::getParam($sKey);
	}
}