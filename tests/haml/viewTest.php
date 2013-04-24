<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2013-02-05 at 09:23:37.
 */
class Haml_View_Test extends PHPUnit_Framework_TestCase
{

	protected $object;

	protected function setUp()
	{
		$this->file = $file = tempnam(sys_get_temp_dir(), 'view');
		$this->partial = $partial = tempnam(sys_get_temp_dir(), 'view2');
		$this->render_dir = sys_get_temp_dir().'/rendered';
		if (is_dir($this->render_dir))
			$this->delTree($this->render_dir);
		mkdir($this->render_dir);
		
		file_put_contents($file,
"#fairy.fairy 
	- echo \$fairy
");

		file_put_contents($partial,"- echo \$fairy");
		$pixie = $this->getMock("\\PHPixie\\Pixie", array('find_file'));
		$pixie->expects($this->any())
                 ->method('find_file')
                 ->will($this->returnCallback(function($type, $name) use($file, $partial){
					if ($type == 'config')
						return '';
					if ($name == 'view')
						return $file;
					if ($name == 'view2')
						return $partial;
				 }));
		$pixie-> config->set('haml.render_dir', '/');
		$pixie-> root_dir = $this->render_dir;
		$this->object = new \PHPixie\Haml\View($pixie, 'view');
	}
	
	private function delTree($dir) {
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
	}
	
	protected function tearDown()
	{
		unlink($this->file);
		$this->delTree($this->render_dir);
	}
	
	public function test__set()
	{
		$this->object->fairy = 'Tinkerbell';
		$this->assertEquals($this->object->fairy, 'Tinkerbell');
	}
}