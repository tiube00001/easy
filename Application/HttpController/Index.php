<?php
/**
 * Created by PhpStorm.
 * User: yhl
 * Date: 2018/3/26
 * Time: 22:29
 */

namespace App\HttpController;

use EasySwoole\Core\Http\AbstractInterface\Controller;

class Index extends Controller
{
    public function index()
    {

        // TODO: Implement index() method.
        $this->response()->write('bbbb');
    }
}