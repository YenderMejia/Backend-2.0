<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->string('image_path')->nullable();  // Agregar el campo para almacenar la ruta de la imagen
        });
    }
    
    public function down()
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('image_path');  // Eliminar la columna si revertimos la migración
        });
    }
};
