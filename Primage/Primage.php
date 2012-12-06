<?php

/**
 *
 * @see https://github.com/barbushin/primage
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 *
 */
class Primage {

	protected $image;
	protected $wdith;
	protected $height;
	protected static $supportedTypesCodes = array('jpeg' => IMG_JPG, 'png' => IMG_PNG, 'gif' => IMG_GIF);

	public function __construct($image) {
		$this->initImage($image);
	}

	protected function initImage($image) {
		if(get_resource_type($image) != 'gd') {
			throw new Exception('Argument "image" must be type of GD resource');
		}
		$this->image = $image;
		$this->width = imagesx($image);
		$this->height = imagesy($image);
	}

	public static function getTypeName($type, $throwException = false) {
		if($type == 'jpg') {
			$type = 'jpeg';
		}
		if(isset(self::$supportedTypesCodes[$type])) {
			return $type;
		}
		elseif($throwException) {
			throw new Exception('Unkown or unsupported type "' . $type . '"');
		}
	}

	public static function buildFromFile($filepath) {
		$type = self::getImageTypeByFilename($filepath);
		if(!$type) {
			$type = self::getImageTypeByFilepath($filepath);
		}
		$image = null;
		if($type) {
			$image = call_user_func('imagecreatefrom' . $type, $filepath);
		}

		if(!$type || !$image) {
			throw new Exception('Unkown format of image "' . $filepath . '"');
		}

		$class = __CLASS__;
		return new $class($image);
	}

	public static function getImageTypeByFilename($filename) {
		return self::getTypeName(pathinfo($filename, PATHINFO_EXTENSION));
	}

	public static function getImageTypeByFilepath($filepath) {
		$info = getimagesize($filepath);
		if($info) {
			list($width, $height, $typeCode) = $info;
			return array_search($typeCode, self::$supportedTypesCodes);
		}
	}

	public function resize($maxWidth = null, $maxHeight = null, $onlyBigger = true) {
		if($onlyBigger && (!$maxWidth || $this->width < $maxWidth) && (!$maxHeight || $this->height < $maxHeight)) {
			return $this;
		}

		$ratio = $this->height / $this->width;

		if(!$maxWidth || ($maxHeight && ($maxHeight / $ratio) < $maxWidth)) {
			$dstHeight = $maxHeight;
			$dstWidth = round($maxHeight / $ratio);
		}
		else {
			$dstWidth = $maxWidth;
			$dstHeight = round($maxWidth * $ratio);
		}

		$resizedImage = imagecreatetruecolor($dstWidth, $dstHeight);
		imagealphablending($resizedImage, false);
		imagefill($resizedImage, 0, 0, imagecolorallocatealpha($resizedImage, 0, 0, 0, 127));
		imagesavealpha($resizedImage, true);

		if(!imagecopyresampled($resizedImage, $this->image, 0, 0, 0, 0, $dstWidth, $dstHeight, $this->width, $this->height)) {
			throw new Exception('Resizing failed');
		}

		$this->updateImage($resizedImage);
		return $this;
	}

	public function addWatermark(Primage $waterImage, $x = 'center', $y = 'center', $transparencyPercents = 0) {
		if($x == 'center') {
			$x = $this->width / 2 - $waterImage->width / 2;
		}
		else {
			$x = $x >= 0 ? $x : ($this->width + $x - $waterImage->width);
		}
		if($y == 'center') {
			$y = $this->height / 2 - $waterImage->height / 2;
		}
		else {
			$y = $y >= 0 ? $y : ($this->height + $y - $waterImage->height);
		}

		if($transparencyPercents) {
			$cut = imagecreatetruecolor($waterImage->width, $waterImage->height);
			imagecopy($cut, $this->image, 0, 0, $x, $y, $waterImage->width, $waterImage->height);
			imagecopy($cut, $waterImage->image, 0, 0, 0, 0, $waterImage->width, $waterImage->height);
			imagecopymerge($this->image, $cut, $x, $y, 0, 0, $waterImage->width, $waterImage->height, 100 - $transparencyPercents);
		}
		else {
			imagelayereffect($this->image, IMG_EFFECT_ALPHABLEND);
			imagecopy($this->image, $waterImage->image, $x, $y, 0, 0, $waterImage->width, $waterImage->height);
		}

		return $this;
	}

	/**
	 * @param string $effect (blur, edge, sharpena, sharpenb, emboss, light)
	 * @return Primage
	 */
	public function addEffect($effect) {
		$effects = array('blur' => array(array(1 / 9, 1 / 9, 1 / 9), array(1 / 9, 1 / 9, 1 / 9), array(1 / 9, 1 / 9, 1 / 9)), 'edge' => array(array(0, -1, 0), array(-1, 4, -1), array(0, -1, 0)), 'sharpena' => array(array(0, -1, 0), array(-1, 5, -1), array(0, -1, 0)), 'sharpenb' => array(array(-1, -1, -1), array(-1, 16, -1), array(-1, -1, -1)), 'emboss' => array(array(2, 0, 0), array(0, -1, 0), array(0, 0, -1)), 'light' => array(array(0, 0, 1), array(0, 1, 0), array(1, 0, 0)));

		if(!isset($effects[$effect])) {
			throw new Exception('Unkown effect "' . $effect . '"');
		}
		imageconvolution($this->image, $effects[$effect], 1, 0);
		return $this;
	}

	public function rotate($degrees) {
		$rotatedImage = imagerotate($this->image, $degrees, -1);
		$this->updateImage($rotatedImage);
		return $this;
	}

	protected function handleImage($type, $filepath = null, $quality = 100, $pngFilters = PNG_NO_FILTER) {
		if($type == 'png') {
			$quality = round(9 / 100 * $quality);
		}
		return call_user_func_array('image' . $type, array($this->image, $filepath, $quality, $pngFilters));
	}

	public function saveToFile($filepath, $quality = 100, $pngFilters = PNG_NO_FILTER) {
		if(!$this->handleImage(self::getImageTypeByFilename($filepath), $filepath, $quality, $pngFilters)) {
			throw new Exception('Saving image to file "' . $filepath . '" failed');
		}
		return $this;
	}

	public function sendToStdout($type = 'jpeg', $quality = 100, $pngFilters = PNG_NO_FILTER) {
		$type = $this->getTypeName($type, true);
		header('Content-type: ' . image_type_to_mime_type(self::$supportedTypesCodes[$type]));
		if(!$this->handleImage($type, null, $quality, $pngFilters)) {
			throw new Exception('Sending image to STDOUT failed');
		}
		return $this;
	}

	public function getCopy() {
		$copyImage = imagecreatetruecolor($this->width, $this->height);
		imagealphablending($copyImage, false);
		imagefill($copyImage, 0, 0, imagecolorallocatealpha($copyImage, 0, 0, 0, 127));
		imagesavealpha($copyImage, true);
		imagecopy($copyImage, $this->image, 0, 0, 0, 0, $this->width, $this->height);

		$class = get_class($this);
		return new $class($copyImage);
	}

	protected function updateImage($image) {
		$this->destroy();
		$this->initImage($image);
	}

	public function destroy() {
		if($this->image) {
			imagedestroy($this->image);
		}
	}

	public function __clone() {
		return $this->getCopy();
	}

	public function __get($var) {
		return $this->$var;
	}

	public function __destruct() {
		$this->destroy();
	}
}
