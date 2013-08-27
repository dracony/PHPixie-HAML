<?php

namespace PHPixie\Haml;

/**
 * Haml View.
 * You can treat it as a regular View,
 * as it follows the same interface.
 * The only difference is that the template must have
 * a .haml extension.
 *
 * @package    Haml
 */
class View extends \PHPixie\View {

	/**
	 * Haml Parser
	 * @var \MtHaml\Environment   
	 */
	protected $_parser;
	
	/**
	 * File extension of the templates
	 * @var string   
	 */
	protected $_extension = 'haml';
	
	/**
	 * Directory to parse haml templates into.
	 * Relative to project root.
	 * @var string   
	 */
	protected $_render_dir;
	
	/**
	 * Constructs the haml view.
	 * 
	 * @param \PHPixie\Pixie $pixie Pixie dependency container
	 * @param \PHPixie\View\Helper View Helper
	 * @param string   $name The name of the template to use.
	 * @return Haml    
	 */
	public function __construct($pixie, $helper, $name) {
		parent::__construct($pixie, $helper, $name);
		$this->_parser = new \MtHaml\Environment('php');
		$this->_render_dir = $pixie->root_dir.$pixie->config->get('haml.render_dir','/assets/rendered').'/';
	}
	
	/**
	 * Renders the template, all dynamically set properties
	 * will be available inside the view file as variables.
	 *
	 * @return string Rendered template
	 * @see \PHPixie\View::render()
	 */
	public function render() {
		$rendered = $this->parse_template($this->name);
		extract($this->helper->get_aliases());
		extract($this->_data);
		$renderer = $this;
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
	 * @throw \Exception If the template is not found
	 */
	protected function parse_template($name){
		$file = $this->pixie->find_file('views', $name, $this->_extension);
		if ($file == false)
			throw new \Exception("HAML view {$name} not found.");
			
		$dir = $this->_render_dir.dirname($name);
		$rendered = $this->_render_dir.$name.'.php';
		if (!file_exists($rendered) || filemtime($rendered) <= filemtime($file)){
				
			$haml = file_get_contents($file);
			$haml = preg_replace_callback('#^([ \t]*)partial\:(.*?)[ \t]*$#m', 
				function($match) {
					$partial=trim($match[2]);
					return "{$match[1]}- include(\$renderer->parse_template({$partial}));";
				}, $haml);	
				
			$content = $this->_parser->compileString($haml, $file);
			if(!is_dir($dir))
				mkdir($dir, 0777, true);
			file_put_contents($rendered, $content);
			
		}
		
		return $rendered;
	}
}
