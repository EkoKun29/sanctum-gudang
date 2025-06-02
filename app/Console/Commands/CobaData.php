<?php

namespace App\Console\Commands;

use App\Models\Coba;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class CobaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:coba-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        
    try {
        $url = 'https://script.google.com/macros/s/AKfycbzXY4XAztMFNrgZfZTAG6JA0bHdroEHiiIkOt7q2RSsRSfDShFBfvCe-mHSqjdzp7Yjzg/exec';

        $response = Http::get($url);

        if ($response->successful()) {
            $data = collect($response->json());

            $this->info("Jumlah data dari API: " . $data->count());

            foreach ($data as $item) {
                $this->info("Memproses data: " . json_encode($item));

                try {
                    $tanggal = Carbon::createFromFormat('d-m-Y', $item['tanggal'])->format('Y-m-d');

                    $exists = Coba::where('tanggal', $tanggal)
                        ->where('nama_konsumen', $item['nama_konsumen'])
                        ->where('nama_barang', $item['nama_barang'])
                        ->exists();

                    if (!$exists) {
                        Coba::create([
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
