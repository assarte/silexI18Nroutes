<?php
/**
 * Created by PhpStorm.
 * User: assarte
 * Date: 2015.08.13.
 * Time: 17:51
 */

namespace Sir;


class RouteNotExistsException extends \Exception {
	protected $for;

	public function __construct($for) {
		$this->for = $for;
		parent::__construct('Route not exists for '.$for.'.');
	}

	public function getFor() {
		return $this->for;
	}
}