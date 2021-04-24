<?php

class Route 
{
    private $request;
    private $supportedHttpMethods = ["GET", "POST"];

    public function __construct(IRequest $request) {
        $this->request = $request;
    }

    public function __call($name, $args) {
        list($route, $method) = $args;
        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }
        $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '')
        {
            return '/';
        }
        return $result;
    }

    private function invalidMethodHandler()
    {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler()
    {
        header("{$this->request->serverProtocol} 404 Not Found");
    }


    public function resolve() {
        $methodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formattedRoute = $this->formatRoute($this->request->requestUri);
        $method = $methodDictionary[$formattedRoute];
        if (is_null($method)) {
            $this->defaultRequestHandler();
            return;
        }

        echo call_user_func($method, array($this->request));
    }

    public function __destruct() {
        $this->resolve();
    }
}