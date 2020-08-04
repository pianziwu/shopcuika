<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/4/2
 * Time: 15:38
 */

namespace app\cuika\controller;


class Problem extends Common
{
    // 如何挑选
    public function choose()
    {
        return $this->fetch('faq');
    }

}