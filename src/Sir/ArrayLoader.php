<?php
/**
 * Created by PhpStorm.
 * User: assarte
 * Date: 2015.08.14.
 * Time: 1:41
 */

namespace Sir;


class ArrayLoader implements LoaderInterface {
	protected $storage = array();

	public function __construct(array $storage) {
		$this->storage = $storage;
	}

	public function load($language) {
		if (!isset($this->storage[$language])) throw new \RuntimeException('Routes not defined for language: '.$language);

		return $this->storage[$language];
	}
}