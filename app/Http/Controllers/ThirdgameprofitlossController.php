<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\ThirdGameProfitlossIndexRequest;
use Service\API\ThirdGame\Report\Creator;
use Service\API\ThirdGame\ThirdGame;
use Service\Models\User;
use Service\Models\UserGroup as ModelUserGroup;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Service\API\ThirdGame\ThirdGame as ApiThirdGame;

class ThirdgameprofitlossController extends Controller
{
    public function getIndex(Request $request)
    {
        $platforms = ApiThirdGame::getPlatform('', 'array', 0);
        $user_group = ModelUserGroup::all();
        return view('third-game-profitloss.index', [
            'user_group' => $user_group,
            'platforms' => $platforms,
            'id' => (int)$request->get('id', 0),
            'start_date' => Carbon::today()->format('Y-m-d 00:00:00'),
            'end_date' => Carbon::today()->format('Y-m-d 23:59:59'),
        ]);
    }

    public function postIndex(ThirdGameProfitlossIndexRequest $request)
    {
        $data = ['recordsTotal' => 0, 'data' => []];
        $param['order'] = $request->get('order');
        $param['columns'] = $request->get('columns');
        $param['user_group_id'] = (int)$request->get('user_group_id');
        $param['start_date'] = $request->get('start_date');
        $param['end_date'] = $request->get('end_date');
        $param['platform'] = $request->get('platform');
        $param['is_search'] = $request->get('is_search');
        $param['username'] = trim($request->get('username'));
        $param['user_id'] = (int)$request->get('user_id', 0);
        $param['show_zero'] = (int)$request->get('show_zero', 1);
        $export = (int)$request->get('export', 0);
        if ($param['username']) {
            $param['user_id'] = User::where('username', $param['username'])->value('id');
            if (!$param['user_id']) {
                return $data;
            }
        }
        if ($param['platform']) {
            $param['from'] = 'backend';

            if ($param['platform'] == 'all') {
                $profits = [];
                $all_platform = ThirdGame::getPlatform('', 'array', 0);
                foreach ($all_platform as $_row) {
                    $obj = Creator::factory($_row['ident']);
                    if ($obj === false) {
                        continue;
                    }
                    $profits[$_row['ident']] = $obj->getProfit($param);
                }

                $rows = $total = [];
                foreach ($profits as $_ident => $_row) {
                    foreach ($_row['data'] as $user_row) {
                        $user_id = $user_row['user_id'];
                        $rows[$user_id]['user_id'] = $user_id;
                        $rows[$user_id]['username'] = $user_row['username'];
                        $rows[$user_id]['user_observe'] = $user_row['user_observe'];
                        $user_row['bet'] = $user_row['bet'] ?? 0;
                        $user_row['win'] = $user_row['win'] ?? 0;
                        $user_row['user_win'] = $user_row['user_win'] ?? 0;
                        $user_row['admin_deduct'] = $user_row['admin_deduct'] ?? 0;
                        $user_row['chou_shui'] = $user_row['chou_shui'] ?? 0;
                        $user_row['ds'] = $user_row['ds'] ?? 0;
                        $rows[$user_id]['win'] = isset($rows[$user_id]['win']) ? $rows[$user_id]['win'] + $user_row['win'] : $user_row['win'];
                        $rows[$user_id]['user_win'] = isset($rows[$user_id]['user_win']) ? $rows[$user_id]['user_win'] + $user_row['user_win'] : $user_row['user_win'];
                        $rows[$user_id]['admin_deduct'] = isset($rows[$user_id]['admin_deduct']) ? $rows[$user_id]['admin_deduct'] + $user_row['admin_deduct'] : $user_row['admin_deduct'];
                        $rows[$user_id]['bet'] = isset($rows[$user_id]['bet']) ? $rows[$user_id]['bet'] + $user_row['bet'] : $user_row['bet'];
                        $rows[$user_id]['fd'] = isset($rows[$user_id]['fd']) ? $rows[$user_id]['fd'] + $user_row['fd'] : $user_row['fd'];
                        $rows[$user_id]['chou_shui'] = isset($rows[$user_id]['chou_shui']) ? $rows[$user_id]['chou_shui'] + $user_row['chou_shui'] : $user_row['chou_shui'];
                        $rows[$user_id]['ds'] = isset($rows[$user_id]['ds']) ? $rows[$user_id]['ds'] + $user_row['ds'] : $user_row['ds'];
                        $rows[$user_id]['real_win'] = isset($rows[$user_id]['real_win']) ? $rows[$user_id]['real_win'] + $user_row['real_win'] : $user_row['real_win'];
                    }
                    $t = $_row['total'];
                    $t['bet'] = $t['bet'] ?? 0;
                    $t['win'] = $t['win'] ?? 0;
                    $t['user_win'] = $t['user_win'] ?? 0;
                    $t['admin_deduct'] = $t['admin_deduct'] ?? 0;
                    $t['chou_shui'] = $t['chou_shui'] ?? 0;
                    $t['ds'] = $t['ds'] ?? 0;
                    $total['bet'] = isset($total['bet']) ? $total['bet'] + $t['bet'] : $t['bet'];
                    $total['win'] = isset($total['win']) ? $total['win'] + $t['win'] : $t['win'];
                    $total['user_win'] = isset($total['user_win']) ? $total['user_win'] + $t['user_win'] : $t['user_win'];
                    $total['admin_deduct'] = isset($total['admin_deduct']) ? $total['admin_deduct'] + $t['admin_deduct'] : $t['admin_deduct'];
                    $total['fd'] = isset($total['fd']) ? $total['fd'] + $t['fd'] : $t['fd'];
                    $total['chou_shui'] = isset($total['chou_shui']) ? $total['chou_shui'] + $t['chou_shui'] : $t['chou_shui'];
                    $total['ds'] = isset($total['ds']) ? $total['ds'] + $t['ds'] : $t['ds'];
                    $total['real_win'] = isset($total['real_win']) ? $total['real_win'] + $t['real_win'] : $t['real_win'];
                }
                foreach ($total as $_k => $_v) {
                    $total[$_k] = round($_v, 4);
                }
                $data['recordsTotal'] = count($rows);
                $data['total'] = $total;
                $data['profits'] = $profits;
                foreach ($rows as $_row) {
                    foreach ($_row as $_k => $_v) {
                        if ($_k != 'username' && $_k != 'user_observe') {
                            $_row[$_k] = round($_v, 4);
                        }
                    }
                    $data['data'][] = $_row;
                }
            } else {
                $data = Creator::factory($param['platform'])->getProfit($param);
            }
        }
        if ($export) {
            //导出数据
            $name = $param['platform'] == 'all' ? '第三方游戏' : $param['platform'];
            $start = date("m_dHi", strtotime($param['start_date']));
            $end = date("m_dHi", strtotime($param['end_date']));
            $file_name = "{$name}盈亏{$start}-{$end}.csv";
            $response = new StreamedResponse(null, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ]);
            $response->setCallback(function () use ($data, $param) {
                $out = fopen('php://output', 'w');
                fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                $columns = [
                    'default' => [
                        'username' => '用户名',
                        'bet' => '总投注',
                        'user_win' => '中奖金额',
                        'admin_deduct' => '管理员扣减',
                        'win' => '平台盈亏',
                        'fd' => '返水',
                        'real_win' => '最终结算'
                    ],
                    'Ky' => [
                        'username' => '用户名',
                        'bet' => '总投注',
                        'user_win' => '中奖金额',
                        'admin_deduct' => '管理员扣减',
                        'win' => '平台盈亏',
                        'fd' => '返水',
                        'chou_shui' => '系统抽水',
                        'real_win' => '最终结算'
                    ],
                    'Vr' => [
                        'username' => '用户名',
                        'bet' => '总投注',
                        'admin_deduct' => '管理员扣减',
                        'win' => '平台盈亏',
                        'fd' => '返水',
                        'ds' => '打赏',
                        'real_win' => '最终结算'
                    ],
                    'Fhleli' => [
                        'username' => '用户名',
                        'bet' => '总投注',
                        'admin_deduct' => '管理员扣减',
                        'win' => '平台盈亏',
                        'fd' => '返水',
                        'ds' => '打赏',
                        'real_win' => '最终结算'
                    ],
                    'wml' => [
                        'username' => '用户名',
                        'bet' => '总投注',
                        'user_win' => '中奖金额',
                        'admin_deduct' => '管理员扣减',
                        'win' => '平台盈亏',
                        'fd' => '返水',
                        'ds' => '打赏',
                        'real_win' => '最终结算'
                    ],
                    'all' => [
                        'username' => '用户名',
                        'bet' => '总投注',
                        'user_win' => '中奖金额',
                        'admin_deduct' => '管理员扣减',
                        'win' => '平台盈亏',
                        'fd' => '返水',
                        'ds' => '打赏',
                        'chou_shui' => '系统抽水',
                        'real_win' => '最终结算'
                    ],
                ];
                $colum = $columns[$param['platform']] ?? $columns['default'];
                fputcsv($out, $colum);
                foreach ($data['data'] as $_row) {
                    $item = [];
                    foreach ($colum as $_k => $_v) {
                        $item[] = $_row[$_k];
                    }
                    fputcsv($out, $item);
                }
                $item = [];
                foreach ($colum as $_k => $_v) {
                    $item[] = $data['total'][$_k] ?? '合计';
                }
                fputcsv($out, $item);
                fclose($out);
            });
            $response->send();
        } else {
            return response()->json($data);
        }
    }
}
