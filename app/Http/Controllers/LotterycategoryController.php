<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotteryCategoryCreateRequest;
use App\Http\Requests\LotteryCategoryUpdateRequest;
use Service\Models\LotteryCategory;

class LotterycategoryController extends Controller
{
    protected $fields = [
        'ident' => '',
        'name' => '',
    ];

    public function getIndex()
    {
        return view('lottery-category.index');
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
            $data['recordsTotal'] = LotteryCategory::count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = LotteryCategory::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%');
                })->count();

                $data['data'] = LotteryCategory::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%');
                })->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = LotteryCategory::skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
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
        return view('lottery-category.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LotteryCategoryCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(LotteryCategoryCreateRequest $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = new LotteryCategory();
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/lotterycategory\/')->withSuccess('添加成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = LotteryCategory::find($id);
        if (!$row) {
            return redirect('/lotterycategory\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = (int)$id;
        return view('lottery-category.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LotteryCategoryUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(LotteryCategoryUpdateRequest $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = LotteryCategory::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/lotterycategory\/')->withErrors("找不到该角色");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/lotterycategory\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = LotteryCategory::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}
