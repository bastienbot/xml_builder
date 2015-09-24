<?php

/**
* The method save_xml() saves the file underthe file name that has been submitted upon instanciation
* The method return_xml() returns the ml content as a string (note that tags will obviously be displayed as output only in the source file)
* 
* I'd like to also tell you how frustrating developping this class has been for me, 
* I felt as useless and counter productive as I could ever be,
* therefore it might look pretty bad, even disgusting to the eyes of some of you,
* and if you are the one who's in charge of editing this pile of junk (future me ?),
* well I do apologize in advance for the aweful time you're just about to step into.
* That being said, Good Luck !
*/

class create_xml {

	private $xml_file;
	private $website = "website.com";
	private $website_url = "http://website.com";
	private $feed = "http://website.com/rssfeed.xml";
	private $description = "description";
	private $language = "fr-FR";
	private $generator = "";
	private $title;
	private $content;
	private $date;
	private $backlink;
	private $filename;
	private $var_array = array();
	private $query_xml;


	public function __construct($filename, $query_xml) {
			$this->query_xml = $query_xml;
			$this->xml_file = new DOMDocument('1.0', 'utf-8');
			$this->xml_file->formatOutput = true;
			$this->filename = $filename;
	}

	//Creates a new channel without any content
	private function cless_channel($chan_name, $parent) {
		$this->var_array[$chan_name] = $this->xml_file->createElement($chan_name);
		$this->var_array[$chan_name] = $this->var_array[$parent]->appendChild($this->var_array[$chan_name]);
	}
	
	//Creates a populated channel
	private function popu_channel($chan_name, $content, $parent) {
		$this->var_array[$chan_name] = $this->xml_file->createElement($chan_name);
		$this->var_array[$chan_name] = $this->var_array[$parent]->appendChild($this->var_array[$chan_name]);
		$sub_content = $this->xml_file->createTextNode($content);
		$sub_content = $this->var_array[$chan_name]->appendChild($sub_content);
	}
	
	//Creates channel populated by inner tags
	private function tag_channel($chan_name, $content, $parent) {
		$this->var_array[$chan_name] = $this->xml_file->createElement($chan_name);
		$this->var_array[$chan_name] = $this->var_array[$parent]->appendChild($this->var_array[$chan_name]);
		foreach ($content as $key => $value) {
			$sub_content = $this->xml_file->createAttribute($key);
			$sub_content->value = $value;
			$sub_content = $this->var_array[$chan_name]->appendChild($sub_content);
		}
		
	}
	
	//This function builds the whole xml document, if you intend to use this class,
	//you might want to edit this part.
	private function gen_xml() {			
		
		//First node just quicker to hard code, if you're not happy with that, you can code it yourself, 
		//have fun coding a 20 lines method that will return 2 lines
		//Please note that in this method, indentation is based on xml nesting instead of php indentation
		$this->var_array["rss"] = $this->xml_file->createElement("rss");
		$this->var_array["rss"] = $this->xml_file->appendChild($this->var_array["rss"]);
			//Now deploying all nodes using both function coded above...quick as hell
			$this->cless_channel("channel", "rss");
				$this->popu_channel("title", $this->website, "channel");
				$this->tag_channel("link", array(
													"href" => $this->feed, 
													"rel" => "self", 
													"type" => "application/rss+xml"
													), "channel");
				$this->popu_channel("link", $this->website_url, "channel");
				foreach ($this->query_xml as $query_xml){
				$this->cless_channel("item", "channel");
					$this->popu_channel("title", ucfirst(stripslashes($query_xml->post_title)), "item");
					$this->popu_channel("link", $query_xml->guid, "item");
				}
				//Debug below in case the little cheat we used (array) to pass variables from methods to others would reach its limits somehow
				//echo '<pre>' . print_r($this->var_array) . '</pre>';
	}
	
	//Saves xml document
	public function save_xml() {
		$this->gen_xml();
		return $this->xml_file->save($this->filename);
	}
	
	//Returns xml content as string
	public function return_xml() {
		$this->gen_xml();
		return $this->xml_file->saveXML();
	}
}
?>
