<?php

namespace App\Console\Commands;

use App\Models\attrn2;
use App\Models\employee;
use App\Models\hirarki;
use App\Models\hirarkiDesc;
use App\Models\kehadiran1;
use App\Models\kehadiran2;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CopyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:copy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy data from SQL Server to MySQL';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $kehadiran1 = Kehadiran1::orderBy('datin', 'desc')->take(10)->get(); // Data yang  masuk berapa nanti tanya HRD

        foreach ($kehadiran1 as $data1) {
            // Pengecekan apakah data sudah ada di MySQL2
            $exists = DB::connection('mysql2')
                ->table('kehadiran1')
                ->where('empno', $data1->empno)
                ->where('datin', $data1->datin)
                ->exists();

            // Jika data belum ada, lakukan insert
            if (!$exists) {
                DB::connection('mysql2')->table('kehadiran1')->insert([
                    'empno' => $data1->empno,
                    'datin' => $data1->datin,
                    'timin' => $data1->timin,
                    'datot' => $data1->datot,
                    'timot' => $data1->timot,
                ]);
            }
        }

        $kehadiran2 = kehadiran2::orderBy('schdt', 'desc')->take(10)->get();

        foreach ($kehadiran2 as $data2) {
            // Pengecekan apakah data sudah ada di MySQL2
            $exists = DB::connection('mysql2')
                ->table('kehadiran2')
                ->where('empno', $data2->empno)
                ->where('schdt', $data2->schdt)
                ->exists();

            // Jika data belum ada, lakukan insert
            if (!$exists) {
                DB::connection('mysql2')->table('kehadiran2')->insert([
                    'coid' => $data2->coid,
                    'empno' => $data2->empno,
                    'schdt' => $data2->schdt,
                    'rsccd' => $data2->rsccd,
                ]);
            }
        }

        $employee = employee::all();

        foreach ($employee as $dataEmpl) {
            $exists = DB::connection('mysql2')
                ->table('employee')
                ->where('empno', $dataEmpl->empno)
                ->exists();

            if (!$exists) {
                DB::connection('mysql2')->table('employee')->insert([
                    'coid' => $dataEmpl->coid,
                    'empno' => $dataEmpl->empno,
                    'empnm' => $dataEmpl->empnm,
                ]);
            }
        }

        $hirarki = hirarki::all();

        foreach ($hirarki as $dataHirar) {
            $exists  = DB::connection('mysql2')
                ->table('hirarki')
                ->where('empno', $dataHirar->empno)
                ->where('mutdt', $dataHirar->mutdt)
                ->exists();

            if (!$exists) {
                DB::connection('mysql2')->table('hirarki')->insert([
                    'empno' => $dataHirar->empno,
                    'hirar' => $dataHirar->hirar,
                    'mutdt' => $dataHirar->mutdt,
                ]);
            }
        }

        $hirarkiDesc = hirarkiDesc::all();

        foreach ($hirarkiDesc as $dataHirarDesc) {
            $exists  = DB::connection('mysql2')
                ->table('hirarkidesc')
                ->where('hirar', $dataHirarDesc->hirar)
                ->exists();

            if (!$exists) {
                DB::connection('mysql2')->table('hirarkidesc')->insert([
                    'hirar' => $dataHirarDesc->hirar,
                    'descr' => $dataHirarDesc->descr,
                ]);
            }
        }

        $attrn2 = attrn2::orderBy('schdt', 'desc')->take(10)->get(); // Data yang  masuk berapa nanti tanya HRD

        foreach ($attrn2 as $dataAttrn2) {
            // Pengecekan apakah data sudah ada di MySQL2
            $exists = DB::connection('mysql2')
                ->table('kehadiranmu')
                ->where('empno', $dataAttrn2->empno)
                ->where('schdt', $dataAttrn2->schdt)
                ->exists();

            // Jika data belum ada, lakukan insert
            if (!$exists) {
                DB::connection('mysql2')->table('kehadiranmu')->insert([
                    'coid' => $dataAttrn2->coid,
                    'empno' => $dataAttrn2->empno,
                    'schdt' => $dataAttrn2->schdt,
                    'rsccd' => $dataAttrn2->rsccd,
                ]);
            }
        }

        $this->info('Data copied successfully!');
        return 1;
    }
}
