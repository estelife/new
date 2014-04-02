<?php
namespace filters\decorators;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VPreparationsMakers extends VDecorator {
	public function __construct(){
		parent::__construct('preparations_makers');
		parent::setDefaultField('name', '');
		parent::setDefaultField('country', '');
	}

	public function getParams(){
		return parent::getParams();
	}

	public function getParam($sKey){
		return parent::getParam($sKey);
	}
}