<?php

/**
 *
 * @see https://github.com/barbushin/primage
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
class Primage_Proxy_Action_Watermark extends Primage_Proxy_Action_Abstract {
	
	protected $watermarkFilepath;
	protected $x;
	protected $y;
	protected $transparentPercents;

	public function __construct($watermarkFilepath, $x = null, $y = null, $transparentPercents = 0) {
		$this->watermarkFilepath = $watermarkFilepath;
		$this->x = $x;
		$this->y = $y;
		$this->transparentPercents = $transparentPercents;
	}

	protected function getWatermarkImage() {
		static $watermarkImage;
		if(!$watermarkImage) {
			$watermarkImage = Primage::buildFromFile($this->watermarkFilepath);
		}
		return $watermarkImage;
	}

	public function make(Primage $image) {
		$image->addWatermark($this->getWatermarkImage(), $this->x, $this->y, $this->transparentPercents);
	}
}