<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFields2ToPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('religion')->nullable(); // Agama
            $table->string('ethnicity')->nullable(); // Suku
            $table->string('marital_status')->nullable(); // Status pernikahan
            $table->string('medical_condition')->nullable(); // Penyakit peserta
            $table->string('wound_history')->nullable(); // Riwayat luka
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'religion',
                'ethnicity',
                'marital_status',
                'medical_condition',
                'wound_history',
            ]);
        });
    }
}
