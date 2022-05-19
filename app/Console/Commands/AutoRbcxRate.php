<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Log;
use Service\Models\BankVirtual;

class AutoRbcxRate extends Command
{
    use Log;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoRbcxRate:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动更新虚拟货币银行汇率';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_initLogger('auto_rbcx_rate', 'RBCX');
    }

    public function getRateIdent($ident, $currency = 'cny')
    {
        if ($ident == 'USDTTRC20') {
            return 'usdtcny';
        }
        return strtolower($ident . $currency);
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->_log('开始查询汇率');
        $banks = BankVirtual::where('api_fetch', 1)->get();
        if ($banks->count() <= 0) {
            $this->_log('无开啓RBCX汇率之虚拟货币银行');
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
                $ident = $this->getRateIdent($bank->ident, $bank->currency);
                if (isset($res[$ident])) {
                    $this->_log("开始写入{$bank->name},{$ident}汇率: {$res[$ident]}");
                    $bank->rate = number_format($res[$ident], 4, '.', '');
                    $bank->save();
                } else {
                    $this->_log("{$ident}汇率不存在！");
                }
            }
        } catch (\Exception $e) {
            $this->_log($e->getMessage());
        }
    }
}
