<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BlackCardListRequest;
use \Service\Models\BlackCardList;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BlackcardlistController extends Controller
{
    private $fields = [
        'account_name' => '',
        'account' => '',
        'remark' => '',
    ];

    public function getIndex()
    {
        return view('black-card-list.index');
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $param = array();
            $param['account_name'] = $request->get('account_name', '');     // 账户名
            $param['account'] = $request->get('account', '');               // 卡号
            $param['type'] = $request->get('type', '');                     // 类型

            //查询条件
            $where = function ($query) use ($param) {
                if (!empty($param['account_name'])) {
                    $query->where('account_name', $param['account_name']);
                }
                if (!empty($param['account'])) {
                    $query->where('account', $param['account']);
                }
                if (!empty($param['type'])) {
                    $query->where('type', $param['type']);
                }
            };

            //计算过滤后总数
            $data['recordsTotal'] = $data['recordsFiltered'] = BlackCardList::where($where)
                ->count();

            $data['data'] = BlackCardList::select(['id', 'account_name', 'account', 'type', 'remark', 'created_at'])
                ->where($where)->skip($start)->take($length)
                ->orderBy("id", 'DESC')
                ->get();

            return response()->json($data);
        }
    }

    public function getCreate()
    {
        return view('black-card-list.create_many_form');
    }

    public function postCreate(Request $request)
    {
        //批量添加银行卡
        if ($request->get('type') == 'many') {
            $rtn = $this->postCreateMany($request);
            if ($rtn['status'] == 1) {
                return redirect()->back()->withErrors($rtn['msg']);
            } else {
                return redirect('/blackcardlist\/')->withSuccess($rtn['msg']);
            }
        }

        $req = new BlackCardListRequest();
        $this->validate($request, $req->rules(), $req->messages());

        $black_card_list = new BlackCardList();

        if (BlackCardList::where(['account' => $request->get('account'), 'type' => '1'])->count() > 0) {
            return redirect('/blackcardlist/')->withErrors('添加失败,卡号已存在！');
        }

        foreach ($this->fields as $key => $value) {
            $black_card_list->$key = $request->get($key, $value);
        }
        $black_card_list->type = 1;
        $black_card_list->created_at = date('Y-m-d H:i:s');

        if ($black_card_list->save()) {
            return redirect('/blackcardlist/')->withSuccess('添加成功');
        }

        return redirect('/blackcardlist/')->withErrors('添加失败');
    }

    /**
     * 批量添加银行卡
     * @param Request $request
     * @return array
     */
    private function postCreateMany(Request $request)
    {
        $rtn = ['status' => 1, 'msg' => '未知错误'];

        $banks_many = $request->post('banks_many', '');

        if ($banks_many == '') {
            //上传文件
            $file = $request->file('banks_file');
            if ($file) {
                $validator = Validator::make($request->all(), [
                    'banks_file' => 'required|mimes:csv,txt'
                ]);
                if ($validator->fails()) {
                    $rtn['msg'] = "失败：上传文件不正确，不是CSV格式";
                    return $rtn;
                }
                $banks_many = File::get($file);
                if (mb_detect_encoding($banks_many) != "UTF-8") {
                    $rtn['msg'] = "只能上传UTF-8格式文件";
                    return $rtn;
                }
            }
        }

        $inserts_data = [];
        $errors = [];

        if ($banks_many) {
            $banks_many = str_replace("'", "", $banks_many);    //过滤危险字符单引号
            $banks_many = str_replace(' ', '', $banks_many);

            $rows = explode("\n", $banks_many);
            $row_count = count($rows);
            if ($row_count > 1000) {
                $rtn['msg'] = "失败：记录上限1000条，当前为" . $row_count . "条";
                return $rtn;
            }

            $bank_cards = [];
            foreach ($rows as $line) {
                $line = trim($line);
                if ($line == '') {
                    continue;  //跳过空行
                }
                $temp = array_filter(explode(',', $line));
                if (count($temp) != 3) {
                    $errors[] = $line . "格式错误";
                } else {
                    $account_number = $temp[1];
                    if (strlen($account_number) < 16 || strlen($account_number) > 20) {
                        $errors[] = $line . "银行卡号格式错误";
                    }
                    if (!in_array($account_number, $bank_cards)) {
                        $bank_cards[] = $account_number;
                        $inserts_data[] = [
                            'account_name' => addslashes($temp[0]),
                            'account' => addslashes($account_number),
                            'remark' => addslashes($temp[2]),
                        ];
                    }
                }
            }
        }

        if ($errors) {
            $rtn['msg'] = "失败：" . implode(',', $errors);
            return $rtn;
        }
        if (empty($inserts_data)) {
            $rtn['msg'] = "失败：内容为空";
            return $rtn;
        }

        $sql = "";
        foreach ($inserts_data as $data) {
            $sql .= "INSERT INTO black_card_list(account_name,account,remark,type) " .
                " VALUES('" . $data['account_name'] . "','" . $data['account'] . "','" . $data['remark'] . "',1) ON CONFLICT (ACCOUNT,TYPE) DO NOTHING;";
        }

        try {
            DB::unprepared($sql);
        } catch (\Exception $e) {
            $rtn['msg'] = "插入数据失败！";
            return $rtn;
        }

        return ['status' => 0, 'msg' => '批量添加银行卡成功！'];
    }

    public function deleteIndex(Request $request)
    {
        $id = (int)$request->get('id', 0);

        $row = BlackCardList::find($id);
        if ($row && $row->delete()) {
            return redirect()->back()->withSuccess("删除成功");
        } else {
            return redirect()->back()->withErrors("删除失败");
        }
    }
}
