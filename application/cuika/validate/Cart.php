<?php
/**
 * Created by yang
 * User: bonzaphp@gmail.com
 * Date: 2020-04-13
 * Time: 11:38
 */

namespace app\cuika\validate;

use think\Validate;

/**
 * 购物车验证器
 * Class Address
 * @author bonzaphp@gmail.com
 * @Date 2020-04-13 11:45
 * @package app\cuika\validate
 */
class Cart extends Validate
{
    /* 验证规则 */
    protected $rule = [
        'goods_id' => ['require'],
        'stock'    => ['require', 'min' => 1, 'integer'],
    ];
    /* 提示信息 */
    protected $message = [
        'goods_id.require' => '姓名不能为空',
        'stock.require'    => '省不能为空',
        'stock.min'        => '最少为1件',
        'stock.integer'    => '数量为整数',
    ];
    /* 场景 */
    protected $scene = [
        'save' => ['goods_id', 'stock'],
    ];


}