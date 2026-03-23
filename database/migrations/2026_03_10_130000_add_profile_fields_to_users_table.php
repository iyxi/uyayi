<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'photo') || !Schema::hasColumn('users', 'status') || !Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'photo')) {
                    $table->string('photo')->nullable()->after('address');
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->enum('status', ['active', 'inactive'])->default('active')->after('photo');
                }
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['customer', 'admin'])->default('customer')->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'status', 'role']);
        });
    }
}
