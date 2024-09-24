<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id'); // Relasi ke tabel patients
            $table->string('angiopati')->nullable(); // Data angiopati
            $table->string('neuropati')->nullable(); // Data neuropati
            $table->string('deformitas')->nullable(); // Data deformitas
            $table->string('riwayat_luka')->nullable(); // Riwayat luka
            $table->string('diet')->nullable(); // Jenis diet
            $table->integer('kategori_risiko')->nullable(); // Kategori risiko
            $table->text('hasil')->nullable(); // Hasil diagnosa
            $table->timestamps();

            // Relasi ke tabel patients
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medical_records');
    }
}
