<?php
namespace DeployStudio\Style\Angle;

use DeployStudio\Style\StyleBaseClass;
use DeployStudio\Style\Base\NFormBase;


class NFormGroup extends \DeployStudio\Style\Base\NFormGroup {
	static function open($action, $multipart = false, $id = "", $get = false,
		$validationClass = "validate-form-custom") {
		self::openForm ($action, $multipart, $id, $get, $validationClass, NFormBase::FORM_TYPE_VERTICAL, 4);
	}

	static function checkboxesBuild ($col, $label, $outputArr, $checkboxTags, $options) {
		$formOptions = &self::$forms[self::$openForm];

		$label_output = '<label>' . $label.' '.$outputArr['requiredLabel'] . '</label>';
		$build_field ='<div class="'.$col.' mb-3">'.'
			'.$label_output.'
			<div class="controls">
				<div class="'.implode(' ', $outputArr['additionalDivClasses']).'">
			
				' . (!empty($outputArr['additionalDivClasses']) ? '<div class="' . implode(' ', $outputArr['additionalDivClasses']) . '">' : '') . '
			
				' . implode("\n", $checkboxTags) . '
			
				' . (!empty($options['description']) ? '<p class="help-block"><small class="text-muted">' . $options['description'] . '</small>' : '') . '
				' . (!empty($outputArr['additionalDivClasses']) ? '</div>' : '') . '
			
				</div>
			</div>
		</div>' . "\n\n";
		echo $build_field;
	}

	static function select($col, $label, $name, $values, $required = false, $options = array()) {
		StyleBaseClass::checkOption($options['theme'], 'bootstrap4');
		parent::select($col, $label, $name, $values, $required, $options);
	}

	static function selectRs($col, $label, $name, $rs, $columns_labels, $columns_values, $required = false, $options = array()) {
		StyleBaseClass::checkOption($options['theme'], 'bootstrap4');
		parent::selectRs($col, $label, $name, $rs, $columns_labels, $columns_values, $required, $options);
	}
}