<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id')->index()->nullable();
            $table->unsignedBigInteger('assigned_user_id')->nullable()->index();
            $table->string('title');
            $table->text('description');
            $table->unsignedTinyInteger('status_id')->nullable()->index();
            $table->dateTime('estimation_date')->nullable();
            $table->dateTime('started_date')->nullable();
            $table->timestamp('created_at')
                ->useCurrent();
            $table->timestamp('updated_at')
                ->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('creator_id', 'creator_task_user_fk')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('assigned_user_id', 'assigned_task_user_fk')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('status_id')
                ->references('id')
                ->on('task_statuses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
