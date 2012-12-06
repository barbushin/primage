<?php

/**
 *
 * @see https://github.com/barbushin/primage
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
abstract class Primage_Proxy_Controller_Abstract extends Primage_Proxy_Handler {

	abstract protected function getImageByParams(array $params);

	/**
	 * @param array $params
	 * @param bool $sendResultToStdout
	 * @return Primage
	 */
	public function dispatch(array $params, $sendResultToStdout = true) {
		$image = $this->getImageByParams($params);
		if(!$image) {
			throw new Primage_Proxy_Storage_SourceNotFound();
		}
		
		$this->makeActionsOnImage($image);
		$this->postDispatch($image, $params);
		
		if($sendResultToStdout) {
			$image->sendToStdout($this->dstStorage->imageType, $this->dstStorage->imageQuality);
		}
		
		return $image;
	}

	/**
	 * @param Primage $image
	 * @param array $params
	 */
	protected function postDispatch(Primage $image, array $params) {
	}
}

class Primage_Proxy_Controller_RequestException extends Exception {
}