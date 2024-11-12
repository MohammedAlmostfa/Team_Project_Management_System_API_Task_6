<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
            $table->date('last_activity')->nullable()->default(null);
            $table->integer('contribution_hours')->nullable()->default(null);
            $table->dateTime('created_at')->default(now());
            $table->dateTime('updated_at')->default(now());



        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
