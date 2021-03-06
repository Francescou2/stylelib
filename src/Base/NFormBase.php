<?php
namespace DeployStudio\Style\Base;

use \DeployStudio\Style\Base\StyleLib;
use \DeployStudio\Style\StyleBaseClass;

class NFormBase {
	const FORM_TYPE_HORIZONTAL = 1;
	const FORM_TYPE_VERTICAL = 2;
	const FORM_TYPE_INLINE = 3;

	protected static $forms;
	protected static $openForm;

	protected static function openForm($action, $multipart, $id, $get, $validationClass, $form_type, $bootstrap_version) {
		StyleBaseClass::checkOption($id, 'form'.rand(1000,9999));
		$formOptions = array(
			'id' => $id,
			'fields' => array()
		);

		self::$openForm = $id;
		self::$forms[$id] = &$formOptions;
		
		switch ($form_type) {
			case NFormBase::FORM_TYPE_VERTICAL:
				$horizontalClass = '';
				$formOptions['classLabel'] = "";
				$formOptions['classInput'] = "";
				break;

			case NFormBase::FORM_TYPE_INLINE:
				$horizontalClass = 'form-inline ';
				$formOptions['classLabel'] = "";
				$formOptions['classInput'] = "";
				break;

			case NFormBase::FORM_TYPE_HORIZONTAL:
			default:
				$horizontalClass = 'form-horizontal ';
				$formOptions['classLabel'] = "col-md-2";
				$formOptions['classInput'] = "col-md-10";
				break;
		}

		if ($bootstrap_version == 3) {
			$formOptions['formConst'] = array(
				'form-group' => 'form-group'
			);
		} else {
			$formOptions['formConst'] = array(
				'form-group' => 'form-group row'
			);
		}
		
		echo '
		<form class="form '.$horizontalClass.$validationClass.'"
			style="margin-bottom: 0;"
			method="'.($get ? 'get' : 'post').'"
			action="'.$action.'" novalidate="novalidate"
			'.($multipart ? 'enctype="multipart/form-data"' : '').
			' id="'.$id.'">
		<input type="hidden" name="redirect"
			value="'.(isset($_GET['r']) ? urlencode($_GET['r']) : '').'" />';
	}
	
	static function close() {
		self::$openForm = null;
		echo '</form>';
	}

		
	static protected function getFldAttributes($options, $required) {
		$attr = array();

		$attr[] = 'id="' . $options['id'] . '"';
		$attr[] = 'name="' . $options['name'] . '"';

		// regole di validazione
		if ($required)
			$attr[] = 'data-rule-required="true"';
		if (isset($options['email']) && $options['email'])
			$attr[] = 'data-rule-email="true"';
		if (isset($options['number']) && $options['number'])
			$attr[] = 'data-rule-number="true"';
		if (isset($options['digits']) && $options['digits'])
			$attr[] = 'data-rule-digits="true"';
		if (isset($options['integer']) && $options['integer'])
			$attr[] = 'data-rule-pattern="(-?)([0-9]+)"';
		if (isset($options['min']) && $options['min'])
			$attr[] = 'data-rule-min="' . $options['min'] . '"';
		if (isset($options['max']) && $options['max'])
			$attr[] = 'data-rule-max="' . $options['max'] . '"';
		if (isset($options['maxlength']) && $options['maxlength'])
			$attr[] = 'data-rule-maxlength="' . $options['maxlength'] . '"';
		if (isset($options['length']) && $options['length'])
			$attr[] = 'data-rule-minlength="' . $options['length'] . '"';
		if (isset($options['length']) && $options['length'])
			$attr[] = 'data-rule-maxlength="' . $options['length'] . '"';
		if (isset($options['date']) && $options['date'])
			$attr[] = 'data-rule-date="true"';
		if (isset($options['dateITA']) && $options['dateITA'])
			$attr[] = 'data-rule-dateITA="true"';
		if (isset($options['time']) && $options['time'])
			$attr[] = 'data-rule-time="true"';
		if (isset($options['regexp']) && $options['regexp'])
			$attr[] = 'data-rule-pattern="' . $options['regexp'] . '"';

		if (isset($options['placeholder']))
			$attr[] = 'placeholder="' . $options['placeholder'] . '"';
		if (isset($options['rows']))
			$attr[] = 'rows="' . $options['rows'] . '"';
		if (isset($options['style']))
			$attr[] = 'style="' . $options['style'] . '"';
		if (isset($options['onblur']))
			$attr[] = 'onblur="' . $options['onblur'] . '"';
		if (isset($options['onchange']))
			$attr[] = 'onchange="' . $options['onchange'] . '"';
		if (isset($options['onkeyup']))
			$attr[] = 'onkeyup="' . $options['onkeyup'] . '"';
		if (isset($options['maxlength']))
			$attr[] = 'maxlength="' . $options['maxlength'] . '"';
		if (isset($options['length']))
			$attr[] = 'maxlength="' . $options['length'] . '"';
		if (isset($options['disabled']) && $options['disabled'])
			$attr[] = 'disabled';
		if (isset($options['multiple']) && $options['multiple'])
			$attr[] = 'multiple="multiple"';
		if (isset($options['dateformat']))
			$attr[] = 'data-format="' . $options['dateformat'] . '"';

		return implode(' ', $attr);
	}

	/* ***************** FIELDS ***************** */
	/* ***************** INPUT ***************** */

	static function inputBase($label, $name, $required, &$options, &$outputArr) {
		$formOptions = &self::$forms[self::$openForm];
		
		// INITIALIZE
		// base options
		StyleBaseClass::checkOption($options['id'], 'input'.rand(100000,999999));
		$options['name'] = $name;
		
		// define output options array
		$outputArr = array(
			'requiredLabel' => '',
			'additionalDivClasses' => array(),
			'additionalFldClasses' => array()
		);

		// rules
		if ($required) {
			$outputArr['requiredLabel'] = '<font color="red">*</font> ';
		}

		// additional classes
		if (isset($options['prepend']) || isset($options['append']) || isset($options['prependBtn']) || isset($options['appendBtn'])) {
			$outputArr['additionalDivClasses'][] = 'input-group';
		}
		if (isset($options['additionalDivClasses']) && is_array($options['additionalDivClasses'])) {
			$outputArr['additionalDivClasses'] = array_merge($outputArr['additionalDivClasses'], $options['additionalDivClasses']);
		}

		// additional field options
		if (!isset($options['type'])) {
			$options['type'] = 'text';
		}
		if (!isset($options['description'])) {
			$options['description'] = '';
		}

		// VALUE
		if (isset($options['value']) && strlen($options['value']) > 0) {
			echo '<script type="text/javascript">' . "\n";
			echo '$(function() {' . "\n";
			echo '$("#'.$options['id'].'").val(\'' . StyleBaseClass::jsReplace($options['value']) . '\');' . "\n";
			echo '});</script>';
		}

		// add field to form fields
		$formOptions['fields'][] = array(
			'name' => $options['name'],
			'id' => $options['id'],
			'type' => 'input'
		);
	}

	/* ***** variante: email ***** */
	static function emailBase(&$options) {
		$options['email'] = true;
		$options['type'] = 'email';
	}

	/* ***** variante: password ***** */
	static function passwordBase(&$options) {
		$options['type'] = 'password';
	}

	/* ***** variante: file ***** */
	static function fileBase(&$options) {
		$options['type'] = 'file';
	}

	/* ***** variante: datepicker ***** */
	static function datepickerBase($name, &$options) {
		// definisci qui un id per poterlo replicare nell'hidden
		StyleBaseClass::checkOption($options['id'], 'input'.rand(100000,999999));
		StyleBaseClass::checkOption($options['additionalDivClasses'], array());
		
		// opzioni campo input
		$options = $options;
		if (isset($options['value'])) {
			$options['value'] = date('d/m/Y H:i', strtotime($options['value']));
		}

		$options['additionalDivClasses'] = array_merge($options['additionalDivClasses'], array('date'));
		$options['prepend'] = '<span class="fa fa-calendar"></span>';
		$options['dateITA'] = true;
		
		// campo hidden
		$hiddenValue = empty($options['value']) ? '' : $options['value'];
		$hiddenid = $options['id'].'_hidden';
		NFormBase::hidden($name, $hiddenValue, $hiddenid);
	}

	/* ***** variante: datetimepicker ***** */
	static function datetimepickerBase($name, &$options) {
		// definisci qui un id per poterlo replicare nell'hidden
		StyleBaseClass::checkOption($options['id'], 'input'.rand(100000,999999));
		StyleBaseClass::checkOption($options['additionalDivClasses'], array());
		
		// opzioni campo input
		$options = $options;
		if (isset($options['value'])) {
			$options['value'] = date('d/m/Y', strtotime($options['value']));
		}

		$options['additionalDivClasses'] = array_merge($options['additionalDivClasses'], array('datetime'));
		$options['prepend'] = '<span class="fa fa-calendar"></span>';
		
		// campo hidden
		$hiddenValue = empty($options['value']) ? '' : $options['value'];
		$hiddenid = $options['id'].'_hidden';
		NFormBase::hidden($name, $hiddenValue, $hiddenid);
	}

	/* ***** variante: daterangepicker ***** */
	static function daterangepickerBase($name, &$options) {
		// definisci qui un id per poterlo replicare nell'hidden
		StyleBaseClass::checkOption($options['id'], 'input'.rand(100000,999999));
		StyleBaseClass::checkOption($options['additionalDivClasses'], array());
		
		// opzioni campo input
		$options = $options;
		if (isset($options['value_start']) && isset($options['value_end'])) {
			$options['value'] = date('d/m/Y', strtotime($options['value_start']))
			.' - '.date('d/m/Y', strtotime($options['value_end']));
		}

		$options['additionalDivClasses'] = array_merge($options['additionalDivClasses'], array('daterange'));
		$options['prepend'] = '<span class="fa fa-calendar"></span>';

		// campo hidden start
		$hiddenValue = empty($options['value']) ? '' : $options['value'];
		$hiddenid = $options['id'].'_hidden_start';
		NFormBase::hidden($name.'_start', $hiddenValue, $hiddenid);
		
		// campo hidden end
		$hiddenValue = empty($options['value']) ? '' : $options['value_to'];
		$hiddenid = $options['id'].'_hidden_end';
		NFormBase::hidden($name.'_end', $hiddenValue, $hiddenid);
	}
	
	/* ***** variante: datetimerangepicker ***** */
	static function datetimerangepickerBase($name, &$options) {
		// definisci qui un id per poterlo replicare nell'hidden
		StyleBaseClass::checkOption($options['id'], 'input'.rand(100000,999999));
		StyleBaseClass::checkOption($options['additionalDivClasses'], array());
		
		// opzioni campo input
		$options = $options;
		if (isset($options['value_start']) && isset($options['value_end'])) {
			$options['value'] = date('d/m/Y H:i', strtotime($options['value_start']))
			.' - '.date('d/m/Y H:i', strtotime($options['value_end']));
		}

		$options['additionalDivClasses'] = array_merge($options['additionalDivClasses'], array('datetimerange'));
		$options['prepend'] = '<span class="fa fa-calendar"></span>';

		// campo hidden start
		$hiddenValue = empty($options['value']) ? '' : $options['value'];
		$hiddenid = $options['id'].'_hidden_start';
		NFormBase::hidden($name.'_start', $hiddenValue, $hiddenid);
		
		// campo hidden end
		$hiddenValue = empty($options['value']) ? '' : $options['value_to'];
		$hiddenid = $options['id'].'_hidden_end';
		NFormBase::hidden($name.'_end', $hiddenValue, $hiddenid);
	}
	
	/* ***** variante: clockpicker ***** */
	static function clockpickerBase(&$options) {
		if (isset($options['additionalDivClasses'])) {
			$options['additionalDivClasses'] = array_merge($options['additionalDivClasses'], array('clockpicker'));
		} else {
			$options['additionalDivClasses'] = array('clockpicker');
		}
		
		$options['prepend'] = '<span class="fa fa-clock"></span>';
		$options['time'] = true;
	}
	
	/* ***** variante: touchspin ***** */
	static function touchspinBase(&$options) {
		if (isset($options['additionalDivClasses'])) {
			$options['additionalDivClasses'] = array_merge($options['additionalDivClasses'], array('touchspin'));
		} else {
			$options['additionalDivClasses'] = array('touchspin');
		}
		$options['numeric'] = true;
	}

	/* ***************** TEXTAREA ***************** */

	protected static function textareaBase($label, $name, $required, &$options, &$outputArr) {
		$formOptions = &self::$forms[self::$openForm];
		
		// INITIALIZE
		// base options
		StyleBaseClass::checkOption($options['id'], 'textarea'.rand(100000,999999));
		$options['name'] = $name;
		
		// define output options array
		$outputArr = array(
			'requiredLabel' => '',
			'additionalDivClasses' => array(),
			'additionalFldClasses' => array()
		);

		// rules
		if ($required) {
			$outputArr['requiredLabel'] = '<font color="red">*</font> ';
		}

		// additional classes
		if (isset($options['autosize']) && $options['autosize']){
			$outputArr['additionalFldClasses'][] = 'autosize';
		}
		if (isset($options['additionalFldClasses']) && is_array($options['additionalFldClasses'])) {
			$outputArr['additionalFldClasses'] = array_merge($outputArr['additionalFldClasses'], $options['additionalFldClasses']);
		}
		
		// VALUE
		if (isset($options['value']) && strlen($options['value']) > 0) {
			echo '<script type="text/javascript">' . "\n";
			echo '$(function() {' . "\n";
			echo '$("#'.$options['id'].'").val(\'' . StyleBaseClass::jsReplace($options['value']) . '\');' . "\n";
			echo '});</script>';
		}

		// add field to form fields
		$formOptions['fields'][] = array(
			'name' => $options['name'],
			'id' => $options['id'],
			'type' => 'textarea'
		);
	}

	/* ***** variante: wysiwyg ***** */
	static function wysiwygBase(&$options) {
		$options['additionalFldClasses'][] = 'wysiwyg';
	}

	/* ***************** INPUT CHECKBOXES ***************** */

	static function checkboxesBase($label, $mainName, $checkboxes, &$checkboxTags, &$options, &$outputArr) {
		$formOptions = &self::$forms[self::$openForm];
		
		// INITIALIZE
		// base options
		StyleBaseClass::checkOption($options['id'], 'checkbox'.rand(100000,999999));
		StyleBaseClass::checkOption($options['radio'], false);
		StyleBaseClass::checkOption($options['value'], array());

		// define output options array
		$outputArr = array(
			'requiredLabel' => '',
			'additionalDivClasses' => array(),
			'additionalFldClasses' => array()
		);

		// main rule - checkbox
		// mostra l'asterisco se c'e' una sola checkbox e questa non ha un'etichetta
		$primachiave = key($checkboxes);
		if (!$options['radio'] && count($checkboxes) == 1 && (!isset($checkboxes[$primachiave]['name']) || is_null($checkboxes[$primachiave]['name']))
		&& isset($checkboxes[$primachiave]['required']) && $checkboxes[$primachiave]['required']) {
			$outputArr['requiredLabel'] = '<font color="red">*</font> ';
		}

		// main rule - radio
		// mostra l'asterisco se c'e' la required nella prima chiave (se true, vale per tutte)
		if ($options['radio'] && $checkboxes[$primachiave]['required']) {
			$outputArr['requiredLabel'] = '<font color="red">*</font> ';
		}

		// VALUE
		if (!is_array($options['value'])) {
			$options['value'] = array($options['value']);
		}

		// BUILD CHECKBOXES
		$checkboxTags = array();
		foreach ($checkboxes as $k => $c) {
			if (!is_array($c)) {
				$c = array('label' => $c, 'value' => $k);
			}
			StyleBaseClass::checkOption($c['required'], false);
			StyleBaseClass::checkOption($c['disabled'], false);
			StyleBaseClass::checkOption($c['label'], '');

			// checkbox name
			StyleBaseClass::checkOption($c['name'], null);
			$chkname = $mainName;
			if (!$options['radio']) {
				if (!is_null($c['name'])) {
					$chkname .= '[' . $c['name'] . ']';
				} elseif (count($checkboxes) > 1) {
					$chkname .= '[]';
				}
			}

			// checkbox id
			if (isset($c['name'])) {
				StyleBaseClass::checkOption($c['id'], $c['name']);
			}
			if (isset($c['value'])) {
				StyleBaseClass::checkOption($c['id'], $c['value']);
			}
			if (count($checkboxes) > 1) {
				StyleBaseClass::checkOption($c['id'], 'c'.rand(100000,999999));
			}
			StyleBaseClass::checkOption($c['id'], null);
			$chkid = $options['id'] . (!is_null($c['id']) ? '_' . StyleLib::idGen($c['id']) : '');

			// checkbox value
			$chkvalue = !empty($c['value']) ? $c['value'] : null;

			// add field to form fields
			$formOptions['fields'][] = array(
				'name' => $chkname,
				'id' => $chkid,
				'type' => 'checkbox',
				'value' => $chkvalue
			);

			// check main value
			if (count($options['value']) > 0 &&
				($options['value'][0] == 'on' || (!is_null($chkvalue) && in_array($chkvalue, $options['value'])))) {
				echo '<script type="text/javascript">' . "\n";
				echo '$(function() {' . "\n";
				echo '$("#'.$chkid.'").prop("checked", true);' . "\n";
				echo '});</script>';
			}

			// build options
			if ($options['radio']) {
				$checkboxTags[] = get_called_class()::radioBuild($chkname, $chkid, $c);
			} else {
				$checkboxTags[] = get_called_class()::checkboxBuild($chkname, $chkid, $c);
			}
		}
	}

	/* ***************** SELECT ***************** */

	static function selectBase($label, $name, $values, $required, &$options, &$outputArr) {
		$formOptions = &self::$forms[self::$openForm];
		
		// INITIALIZE
		// base options
		StyleBaseClass::checkOption($options['id'], 'select'.rand(100000,999999));
		StyleBaseClass::checkOption($options['noinit'], false);
		$options['name'] = $name;
		if (isset($options['multiple']) && $options['multiple']) {
			$options['name'] .= '[]';
		}

		// define output options array
		$outputArr = array(
			'requiredLabel' => '',
			'additionalDivClasses' => array(),
			'additionalFldClasses' => array()
		);

		// rules
		if ($required) {
			$outputArr['requiredLabel'] = '<font color="red">*</font> ';
		}
			
		// SELECT2 INIT
		$select2Options = array();
		StyleBaseClass::checkOption($options['tokenSeparator'], ',');
		$select2Options['tokenSeparators'] = array($options['tokenSeparator']);

		// parse data
		StyleBaseClass::checkOption($options['dataProcessed'], false);
		$data = array(array());
		if (!is_array($values)) {
			echo '<b>Debug</b>: $values for the ' . $name . ' field is not an array, please check the select declaration.';
		} elseif ($options['dataProcessed'] && !empty($values)) {
			$select2Options['data'] = array_merge($data, $values);
		} elseif (isset($options['processData']) && !empty($values)) {
			$data = $options['processData'].'({items: '.json_encode($values).'}).results';
			$select2Options['data'] = '##DATA##';  // will be parsed as function
		} elseif (isset($options['labelAsValue']) && $options['labelAsValue']) {
			$refactored_values = array();
			foreach ($values as $val => $lab) {
				$data[] = array('id' => $lab, 'text' => $lab);
				$refactored_values[$lab] = $lab;
			}
			$values = $refactored_values;
			$select2Options['data'] = $data;
		} else {
			foreach ($values as $val => $lab) {
				$data[] = array('id' => strval($val), 'text' => $lab);
			}
			$select2Options['data'] = $data;
		}

		// placeholder
		if (isset($options['placeholder'])) {
			$select2Options['placeholder'] = $options['placeholder'];
		} elseif (isset($options['custom']) && $options['custom']) {
			$select2Options['placeholder'] = __('universal_stylelib::stylelib.select_enter');
		} else {
			$select2Options['placeholder'] = __('universal_stylelib::stylelib.select');
		}

		// theme
		if (isset($options['theme'])) {
			$select2Options['theme'] = $options['theme'];
		}
		$matcherFunction = '';
		if (isset($options['matcher'])) {
			$select2Options['matcher'] = '##MATCHERFUNCTION##';
			$matcherFunction = $options['matcher'];
		}

		// look for other select2 options
		StyleBaseClass::checkOption($options['custom'], false);
		$select2Options['tags'] = $options['custom'];
		StyleBaseClass::checkOption($options['clear'], false);
		$select2Options['allowClear'] = $options['clear'];
		StyleBaseClass::checkOption($options['minlength'], 0);
		$select2Options['minimumInputLength'] = $options['minlength'];
		StyleBaseClass::checkOption($options['maxlength'], 1000);
		$select2Options['maximumInputLength'] = $options['maxlength'];

		// init select2 ajax option
		$processData = null;
		$ajax_data = null;
		$ajax_params_fields = null;
		if (isset($options['ajax'])) {
			// prepare ajax request parameters
			$urlparams = array();
			if (isset($options['paramAjax']) && is_array($options['paramAjax'])) {
				foreach ($options['paramAjax'] as $key => $value) {
					// check if value is a field ID ...
					$value_is_field = false;
					foreach ($formOptions['fields'] as $field) {
						if ($value == $field['id']) {
							$ajax_params_fields[] = $value;
							$value_is_field = true;
							break;
						}
					}

					// ... if yes, ajax parameter will be its content
					if ($value_is_field) {
						$urlparams[] = "$key: $('#$value').val()";
					}
					// ... otherwise, ajax parameter will be a constant
					else {
						$urlparams[] = "$key: ".urlencode($value);
					}
				}
			}

			// finalize ajax request parameters
			$ajax_data = 'function (term, page) {
				return {
					q: term.term,
					page_limit: 10,
					'.implode(", ", $urlparams).'
				};
			}';

			// process ajax response if needed
			if (isset($options['processData'])) {
				$processData = $options['processData'];
				$select2Options['ajax']['processResults'] = '##PROCESSDATA##';  // will be parsed as function
			}

			// add ajax options to select2Options
			$select2Options['ajax'] = array(
				'url' => $options['ajax'],
				'datatype' => 'json',
				'data' => '##AJAXDATA##'
			);
		}

		// init templateResult and templateSelection select2 options
		$templateResultFunction = null;
		$templateSelectionFunction = null;
		StyleBaseClass::checkOption($options['html'], false);
		StyleBaseClass::checkOption($options['templateResult'], null);
		StyleBaseClass::checkOption($options['templateResultFunction'], false);
		StyleBaseClass::checkOption($options['templateSelection'], null);
		StyleBaseClass::checkOption($options['templateSelectionFunction'], false);
		if (!is_null($options['templateResult']) || $options['html']) {
			// add options to select2
			$select2Options['templateResult'] = '##TPLRESULTFUNCTION##';  // will be parsed as function
			$select2Options['templateSelection'] = '##TPLRESULTFUNCTION##';  // will be parsed as function
			
			// set 'result.text' if templateResult is not set
			StyleBaseClass::checkOption($options['templateResult'], 'result.text');
			// add 'result.' in templateResult if missing
			if (isset($options['templateResult']) && substr($options['templateResult'], 0, 7) != 'result.') {
				$options['templateResult'] = 'result.'.$options['templateResult'];
			}
			
			// prepare templateResultFunction function
			if ($options['templateResultFunction']) {
				$templateResultFunction = $options['templateResult'];
			} elseif ($options['html']) {
				$templateResultFunction = 'function (result) {
					if ('.$options['templateResult'].' == undefined) {
						return $("<span>").text(result.text);
					} else {
						return $("<span>").html('.$options['templateResult'].');
					}
				}';
			} else {
				$templateResultFunction = 'function (result) {
					return result.text || '.$options['templateResult'].';
				}';
			}

			// override templateSelection select2 options
			if (!is_null($options['templateSelection'])) {
				// override option into select2
				$select2Options['templateSelection'] = '##TPLSELECTIONFUNCTION##';  // will be parsed as function
				
				// add 'result.' in templateSelection if missing
				if (isset($options['templateSelection']) && substr($options['templateSelection'], 0, 7) != 'result.') {
					$options['templateSelection'] = 'result.'.$options['templateSelection'];
				}
				
				// prepare templateSelectionFunction function
				if ($options['templateSelectionFunction']) {
					$templateSelectionFunction = $options['templateSelection'];
				} elseif ($options['html']) {
					$templateSelectionFunction = 'function (result) {
						if ('.$options['templateSelection'].' == undefined) {
							return $("<span>").text(result.text);
						} else {
							return $("<span>").html('.$options['templateSelection'].');
						}
					}';
				} else {
					$templateSelectionFunction = 'function (result) {
						return result.text || '.$options['templateSelection'].';
					}';
				}
			}
		}

		// output
		if (!$options['noinit']) {
			$select2JsonOptions = json_encode($select2Options);

			// parse functions
			if (!is_array($data)) {
				$select2JsonOptions = str_replace('"##DATA##"', $data, $select2JsonOptions);
			}
			$select2JsonOptions = str_replace('"##AJAXDATA##"', $ajax_data, $select2JsonOptions);
			$select2JsonOptions = str_replace('"##TPLRESULTFUNCTION##"', $templateResultFunction, $select2JsonOptions);
			$select2JsonOptions = str_replace('"##TPLSELECTIONFUNCTION##"', $templateSelectionFunction, $select2JsonOptions);
			$select2JsonOptions = str_replace('"##PROCESSDATA##"', $processData, $select2JsonOptions);
			$select2JsonOptions = str_replace('"##MATCHERFUNCTION##"', $matcherFunction, $select2JsonOptions);

			echo '
				<script type="text/javascript">
					$(function() {
						select2' . preg_replace("/[^A-Za-z0-9]/", "", $options['id']) . ' = $("#' . $options['id'] . '").select2('.$select2JsonOptions.')
						.on("select2:open", function (e) {
							$(this).parents(".controls").find("span.has-error").remove();
							$(this).removeClass("has-error is-invalid");
						});
					});
					' . (isset($options['globalData']) && $options['globalData'] ? 'var selectOptions_' . $options['id'] . ' = ' . json_encode($data) : '') . '
					' . (!is_null($ajax_params_fields) && count($ajax_params_fields) > 0 ? '$("#' . $options['id'] . '").prop("disabled", true)' : '') . '
				</script>' . "\n\n";
			if (!is_null($ajax_params_fields)) {
				foreach ($ajax_params_fields as $field) {
					echo '<script>
						$("#'.$field.'").change(function() {
							if ($(this).val().length > 0 && $(this).val() != "Invalid date") {
								$("#' . $options['id'] . '").prop("disabled", false);
							} else {
								$("#' . $options['id'] . '").prop("disabled", true);
							}
						});
					</script>';
				}
			}
		}
		
		// VALUE
		if (isset($options['value']) && !is_array($options['value'])) {
			$options['value'] = array($options['value']);
		}
		StyleBaseClass::checkOption($options['_key_values'], 'id');
		if (isset($options['value']) && is_array($options['value']) && count($options['value']) > 0) {
			echo '<script type="text/javascript">' . "\n";
			echo '$(function() {' . "\n";
			if (is_array($data)) {
				foreach ($options['value'] as $val) {
					$exists = false;
					foreach ($data as $d) {
						if (isset($d['id']) && $d['id'] == $val) {
							$exists = true;
						}
					}
					if (!$exists) {
						echo 'select2' . preg_replace("/[^A-Za-z0-9]/", "", $options['id']) . '.select2().append("<option value=\"' . StyleBaseClass::jsReplace($val) . '\">' . StyleBaseClass::jsReplace($val) . '</option>");' . "\n";
					}
				}
			}
			if (!empty($options['value'][0]) && is_array($options['value'][0])) {
				// TODO: manca supporto a prepopolamento multiplo delle select ajax
				echo 'select2' . preg_replace("/[^A-Za-z0-9]/", "", $options['id']) . '.val("' . StyleBaseClass::jsReplace($options['value'][0][$options['_key_values']]) . '").trigger("change");' . "\n";
			} else {
				echo 'select2' . preg_replace("/[^A-Za-z0-9]/", "", $options['id']) . '.val(["' . StyleBaseClass::jsReplace(implode('","', $options['value'])) . '"]).trigger("change");' . "\n";
			}
			echo '});</script>';
		}
		
		// add field to form fields
		$formOptions['fields'][] = array(
			'name' => $options['name'],
			'id' => $options['id'],
			'type' => 'select',
			'values' => $values,
			'noinit' => $options['noinit']
		);
	}

	/* ***** variante: select ajax ***** */
	static function selectAjaxBase($ajaxUrl, $keys_labels, $key_values, &$options) {
		$process_function = 'processResults'.rand(100000,999999);
		$options['processData'] = $process_function;
		if ($key_values != 'id') {
			echo '<script>
				function '.$process_function.' (data) {
					return {
						results: $.map(data.items, function (item) {
							item.id = item.'.$key_values.';
							return item;
						})
					}
				}
				</script>';
		} else {
			echo '<script>
				function '.$process_function.' (data) {
					return {
						results: data.items
					}
				}
				</script>';
		}

		if ($keys_labels != 'text') {
			$options['templateResult'] = $keys_labels;
		}

		$options['ajax'] = $ajaxUrl;
		$options['minLength'] = 2;
	}

	/* ***** variante: select recordset ***** */
	static function selectRSBase($rs, $columns_labels, $columns_values, &$values) {
		if (count($rs) > 0) {
			foreach ($rs as $row) {

				// get option labels
				if (is_array($columns_labels)) {
					$opt_label = '';
					foreach ($columns_labels as $collabel) {
						if (isset($row[$collabel]))
							$opt_label .= $row[$collabel];
						else
							$opt_label .= $collabel;
					}
				} else
					$opt_label = $row[$columns_labels];

				// get option value
				if (is_array($columns_values)) {
					$opt_value = '';
					foreach ($columns_values as $collabel) {
						if (isset($row[$collabel]))
							$opt_value .= $row[$collabel];
						else
							$opt_value .= $collabel;
					}
				} else
					$opt_value = $row[$columns_values];

				// append option
				$values[$opt_value] = $opt_label;
			};
		}
	}

	/* ***************** HIDDEN ***************** */

	static function hidden($name, $value, $id = null) {
		$formOptions = &self::$forms[self::$openForm];

		// INITIALIZE
		StyleBaseClass::checkOption($id, 'hidden'.rand(100000,999999));

		echo '<input name="' . $name . '" id="' . $id . '" type="hidden">
			<script type="text/javascript">$("#' . $id . '").val(\'' . StyleBaseClass::jsReplace($value) . '\');</script>';

		// add field to form fields
		$formOptions['fields'][] = array('name' => $name, 'id' => $id, 'type' => 'hidden');
	}

	/* ***************** FILL FORM ***************** */

	/**
	* Function to fill-in automatically a form based with array's values
	* @param array $set dataset used to fill the form
	*/
	static function fillForm($set, $debug = false) {
		$istruzioni = array();

		foreach (self::$forms[self::$openForm]['fields'] as $form_field) {
			$form_field['name'] = str_replace('[]', '', $form_field['name']);

			// controlla se il campo deve essere riempito
			$field_name = $form_field['name'];
			$fill_value = null;
			if (isset($set[$field_name])) {
				$fill_value = $set[$field_name];
			}

			// controlla datepicker
			elseif (substr($form_field['name'], -3) == '_in') {
				$field_name = substr($form_field['name'], 0, -3);
				if (isset($set[$field_name])) {
					$fill_value = date('d/m/Y', strtotime($set[$field_name]));
				}
			}

			// fill form
			if (is_array($fill_value)) {
				switch ($form_field['type']) {
					case 'select':
						if (!$form_field['noinit']) {
							foreach ($fill_value as $val) {
								if (!array_key_exists($val, $form_field['values'])) {
									$istruzioni[] = 'select2' . preg_replace("/[^A-Za-z0-9]/", "", $form_field['id']) . '.append("<option value=\"' . StyleBaseClass::jsReplace($val) . '\">' . StyleBaseClass::jsReplace($val) . '</option>");' . "\n";
								}
							}
							if (count($fill_value) > 0) {
								$istruzioni[] = 'select2' . preg_replace("/[^A-Za-z0-9]/", "", $form_field['id']) . '.val(["' . StyleBaseClass::jsReplace(implode('","', $fill_value)) . '"]).trigger("change");' . "\n";
							}
						}
						break;
						
					case 'checkbox':
						if (strlen($form_field['value']) > 0 && in_array($form_field['value'], $fill_value)) {
							$istruzioni[] = '$("#' . $form_field['id'] . '[value=\\"'.$form_field['value'].'\\"]").prop("checked", true);' . "\n";
						}
						break;

					default:
						; //do nothing
				}
			}
			elseif (strlen($fill_value) > 0) {
				switch ($form_field['type']) {
					case 'hidden':
					case 'input':
					case 'textarea':
						$istruzioni[] = '$("#' . $form_field['id'] . '").val(\'' . StyleBaseClass::jsReplace($fill_value) . '\');' . "\n";
						break;

					case 'select':
						if (!$form_field['noinit']) {
							if (!array_key_exists($fill_value, $form_field['values'])) {
								$istruzioni[] = 'select2' . preg_replace("/[^A-Za-z0-9]/", "", $form_field['id']) . '.append("<option value=\"' . StyleBaseClass::jsReplace($fill_value) . '\">' . StyleBaseClass::jsReplace($fill_value) . '</option>");' . "\n";
							}
							$istruzioni[] = 'select2' . preg_replace("/[^A-Za-z0-9]/", "", $form_field['id']) . '.val(\'' . StyleBaseClass::jsReplace($fill_value) . '\').trigger("change");' . "\n";
						}
						break;

					case 'checkbox':
						if (strlen($form_field['value']) > 0 && $form_field['value'] == $fill_value) {
							$istruzioni[] = '$("#' . $form_field['id'] . '[value=\\"'.$form_field['value'].'\\"]").prop("checked", true);' . "\n";
						} elseif (is_null($form_field['value']) && $fill_value !== 0) {
							$istruzioni[] = '$("#' . $form_field['id'] . '").prop("checked", true);' . "\n";
						}
						break;

					default:
						; //do nothing
				}
			}
		}

		echo '<script type="text/javascript">
		$(function() {' . "\n";
		echo implode("\n", $istruzioni);
		echo '});
		</script>';

		if ($debug) {
			echo '<pre>';
			echo '### FORM ###'."\n";
			print_r(self::$forms[self::$openForm]['fields']);
			echo "\n".'### DATASET ###'."\n";
			print_r($set);
			echo "\n".'## RISULTATO ###'."\n";
			echo implode("\n", $istruzioni);
			echo '</pre>';
		}
	}

	/* ***************** SUBMIT FORM ***************** */

	static function submitOnlyButtons($save_icon, $save_label, $cancel_btn = true, $other_actions = array(), $save_btn_class = 'primary') {
		$formOptions = &self::$forms[self::$openForm];
		
		$other_actions_str = '';
		if (!empty($other_actions)) {
			foreach ($other_actions as $action) {
				if (isset($action['modaldismiss']) && $action['modaldismiss']) {
					$modaldismiss = ' data-dismiss="modal"';
					$action['href'] = '#';
				} else {
					$modaldismiss = '';
				}

				if (!empty($action['icon']))
					$icon = '<span class="' . $action['icon'] . '"></span> ';
				else
					$icon = '';

				if (!empty($action['class']))
					$class = 'btn-'.$action['class'];
				else
					$class = '';

				// confirm text
				StyleBaseClass::checkOption($action['confirm'], false);
				StyleBaseClass::checkOption($action['confirm_text'], '');
				if (isset($action['confirm']) && isset($action['confirm_text']) && $action['confirm'] && strlen($action['confirm_text'])) {
					$warn = ' onclick="if(!confirm(\''.str_replace("'", "\\'", $action['confirm_text']).'\')) return false;"';
				} elseif (isset($action['confirm']) && $action['confirm']) {
					$warn = ' onclick="if(!confirm(\''.__('universal_stylelib::stylelib.delete_question').'\')) return false;"';
				} else {
					$warn = '';
				}

				$other_actions_str .= '<a class="btn '.$class.'"'.$warn.' href="' . $action['href'] . '"' . $modaldismiss . '>' . $icon . $action['label'] . '</a> ';
			}
		}

		echo '
			<button class="btn btn-' . $save_btn_class . ' disabled" type="submit">
				<i class="' . $save_icon . '"></i> ' . $save_label . '
			</button>
			' . ($cancel_btn ? '<a class="btn btn-white" href="javascript:window.history.back()">' . __('universal_stylelib::stylelib.cancel') . '</a>' : '') . '
			' . $other_actions_str.'
			<script>$(function() {$(\'#'.self::$openForm.' button[type=submit]\').removeClass(\'disabled\')})</script>';
	}

	static function submitCustom($save_icon, $save_label, $cancel_btn = true, $other_actions = array(), $save_btn_class = 'primary') {
		$formOptions = &self::$forms[self::$openForm];

		$class = str_replace('md-', 'md-offset-', $formOptions['classLabel']);
		$class .= ' '.$formOptions['classInput'];

		if (strlen(trim($class)) == 0) {
			$class = 'col-xs-12 col-12';
		}

		echo '
			<div class="form-actions formactions-padding-sm">
				<div class="row">
					<div class="'.$class.'">
						';
						NFormBase::submitOnlyButtons($save_icon, $save_label, $cancel_btn, $other_actions, $save_btn_class);
						echo '
					</div>
				</div>
			</div>';
	}

	static function submit() {
		NFormBase::submitCustom('fa fa-save', __('universal_stylelib::stylelib.save'), true, array());
	}

	static function submitAsAjax($redirect, $other_actions = array()) {
		NFormBase::submitCustom('fa fa-save', 'Salva', true, $other_actions);
		echo '
			<div class="row" id="progress-bar-container" style="display: none">
			<label class="'.$formOptions['classLabel'].'" control-label">In corso...</label>
			<div class="col-md-10" style="padding-top: 7px">
				<div class="progress">
					<div class="progress-bar progress-bar-success"
						 style="width: 0%;">0%</div>
				</div>

				<div class="modal fade" id="formUploadModal" data-redirect="'.$redirect.'">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">'.__('universal_stylelib::stylelib.ajax_warning').'</h4>
								</div>
								<div class="modal-body">
									<p>
										<span class="text-danger fa fa-exclamation-circle"></span>
										'.__('universal_stylelib::stylelib.ajax_message').'
								<div id="status" style="max-height: 300px; overflow: auto">
								</div>
							</div>
							<div class="modal-footer">
								<a href="<?php echo $redirect ?>"
									class="btn btn-primary">'.__('universal_stylelib::stylelib.continue').'</a>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
			</div>
		</div>
		';
	}

}
