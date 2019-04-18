<?php
/**
 * Created by PhpStorm.
 * User: yhl
 * Date: 2019/4/18
 * Time: 11:18
 */

namespace App\HttpController;
use EasySwoole\Http\AbstractInterface\Controller;

class Index extends Controller
{
    function index()
    {
        // TODO: Implement index() method.
        $this->response()->write('three');
    }

}