<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'role')) {
                $table->string('role')->default('user')->after('content');
            }
            if (!Schema::hasColumn('messages', 'session_id')) {
                $table->string('session_id')->nullable()->after('role');
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['role', 'session_id']);
        });
    }
}
