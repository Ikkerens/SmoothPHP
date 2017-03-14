<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * SubstractionOperatorElement.php
 * Handles substracting 2 elements (-)
 */

namespace SmoothPHP\Framework\Templates\Elements\Operators;

use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class SubstractionOperatorElement extends ArithmeticOperatorElement {

    public function getPriority() {
        return 1;
    }

    public function optimize(CompilerState $tpl) {
        $left = $this->left->optimize($tpl);
        $right = $this->right->optimize($tpl);

        if ($left instanceof PrimitiveElement && $right instanceof PrimitiveElement)
            return new PrimitiveElement($left->getValue() - $right->getValue());
        else
            return new self($left, $right);
    }
}
