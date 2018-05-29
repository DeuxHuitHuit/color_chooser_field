<?php

	require_once(EXTENSIONS.'/color_chooser_field/extension.driver.php');



	Class fieldColorChooser extends Field
	{

		function __construct(){
			parent::__construct();
			$this->_name = 'Color Chooser';
			$this->_required = true;
			$this->entryQueryFieldAdapter = new EntryQueryFieldAdapter($this);
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

			if( !parent::commit() ) return false;

			$id = $this->get('id');

			if( $id === false ) return false;

			$fields = array();

			$fields['field_id'] = $id;

			Symphony::Database()
				->delete('tbl_fields_' . $this->handle())
				->where(['field_id' => $id])
				->limit(1)
				->execute()
				->success();
			return Symphony::Database()
				->insert('tbl_fields_' . $this->handle())
				->values($fields)
				->execute()
				->success();
		}

		function groupRecords($records){

			if( !is_array($records) || empty($records) ) return;

			$groups = array($this->get('element_name') => array());

			foreach( $records as $r ){
				$data = $r->getData($this->get('id'));

				$value = $data['value'];

				if( !isset($groups[$this->get('element_name')][$value]) ){
					$groups[$this->get('element_name')][$value] = array('attr' => array('value' => $value),
						'records' => array(), 'groups' => array());
				}

				$groups[$this->get('element_name')][$value]['records'][] = $r;
			}

			return $groups;
		}

		function displaySettingsPanel(XMLElement &$wrapper, $errors = null){

			parent::displaySettingsPanel($wrapper, $errors);

			/* Option Group */
			$optgroup = new XMLElement('div', null, array('class' => 'two columns'));
			$this->appendRequiredCheckbox($optgroup);
			$this->appendShowColumnCheckbox($optgroup);
			$wrapper->appendChild($optgroup);

		}

		function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $fieldnamePrefix = null, $fieldnamePostfix = null, $entry_id = null){

			$value = $data['value'];
			$label = Widget::Label($this->get('label'));
			$label->setAttribute('class', 'color-chooser');
			if( $this->get('required') != 'yes' ) $label->appendChild(new XMLElement('i', 'Optional'));
			$label->appendChild(Widget::Input('fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix, (strlen($value) != 0 ? $value : '#')));

			if( $flagWithError != null ) $wrapper->appendChild(Widget::Error($label, $flagWithError));
			else $wrapper->appendChild($label);
		}

		public function displayDatasourceFilterPanel(XMLElement &$wrapper, $data = null, $errors = null, $fieldnamePrefix = null, $fieldnamePostfix = null){
			$wrapper->appendChild(new XMLElement('h4', $this->get('label').' <i>'.$this->Name().'</i>'));
			$label = Widget::Label('Value');
			$label->appendChild(Widget::Input('fields[filter]'.($fieldnamePrefix ? '['.$fieldnamePrefix.']' : '').'['.$this->get('id').']'.($fieldnamePostfix ? '['.$fieldnamePostfix.']' : ''), ($data ? General::sanitize($data) : null)));
			$wrapper->appendChild($label);

			$wrapper->appendChild(new XMLElement('p', 'Accepts a 6 character color hex value beginning with \'#\'.', array('class' => 'help')));

		}

		public function checkPostFieldData($data, &$message, $entry_id = null){
			$message = null;

			if( $this->get('required') == 'yes' && strlen($data) == 0 ){
				$message = "This is a required field.";
				return self::__MISSING_FIELDS__;
			}
			// The Farbtastic needs a value in the field to work. '#' is the default value placed by the js.
			if( $this->get('required') == 'yes' && $data == "#" ){
				$message = "This is a required field.";
				return self::__MISSING_FIELDS__;
			}
			// Make sure the value entered is a valid hex color, '#' or '' is OK
			if( $data !== '#' && strlen($data) !== 0 ){
				if( !preg_match("/^#[0-9a-f]{6}$/i", $data) ){
					$message = "This is not a valid 6 character hex color value.";
					return self::__MISSING_FIELDS__;
				}
			}

			return self::__OK__;

		}

		public function getDecimalValue($data) {
			return hexdec($data);
		}

		public function splitToDecimal($data) {
			$rgb[0] = $this->getDecimalValue(substr($data,1,2));
			$rgb[1] = $this->getDecimalValue(substr($data,3,2));
			$rgb[2] = $this->getDecimalValue(substr($data,5,2));
			return $rgb;
		}

		public function rgb2brightness($r,$g,$b) {
			$brightness = round( (.2126 * $r + .7152 * $g + .0722 * $b) / 255 * 100 );
			return $brightness;
		}

		public function rgb2cmyk($r,$g,$b) {

			$r = $r / 255;
			$g = $g / 255;
			$b = $b / 255;

			$k = min(array( 1 - $r, 1 - $g, 1 - $b));
			if ($k < 1) {
				$c = (1 - $r - $k) / (1 - $k);
				$m = (1 - $g - $k) / (1 - $k);
				$y = (1 - $b - $k) / (1 - $k);
			} else {
				$c = .3;
				$m = .3;
				$y = .3;
			}

			$cmyk[0] = round($c * 100);
			$cmyk[1] = round($m * 100);
			$cmyk[2] = round($y * 100);
			$cmyk[3] = round($k * 100);

			return $cmyk;
		}
		/**
		 * Append the formatted XML output of this field as utilized as a data source.
		 *
		 * @param XMLElement $wrapper
		 *	the XML element to append the XML representation of this to.
		 * @param array $data
		 *	the current set of values for this field. the values are structured as
		 *	for displayPublishPanel.
		 * @param boolean $encode (optional)
		 *	flag as to whether this should be html encoded prior to output. this
		 *	defaults to false.
		 * @param string $mode
		 *	 A field can provide ways to output this field's data. For instance a mode
		 *  could be 'items' or 'full' and then the function would display the data
		 *  in a different way depending on what was selected in the datasource
		 *  included elements.
		 * @param integer $entry_id (optional)
		 *	the identifier of this field entry instance. defaults to null.
		 */
		public function appendFormattedElement(XMLElement &$wrapper, $data, $encode = false, $mode = null, $entry_id = null) {
			if ($data == null) {
				return;
			}

			$value = $this->prepareTextValue($data, $entry_id);
			if ($encode) {
				$value = General::sanitize($value);
			}

			$newItem = new XMLElement($this->get('element_name'), $value );

			//Check if we have a full color before split
			if (strlen($data["value"]) == 7 ) {
				$rgb = $this->splitToDecimal($data["value"]);
				$cmyk = $this->rgb2cmyk($rgb[0],$rgb[1],$rgb[2]);
				$brightness = $this->rgb2brightness($rgb[0],$rgb[1],$rgb[2]);
				$newItem->setAttribute("r",$rgb[0]);
				$newItem->setAttribute("g",$rgb[1]);
				$newItem->setAttribute("b",$rgb[2]);
				$newItem->setAttribute("c",$cmyk[0]);
				$newItem->setAttribute("m",$cmyk[1]);
				$newItem->setAttribute("y",$cmyk[2]);
				$newItem->setAttribute("k",$cmyk[3]);
				$newItem->setAttribute("brightness",$brightness);
				$newItem->setAttribute("has-color","yes");
			} else {
				$newItem->setAttribute("has-color","no");
			}
			$wrapper->appendChild($newItem);
		}

		public function prepareTextValue($data, $entry_id = null) {
			if(strlen($data["value"]) > 1 ) {
				return $data["value"];
			}
			return null;
		}

		public function createTable(){
			return Symphony::Database()
				->create('tbl_entries_data_' . $this->get('id'))
				->ifNotExists()
				->charset('utf8')
				->collate('utf8_unicode_ci')
				->fields([
					'id' => [
						'type' => 'int(11)',
						'auto' => true,
					],
					'entry_id' => 'int(11)',
					'value' => [
						'type' => 'varchar(32)',
						'null' => true,
					],
				])
				->keys([
					'id' => 'primary',
					'entry_id' => 'key',
					'value' => 'key',
				])
				->execute()
				->success();
		}

	}

?>
