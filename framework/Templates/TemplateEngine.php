<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateEngine.php
 * Template engine, responsible for invoking the compiler, caching and returning the output page
 */

namespace SmoothPHP\Framework\Templates;

use SmoothPHP\Framework\Cache\Builder\FileCacheProvider;
use SmoothPHP\Framework\Cache\Builder\RuntimeCacheProvider;
use SmoothPHP\Framework\Templates\Compiler\CompilerState;
use SmoothPHP\Framework\Templates\Elements\PrimitiveElement;

class TemplateEngine {
    private $compiler;

    private $runtimeCache;

    public function __construct() {
        $this->compiler = new TemplateCompiler();

        $compileCache = new FileCacheProvider('ctpl', 'ctpl',
            function ($fileName) {
                return $this->compiler->compile($fileName);
            },
            function ($fileName) {
                return unserialize(gzinflate(file_get_contents($fileName)));
            },
            function ($fileName, $data) {
                file_put_contents($fileName, gzdeflate(serialize($data)));
            }
        );
        $this->runtimeCache = RuntimeCacheProvider::create(function ($fileName) use ($compileCache) {
            return $compileCache->fetch($fileName);
        });
    }

    public function fetch($templateName, array $args) {
        $path = sprintf('%ssrc/templates/%s', __ROOT__, $templateName);
        $template = $this->runtimeCache->fetch($path);

        return $this->prepareOutput($template, $args);
    }

    public function simpleFetch($absoluteFile, array $args = array()) {
        return $this->prepareOutput($this->compiler->compile($absoluteFile), $args);
    }

    private function prepareOutput($template, array $args) {
        $state = new CompilerState();
        foreach($args as $key => $value)
            $state->vars->{$key} = new PrimitiveElement($value);
        $state->performCalls = true;
        $template = $template->optimize($state);

        // Gather output and return
        ob_start();
        $template->output($state);
        return ob_get_clean();
    }

}