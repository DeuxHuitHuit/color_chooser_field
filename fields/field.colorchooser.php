<?php
	
	Class fieldColorChooser extends Field{
		
				function __construct(&$parent){
					parent::__construct($parent);
					$this->_name = 'Color Chooser';
					$this->_required = true;
					$this->set('required', 'yes');
				}

				function isSortable(){
					return true;
				}

				function canFilter(){
					return true;
				}

				function allowDatasourceOutputGrouping(){
					return true;
				}

				function allowDatasourceParamOutput(){
					return true;
				}

				function canPrePopulate(){
					return true;
				}		

				function commit(){

					if(!parent::commit()) return false;

					$id = $this->get('id');

					if($id === false) return false;

					$fields = array();

					$fields['field_id'] = $id;

					$this->_engine->Database->query("DELETE FROM `tbl_fields_".$this->handle()."` WHERE `field_id` = '$id' LIMIT 1");		
					return $this->_engine->Database->insert($fields, 'tbl_fields_' . $this->handle());

				}

				function groupRecords($records){

					if(!is_array($records) || empty($records)) return;

					$groups = array($this->get('element_name') => array());

					foreach($records as $r){
						$data = $r->getData($this->get('id'));

						$value = $data['value'];

						if(!isset($groups[$this->get('element_name')][$value])){
							$groups[$this->get('element_name')][$value] = array('attr' => array('value' => $value),
																				 'records' => array(), 'groups' => array());
						}	

						$groups[$this->get('element_name')][$value]['records'][] = $r;

					}

					return $groups;
				}

				function displaySettingsPanel(&$wrapper, $errors=NULL){
					parent::displaySettingsPanel($wrapper, $errors);
					$this->appendRequiredCheckbox($wrapper);
					$this->appendShowColumnCheckbox($wrapper);
				}

				function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL){
					
					$this->_engine->Page->addScriptToHead(URL . '/extensions/color_chooser_field/assets/jquery.js', 75);
					$this->_engine->Page->addScriptToHead(URL . '/extensions/color_chooser_field/assets/farbtastic.js', 80);
					$this->_engine->Page->addScriptToHead(URL . '/extensions/color_chooser_field/assets/color-chooser.js', 85);
					$this->_engine->Page->addStylesheetToHead(URL . '/extensions/color_chooser_field/assets/farbtastic.css', 'screen', 90);
					
					$value = $data['value'];		
					$label = Widget::Label($this->get('label'));
					$label->setAttribute('class', 'color-chooser');
					if($this->get('required') != 'yes') $label->appendChild(new XMLElement('i', 'Optional'));
					$label->appendChild(Widget::Input('fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix, (strlen($value) != 0 ? $value : '#')));

					if($flagWithError != NULL) $wrapper->appendChild(Widget::wrapFormElementWithError($label, $flagWithError));
					else $wrapper->appendChild($label);
				}

				public function displayDatasourceFilterPanel(&$wrapper, $data=NULL, $errors=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL){
					$wrapper->appendChild(new XMLElement('h4', $this->get('label') . ' <i>'.$this->Name().'</i>'));
					$label = Widget::Label('Value');
					$label->appendChild(Widget::Input('fields[filter]'.($fieldnamePrefix ? '['.$fieldnamePrefix.']' : '').'['.$this->get('id').']'.($fieldnamePostfix ? '['.$fieldnamePostfix.']' : ''), ($data ? General::sanitize($data) : NULL)));	
					$wrapper->appendChild($label);

					$wrapper->appendChild(new XMLElement('p', 'Accepts either a 32 character hash, or plain text value. If plain text, it will be hashed before comparing.', array('class' => 'help')));

				}

				public function checkPostFieldData($data, &$message, $entry_id=NULL){
					$message = NULL;

					if($this->get('required') == 'yes' && strlen($data) == 0){
						$message = "This is a required field.";
						return self::__MISSING_FIELDS__;
					}

					return self::__OK__;		
				}
				
				/* Hashit code that needs to be removed */
				private static function __hashit($data){

					if(strlen($data) == 0) return;
					elseif(strlen($data) != 7) return $data;

					return $data;
				}

				public function processRawFieldData($data, &$status, $simulate=false, $entry_id=NULL){

					$status = self::__OK__;

					return array(
						'value' => self::__hashit($data),
					);
				}


				public function createTable(){

					return $this->Database->query(

						"CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
						  `id` int(11) unsigned NOT NULL auto_increment,
						  `entry_id` int(11) unsigned NOT NULL,
						  `value` varchar(32) default NULL,
						  PRIMARY KEY  (`id`),
						  KEY `entry_id` (`entry_id`),
						  KEY `value` (`value`)
						) TYPE=MyISAM;"

					);
				}		

				function buildDSRetrivalSQL($data, &$joins, &$where, $andOperation=false){

					$data[0] = self::__hashit($data[0]);

					parent::buildDSRetrivalSQL($data, $joins, $where, $andOperation);

					return true;

				}

			}

		?>