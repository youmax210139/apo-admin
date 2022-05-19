<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Log;
use Service\Models\BankVirtual;

class AutoOtc365Rate extends Command
{
    use Log;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoOtc365Rate:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动更新虚拟货币银行汇率[otc365]';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_initLogger('auto_otc365_rate', 'otc365');
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->_log('开始查询汇率');
        $banks = BankVirtual::where('api_fetch', 2)->get();
        if ($banks->count() <= 0) {
            $this->_log('无开啓otc365汇率之虚拟货币银行');
            return;
        }
        $client = new \GuzzleHttp\Client();
        try {
            foreach ($banks as $bank) {
                if (filter_var($bank->url, FILTER_VALIDATE_URL) == false) {
                    $this->_log("{$bank->name} url不合法！");
                    continue;
                }
                $res = $client->request('GET', $bank->url);
                $res = json_decode($res->getBody()->getContents(), true);
                $this->_log('取得数据:' . json_encode($res));
                if (isset($res['code']) && $res['code'] == 200 && isset($res['data'])) {
                    $this->_log("开始写入{$bank->name},{$res['data']['coinType']}汇率: {$res['data']['sellPrice']}");
                    $bank->rate = number_format($res['data']['sellPrice'], 4, '.', '');
                    $bank->save();
                } else {
                    $this->_log("汇率不存在！ 汇率URL".$bank->url);
                }
            }
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
        }
    }
}
