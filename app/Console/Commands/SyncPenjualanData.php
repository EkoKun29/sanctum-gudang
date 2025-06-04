<?php

namespace App\Console\Commands;

use App\Models\HargaKonsumen;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

use Illuminate\Console\Command;

class SyncPenjualanData extends Command
{

    protected $signature = 'app:sync-penjualan-data';

    protected $description = 'Sinkronisasi data harga konsumen otomatis dari API';

    public function handle()
{
    try {
        $url = 'https://script.google.com/macros/s/AKfycbyui5MiVwMV1SkgUcHzY_2KJ44hLQmJtRhWDIG2YYU0js2AihB5K-crHKZUvkdENebn/exec';

        $response = Http::get($url);

        if ($response->successful()) {
            $data = collect($response->json());

            $this->info("Jumlah data dari API: " . $data->count());
            $tanggalTerakhir = HargaKonsumen::max('tanggal');
            if (!$tanggalTerakhir) {
                $tanggalTerakhir = '2000-01-01'; // jika belum ada data
            }

            $this->info("Tanggal terakhir di database: " . $tanggalTerakhir);

            foreach ($data as $item) {
                $this->info("Memproses data: " . json_encode($item));

                try {
                    $tanggal = Carbon::createFromFormat('d-m-Y', $item['tanggal'])->format('Y-m-d');
                    
                    if ($tanggal <= $tanggalTerakhir) {
                        $this->info("Lewati data lama: " . $item['tanggal'] . " - " . $item['nama_barang']);
                        continue;
                    }

                    $exists = HargaKonsumen::where('tanggal', $tanggal)
                        ->where('nama_konsumen', $item['nama_konsumen'])
                        ->where('nama_barang', $item['nama_barang'])
                        ->exists();

                    if (!$exists) {
                        HargaKonsumen::create([
                            'sales' => $item['sales'],
                            'tanggal' => $tanggal,
                            'nama_konsumen' => $item['nama_konsumen'],
                            'nama_barang' => $item['nama_barang'],
                            'harga_jual' => $item['harga_jual'],
                        ]);
                        $this->info("Disimpan: " . $item['nama_konsumen'] . " - " . $item['nama_barang']);
                    } else {
                        $this->info("Sudah ada: " . $item['nama_konsumen'] . " - " . $item['nama_barang']);
                    }
                } catch (\Exception $e) {
                    $this->error("Gagal memproses data: " . $e->getMessage());
                }
            }

            $this->info('Sinkronisasi selesai.');
        } else {
            $this->error('Gagal ambil data dari API');
        }

    } catch (\Throwable $e) {
        $this->error('Error: ' . $e->getMessage());
    }
}

}
