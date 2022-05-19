<div class="box box-default collapsed-box">
    <div class="box-header with-border">
        <div class="box-title text-muted  small">使用帮助/表格栏目说明</div>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th width="200px">小栏</th>
                <th>备注</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>日工资</td>
                <td><span class="text-orange">工资报表</span>金额 与 帐变类型为<span class="text-orange">系统私返发放[XTSFFF]</span>帐变金额的总和</td>
            </tr>
            @if ($dividend_to_report)
                <tr>
                    <td>系统分红</td>
                    <td><span class="text-orange">契约分红报表</span>中系统发放类型的金额 、<span class="text-orange">分红发放[FHFF]</span>、<span class="text-orange">系统佣金发放[XTYJFF]</span>的帐变金额的总和</td>
                </tr>
                <tr>
                    <td>经营扣款</td>
                    <td>帐变类型为<span class="text-orange">系统经营扣款[XTJYKK]</span>帐变金额的总和</td>
                </tr>
                <tr>
                    <td>运营盈亏</td>
                    <td>全称为<span class="text-orange">运营最终盈亏</span>，公式：<span class="text-orange">最终盈亏</span> - <span class="text-orange">系统分红</span> + <span class="text-orange">经营扣款</span></td>
                </tr>
            @endif
            @if ($dividend_last_amount_to_report)
                <tr>
                    <td>前营扣款</td>
                    <td>全称为<span class="text-orange">前期经营扣款</span>，前期为当前选择时间段往前挪一天</td>
                </tr>
                <tr>
                    <td>前营盈亏</td>
                    <td>全称为<span class="text-orange">前期运营盈亏</span>，公式：<span class="text-orange">最终盈亏</span> - <span class="text-orange">前期分红</span> + <span class="text-orange">前营扣款</span></td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
