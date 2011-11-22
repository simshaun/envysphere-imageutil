<?php
class EnvImage {

	/**
	 * @var EnvImage_Driver_Interface
	 */
	protected $_driver;

	/**
	 * Returns a new EnvImage object.
	 *
	 *    $env_image = EnvImage::factory($driver);
	 *
	 * @static
	 * @param   object  $driver
	 * @return  EnvImage_Abstract_Driver
	 */
	public static function factory($driver)
	{
		return new EnvImage($driver);
	}

	/**
	 * Sets the driver. The EnvImage utility should usually only be instantiated
	 * using EnvImage::factory($driver).
	 *
	 *    $env_image = new EnvImage($driver)
	 *
	 * @param  object  $driver
	 */
	public function __construct($driver)
	{
		// $driver = __CLASS__ .'_'. ucfirst( strtolower($driver) );

		$this->_driver = $driver;
	}

	/**
	 * Passes method calls to the loaded driver.
	 *
	 * @param   string  $name
	 * @param   array   $arguments
	 * @return  mixed
	 */
	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->_driver, $name), $arguments);
	}

}