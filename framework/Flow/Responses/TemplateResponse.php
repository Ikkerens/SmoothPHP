<?php

/*!
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * TemplateResponse.php
 * A template-based response, passed to the template engine.
 */

namespace SmoothPHP\Framework\Flow\Responses;

use PHPWee\HtmlMin;
use SmoothPHP\Framework\Core\Kernel;
use SmoothPHP\Framework\Flow\Requests\Request;

class TemplateResponse extends Response {
    private $built;
    private $args;

    public function __construct($controllerResponse, array $args = array()) {
        parent::__construct($controllerResponse);
        $this->args = $args;
    }

    public function build(Kernel $kernel, Request $request) {
        $this->args['assets'] = $kernel->getAssetsRegister();
        $this->args['route'] = $kernel->getRouteDatabase();
        $this->args['language'] = $kernel->getLanguageRepository();
        if ($kernel->getConfig()->authentication_enabled) {
            $auth = $kernel->getAuthenticationManager();
            $this->args['auth'] = $auth;
            $this->args['user'] = $auth->getActiveUser();
        }
        $this->built = $kernel->getTemplateEngine()->fetch($this->controllerResponse, $this->args);
    }

    protected function sendHeaders() {
        parent::sendHeaders();
        header('Content-Type: text/html; charset=utf-8');
    }

    protected function sendBody() {
        echo $this->built;
    }

}
