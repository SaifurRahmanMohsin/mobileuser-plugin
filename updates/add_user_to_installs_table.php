<?php namespace Mohsin\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddUserToInstallsTable extends Migration
{
    public function up()
    {
        Schema::table('mohsin_mobile_installs', function($table) {
            $table->integer('user_id')->after('instance_id')->unsigned()->nullable()->index();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('mohsin_mobile_installs', 'user_id'))
        {
            Schema::table('mohsin_mobile_installs', function($table) {
                $table->dropColumn(['user_id']);
            });
        }
    }
}
