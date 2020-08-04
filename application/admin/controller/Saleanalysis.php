<?php
/**
 *
 * Created by PhpStorm.
 * User: spirit_cjw@163.com
 * Date: 2020/3/18
 * Time: 14:18
 */

namespace app\admin\controller;

use app\service\CSV;
use app\service\SaleAnalysisService;
use app\service\TimeStamp;
use base\Page;
use think\Db;

class Saleanalysis extends Common
{
    protected $service;

    public function __construct()
    {
        // 调用父类前置方法
        parent::__construct();
        $this->service = new SaleAnalysisService();

        // 参数
        $this->assign('params', input());

        // 订单状态
        $this->assign('order_status', $this->service->orderStatus());
        // 支付状态
        $this->assign('pay_status', $this->service->payStatus());

        // 登录校验
        $this->IsLogin();
//
        // 权限校验
        $this->IsPower();
    }

    /**
     * 销售分析
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function Index()
    {
        $params = input();
        // 获得时间
        $timeInfo = TimeStamp::search($params['start_time'] ?? '', $params['end_time'] ?? '');
        // 条件和字段
        $field = ['pay_time', 'pay_price', 'buy_number_count'];
        $where = [
            ['pay_time', 'between', $timeInfo['time']],
            ['pay_status', '<>', '0'], //支付状态（0未支付, 1已支付, 2已退款, 3部分退款）
        ];

        // 查询
        $list = Db::name('order')->field($field)->where($where)->select();

        // 统计和赋值
        $sales_number = $sales_amount = $timeInfo['coordinates'];
        if (!empty($list)) {
            foreach ($list as $value) {
                if ($value['pay_time']) {
                    $key = date('n/j', $value['pay_time']);
                    $sales_number[$key] += $value['buy_number_count'];
                    $sales_amount[$key] += $value['pay_price'];
                }
            }
        }

        // 销售数量
        $this->assign('sales_number', [
            'total' => (int)array_sum($sales_number),
            'format_time' => $timeInfo['format'],
            'list' => $sales_number,
        ]);
        // 销售总额
        $this->assign('sales_amount', [
            'total' => sprintf("%.2f", array_sum($sales_amount)),
            'format_time' => $timeInfo['format'],
            'list' => $sales_amount,
        ]);

        return $this->fetch();
    }

    /**
     * 销售明细
     * @return mixed
     */
    public function Detail()
    {
        return $this->fetch();
    }
    /**
     * 销售明细-概况
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function DetailBasic()
    {
        // 销售概况
        $list = $this->service->overview(input());
        $this->assign('data_list', $list);
        // 分页
        $get = input('get.');
        $page_params = array(
            'number'    =>  MyC('admin_page_number', 10, true),
            'total'     =>  $list['total'] ?? 0,
            'where'     =>  $get,
            'page'      =>  isset($get['page']) ? intval($get['page']) : 1,
            'url'       =>  MyUrl('admin/Saleanalysis/DetailBasic'),
        );
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        return $this->fetch('detail-basic');
    }

    /**
     * 销售明细-明细
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function DetailIndex()
    {
        // 销售明细
        $data_list = $this->service->detailIndex(input());

        $this->assign('data_list', $data_list);
        // 分页
        $get = input('get.');
        $page_params = array(
            'number'    =>  MyC('admin_page_number', 10, true),
            'total'     =>  $data_list['total'] ?? 0,
            'where'     =>  $get,
            'page'      =>  isset($get['page']) ? intval($get['page']) : 1,
            'url'       =>  MyUrl('admin/Saleanalysis/DetailBasic'),
        );
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        return $this->fetch('detail-index');
    }

    /**
     * 销售明细-排行
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function DetailTop()
    {
        // 销售排行
        $data_list = $this->service->top(input());

        // 分页
        $get = input('get.');
        $page_params = array(
            'number'    =>  MyC('admin_page_number', 10, true),
            'total'     =>  $data_list['total'] ?? 0,
            'where'     =>  $get,
            'page'      =>  isset($get['page']) ? intval($get['page']) : 1,
            'url'       =>  MyUrl('admin/Saleanalysis/DetailTop'),
        );
        $page = new Page($page_params);
        $this->assign('page_html', $page->GetPageHtml());

        $this->assign('data_list', $data_list);

        return $this->fetch('detail-top');
    }

    /**
     * 销售明细-明细-导出
     * @throws \think\exception\DbException
     */
    public function csvDetail()
    {
        $get = input();
        $get['is_export'] = true;
        $list = $this->service->detail($get);
        $title = ['商品名称', '订单号', '状态', '数量', '售价', '总金额', '售出日期'];
        CSV::outputCsv($title, $list, '销售明细');
    }

    /**
     * 销售明细-排行-导出
     * @throws \think\exception\DbException
     */
    public function csvTop()
    {
        $get = input();
        $get['is_export'] = true;
        $list = $this->service->top($get);
        $title = ['商品名称', '数量', '总金额', '平均售价'];
        CSV::outputCsv($title, $list, '销售排行');
    }

    /**
     * 销售明细-概况-导出
     * @throws \think\exception\DbException
     */
    public function csvOverview()
    {
        $get = input();
        $get['is_export'] = true;
        $list = $this->service->overview($get);
        $title = ['商品名称', '分类', '数量', '单价', '总金额', '售出日期'];
        CSV::outputCsv($title, $list, '销售概况');
    }
}
