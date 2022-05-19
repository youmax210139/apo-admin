<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotteryMethodCategoryCreateRequest;
use App\Http\Requests\LotteryMethodCategoryUpdateRequest;
use Service\Models\LotteryMethodCategory;

class LotterymethodcategoryController extends Controller
{
    protected $fields = [
        'ident' => '',
        'name' => '',
        'drop_point' => 0
    ];

    public function getIndex()
    {
        return view('lottery-method-category.index');
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
            $search = $request->get('search');
            $id = $request->get('id', 0);
            $data['recordsTotal'] = LotteryMethodCategory::where('parent_id', $id)->count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = LotteryMethodCategory::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%');
                })->count();

                $data['data'] = LotteryMethodCategory::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%');
                })->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = LotteryMethodCategory::where('parent_id', $id)->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            }

            foreach ($data['data'] as $_data) {
                if ($_data->parent_id > 0 && $_data->drop_point == 0) {
                    $_data->drop_point = '与上级下降点数一致';
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
    public function getCreate()
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        return view('lottery-method-category.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LotteryMethodCategoryCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(LotteryMethodCategoryCreateRequest $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = new LotteryMethodCategory();
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/lotterymethodcategory\/')->withSuccess('添加成功');
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
        $row = LotteryMethodCategory::find($id);
        if (!$row) {
            return redirect('/lotterymethodcategory\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = (int)$id;
        return view('lottery-method-category.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LotteryMethodCategoryUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(LotteryMethodCategoryUpdateRequest $request)
    {
        if (app()->environment() == 'production' && auth()->id() !== 1) {
            return $this->disabled();
        }

        $row = LotteryMethodCategory::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/lotterymethodcategory\/')->withErrors("找不到该角色");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/lotterymethodcategory\/')->withSuccess('修改成功');
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

        $row = LotteryMethodCategory::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
