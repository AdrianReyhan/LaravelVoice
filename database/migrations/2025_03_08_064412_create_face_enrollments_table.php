<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaceEnrollmentsTable extends Migration
{
    public function up()
    {
        Schema::create('face_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi dengan tabel users
            $table->string('image_path'); // Path untuk gambar wajah
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('face_enrollments');
    }
}
