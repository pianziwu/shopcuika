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
 * 地址验证器
 * Class Address
 * @author bonzaphp@gmail.com
 * @Date 2020-04-13 11:45
 * @package app\cuika\validate
 */
class Address extends Validate
{
    /* 验证规则 */
    protected $rule = [
        'name'     => ['require'],
        'tel'      => ['require', 'mobile'],
        'province' => ['require'],
        'city'     => ['require'],
        'county'   => ['requireCallback:checkSpecial','number'],
        'address'  => ['require', 'min' => 2],
    ];
    /* 提示信息 */
    protected $message = [
        'name.require'     => '姓名不能为空',
        'province.require' => '省不能为空',
        'city.require'     => '城市不能为空',
        'county.requireCallback'   => '区/县不能为空',
        'address.require'  => '详细地址不能为空',
    ];
    /* 场景 */
    protected $scene = [
        'save' => ['name', 'tel', 'province', 'city', 'county', 'address'],
    ];

    /**
     * 检查省市中特别的
     * @param  array  $data
     * @return bool
     * @author bonzaphp@gmail.com
     */
    public function checkSpecial(string $value, array $data): bool
    {
        $province = \app\common\model\Address::findOrEmpty($data['province']);
        if ($province->isEmpty()) {
            return false;
        }
        $data['province'] = $province->name;
        return (strpos($data['province'], '香港') === false && strpos($data['province'], '澳门') === false && strpos($data['province'], '台湾') === false);
    }

}