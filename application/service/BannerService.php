<?php
// +----------------------------------------------------------------------
// | ShopXO 国内领先企业级B2C免费开源电商系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011~2019 http://1000-x.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Devil
// +----------------------------------------------------------------------
namespace app\service;

use think\Db;
use app\service\ResourcesService;

/**
 * 轮播服务层
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class BannerService
{
    /**
     * 获取轮播
     * @author   Devil
     * @blog    http://1000-x.cn/
     * @version 1.0.0
     * @date    2018-08-29
     * @desc    description
     * @param   [array]          $params [输入参数]
     */
    public static function Banner($params = [])
    {
        // 平台
        $platform = APPLICATION_CLIENT_TYPE;

        // web端手机访问
        if($platform == 'pc' && IsMobile())
        {
            $platform = 'h5';
        }

        // 获取banner数据
        $banner = Db::name('Slide')->field('name,images_url,event_value,event_type,bg_color')->where(['platform'=>$platform, 'is_enable'=>1])->order('sort asc,id asc')->select();
        if(!empty($banner))
        {
            foreach($banner as &$v)
            {
                $v['images_url_old'] = $v['images_url'];
                $v['images_url'] = ResourcesService::AttachmentPathViewHandle($v['images_url']);
                $v['event_value'] = empty($v['event_value']) ? null : $v['event_value'];
            }
        }
        return $banner;
    }
}
?>