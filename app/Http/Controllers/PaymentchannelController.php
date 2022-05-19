<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\PaymentChannel;
use Service\Models\PaymentChannelAttribute;
use Service\Models\PaymentCategory;
use App\Http\Requests\PaymentChannelRequest;
use Service\API\Payment as ApiPayment;
use Service\Models\UserLevel;

class PaymentchannelController extends Controller
{
    protected $fields = [
        'name' => '', //后台名称
        'front_name' => '', //前台菜单名称
        'payment_category_id' => 0, //支付渠道id
        'payment_method_id' => 0, //支付类型id
        'payment_domain_id' => 0, //支付域名id
        'platform' => 0, //限制平台
        'register_time_limit' => 0, //注册时间限制，单位小时
        'recharge_times_limit' => 0, //以前充值次数限制
        'recharge_amount_total_limit' => 0, //以前充值总金额
        'invalid_times_limit' => 99, //用户每天无效申请最多次数
        'invalid_times_lock' => 0,//用户10分钟无效申请最多次数
        'top_user_ids' => [], //总代id
        'sort' => 0, //排序
        'status' => true, //状态
    ];

    protected $attribute_fields = [
        'account_number' => '', //商户号或银行卡号
        'account_key' => '', //商户号密钥或私钥
        'account_key2' => '', //商户号第二个密钥（公钥、hash）
        'account_bank_name' => '', //银行卡银行名称
        'account_full_name' => '', //银行卡姓名
        'account_address' => '', //银行卡开户地址
        'api_gateway' => '', //API网关地址
        'qrcode_type' => '', //扫码方式
        'qrcode_url' => '',  //扫码地址
        'amount_min' => 0, //单笔最低充值额
        'amount_max' => 0, //单笔最高充值额
        'banks' => [], //在线网银的支付银行代码，JSON格式
        'user_fee_status' => 0, //用户手续费是否启用
        'user_fee_operation' => 0, //用户手续费操作：0减 1加
        'user_fee_line' => 0, //用户手续费界定金额线
        'user_fee_down_type' => 0, //用户低于界定金额线的手续费类型，0百分比 1固定值
        'user_fee_down_value' => 0, //用户低于界定金额线的手续费值
        'user_fee_up_type' => 0, //用户高于于界定金额线的手续费类型，0百分比 1固定值
        'user_fee_up_value' => 0, //用户高于界定金额线的手续费值
        'platform_fee_status' => 0, //平台手续费是否启用
        'platform_fee_line' => 0, //平台手续费界定金额线
        'platform_fee_down_type' => 0, //平台低于界定金额线的手续费类型，0百分比 1固定值
        'platform_fee_down_value' => 0, //平台低于界定金额线的手续费值
        'platform_fee_up_type' => 0, //平台高于于界定金额线的手续费类型，0百分比 1固定值
        'platform_fee_up_value' => 0, //平台高于界定金额线的手续费值
        'user_level_ids' => [],//用户分层
        'amount_decimal' => 0,//是否自动追加小数金额
        'amount_fixed_list' => '',//固定金额列表
        'postscript_status' => -1,//是否需要附言: 1需要 0不需要 -1无此选项
        'account_bank_flag' => '',//同略云所属银行
        'front_tip_text' => '',//前台自定义温馨提示
        'informants_available' => 1,//举报有奖
        'informants_wechat' => '',//举报微信
        'informants_qq' => '',//举报QQ
        'informants_bonus' => '',//举报奖金
        'agent_account' => '',//代理微信
        'agent_payments' => '',
        'exchange_rate' => 0,//汇率
    ];

    protected $banks = [
        ['code' => 'ABC', 'name' => '农业银行'],
        ['code' => 'ICBC', 'name' => '工商银行'],
        ['code' => 'CCB', 'name' => '建设银行'],
        ['code' => 'BOCOM', 'name' => '交通银行'],
        ['code' => 'BOC', 'name' => '中国银行'],
        ['code' => 'CMB', 'name' => '招商银行'],
        ['code' => 'CMBC', 'name' => '民生银行'],
        ['code' => 'CEB', 'name' => '光大银行'],
        ['code' => 'CIB', 'name' => '兴业银行'],
        ['code' => 'PSBC', 'name' => '邮政银行'],
        ['code' => 'PAB', 'name' => '平安银行'],
        ['code' => 'SPDB', 'name' => '浦发银行'],
        ['code' => 'CNCB', 'name' => '中信银行'],
        ['code' => 'GDB', 'name' => '广发银行'],
        ['code' => 'HXB', 'name' => '华夏银行'],
        ['code' => 'BOB', 'name' => '北京银行'],
        ['code' => 'CBHB', 'name' => '渤海银行'],
        ['code' => 'HKBEA', 'name' => '东亚银行'],
        ['code' => 'NCBC', 'name' => '宁波银行'],
        ['code' => 'BNCB', 'name' => '北京农村商业银行'],
        ['code' => 'NJCB', 'name' => '南京银行'],
        ['code' => 'CZBANK', 'name' => '浙商银行'],
        ['code' => 'SHBANK', 'name' => '上海银行'],
        ['code' => 'SNCB', 'name' => '上海农村商业银行'],
        ['code' => 'HCCB', 'name' => '杭州银行'],
        ['code' => 'ZJJZB', 'name' => '浙江江稠州商业银行'],
    ];

    protected $encrypt_key = null;

    protected $controller_name = 'paymentchannel';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->encrypt_key = getenv('PAYMENT_ENCRYPT_KEY');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex(Request $request)
    {

        $data = [];
        $data['status'] = (int)$request->get('status', 1);
        $data['rows'] = \Service\Models\PaymentChannel::select([
            'payment_channel.id',
            'payment_channel.name',
            'payment_channel.front_name',
            'payment_channel.payment_category_id',
            'payment_channel.payment_method_id',
            'payment_channel.sync_status',
            'payment_channel.sort',
            'payment_channel.status',
            'payment_category.name AS payment_category_name',
            'payment_method.name AS payment_method_name',
            'payment_method.sync AS payment_method_sync',
            'payment_domain.domain AS payment_domain_domain',
            'payment_channel_attribute.value AS account_number',
        ])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_channel.payment_category_id')
            ->leftJoin('payment_method', 'payment_method.id', 'payment_channel.payment_method_id')
            ->leftJoin('payment_domain', 'payment_domain.id', 'payment_channel.payment_domain_id')
            ->leftJoin('payment_channel_attribute', 'payment_channel_attribute.payment_channel_id', 'payment_channel.id')
            ->where('payment_channel_attribute.type', 'account_number')
            ->where('payment_channel.status', $data['status'])
            ->orderBy('payment_channel.status', 'DESC')
            ->orderBy('payment_channel.sort', 'ASC')
            ->orderBy('payment_channel.id', 'ASC')
            ->get()
            ->toArray();

        //服务器同步状态显示
        $server_rows = \Service\Models\IntermediateServers::select(['id', 'name'])->where('status', true)->orderBy('id', 'ASC')->get()->toArray();
        foreach ($data['rows'] as $key => $item) {
            $sync_status_array = empty($item['sync_status']) ? [] : json_decode($item['sync_status'], true);
            foreach ($server_rows as $server) {
                if (isset($sync_status_array[$server['id']]) && $sync_status_array[$server['id']] == 1) {
                    $server['checked'] = true;
                } else {
                    $server['checked'] = false;
                }
                $data['rows'][$key]['servers_sync_status'][] = $server;
            }
        }

        return view("{$this->controller_name}.index", $data);
    }

    /**
     * display create page
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getCreate(Request $request)
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        foreach ($this->attribute_fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        //获取所有总代
        $data['top_users'] = \Service\Models\User::select(['id', 'username'])->where('parent_id', 0)->orderBy('username', 'asc')->get();
        //支付渠道
        $data['categories'] = \Service\Models\PaymentCategory::select(['id', 'name', 'methods'])->where('status', true)->orderBy('id', 'asc')->get();
        //支付类型
        $data['methods'] = \Service\Models\PaymentMethod::select(['id', 'ident', 'name'])->where('status', true)->orderBy('id', 'asc')->get();
        $data['methods_json'] = json_encode($data['methods'], JSON_UNESCAPED_UNICODE);
        //支付域名
        $data['domains'] = \Service\Models\PaymentDomain::select(['payment_domain.*', 'payment_category.name AS payment_category_name', 'intermediate_servers.name AS intermediate_servers_name'])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_domain.payment_category_id')
            ->leftJoin('intermediate_servers', 'intermediate_servers.id', 'payment_domain.intermediate_servers_id')
            ->where('payment_domain.status', true)
            ->orderBy('payment_domain.id', 'asc')
            ->get();

        // 获取用户分层
        $data['user_level'] = UserLevel::select(['id', 'name'])->orderBy('id', 'asc')->get();
        // 分层
        foreach ($data['user_level'] as $key => $level) {
            $level->checked = false;
            $data['user_level'][$key] = $level;
        }

        //总代
        foreach ($data['top_users'] as $key => $user) {
            $user->checked = false;
            $data['top_users'][$key] = $user;
        }
        //网银的银行
        foreach ($this->banks as $key => $item) {
            $item['checked'] = false;
            $this->banks[$key] = $item;
        }
        $data['banks'] = $this->banks;
        $data['payments'] = [['银行卡', 'bank'], ['支付宝', 'alipay'], ['微信', 'wechat'], ['USDT', 'USDT']];
        return view("{$this->controller_name}.create", $data);
    }

    /**
     * 添加记录
     *
     * @param ConfigCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(PaymentChannelRequest $request)
    {
        $object = new PaymentChannel();
        foreach (array_keys($this->fields) as $field) {
            $object->$field = $request->get($field, $this->fields[$field]);
        }

        $channel_attribute_array = [];
        foreach (array_keys($this->attribute_fields) as $field) {
            $channel_attribute_array[$field] = $request->get($field, $this->attribute_fields[$field]);
        }

        //总代
        if (empty($object->top_user_ids)) {
            $object->top_user_ids = '[]';
        } else {
            $object->top_user_ids = json_encode($object->top_user_ids);
        }

        // 分层
        if (empty($channel_attribute_array['user_level_ids'])) {
            $channel_attribute_array['user_level_ids'] = '[]';
        } else {
            $channel_attribute_array['user_level_ids'] = json_encode($channel_attribute_array['user_level_ids']);
        }

        // 代理号
        if (empty($channel_attribute_array['agent_account'])) {
            $channel_attribute_array['agent_account'] = '[]';
        } else {
            $channel_attribute_array['agent_account'] = json_encode(array_values(array_filter($channel_attribute_array['agent_account'])));
        }
        // 代理充值方式
        if (empty($channel_attribute_array['agent_payments'])) {
            $channel_attribute_array['agent_payments'] = '[]';
        } else {
            $channel_attribute_array['agent_payments'] = json_encode(array_values(array_filter($channel_attribute_array['agent_payments'])));
        }
        //在线网银的银行
        if (empty($channel_attribute_array['banks'])) {
            $channel_attribute_array['banks'] = '[]';
        } else {
            $channel_attribute_array['banks'] = json_encode($channel_attribute_array['banks']);
        }

        //密钥
        $channel_attribute_array['account_key'] = $channel_attribute_array['account_key'] ? ssl_encrypt($channel_attribute_array['account_key'], $this->encrypt_key) : $channel_attribute_array['account_key'];
        $channel_attribute_array['account_key2'] = $channel_attribute_array['account_key2'] ? ssl_encrypt($channel_attribute_array['account_key2'], $this->encrypt_key) : $channel_attribute_array['account_key2'];

        \DB::beginTransaction();
        $insert_result = $object->save();
        if (empty($insert_result)) {
            \DB::rollBack();
            return redirect("/{$this->controller_name}/create/")->withErrors('添加失败');
        }

        $id = $object->id;
        foreach ($channel_attribute_array as $type => $value) {
            $data_insert_array = ['payment_channel_id' => $id, 'type' => $type, 'value' => $value];
            $result = \Service\Models\PaymentChannelAttribute::insert($data_insert_array);
            if (empty($result)) {
                \DB::rollBack();
                return redirect("/{$this->controller_name}/create/")->withErrors('添加失败');
            }
        }
        \DB::commit();

        if ($id) {
            //同步到服务器
            $method = \Service\Models\PaymentMethod::select(['sync'])->where('id', $object->payment_method_id)->first();
            $msg = '';
            if ($method->sync) {
                $ApiPayment = new ApiPayment();
                $sync_result = $ApiPayment->sync(0, $id);
                $msg = $sync_result['msg'];
            }
            return redirect("/{$this->controller_name}/")->withSuccess('添加成功。' . $msg);
        } else {
            return redirect("/{$this->controller_name}/create/")->withErrors('添加失败');
        }
    }

    /**
     * 显示编辑页面
     *
     * @param Request $request
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $row = \Service\Models\PaymentChannel::find($id);
        if (!$row) {
            return redirect("/{$this->controller_name}/")->withErrors("找不到这个支付通道");
        }

        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['top_user_ids'] = is_array($data['top_user_ids']) ? $data['top_user_ids'] : json_decode($data['top_user_ids'], true);

        //获取所有总代
        $data['top_users'] = \Service\Models\User::select(['id', 'username'])->where('parent_id', 0)->orderBy('username', 'asc')->get();
        //支付渠道
        $data['categories'] = \Service\Models\PaymentCategory::select(['id', 'name', 'methods'])->where('status', true)->orderBy('id', 'asc')->get();
        //支付类型
        $data['methods'] = \Service\Models\PaymentMethod::select(['id', 'ident', 'name'])->where('status', true)->orderBy('id', 'asc')->get();
        $data['methods_json'] = json_encode($data['methods'], JSON_UNESCAPED_UNICODE);
        //支付域名
        $data['domains'] = \Service\Models\PaymentDomain::select(['payment_domain.*', 'payment_category.name AS payment_category_name', 'intermediate_servers.name AS intermediate_servers_name'])
            ->leftJoin('payment_category', 'payment_category.id', 'payment_domain.payment_category_id')
            ->leftJoin('intermediate_servers', 'intermediate_servers.id', 'payment_domain.intermediate_servers_id')
            ->where('payment_domain.status', true)
            ->orderBy('payment_domain.id', 'asc')
            ->get();
        // 获取用户分层
        $data['user_level'] = UserLevel::select(['id', 'name'])->orderBy('id', 'asc')->get();

        //总代
        foreach ($data['top_users'] as $key => $user) {
            if (in_array($user->id, $data['top_user_ids'])) {
                $user->checked = true;
            } else {
                $user->checked = false;
            }
            $data['top_users'][$key] = $user;
        }

        //数据
        $data_rows = \Service\Models\PaymentChannelAttribute::where('payment_channel_id', $id)->get();
        $data_type2value = [];
        foreach ($data_rows as $item) {
            $data_type2value[$item->type] = $item->value;
        }
        unset($data_rows);
        foreach ($this->attribute_fields as $field => $default) {
            if (isset($data_type2value[$field])) {
                $data[$field] = old($field, $data_type2value[$field]);
            } else {
                $data[$field] = old($field, $default);
            }
        }
        if (!empty($data['banks'])) {
            $data['banks'] = is_array($data['banks']) ? $data['banks'] : json_decode($data['banks']);
            foreach ($this->banks as $key => $item) {
                if (in_array($item['code'], $data['banks'])) {
                    $item['checked'] = true;
                } else {
                    $item['checked'] = false;
                }
                $this->banks[$key] = $item;
            }
            $data['banks'] = $this->banks;
        }

        // 分层
        if (!empty($data['user_level_ids'])) {
            $data['user_level_ids'] = is_array($data['user_level_ids']) ? $data['user_level_ids'] : json_decode($data['user_level_ids'], true);
            foreach ($data['user_level'] as $key => $level) {
                if (in_array($level->id, $data['user_level_ids'])) {
                    $level->checked = true;
                } else {
                    $level->checked = false;
                }
                $data['user_level'][$key] = $level;
            }
        }

        // 分层
        if (!empty($data['agent_account'])) {
            $data['agent_account'] = is_array($data['agent_account']) ? $data['agent_account'] : json_decode($data['agent_account'], true);
        }
        if (!empty($data['agent_payments'])) {
            $data['agent_payments'] = is_array($data['agent_payments']) ? $data['agent_payments'] : json_decode($data['agent_payments'], true);
        }
        if (empty(old('account_key'))) {
            $data['account_key'] = $data['account_key'] ? ssl_decrypt($data['account_key'], $this->encrypt_key) : $data['account_key'];
            $data['account_key'] = !empty($data['account_key']) ? '******' . substr($data['account_key'], -3) : '';
        }

        if (empty(old('account_key2'))) {
            $data['account_key2'] = $data['account_key2'] ? ssl_decrypt($data['account_key2'], $this->encrypt_key) : $data['account_key2'];
            $data['account_key2'] = !empty($data['account_key2']) ? '******' . substr($data['account_key2'], -3) : '';
        }
        $data['payments'] = [['银行卡', 'bank'], ['支付宝', 'alipay'], ['微信', 'wechat'], ['USDT', 'USDT']];
        return view("{$this->controller_name}.edit", $data);
    }

    /**
     * 编辑支付接口
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putEdit(PaymentChannelRequest $request)
    {
        $id = (int)$request->get('id', 0);

        $row = \Service\Models\PaymentChannel::find($id);
        if (empty($row)) {
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('支付通道不存在');
        }

        //检查是否有需要同步的数据
        $is_sync = false;
        $need_sync_fields = ['payment_category_id', 'status'];
        $need_sync_attribute_fields = ['account_number', 'account_key', 'account_key2'];
        foreach ($need_sync_fields as $field) {
            if ($request->get($field) != $row->$field) {
                $is_sync = true;
                break;
            }
        }
        if ($is_sync == false) {
            $attribute_rows = \Service\Models\PaymentChannelAttribute::select(['type', 'value'])->whereIn('type', $need_sync_attribute_fields)->where('payment_channel_id', $id)->get();
            $attribute_type2value = [];
            foreach ($attribute_rows as $item) {
                $attribute_type2value[$item->type] = $item->value;
            }
            foreach ($need_sync_attribute_fields as $field) {
                if (!isset($attribute_type2value[$field]) || $request->get($field) != $attribute_type2value[$field]) {
                    $is_sync = true;
                    break;
                }
            }
        }
        if ($is_sync) {
            //重置同步服务器状态
            $row->sync_status = '[]';
        }
        if ($request->get('set_status', '') == '1') {
            $row->status = (int)$request->get('status', 0);
            $row->save();
            $status_txt = $row->status == 1 ? '启用' : '禁用';
            return response()->json(['status' => 0, 'msg' => "{$status_txt} {$row->name} 成功"]);
        }
        if (empty($row)) {
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('记录不存在');
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field, $this->fields[$field]);
        }

        // 检查是否存在
        $payment_channel_row = PaymentChannel::select(['id'])->where('name', $row->name)->first();
        if (isset($payment_channel_row->id) && $row->id != $payment_channel_row->id) {
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('对不起，后台名称已存在');
        }

        $channel_attribute_array = [];
        foreach (array_keys($this->attribute_fields) as $field) {
            if (in_array($field, ['account_key', 'account_key2'])) {
                $_tmp_key = $request->get($field, $this->attribute_fields[$field]);
                if (strpos($_tmp_key, '******') === 0) {
                    continue;
                }
                $channel_attribute_array[$field] = $_tmp_key;
            } else {
                $channel_attribute_array[$field] = $request->get($field, $this->attribute_fields[$field]);
            }
        }

        //总代
        if (empty($row->top_user_ids)) {
            $row->top_user_ids = '[]';
        } else {
            $row->top_user_ids = json_encode($row->top_user_ids);
        }

        // 分层
        if (empty($channel_attribute_array['user_level_ids'])) {
            $channel_attribute_array['user_level_ids'] = '[]';
        } else {
            $channel_attribute_array['user_level_ids'] = json_encode($channel_attribute_array['user_level_ids']);
        }

        // 代理
        if (empty($channel_attribute_array['agent_account'])) {
            $channel_attribute_array['agent_account'] = '[]';
        } else {
            $channel_attribute_array['agent_account'] = json_encode(array_values(array_filter($channel_attribute_array['agent_account'])));
        }
        // 代理
        if (empty($channel_attribute_array['agent_payments'])) {
            $channel_attribute_array['agent_payments'] = '[]';
        } else {
            $channel_attribute_array['agent_payments'] = json_encode(array_values(array_filter($channel_attribute_array['agent_payments'])));
        }
        //在线网银的银行
        if (empty($channel_attribute_array['banks'])) {
            $channel_attribute_array['banks'] = '[]';
        } else {
            $channel_attribute_array['banks'] = json_encode($channel_attribute_array['banks']);
        }

        //密钥
        if (!empty($channel_attribute_array['account_key'])) {
            $channel_attribute_array['account_key'] = ssl_encrypt($channel_attribute_array['account_key'], $this->encrypt_key);
        }
        if (!empty($channel_attribute_array['account_key2'])) {
            $channel_attribute_array['account_key2'] = ssl_encrypt($channel_attribute_array['account_key2'], $this->encrypt_key);
        }

        \DB::beginTransaction();
        $update_result = $row->save();
        if (empty($update_result)) {
            \DB::rollBack();
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('修改失败');
        }
        foreach ($channel_attribute_array as $type => $value) {
            $result = \Service\Models\PaymentChannelAttribute::updateOrCreate(['payment_channel_id' => $id, 'type' => $type], ['payment_channel_id' => $id, 'type' => $type, 'value' => $value]);
            if (empty($result)) {
                \DB::rollBack();
                return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('修改失败');
            }
        }
        \DB::commit();
        if ($update_result) {
            //同步到服务器
            $method = \Service\Models\PaymentMethod::select(['sync'])->where('id', $row->payment_method_id)->first();
            $msg = '';
            if ($is_sync && $method->sync) {
                $ApiPayment = new ApiPayment();
                $sync_result = $ApiPayment->sync(0, $id);
                $msg = $sync_result['msg'];
            }
            return redirect("/{$this->controller_name}/")->withSuccess('修改成功。' . $msg);
        } else {
            return redirect("/{$this->controller_name}/edit/?id={$id}")->withErrors('修改失败。');
        }
    }

    /**
     * 设置支付接口状态
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putSetStatus(Request $request)
    {
        $id = (int)$request->get('id');
        $status = (int)$request->get('status');

        $row = PaymentCategory::find($id);

        $row->status = $status;

        if ($row->save()) {
            $status_type = ($status == 1) ? '开启' : '关闭';
            return redirect("/{$this->controller_name}/")->withSuccess("【{$row->name}】设置为【{$status_type}】");
        } else {
            return redirect("/{$this->controller_name}/")->withErrors("设置失败");
        }
    }

    /**
     * 删除一个支付接口
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|unknown
     */
    public function deleteDelRecord(Request $request)
    {
        $id = (int)$request->get('id', 0);

        \DB::beginTransaction();
        $row = PaymentChannel::find($id);
        if ($row && $row->delete()) {
            PaymentChannelAttribute::where('payment_channel_id', $id)->delete();
            \DB::commit();
            return redirect()->back()->withSuccess("删除成功");
        } else {
            \DB::rollBack();
            return redirect()->back()->withErrors("删除失败");
        }
    }

    /**
     * 同步到服务器
     * @param $ids_array
     * @return bool
     */
    public function getRefreshServer(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $ApiPayment = new ApiPayment();
        $sync_result = $ApiPayment->sync(0, $id);
        if ($sync_result['result'] == true) {
            return redirect("/{$this->controller_name}/")->withSuccess($sync_result['msg']);
        } else {
            return redirect("/{$this->controller_name}/")->withErrors($sync_result['msg']);
        }
    }
}
