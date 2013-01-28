<?php
/**
 * Haml plugin for PHPixie. 
 * Haml is an alternative markup used for creating
 * well-indented clean templates for HTML.
 *
 * Find out more about haml at http://haml.info/
 * this module allows you to include subtemplates inside
 * your Haml templates. Include templates using 'partial' keyword:
 * <code>
 *	 partial:templatename 
 * </code>
 * 
 * This module is not included by default, download it here:
 *
 * https://github.com/dracony/PHPixie-HAML
 * 
 * To enable it add 'haml' to modules array in /application/config/core.php,
 * then download MtHaml from https://github.com/arnaud-lb/MtHaml and put contents
 * of its /lib folder inside /modules/haml/vendor/mthaml folder.
 * 
 * @link https://github.com/dracony/PHPixie-HAML Download this module from Github
 * @package    Haml
 */
class Haml extends View {

    /**
     * Haml Parser
     * @var MtHaml\Environment   
     * @access protected 
     */
	protected $_parser;
	
	/**
     * File extension of the templates
     * @var string   
     * @access protected 
     */
	protected $_extension = 'haml';
	
	/**
     * Directory to parse haml templates into.
	 * Relative to ROOTDIR
     * @var string   
     * @access protected 
     */
	protected $_render_dir;
	
	/**
     * Constructs the haml view.
	 * Use it as you would a basic View, the only
     * difference is that the template must have
	 * a .haml extension
	 * 
     * @param string   $name The name of the template to use.
     * @return Haml    
     * @access protected
     * @throws Exception If MtHaml is not found
     */
	protected function __construct($name) {
		parent::__construct($name);
		
		if(!class_exists('MtHaml\Autoloader')){
			$file = Misc::find_file('vendor', 'mthaml/Autoloader');
			if (!$file)
				throw new Exception('Could not find MtHaml.');
			include $file;
			MtHaml\Autoloader::register();
		}
		
		$this->_parser = new MtHaml\Environment('php');
		$this->_render_dir = Config::get('haml.render_dir').'/';
	}
	
	/**
     * Renders the template, all dynamically set properties
	 * will be available inside the view file as variables.
     *
     * @return string Rendered template
     * @access public  
	 * @see View::render()
     */
	public function render() {
		$rendered = $this->parse_template($this->name);
		extract($this->_data);
		ob_start();
		include($rendered);
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	
	/**
     * Parses HAML into a php template.
	 * Will only parse haml template if the parsed version
	 * does not exist yet or if the Haml version is more recent.
     *
	 * @param string  $name The name of the template to parse.
     * @return string Path to the parsed template
     * @access protected  
     */
	protected function parse_template($name){
		$file = Misc::find_file('views', $name, $this->_extension);
		if ($file == false)
			throw new Exception("HAML view {$name} not found.");
			
		$dir = ROOTDIR.$this->_render_dir.dirname($name);
		$rendered = ROOTDIR.$this->_render_dir.$name.'.php';
	
		$haml = file_get_contents($file);
		
		$partials = array();
		$render_dir = $this->_render_dir;
		$haml = preg_replace_callback('#^([ \t]*)partial\:(.*?)[ \t]*$#m', 
			function($match) use ( & $partials, $render_dir) {
				$partial=trim($match[2]);
				$partials[] = $partial;
				return "{$match[1]}- include(ROOTDIR.'{$render_dir}{$partial}.php');";
			}, $haml);	
			
		if (!file_exists($rendered) || filemtime($rendered) <= filemtime($file)) {
			$content = $this->_parser->compileString($haml, $file);
			if(!is_dir($dir))
				mkdir($dir, 0777, true);
			file_put_contents($rendered, $content);
		}
		
		foreach($partials as $partial)
			$this->parse_template($partial);
			
		return $rendered;
	}
}