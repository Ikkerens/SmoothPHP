<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2016 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AssignElement.php
 * Element that will assign a variable to the currently active scope
 */

namespace SmoothPHP\Framework\Templates\Elements\Commands;

use SmoothPHP\Framework\Templates\TemplateCompiler;
use SmoothPHP\Framework\Templates\Compiler\TemplateLexer;
use SmoothPHP\Framework\Templates\Elements\Chain;

use SmoothPHP\Framework\Templates\Compiler\TemplateState;
use SmoothPHP\Framework\Templates\Elements\Element;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class AssignElement extends Element {
    private $varName;
    private $value;
    
    public static function handle(TemplateCompiler $compiler, TemplateLexer $command, TemplateLexer $lexer, Chain $chain) {
        $command->skipWhitespace();
        $command->peek('$');
        $varName = $command->readAlphaNumeric();

        $command->skipWhitespace();
        $value = new Chain();
        $compiler->handleCommand($command, $lexer, $value, $stackEnd);
        $chain->addElement(new self($varName, TemplateCompiler::flatten($value)));

    }
    
    public function __construct($varName, Element $value) {
        $this->varName = $varName;
        $this->value = $value;
    }
    
    public function simplify(TemplateState $tpl) {
        $this->value = $this->value->simplify($tpl);
        
        if ($this->value instanceof PrimitiveElement)
            $tpl->vars[$this->varName] = $this->value;
        
        return $this;
    }
}