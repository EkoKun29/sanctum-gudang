<?php

namespace App\Console\Commands;

use App\Models\Hpp;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


class SyncHppData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-hpp-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data hpp otomatis dari API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
        $url = 'https://script.google.com/macros/s/AKfycbzQmhTRO1gxHIE9RbsJjJN-T11zOYG_u0S-lYpxfLCH1PaNAfr80muvl8HH9n4RBaJFMA/exec';

        $response = Http::get($url);

        if ($response->successful()) {
            $data = collect($response->json());

            $this->info("Jumlah data dari API: " . $data->count());

            foreach ($data as $item) {
                $this->info("Memproses data: " . json_encode($item));

                try {
                    $tanggal = Carbon::createFromFormat('d-m-Y', $item['tanggal'])->format('Y-m-d');

                    $exists = Hpp::where('tanggal', $tanggal)
                        ->where('nama_barang', $item['nama_barang'])
                        ->where('harga', $item['harga'])
                        ->exists();

                    if (!$exists) {
                        Hpp::create([
                            'tanggal' => $tanggal,
                            'nama_barang' => $item['nama_barang'],
                            'harga' => $item['harga'],
                        ]);
                        $this->info("Disimpan: " . $item['nama_barang'] . " - " . $item['nama_barang']);
                    } else {
                        $this->info("Sudah ada: " . $item['nama_barang'] . " - " . $item['nama_barang']);
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
