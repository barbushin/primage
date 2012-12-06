<?php

/**
 *
 * @see http://code.google.com/p/dabase
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 *
 */
class Primage_Proxy_Handler {
	
	protected $actions = array();

	public function addAction(Primage_Proxy_Action_Abstract $action) {
		$this->actions[] = $action;
	}

	public function makeActionsOnImage(Primage $image) {
		foreach($this->actions as $action) {
			$action->make($image);
		}
	}
}