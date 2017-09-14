<?php

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class OrOperatorElement extends ArithmeticOperatorElement {

	public function getPriority() {
		return 5;
	}

	public function optimize(CompilerState $tpl) {
		$left = $this->left->optimize($tpl);

		if ($left instanceof PrimitiveElement && $left->getValue())
			return new PrimitiveElement(true); // Cancel out early before we start calling $right

		$right = $this->right->optimize($tpl);

		if ($left instanceof PrimitiveElement && $right instanceof PrimitiveElement)
			return new PrimitiveElement($left->getValue() || $right->getValue());
		else
			return new self($left, $right);
	}

}