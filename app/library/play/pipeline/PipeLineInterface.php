<?php

namespace play\pipeline;


interface PipeLineInterface
{
    public function send($travelor);

    public function through($stops);

    public function via($method);

    public function then(\Closure $destination);
}