<?php
namespace filters\decorators;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VPromotions extends VDecorator {
	public function __construct(){

		$nSity = (!empty($_GET['city']) && $_GET['city'] !='all') ?
			intval($_GET['city']) :
			intval($_COOKIE['estelife_city']);

		if($nSity ==0)
			$nSity = 'all';

		parent::__construct('promotions');
		parent::setDefaultField('name','');
		parent::setDefaultField('city',$nSity);
		parent::setDefaultField('metro','');
		parent::setDefaultField('spec','');
		parent::setDefaultField('service','');
		parent::setDefaultField('method','');
		parent::setDefaultField('concreate','');
	}

	public function getParams(){
		return parent::getParams();
	}

	public function getParam($sKey){
		return parent::getParam($sKey);
	}
}