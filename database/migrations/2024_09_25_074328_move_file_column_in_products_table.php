<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('document', 'file');
        });

        // Menambahkan kolom sementara untuk memindahkan posisi
        Schema::table('products', function (Blueprint $table) {
            $table->string('temp_file')->after('stock')->nullable(); // Ganti nullable() sesuai kebutuhan
        });

        // Memindahkan data dari kolom 'file' ke 'temp_file'
        DB::table('products')->update(['temp_file' => DB::raw('file')]);

        // Menghapus kolom 'file' yang lama
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('file');
        });

        // Mengganti nama kolom 'temp_file' menjadi 'file'
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('temp_file', 'file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
                // Mengembalikan perubahan jika perlu
                $table->renameColumn('file', 'document');
            });
    }
};
