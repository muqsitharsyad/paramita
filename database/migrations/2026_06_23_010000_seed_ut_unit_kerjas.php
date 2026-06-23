<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('unit_kerjas')->upsert($this->units(), ['kode'], ['nama', 'keterangan', 'updated_at']);
    }

    public function down(): void
    {
        DB::table('unit_kerjas')
            ->whereIn('kode', array_column($this->units(), 'kode'))
            ->delete();
    }

    private function units(): array
    {
        $now = now();

        return array_map(fn (array $unit) => [
            'nama' => $unit[0],
            'kode' => $unit[1],
            'keterangan' => '-',
            'created_at' => $now,
            'updated_at' => $now,
        ], [
            ['UT Pusat', 'UN31'],
            ['UT Sorong', 'UN31.UT1'],
            ['UT Banda Aceh', 'UN31.UT2'],
            ['UT Medan', 'UN31.UT3'],
            ['UT Batam', 'UN31.UT4'],
            ['UT Padang', 'UN31.UT5'],
            ['UT Pangkalpinang', 'UN31.UT6'],
            ['UT Pekanbaru', 'UN31.UT7'],
            ['UT Jambi', 'UN31.UT8'],
            ['UT Palembang', 'UN31.UT9'],
            ['UT Bengkulu', 'UN31.UT10'],
            ['UT Bandar Lampung', 'UN31.UT11'],
            ['UT Jakarta', 'UN31.UT12'],
            ['UT Serang', 'UN31.UT13'],
            ['UT Bogor', 'UN31.UT14'],
            ['UT Bandung', 'UN31.UT15'],
            ['UT Purwokerto', 'UN31.UT16'],
            ['UT Semarang', 'UN31.UT17'],
            ['UT Surakarta', 'UN31.UT18'],
            ['UT Yogyakarta', 'UN31.UT19'],
            ['UT Pontianak', 'UN31.UT20'],
            ['UT Palangkaraya', 'UN31.UT21'],
            ['UT Banjarmasin', 'UN31.UT22'],
            ['UT Samarinda', 'UN31.UT23'],
            ['UT Tarakan', 'UN31.UT24'],
            ['UT Surabaya', 'UN31.UT25'],
            ['UT Malang', 'UN31.UT26'],
            ['UT Jember', 'UN31.UT27'],
            ['UT Denpasar', 'UN31.UT28'],
            ['UT Mataram', 'UN31.UT29'],
            ['UT Kupang', 'UN31.UT30'],
            ['UT Makassar', 'UN31.UT31'],
            ['UT Majene', 'UN31.UT32'],
            ['UT Palu', 'UN31.UT33'],
            ['UT Kendari', 'UN31.UT34'],
            ['UT Manado', 'UN31.UT35'],
            ['UT Gorontalo', 'UN31.UT36'],
            ['UT Ambon', 'UN31.UT37'],
            ['UT Jayapura', 'UN31.UT38'],
            ['UT Ternate', 'UN31.UT39'],
            ['Layanan Luar Negeri', 'UN31.UT40'],
        ]);
    }
};
