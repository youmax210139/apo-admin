<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQuotas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotas', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('low', 14, 4)->default(0)->comment('返点下限');
            $table->decimal('high', 14, 4)->default(0)->comment('返点上限');
            $table->timestamps();
        });
        $this->data();
    }
    public function data()
    {
        $sql = "
insert into quotas (low, high) values('0.130','0.130');
insert into quotas (low, high) values('0.129','0.129');
insert into quotas (low, high) values('0.128','0.128');
insert into quotas (low, high) values('0.127','0.127');
insert into quotas (low, high) values('0.126','0.126');
insert into quotas (low, high) values('0.125','0.125');
insert into quotas (low, high) values('0.124','0.124');
insert into quotas (low, high) values('0.123','0.123');
insert into quotas (low, high) values('0.122','0.122');
insert into quotas (low, high) values('0.121','0.121');
insert into quotas (low, high) values('0.120','0.120');
insert into quotas (low, high) values('0.119','0.119');
insert into quotas (low, high) values('0.118','0.118');
insert into quotas (low, high) values('0.117','0.117');
insert into quotas (low, high) values('0.116','0.116');
insert into quotas (low, high) values('0.115','0.115');
insert into quotas (low, high) values('0.114','0.114');
insert into quotas (low, high) values('0.113','0.113');
insert into quotas (low, high) values('0.112','0.112');
insert into quotas (low, high) values('0.111','0.111');
insert into quotas (low, high) values('0.110','0.110');
insert into quotas (low, high) values('0.109','0.109');
insert into quotas (low, high) values('0.108','0.108');
insert into quotas (low, high) values('0.107','0.107');
insert into quotas (low, high) values('0.106','0.106');
insert into quotas (low, high) values('0.105','0.105');
insert into quotas (low, high) values('0.104','0.104');
insert into quotas (low, high) values('0.103','0.103');
insert into quotas (low, high) values('0.102','0.102');
insert into quotas (low, high) values('0.101','0.101');
insert into quotas (low, high) values('0.100','0.100');
insert into quotas (low, high) values('0.099','0.099');
insert into quotas (low, high) values('0.098','0.098');
insert into quotas (low, high) values('0.097','0.097');
insert into quotas (low, high) values('0.096','0.096');
insert into quotas (low, high) values('0.095','0.095');
insert into quotas (low, high) values('0.094','0.094');
insert into quotas (low, high) values('0.093','0.093');
insert into quotas (low, high) values('0.092','0.092');
insert into quotas (low, high) values('0.091','0.091');
insert into quotas (low, high) values('0.090','0.090');
insert into quotas (low, high) values('0.089','0.089');
insert into quotas (low, high) values('0.088','0.088');
insert into quotas (low, high) values('0.087','0.087');
insert into quotas (low, high) values('0.086','0.086');
insert into quotas (low, high) values('0.085','0.085');
insert into quotas (low, high) values('0.084','0.084');
insert into quotas (low, high) values('0.083','0.083');
insert into quotas (low, high) values('0.082','0.082');
insert into quotas (low, high) values('0.081','0.081');
insert into quotas (low, high) values('0.080','0.080');
insert into quotas ( low, high) values('0.079','0.079');
insert into quotas ( low, high) values('0.078','0.078');
insert into quotas ( low, high) values('0.077','0.077');
insert into quotas ( low, high) values('0.076','0.076');
insert into quotas ( low, high) values('0.075','0.075');
insert into quotas ( low, high) values('0.074','0.074');
insert into quotas ( low, high) values('0.071','0.073');
" ;
        DB::unprepared($sql);
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }
          DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'quotas/index',
                'name' => '配额组管理'
            ],[
                'parent_id' => $row->id,
                'rule' => 'quotas/delete',
                'name' => '配额组删除'
            ],[
                'parent_id' => $row->id,
                'rule' => 'quotas/create',
                'name' => '增加配额组'
            ]
              ]);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('quotas');
        }
    }
}
