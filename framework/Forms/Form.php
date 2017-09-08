<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * Form.php
 * A built form, which can be used for printing and validating the form.
 */

namespace SmoothPHP\Framework\Forms;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Containers\FormContainer;
use SmoothPHP\Framework\Forms\Containers\FormHeader;

class Form extends FormContainer {
	private $action;

	private $hasResult;
	private $failReasons;

	public function __construct($action, array $headerArgs, array $elements) {
		parent::__construct([
			'header'     => new FormHeader($this, $headerArgs),
			'tablestart' => '<table>',
			'inputs'     => new FormContainer($elements),
			'tableend'   => '</table>',
			'footer'     => '</form>'
		]);
		$this->hasResult = false;
		$this->action = $action;
	}

	public function getAction() {
		if (!isset($this->action))
			return $_SERVER['REQUEST_URI'];
		return $this->action;
	}

	public function hasField($key) {
		return isset($this->inputs->{$key});
	}

	public function setValue($key, $value) {
		$this->setAttribute($key, 'value', $value);
	}

	public function setAttribute($key, $attribute, $value) {
		$this->inputs->{$key}->input->setAttribute($attribute, $value);
	}

	public function setAction() {
		global $kernel;
		$action = func_get_arg(0);

		if ($kernel->getRouteDatabase()->getRoute($action))
			$this->action = call_user_func_array([$kernel->getRouteDatabase(), 'buildPath'], func_get_args());
		else
			$this->action = $action;
	}

	public function validate(Request $request) {
		if (!$request->post->hasData())
			return;

		$this->hasResult = true;
		$this->failReasons = [];
		$this->checkConstraint($request, null, null, null, $this);
	}

	public function hasResult() {
		return $this->hasResult;
	}

	public function isValid() {
		if (!$this->hasResult)
			return true;
		return isset($this->failReasons) && count($this->failReasons) == 0;
	}

	public function addErrorMessage($message) {
		if (!isset($this->failReasons))
			throw new \Exception('Form has not yet been validated.');
		$this->failReasons[] = $message;
	}

	public function getErrorMessages() {
		return $this->failReasons;
	}

}