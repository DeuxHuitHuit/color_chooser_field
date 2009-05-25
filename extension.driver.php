<?php

	Class extension_color_chooser_field extends Extension{
	
		public function about(){
			return array('name' => 'Field: Color Chooser',
						 'version' => '1.0',
						 'release-date' => '2009-05-25',
						 'author' => array('name' => 'Josh Nichols',
										   'website' => 'http://www.joshnichols.com',
										   'email' => 'mrblank@gmail.com')
				 		);
		}
		
		public function uninstall(){
			$this->_Parent->Database->query("DROP TABLE `tbl_fields_colorchooser`");
		}

		public function install(){

			return $this->_Parent->Database->query("CREATE TABLE `tbl_fields_colorchooser` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `field_id` int(11) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `field_id` (`field_id`)
			) TYPE=MyISAM");

		}
		
		public function getSubscribedDelegates() {
			return array(
				array(
					'page'		=> '/backend/',
					'delegate'	=> 'InitaliseAdminPageHead',
					'callback'	=> 'initaliseAdminPageHead'
				)
			);
		}

		public function initaliseAdminPageHead($context) {
			$page = $context['parent']->Page;

            $page->addScriptToHead(URL . '/extensions/color_chooser_field/assets/jquery.js', 3466701);
            $page->addScriptToHead(URL . '/extensions/color_chooser_field/assets/color-list.js', 3466703);
		}
			
	}

?>