<?php
class Haml extends View {
	protected $_parser;
	private function __construct() {
		require_once Misc::find_file('vendor', 'mthaml/Autoloader');
		MtHaml\Autoloader::register();
		
	}
	public static function get($name){
		$view = new Haml();
		$view->name = $name;
		$view->_parser =  new MtHaml\Environment('php');
		$file = Misc::find_file('views', $name, 'haml');
		
		if ($file == false)
			throw new Exception("Haml view {$name} not found.");
			
		$view->path=$file;
		return $view;
	}
	
	public function render() {
		$rendered = MODDIR.'/haml/rendered/'.$this->name.'.php';
		$content = $this->_parser->compileString(file_get_contents($this->path),$this->path);
		file_put_contents($rendered,$content);
		extract($this->_data);
		ob_start();
		include($rendered);
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}