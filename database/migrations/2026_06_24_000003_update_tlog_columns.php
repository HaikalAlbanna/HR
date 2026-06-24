<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tlog')) {
            Schema::create('tlog', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal');
                $table->time('jam');
                $table->text('keterangan')->nullable();
            });

            return;
        }

        Schema::table('tlog', function (Blueprint $table) {
            if (!Schema::hasColumn('tlog', 'tanggal')) {
                $table->date('tanggal')->default('1970-01-01');
            }

            if (!Schema::hasColumn('tlog', 'jam')) {
                $table->time('jam')->default('00:00:00');
            }

            if (!Schema::hasColumn('tlog', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('tlog', function (Blueprint $table) {
            if (Schema::hasColumn('tlog', 'tanggal')) {
                $table->dropColumn('tanggal');
            }

            if (Schema::hasColumn('tlog', 'jam')) {
                $table->dropColumn('jam');
            }

            if (Schema::hasColumn('tlog', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};
