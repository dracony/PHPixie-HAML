<?php

class HAML_Test extends PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
		$this->file = $file = tempnam(sys_get_temp_dir(), 'view');
		$pixie = $this->getMock("\\PHPixie\\Pixie", array('find_file'));
		$pixie->expects($this->any())
                 ->method('find_file')
                 ->will($this->returnValue($this->file));
		$pixie->config->set('haml.render_dir', '/');
		$this->object = new \PHPixie\Haml($pixie);
    }

    protected function tearDown()
    {
		unlink($this->file);
    }

    public function testGet()
    {
		$this->assertEquals('PHPixie\Haml\View',get_class($this->object->get('view')));
    }

}
