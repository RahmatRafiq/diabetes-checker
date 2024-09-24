<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('education_level')->nullable(); // Tingkat Pendidikan
            $table->string('occupation')->nullable(); // Pekerjaan
            $table->decimal('weight', 5, 2)->nullable(); // Berat Badan (kg)
            $table->decimal('height', 5, 2)->nullable(); // Tinggi Badan (cm)
            $table->integer('years_with_diabetes')->nullable(); // Lama Menderita Diabetes (tahun)
            $table->string('dm_therapy')->nullable(); // Terapi DM
            $table->decimal('gds', 5, 2)->nullable(); // Nilai GDS
            $table->decimal('hba1c', 5, 2)->nullable(); // Nilai HbA1c
            $table->string('diet_type')->nullable(); // Jenis Diet
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
                'education_level',
                'occupation',
                'weight',
                'height',
                'years_with_diabetes',
                'dm_therapy',
                'gds',
                'hba1c',
                'diet_type',
            ]);
        });
    }
}
