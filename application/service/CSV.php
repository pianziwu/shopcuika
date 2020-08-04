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

/**
 * 时间
 * @author   Devil
 * @blog     http://1000-x.cn/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class CSV
{
    /**
     * csv导出
     * @param array $title 标题(一维数组)
     * @param array $list 内容(二维数组)
     * @param string $fileName 文件名
     */
    public static function outputCsv($title, $list, $fileName)
    {
        $tit = $tArr = [];
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . sprintf("%s%s.csv", $fileName, date("Y-m-d His")));
        header('Cache-Control: max-age=0');
        $file = fopen('php://output', "a");
        $limit = 1000;
        $calc = 0;
        foreach ($title as $v) {
            $tit[] = iconv('UTF-8', 'GB2312//IGNORE', $v);
        }
        fputcsv($file, $tit);
        foreach ($list as $v) {
            $calc++;
            if ($limit == $calc) {
                ob_flush();
                flush();
                $calc = 0;
            }
            foreach ($v as $t) {
                $tArr[] = iconv('UTF-8', 'GB2312//IGNORE', $t);
            }
            fputcsv($file, $tArr);
            unset($tArr);
        }
        unset($list);
        fclose($file);
        exit();
    }

}
