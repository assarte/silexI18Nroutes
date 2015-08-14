<?php
/**
 * Created by PhpStorm.
 * User: assarte
 * Date: 2015.08.14.
 * Time: 1:45
 */

namespace Sir;


class CallbackLoader implements LoaderInterface {
	protected $callback;

	public function __construct($callback) {
		if (!is_callable($callback)) throw new \InvalidArgumentException('Argument 1 must be callable, '.gettype($callback).' given.');

		$this->callback = $callback;
	}

	public function load($language) {
		if (is_object($this->callback) or is_string($this->callback)) {
			$cb = $this->callback;
			return $cb($language);
		} else {
			return call_user_func($this->callback, $language);
		}
	}
}