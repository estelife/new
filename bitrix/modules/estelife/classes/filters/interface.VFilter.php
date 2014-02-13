<?php
namespace filters;

/**
 *
 * @author Maxim Shlemarev <shlemarev@gmail.com>
 * @since 30.01.14
 */

interface VFilter {
	public function getParam($nKey);
	public  function getParams();
}