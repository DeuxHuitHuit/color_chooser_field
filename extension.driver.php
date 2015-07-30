<?php

	Class Extension_Color_Chooser_Field extends Extension {

		/*
		 * Subscribe to delegates
		 */
		
		public function getSubscribedDelegates() {
			return array(
				array(
					'page'     => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'appendResources'
				),
			);
		}
		
		/*********** DELEGATES ***********************/

		/*
		 * Appends resource (js/css) files references into the head, if needed
		 * @param array $context
		 */
		
		public function appendResources() {
		
			// store callback array locally
			$c = Administration::instance()->getPageCallback();

			// publish page, new or edit
			if(in_array($c['context']['page'], array('index', 'new', 'edit'))){
				
				$page = Administration::instance()->Page;

				$page->addScriptToHead(URL.'/extensions/color_chooser_field/assets/jquery.farbtastic.js', 3001);
				$page->addScriptToHead(URL.'/extensions/color_chooser_field/assets/jquery.tools.min.js', 3002);
				$page->addScriptToHead(URL.'/extensions/color_chooser_field/assets/jquery.color-chooser.js', 3003);
				$page->addStylesheetToHead(URL.'/extensions/color_chooser_field/assets/farbtastic.css', 'screen', 3004);

				return;
			}
		}
		
		/*
		 * Delegate fired when the extension is installed
		 */
		
		public function install(){
			return Symphony::Database()->query("CREATE TABLE `tbl_fields_colorchooser` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `field_id` int(11) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `field_id` (`field_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
		}
		
		/*
		 * Delegate fired when the extension is uninstalled
		 * Cleans settings and Database
		 */
		
		public function uninstall() {
			try {
				Symphony::Database()->query("DROP TABLE `tbl_fields_colorchooser`");
			} catch( Exception $e ){}
			return true;
		}	
	}

?>
