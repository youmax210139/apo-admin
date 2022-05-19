<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThirdGamePlatformCreateRequest;
use App\Http\Requests\ThirdGamePlatformUpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ThirdGameCreateRequest;
use App\Http\Requests\ThirdGameUpdateRequest;
use Illuminate\Support\Facades\Redis;
use Service\API\ThirdGame\API\Creator;
use Service\Models\ThirdGame;
use Service\Models\ThirdGameExtend;
use Service\Models\ThirdGamePlatform;

class ThirdgameController extends Controller
{
    protected $fields = [
        'ident' => '',
        'name' => '',
        'merchant' => '',
        'merchant_key' => '',
        'merchant_test' => '',
        'merchant_key_test' => '',
        'api_base' => '',
        'api_base_test' => '',
        'status' => '',
        'login_status' => '',
        'transfer_status' => '',
        'transfer_type' => '',
        'deny_user_group' => [],
        'last_fetch_time' => null,
        'third_game_platform_id' => ''
    ];

    protected $platform_fields = [
        'ident' => '',
        'name' => '',
        'status' => '',
        'rebate_type' => '',
        'sort' => '0',
    ];

    public function getIndex()
    {
        $data['platforms'] = ThirdGamePlatform::orderBy('status', 'asc')->orderBy('sort', 'asc')->get()->toArray();
        $games = ThirdGame::all();
        foreach ($data['platforms'] as $_k => $_platform) {
            $data['platforms'][$_k]['games'] = [];
            foreach ($games as $_row) {
                if ($_row->third_game_platform_id == $_platform['id']) {
                    $data['platforms'][$_k]['games'][] = $_row;
                }
            }
        }

        return view('third-game.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate()
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        $data['platforms'] = ThirdGamePlatform::orderBy('status', 'asc')->orderBy('sort', 'asc')->get();
        $data['last_fetch_time'] = Carbon::now();
        $data['extend'] = [];

        return view('third-game.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ThirdGameCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(ThirdGameCreateRequest $request)
    {
        $model = new ThirdGame();
        $input = $request->all();

        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('deny_user_group'))) {
                if (isset($input[$field])) {
                    $model->$field = json_encode($input[$field]);
                }
            } else {
                $model->$field = $request->get($field, $this->fields[$field]);
            }
        }

        //扩展属性处理
        DB::beginTransaction();
        $model->save();
        $names = $request->get('extend_name');
        $idents = $request->get('extend_ident');
        $values = $request->get('extend_value');
        unset($names[0], $idents[0], $values[0]);
        if ($names) {
            foreach ($names as $_k => $_name) {
                if (isset($idents[$_k]) && $idents[$_k] && isset($values[$_k])) {
                    $model_extend = new ThirdGameExtend();
                    $model_extend->third_game_id = $model->id;
                    $model_extend->name = $_name;
                    $model_extend->ident = $idents[$_k];
                    $model_extend->value = $values[$_k];
                    if (!$model_extend->save()) {
                        DB::rollBack();
                        return redirect('/thirdgame\/')->withSuccess('添加扩展属性失败');
                    }
                }
            }
        }
        DB::commit();

        return redirect('/thirdgame\/')->withSuccess('添加成功');
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
        $row = ThirdGame::find($id);
        if (!$row) {
            return redirect('/thirdgame\/')->withErrors("找不到该记录");
        }
        $data = ['id' => $id];

        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('deny_user_group'))) {
                $data[$field] = old($field, json_decode($row->$field, true));
            } else {
                $data[$field] = old($field, $row->$field);
            }
        }

        $data['last_fetch_time'] = $data['last_fetch_time'] ?? Carbon::now();
        $data['platforms'] = ThirdGamePlatform::orderBy('status', 'asc')->orderBy('sort', 'asc')->get();
        $data['extend'] = ThirdGameExtend::where('third_game_id', $id)->orderby('id', 'asc')->get();

        return view('third-game.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ThirdGameUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(ThirdGameUpdateRequest $request)
    {
        $row = ThirdGame::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/thirdgame\/')->withErrors("找不到该记录");
        }

        $input = $request->all();

        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('deny_user_group'))) {
                if (isset($input[$field])) {
                    $row->$field = json_encode($input[$field]);
                } else {
                    $row->$field = '[]';
                }
            } else {
                $row->$field = $request->get($field, $this->fields[$field]);
            }
        }

        //扩展属性处理
        DB::beginTransaction();
        $row->save();
        $names = $request->get('extend_name');
        $idents = $request->get('extend_ident');
        $values = $request->get('extend_value');
        unset($names[0], $idents[0], $values[0]);
        ThirdGameExtend::where('third_game_id', $row->id)->delete();
        if ($names) {
            $idents = array_unique($idents);
            if (count($idents) != count($names)) {
                DB::rollBack();
                return redirect('/thirdgame\/')->withErrors('添加扩展属性失败，请检查是否添加了多个相同唯一标识的属性');
            }
            foreach ($names as $_k => $_name) {
                if (isset($idents[$_k]) && $idents[$_k] && isset($values[$_k])) {
                    $model_extend = new ThirdGameExtend();
                    $model_extend->third_game_id = $row->id;
                    $model_extend->ident = $idents[$_k];
                    $model_extend->name = $_name;
                    $model_extend->value = $values[$_k];
                    if (!$model_extend->save()) {
                        DB::rollBack();
                        return redirect('/thirdgame\/')->withSuccess('添加扩展属性失败');
                    }
                }
            }
        }
        DB::commit();

        return redirect('/thirdgame\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {

        $row = ThirdGame::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();

            return response()->json(array('status' => 0, 'msg' => '删除成功！'));
        }

        return response()->json(array('status' => -1, 'msg' => '删除失败！'));
    }

    /**
     * 测试API
     *
     * @return \Illuminate\Http\Response
     */
    public function getTest(Request $request)
    {
        $row = ThirdGame::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/thirdgame\/')->withErrors("找不到该记录");
        }

        $name = $row['ident'];
        $username = get_config('third_game_account_prefix') . 'tester';
        $password = '123456';
        $additional = $request->get('additional', []);
        $api = Creator::factory($name, $username, $password, $additional);
        echo "<pre>";
        if ($row->ident == 'Vr') {
            $api->getBetDetail('2018-09-01 00:00:00Z', '2018-09-01 01:00:00Z', 0, 1, -1, -1);
        } elseif ($row->ident == 'FhLeli') {
            $api->getBetDetail('2019-05-21 00:00:00', '2019-05-21 01:00:00');
        } else {
            $api->existAccount();
        }
        echo $api->getLastErrorMsg('apilog');
        echo "</pre>";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLog(Request $request)
    {
        $ident = $request->get('ident', '');
        $name = 'third_apilog_' . $ident;

        $data['list'] = [];
        $list = Redis::lrange($name, 0, 50);
        foreach ($list as $v) {
            if (preg_match('/接口：(.+)/', $v, $matches)) {
                $data['list'][] = [
                    'api' => $matches[1],
                    'detail' => $v
                ];
            }
        }

        return view('third-game.log', $data);
    }


    public function getCreateplatform()
    {
        $data = [];
        foreach ($this->platform_fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        return view('third-game.create-platform', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ThirdGamePlatformCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateplatform(ThirdGamePlatformCreateRequest $request)
    {
        $model = new ThirdGamePlatform();

        foreach (array_keys($this->platform_fields) as $field) {
            $model->$field = $request->get($field, $this->platform_fields[$field]);
        }
        $model->save();

        return redirect('/thirdgame\/')->withSuccess('添加成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEditplatform(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = ThirdGamePlatform::find($id);
        if (!$row) {
            return redirect('/thirdgame\/')->withErrors("找不到该记录");
        }
        $data = ['id' => $id];

        foreach (array_keys($this->platform_fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }

        return view('third-game.edit-platform', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ThirdGamePlatformUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEditplatform(ThirdGamePlatformUpdateRequest $request)
    {
        $row = ThirdGamePlatform::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/thirdgame\/')->withErrors("找不到该记录");
        }

        foreach (array_keys($this->platform_fields) as $field) {
            $row->$field = $request->get($field, $this->platform_fields[$field]);
        }

        $row->save();

        return redirect('/thirdgame\/')->withSuccess('修改成功');
    }
}
