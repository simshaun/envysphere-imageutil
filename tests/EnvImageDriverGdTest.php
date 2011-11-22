<?php
require_once dirname(__FILE__) . '/../src/driver/gd.php';

/**
 * @covers EnvImage_Driver_Gd::
 */
class EnvImageDriverGdTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EnvImage_Driver_Gd
	 */
	protected $driver;

	protected function setUp()
	{
		$this->driver = new EnvImage_Driver_Gd();
	}

	protected function tearDown()
	{
		$this->driver = NULL;
	}

	/**
	 * @covers EnvImage_Driver_Gd::hex2rgb
	 */
	public function testHex2Rgb() {
		$this->assertEquals( array('r' => 255, 'g' => 255, 'b' => 255), $this->driver->hex2rgb('FFFFFF') );
		$this->assertEquals( array('r' => 255, 'g' => 255, 'b' => 255), $this->driver->hex2rgb('FFF') );
		$this->assertEquals( array('r' => 0,   'g' => 0,   'b' => 0),   $this->driver->hex2rgb('000000') );
		$this->assertEquals( array('r' => 0,   'g' => 0,   'b' => 0),   $this->driver->hex2rgb('000') );
		$this->assertEquals( array('r' => 255, 'g' => 0,   'b' => 0),   $this->driver->hex2rgb('FF0000') );
		$this->assertEquals( array('r' => 0,   'g' => 255, 'b' => 0),   $this->driver->hex2rgb('00FF00') );
		$this->assertEquals( array('r' => 0,   'g' => 0,   'b' => 255), $this->driver->hex2rgb('0000FF') );
	}

	/**
	 * @covers EnvImage_Driver_Gd::setFormat
	 */
	public function testSetFormat() {
		$this->driver->setFormat('jpg');
		$this->assertEquals( 'jpg', $this->driver->getFormat() );

		$this->driver->setFormat('gif');
		$this->assertEquals( 'gif', $this->driver->getFormat() );

		$this->driver->setFormat('png');
		$this->assertEquals( 'png', $this->driver->getFormat() );
	}

	/**
	 * @covers EnvImage_Driver_Gd::isSupportedFile
	 */
	public function testIsSupportedFile() {
		$filenames = array(
			'foo.jpg',
			'foo.jpeg',
			'foo.gif',
			'foo.png',
		);

		foreach ($filenames AS $filename)
		{
			$this->assertTrue( $this->driver->isSupportedFile($filename), $filename .' is unsupported' );
		}
	}

	/**
	 * @covers EnvImage_Driver_Gd::loadFromFile
	 */
	public function testLoadFromFile() {
		$this->driver->loadFromFile( './resources/wide.jpg' );
		$this->assertEquals( 'wide.jpg', $this->driver->getOrigFilename() );
		$this->assertEquals( 'jpg',      $this->driver->getFormat() );
		$this->assertEquals( 1024,       $this->driver->getWidth(),  'Width is not 1024' );
		$this->assertEquals( 683,        $this->driver->getHeight(), 'Height is not 683' );

		$this->driver->loadFromFile( './resources/tall.jpg' );
		$this->assertEquals( 'tall.jpg', $this->driver->getOrigFilename() );
		$this->assertEquals( 'jpg',      $this->driver->getFormat() );
		$this->assertEquals( 768,        $this->driver->getWidth(),  'Width is not 768' );
		$this->assertEquals( 1024,       $this->driver->getHeight(), 'Height is not 1024' );
	}

	/**
	 * @covers EnvImage_Driver_Gd::save
	 */
	public function testSave() {
		$file = './resources/wide.jpg';

		$this->driver
			->loadFromFile( $file )
			->save( './', 'saved.jpg' );

		$file_exists = file_exists('./saved.jpg');
		@unlink('./saved.jpg');

		$this->assertTrue( $file_exists );

	}

	/**
	 * @covers EnvImage_Driver_Gd::resize
	 */
	public function testResize() {
		$file = './resources/wide.jpg';

		$this->driver->loadFromFile( $file )->resize(800, 600);
		$this->assertEquals( 800, $this->driver->getWidth() );
		$this->assertEquals( 533, $this->driver->getHeight() );
		$this->tearDown();

		$this->setUp();
		$this->driver->loadFromFile( $file )->resize(600, 800);
		$this->assertEquals( 600, $this->driver->getWidth() );
		$this->assertEquals( 400, $this->driver->getHeight() );
		$this->tearDown();

		$this->setUp();
		$this->driver->loadFromFile( $file )->resize(600, 600);
		$this->assertEquals( 600, $this->driver->getWidth() );
		$this->assertEquals( 400, $this->driver->getHeight() );
		$this->tearDown();

		$this->setUp();
		$this->driver->loadFromFile( $file )->resize(600, 600, TRUE);
		$this->assertEquals( 600, $this->driver->getWidth() );
		$this->assertEquals( 600, $this->driver->getHeight() );
		$this->tearDown();

		$this->setUp();
		$this->driver->loadFromFile( $file )->resize(600, 600, TRUE);
		$this->assertEquals( 600, $this->driver->getWidth() );
		$this->assertEquals( 600, $this->driver->getHeight() );
		$this->driver->resize(400, 400);
		$this->assertEquals( 400, $this->driver->getWidth() );
		$this->assertEquals( 400, $this->driver->getHeight() );
		$this->driver->resize(200, 100);
		$this->assertEquals( 100, $this->driver->getWidth() );
		$this->assertEquals( 100, $this->driver->getHeight() );
		$this->tearDown();
	}

	/**
	 * @covers EnvImage_Driver_Gd::crop
	 */
	public function testCrop() {
		$file = './resources/wide.jpg';

		$this->driver->loadFromFile( $file )->crop(100, 100, 200, 200);
		$this->assertEquals( 200, $this->driver->getWidth() );
		$this->assertEquals( 200, $this->driver->getHeight() );
	}

}
