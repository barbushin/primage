<?php

/**
 *
 * @see https://github.com/barbushin/dabase
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
class Primage_Proxy_Controller_CopyById extends Primage_Proxy_Controller_Abstract {
	
	/**
	 * @var Primage_Proxy_Storage
	 */
	protected $srcStorage;
	
	/**
	 * @var Primage_Proxy_Storage
	 */
	protected $dstStorage;

	public function __construct(Primage_Proxy_Storage $srcStorage, Primage_Proxy_Storage $dstStorage) {
		$this->srcStorage = $srcStorage;
		$this->dstStorage = $dstStorage;
	}

	protected function getImageByParams(array $params) {
		if(empty($params['id'])) {
			throw new Exception('Parameter "id" is required');
		}
		return $this->srcStorage->getImage($params['id']);
	}

	/**
	 * @param Primage $image
	 * @param array $params
	 */
	protected function postDispatch(Primage $image, array $params) {
		if(!preg_match('!('.preg_quote($params['id'], '!').'.*)$!', urldecode($_SERVER['REQUEST_URI']), $m)) {
			throw new Primage_Proxy_Storage_SourceNotFound();
		}
		$this->dstStorage->storeImage($image, $m[1]);
	}
}