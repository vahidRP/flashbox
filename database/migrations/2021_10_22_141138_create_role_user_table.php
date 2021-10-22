<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_user', function(Blueprint $table){
            $table->id();
            $table->foreignIdFor(\App\Models\Role::class);
            $table->foreignIdFor(\App\Models\User::class);
            $table->index(['role_id', 'user_id'], 'r_u_index');
            $table->unique(['role_id', 'user_id'], 'r_u_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_user');
    }
}
