<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomainController extends Controller
{
    protected $fields = [
        'subject' => '',
        'content' => '',
        'sort' => 0,
        'is_alert' => 0,
        'is_top' => 0
    ];

    public function getIndex()
    {
        $top_users = \Service\Models\User::select(['id', 'username'])->where('parent_id', 0)->get();
        foreach ($top_users as $u) {
        }
        $domain = \Service\Models\Domains::all();
        $domains = array();
        foreach ($domain as $v) {
            $domains[$v->id] = $v->domain;
        }

        return view('domain.index', [
            'top_users' => $top_users,
            'domains' => $domains,
        ]);
    }

    public function putAssignanddel(Request $request)
    {
        $domains = $request->get('domains', '');
        $top_user_id = (int) $request->get('top_user', 0);
        $assign = $request->get('assign', '');
        $del = $request->get('del', '');
        if (empty($domains)) {
            return redirect('/domain/')->withErrors("请选择域名！");
        }
        //分配
        if ($assign) {
            if (!$top_user_id) {
                return redirect('/domain/')->withErrors("请选择总代！");
            }
            //删除旧的在添加防止重复报错
            \Service\Models\UserDomains::where('user_id', $top_user_id)
                    ->whereIn('domain_id', $domains)
                    ->delete();
            foreach ($domains as $domain_id) {
                $user_domain = new \Service\Models\UserDomains;
                $user_domain->user_id = $top_user_id;
                $user_domain->domain_id = $domain_id;
                $user_domain->save();
            }
            return redirect('/domain')->withSuccess("分配域名成功！!");
        } elseif ($del) {
            DB::table('domains')->whereIn('id', $domains)->delete();
            DB::table('user_domains')->whereIn('domain_id', $domains)->delete();
            return redirect('/domain')->withSuccess("删除域名成功！!");
        }
    }

    public function putRecovery(Request $request)
    {
        $user_domain = $request->get('user_domain', '');
        if (empty($user_domain)) {
            return redirect('/domain/')->withErrors("请选择要回收的域名！");
        }
        foreach ($user_domain as $k => $v) {
            \Service\Models\UserDomains::where('user_id', $k)->where('domain_id', $v)->delete();
        }
        return redirect('/domain')->withSuccess("域名回收成功！!");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate()
    {
        return view('domain.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function putCreate(Request $request)
    {
        $url = $request->get('domain', '');
        if (empty($url)) {
            return redirect('/domain/create')->withErrors("请输入域名");
        }

        $url = parse_url(str_start($url, 'http://'), PHP_URL_HOST);
        if (!$url) {
            return redirect('/domain/create')->withErrors("请正确输入域名");
        }
        $domain = new \Service\Models\Domains();
        $domain->domain = $url;

        try {
            $domain->save();
        } catch (\Exception $e) {
            return redirect('/domain/create')->withErrors("域名已存在");
        }

        return redirect('/domain\/')->withSuccess('添加成功');
    }
}
