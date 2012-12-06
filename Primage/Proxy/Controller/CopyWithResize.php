<?php

/**
 *
 * @see https://github.com/barbushin/dabase
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
class Primage_Proxy_Controller_CopyWithResize extends Primage_Proxy_Controller_Abstract {
	
	/**
	 * @var Primage_Proxy_Storage
	 */
	protected $srcStorage;
	
	/**
	 * @var Primage_Proxy_Storage
	 */
	protected $dstStorage;
	
	protected $maxWidth;
	protected $maxHeight;
	protected $step;

	public function __construct(Primage_Proxy_Storage $srcStorage, Primage_Proxy_Storage $dstStorage, $maxWidth = null, $maxHeight = null, $step = null) {
		$this->srcStorage = $srcStorage;
		$this->dstStorage = $dstStorage;
		$this->maxWidth = $maxWidth;
		$this->maxHeight = $maxHeight;
		$this->step = $step;
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
		$width = empty($params['width']) ? null : abs($params['width']);
		$height = empty($params['height']) ? null : abs($params['height']);
		
		if(!$width && !$height) {
			throw new Primage_Proxy_Controller_RequestException('Wrong request. Arguments "width" or "height" must be not empty');
		}
		if($this->maxWidth && $width && $width > $this->maxWidth) {
			throw new Primage_Proxy_Controller_RequestException('Argument "width" cannot be > ' . $this->maxWidth);
		}
		if($this->maxHeight && $height && $height > $this->maxHeight) {
			throw new Primage_Proxy_Controller_RequestException('Argument "height" cannot be > ' . $this->maxHeight);
		}
		if($this->step) {
			if($height && $height % $this->step) {
				throw new Primage_Proxy_Controller_RequestException('Argument "height" is not multiple of ' . $this->step);
			}
			if($width && $width % $this->step) {
				throw new Primage_Proxy_Controller_RequestException('Argument "width" is not multiple of ' . $this->step);
			}
		}
		
		$image->resize($width, $height);
		$this->dstStorage->storeImage($image, basename(urldecode($_SERVER['REQUEST_URI'])));
	}
}