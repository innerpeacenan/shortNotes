<?php
namespace tests;

use nxn\web\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{

    public function testRedirect()
    {
        $response = new Response();
        $response->redirect('/index/index');
    }

}
