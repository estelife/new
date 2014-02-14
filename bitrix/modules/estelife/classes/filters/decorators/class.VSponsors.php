<?php
namespace filters\decorators;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VSponsors extends VDecorator {
	public function __construct(){

		$nSity = (!empty($_GET['city']) && $_GET['city'] !='all') ?
			intval($_GET['city']) :
			intval($_COOKIE['estelife_city']);

		if($nSity ==0)
			$nSity = 'all';

		parent::__construct('sponsors');
		parent::setDefaultField('name','');
		parent::setDefaultField('country','');
		parent::setDefaultField('city',$nSity);
	}

	public function getParams(){
		return parent::getParams();
	}

	public function getParam($sKey){
		return parent::getParam($sKey);
	}
}