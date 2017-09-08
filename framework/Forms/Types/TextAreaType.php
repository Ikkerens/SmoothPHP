<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * StringType.php
 * Type for html's input[type="text"]
 */

namespace SmoothPHP\Framework\Forms\Types;

use SmoothPHP\Framework\Forms\Containers\Type;

class TextAreaType extends Type {

	public function __construct($field) {
		parent::__construct($field);
		$this->options = array_replace_recursive($this->options, [
			'attr' => [
				'placeholder' => '...'
			]
		]);
	}

	public function initialize(array $options) {
		unset($options['attr']['type']);
		parent::initialize($options);
	}

	public function __toString() {
		$attributes = $this->options['attr'];

		$attributes['id'] = $this->field;
		$attributes['name'] = $this->field;

		if (isset($attributes['value'])) {
			$value = $attributes['value'];
			unset($attributes['value']);
		} else {
			$value = '';
		}

		return sprintf('<textarea %s>%s</textarea>', $this->transformAttributes($attributes), $value);
	}

}