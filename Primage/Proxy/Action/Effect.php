<?php 

/**
 *
 * @see https://github.com/barbushin/dabase
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
class Primage_Proxy_Action_Effect extends Primage_Proxy_Action_Abstract {
	
	protected $effectName;
	
	public function __construct($effectName) {
		$this->effectName = $effectName;
	}
	
	public function make(Primage $image) {
		$image->addEffect($this->effectName);
	} 
}