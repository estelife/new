<?php
namespace filters\decorators;

/**
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

class VTrainings extends VDecorator {
	public function __construct(){

		$nSity = (!empty($_GET['city']) && $_GET['city'] !='all') ?
			intval($_GET['city']) :
			intval($_COOKIE['estelife_city']);

		if($nSity ==0)
			$nSity = 'all';

		$nDate = date('d.m.y',time());

		parent::__construct('trainings');
		parent::setDefaultField('city',$nSity);
		parent::setDefaultField('direction','');
		parent::setDefaultField('spec','');
		parent::setDefaultField('date_from',$nDate);
		parent::setDefaultField('date_to','');
	}

	public function getParams(){
		return parent::getParams();
	}

	public function getParam($sKey){
		return parent::getParam($sKey);
	}
}