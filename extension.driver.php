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
			return Symphony::Database()
				->create('tbl_fields_colorchooser')
				->ifNotExists()
				->fields([
					'id' => [
						'type' => 'int(11)',
						'auto' => true,
					],
					'field_id' =>  'int(11)',
				])
				->keys([
					'id' => 'primary',
					'field_id' => 'unique',
				])
				->execute()
				->success();
		}

		/*
		 * Delegate fired when the extension is uninstalled
		 * Cleans settings and Database
		 */

		public function uninstall() {
			return Symphony::Database()
				->drop('tbl_fields_colorchooser')
				->ifExists()
				->execute()
				->success();
		}
	}

?>
