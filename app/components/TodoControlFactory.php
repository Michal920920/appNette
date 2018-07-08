<?php
/**
 * Created by PhpStorm.
 * User: staniik
 * Date: 02.07.18
 * Time: 18:14
 */

namespace App\Components;

use Nette\DI\Container;

class TodoControlFactory {

	private $container;

	function __construct(Container $container) {
		$this->container = $container;
	}

	/**
	 * @return TodoControl
	 */
	public function create() {
		return $this->container->getService("todoControl");
	}
}