<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotteryMethodCreateRequest;
use App\Http\Requests\LotteryMethodUpdateRequest;
use mysql_xdevapi\Exception;
use Service\Models\LotteryMethod;
use Service\Models\LotteryMethodCategory;

class LotterymethodController extends Controller
{
    protected $fields = [
        'id' => null,
        'parent_id' => 0,
        'lottery_method_category_id' => 0,
        'ident' => '',
        'name' => '',
        'draw_rule' => '[]',
        'lock_table_name' => '',
        'lock_init_function' => '',
        'modes' => '[]',
        'prize_level' => '[]',
        'prize_level_name' => '[]',
        'layout' => '[]',
        'sort' => 0,
        'status' => false,
    ];

    public function getIndex(Request $request)
    {
        $data = array();
        $method_categories = LotteryMethodCategory::orderby('id', 'asc')
            ->get(['id', 'parent_id', 'name']);

        $method_categories_group = [];
        foreach ($method_categories as $method_category) {
            if ($method_category->parent_id == 0) {
                $method_categories_group[$method_category->id]['name'] = $method_category->name;
            } else {
                $method_categories_group[$method_category->parent_id]['child'][]
                    = $method_category;
            }
        }
        $data['method_categories'] = $method_categories_group;
        $data['id'] = (int)$request->get("id");
        return view('lottery-method.index', $data);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $lottery_method_category_id = $request->get('lottery_method_category_id');
            $lottery_method_status = $request->get('lottery_method_status');
            $id = intval($request->get('id'));
            $is_search = intval($request->get('is_search'));
            $level = $request->get('level', 0);

            $where = array();
            if (empty($is_search)) {
                $where[] = array('lottery_method.parent_id', '=', $id);
            }
            if ($lottery_method_category_id != 'all') {
                $where[] = array('lottery_method.lottery_method_category_id', '=', $lottery_method_category_id);
            }
            if ($lottery_method_status != 'all') {
                $where[] = array('lottery_method.status', '=', $lottery_method_status == 1);
            }

            $data['recordsFiltered'] = $data['recordsTotal'] = LotteryMethod::where($where)->where(function ($query) use ($level) {
                if ($level == 1) {
                    $query->where("lottery_method.parent_id", 0);
                } elseif ($level == 2) {
                    $query->where("lottery_method.parent_id", '>', 0)->whereRaw("lottery_method.prize_level::text ='[]'");
                } elseif ($level == 3) {
                    $query->whereRaw("lottery_method.prize_level::text <>'[]'");
                }
            })->count();
            $data['data'] = LotteryMethod::select(
                [
                    'lottery_method.id',
                    'lottery_method.parent_id',
                    'm1.name as name1',
                    'm2.name as name2',
                    'lottery_method.name',
                    'lottery_method.ident',
                    'lottery_method.prize_level',
                    'lottery_method.modes',
                    'lottery_method.sort',
                    'lottery_method.status',
                    'lottery_method.prize_level_name',
                    'lottery_method.max_bet_num',
                    'lottery_method_category.name as lottery_method_category_name'
                ]
            )
                ->leftJoin('lottery_method_category', 'lottery_method.lottery_method_category_id', '=', 'lottery_method_category.id')
                ->leftJoin('lottery_method AS m2', 'm2.id', 'lottery_method.parent_id')
                ->leftJoin('lottery_method AS m1', 'm1.id', 'm2.parent_id')
                ->where($where)
                ->where(function ($query) use ($level) {
                    if ($level == 1) {
                        $query->where("lottery_method.parent_id", 0);
                    } elseif ($level == 2) {
                        $query->where("lottery_method.parent_id", '>', 0)->whereRaw("lottery_method.prize_level::text ='[]'");
                    } elseif ($level == 3) {
                        $query->whereRaw("lottery_method.prize_level::text <>'[]'");
                    }
                })
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();
            if (!empty($data['data'])) {
                foreach ($data['data'] as &$row) {
                    $row['tree_level'] = $this->_getTreeLevelFromId($row['id']);
                    $name = [];
                    if (!empty($row['name1'])) {
                        $name[] = $row['name1'];
                    }
                    if (!empty($row['name2'])) {
                        $name[] = $row['name2'];
                    }
                    $name[] = $row['name'];
                    $row['name'] = implode('-', $name);
                }
            }
            return response()->json($data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate(Request $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $parent_id = (int)$request->get('id');
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        $data['draw_rule'] = '';

        $data['lottery_method_category_id'] = 0;
        $data['parents'] = array();
        $data['this_level'] = 1;
        if ($parent_id > 0) {
            $parent = LotteryMethod::where('id', $parent_id)->select('id', 'parent_id', 'name', 'lottery_method_category_id')->first();
            if (empty($parent)) {
                return redirect('/lotterymethod\/')->withErrors('?????? id=' . $parent_id . ' ????????????');
            }
            $parent_level = $this->_getTreeLevelFromId($parent->id);
            if ($parent->parent_id > 0 && $parent_level == 3) {
                return redirect('/lotterymethod\/')->withErrors('id=' . $parent_id . ' ???????????????????????????????????????????????????');
            }
            if ($parent->parent_id > 0) {
                $data['parents'][] = LotteryMethod::where('id', $parent->parent_id)->select('id', 'name')->first();
            }
            $data['parents'][] = $parent;
            $data['parent_id'] = $parent->id;
            $data['this_level'] = $parent_level + 1;
            $data['lottery_method_category_id'] = $parent->lottery_method_category_id;
        }

        $data['lottery_method_category'] = LotteryMethodCategory::all();
        $data['modes_list'] = get_mode();
        return view('lottery-method.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LotteryMethodCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(LotteryMethodCreateRequest $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $method = new LotteryMethod();
        foreach (array_keys($this->fields) as $field) {
            $val = $request->get($field);
            switch ($field) {
                case 'modes':
                case 'prize_level':
                    $val = empty($val) ? $this->fields[$field] : json_encode($val, JSON_NUMERIC_CHECK);
                    break;
                case 'prize_level_name':
                    $val = empty($val) ? $this->fields[$field] : json_encode($val, JSON_UNESCAPED_UNICODE);
                    break;
                case 'status':
                    $val = $val == 1;
                    break;
                case 'layout':
                    $val = empty($val) ? $this->fields[$field] : $val;
                    break;
                case 'draw_rule':
                    if (!empty($val)) {
                        $ret = json_decode($val);
                        if (empty($ret)) {
                            return redirect()->back()->withErrors('???????????? JSON ??????????????? JSON ?????????');
                        }
                    } else {
                        $val = $this->fields[$field];
                    }
                    break;
            }
            $method->$field = $val === null ? $this->fields[$field] : $val; //??????post?????????????????????
        }
        if (!preg_match('/^[1-9][\d]{7,8}$/', $method->id)) { //??????????????????2???????????????????????????
            return redirect()->back()->withErrors('?????? ID ???????????? 0 ????????? 9 ????????????');
        }
        $row = LotteryMethod::where('id', $method->id)->select('id')->first();
        if (!empty($row)) {
            return redirect()->back()->withErrors('??????ID=' . $method->id . ' ???????????????????????????????????????');
        }

        if ($method->parent_id > 0) {
            $parent = lotterymethod::where('id', $method->parent_id)->select('lottery_method_category_id')->first();
            if (empty($parent)) {
                return redirect()->back()->withErrors('??????????????? parent_id=' . $method->parent_id . ' ????????????');
            }
            $method->lottery_method_category_id = $parent->lottery_method_category_id;
        }

        try {
            $row->save();
        } catch (\Exception $e) {
            return redirect('/lotterymethod\/')->withErrors($e->getMessage());
        }

        return redirect('/lotterymethod\/')->withSuccess('????????????????????????');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $data = LotteryMethod::find($id)->toArray();
        if (!$data) {
            return redirect('/lotterymethod\/')->withErrors("????????? id={$id} ????????????");
        }
        $data['modes'] = json_decode($data['modes']);
        $data['draw_rule'] = json_decode($data['draw_rule']) ? $data['draw_rule'] : '';
        $data['parents'] = array();
        $data['this_level'] = $this->_getTreeLevelFromId($data['id']);
        if ($data['parent_id'] > 0) {
            $parent_id = $data['parent_id'];
            $parent = LotteryMethod::where('id', $parent_id)->select('id', 'parent_id', 'name')->first();
            if (empty($parent)) {
                return redirect('/lotterymethod\/')->withErrors('????????? id={$id} ????????? id=' . $parent_id . ' ?????????????????????');
            }
            if ($parent->parent_id > 0) {
                $data['parents'][] = LotteryMethod::where('id', $parent->parent_id)->select('id', 'name')->first();
            }
            $data['parents'][] = $parent;
        }

        $data['lottery_method_category'] = LotteryMethodCategory::orderby('id', 'asc')->get();
        $data['modes_list'] = get_mode();
        return view('lottery-method.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LotteryMethodUpdateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(LotteryMethodUpdateRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $row = LotteryMethod::find($id);
        if (!$row) {
            return redirect('/lotterymethod\/')->withErrors("????????? id={$id} ????????????");
        }
        foreach (array_keys($this->fields) as $field) {
            $val = $request->get($field);
            switch ($field) {
                case 'modes':
                    $val = empty($val) ? $this->fields[$field] : json_encode($val, JSON_NUMERIC_CHECK);
                    break;
                case 'prize_level':
                    $val = empty($val) ? $this->fields[$field] : json_encode($val, JSON_NUMERIC_CHECK);
                    break;
                case 'prize_level_name':
                    $val = empty($val) ? $this->fields[$field] : json_encode($val, JSON_UNESCAPED_UNICODE);
                    break;
                case 'status':
                    $val = $val == 1;
                    break;
                case 'layout':
                    $val = empty($val) ? $this->fields[$field] : $val;
                    break;
                case 'draw_rule':
                    if (!empty($val)) {
                        $ret = json_decode($val);
                        if (empty($ret)) {
                            return redirect()->back()->withErrors('????????????JSON ???????????????JSON?????????');
                        }
                    } else {
                        $val = $this->fields[$field];
                    }
                    break;
            }
            $row->$field = $val === null ? $row->$field : $val;
        }
        //?????????????????????????????????????????????
        unset($row->id, $row->parent_id, $row->lottery_method_category_id);
        try {
            $row->save();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        return redirect()->back()->withSuccess("?????? id={$id} ??????????????????");
    }

    /**
     *
     */
    public function postEdit(Request $request)
    {
        $sorts = $request->get('sort');
        if (empty($sorts) || !is_array($sorts)) {
            return redirect()->back()->withErrors('???????????????');
        }
        foreach ($sorts as $key => $sort) {
            LotteryMethod::where('id', $key)->update(['sort' => (int)$sort]);
        }
        return redirect()->back()->withSuccess("???????????????");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $id = (int)$request->get('id', 0);
        $row = LotteryMethod::find($id);

        if ($row) {
            $operation_ids_array = array();
            $operation_ids_array[] = $row->id;

            $children1 = LotteryMethod::where('parent_id', $row->id)->select('id')->get();
            if ($children1) {
                $children1_ids_array = array();
                foreach ($children1 as $c1) {
                    $children1_ids_array[] = $c1->id;
                    $operation_ids_array[] = $c1->id;
                }
                $children2 = LotteryMethod::wherein('parent_id', $children1_ids_array)->select('id')->get();
                if ($children2) {
                    foreach ($children2 as $c2) {
                        $operation_ids_array[] = $c2->id;
                    }
                }
            }
            LotteryMethod::destroy($operation_ids_array);

            return redirect()->back()->withSuccess("?????? id={$id} ?????????????????????????????? ?????????");
        } else {
            return redirect()->back()->withErrors("????????????????????? id={$id} ????????????");
        }
    }

    public function putStatus(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $status = (bool)$request->get('status', 0);

        $row = LotteryMethod::where('id', $id)->select('id')->first();

        if ($row) {
            $operation_ids_array = array();
            $operation_ids_array[] = $row->id;

            $children1 = LotteryMethod::where('parent_id', $row->id)->select('id')->get();
            if ($children1) {
                $children1_ids_array = array();
                foreach ($children1 as $c1) {
                    $children1_ids_array[] = $c1->id;
                    $operation_ids_array[] = $c1->id;
                }
                $children2 = LotteryMethod::wherein('parent_id', $children1_ids_array)->select('id')->get();
                if ($children2) {
                    foreach ($children2 as $c2) {
                        $operation_ids_array[] = $c2->id;
                    }
                }
            }

            LotteryMethod::wherein('id', $operation_ids_array)->update(array('status' => $status));

            return redirect()->back()->withSuccess(($status ? '??????' : '??????') . " ID = {$id} ?????????????????????????????? ?????????");
        } else {
            return redirect()->back()->withErrors("????????? id={$id} ????????????");
        }
    }

    private function _getTreeLevelFromId($id)
    {
        /*
         * ?????????
         *
         * id = 10 08 01 001
         * ???????????????????????? 1-2 ???????????????????????? 3-4 ?????? 1 ????????? 5-6 ?????? 2 ????????? 7-9 ?????? 3 ???
         *
         */
        $level3_num = intval(substr($id, -3, 3));
        $level2_num = intval(substr($id, -5, 2));
        $level1_num = intval(substr($id, -7, 2));

        $level = false;

        if ($level3_num > 0) {
            $level = 3;
        } elseif ($level2_num > 0) {
            $level = 2;
        } elseif ($level1_num > 0) {
            $level = 1;
        }

        return $level;
    }

    public function getOutfile()
    {
        if (auth()->id() != 1) {
            return $this->disabled();
        }

        $category_rows = LotteryMethodCategory::select(['id', 'ident', 'name'])->get();
        $cat_id2row = [];
        foreach ($category_rows as $row) {
            $cat_id2row[$row->id] = $row;
        }


        $bz_array = array();
        $pk_array = array();
        $last_catid = 0;
        $rows = LotteryMethod::orderBy('lottery_method_category_id', 'asc')->orderby('id', 'asc')->get()->toArray();
        foreach ($rows as $row) {
            $cat_row = $cat_id2row[$row['lottery_method_category_id']];
            if (strpos($cat_row->ident, '_pk') !== false) {
                $array = &$pk_array;
            } else {
                $array = &$bz_array;
            }
            if ($last_catid != $row['lottery_method_category_id']) {
                $array[] = "\n//================================  lottery_method_category_id: {$cat_row->id} {$cat_row->name}  ==================================";
            }
            $last_catid = $row['lottery_method_category_id'];

            if ($row['parent_id'] == 0) {
                $white_space = str_pad('', 4, ' ');
                $array[] = "//" . $row['name'] . " " . $row['id'];
            } elseif (intval(substr($row['id'], -3)) == 0) {
                $white_space = str_pad('', 8, ' ');
                $array[] = "{$white_space}//" . $row['name'] . " " . $row['id'];
            } else {
                $white_space = str_pad('', 12, ' ');
            }

            $row['layout'] = "\n" . json_encode(json_decode($row['layout']), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            $line = $white_space . '[';
            foreach ($row as $key => $val) {
                if (is_null($val)) {
                    $line .= "'{$key}'=>null, ";
                } elseif (is_bool($val)) {
                    $val = $val === true ? 'true' : 'false';
                    $line .= "'{$key}'=>{$val}, ";
                } else {
                    $line .= "'{$key}'=>'" . str_replace("'", "\'", $val) . "', "; //????????????JSON??????
                }
            }
            $line .= "]";
            $array[] = $line;
        }
        $echo = "<?php\n";
        $echo .= "// ??????????????????????????????????????? database/migrations/2017_06_26_054622_create_table_lottery_method.php\n";
        $echo .= "// ?????????????????? \$this->data(); ??? ?????????????????? \$this->dataPk();\n";
        $echo .= "//===========================================  ????????????   ================================================================\n\n";
        $echo .= "DB::table('lottery_method')->insert([\n";
        $echo .= implode(",\n", $bz_array) . "\n";
        $echo .= "\n]);\n\n";
        $echo .= "//===========================================  ????????????   ================================================================\n\n";
        $echo .= "DB::table('lottery_method')->insert([\n";
        $echo .= implode(",\n", $pk_array) . "\n";
        $echo .= "\n]);\n\n";

        $echo = str_replace('==========,', '==========', $echo);

        $filename = 'lottery_method_' . now()->toDateString() . '.php';
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=" . $filename);
        echo $echo;
    }

    /**
     * ????????????
     * @param Request $request
     */
    public function putEditprize(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $prize_level = $request->get('prize_level', 0);
        $lottery_method = LotteryMethod::find($id);
        if (!$lottery_method) {
            return response()->json([
                'status' => 1,
                'msg' => '???????????????'
            ]);
        }
        if (empty($prize_level)) {
            return response()->json([
                'status' => 1,
                'msg' => '??????????????????'
            ]);
        }
        $lottery_method->prize_level = json_encode(array_map('floatval', $prize_level));
        try {
            $lottery_method->save();
            return response()->json([
                'status' => 0,
                'msg' => '??????????????????'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }

    /**
     * ??????????????????
     * @param Request $request
     */
    public function putEditmaxnum(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $max_num = (int)$request->get('max_num', 0);
        $lottery_method = LotteryMethod::find($id);
        if (!$lottery_method) {
            return response()->json([
                'status' => 1,
                'msg' => '???????????????'
            ]);
        }
        if ($lottery_method->modes == '[9]' || $lottery_method->modes == '[]') {
            return response()->json([
                'status' => 1,
                'msg' => '??????????????????'
            ]);
        }
        $lottery_method->max_bet_num = $max_num;
        try {
            $lottery_method->save();
            return response()->json([
                'status' => 0,
                'msg' => '????????????????????????'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
