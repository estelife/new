<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */


interface VBase{

	public function __construct($sType);

	public function setParam($nKey,$sParam);

	public function getParam($nKey);

	public function unsetParam($nKey);

	public  function getAllParams();

}