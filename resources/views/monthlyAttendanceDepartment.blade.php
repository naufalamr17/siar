@extends('layouts.app', ['title' => 'Monthly Attendance Department'])
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Data Kehadiran Karyawan Bulanan per Department</h1>
        </div>

        <div class="card">
            <div class="row px-3 py-3">
                <div class="col-lg-12">
                    <div class="container">
                        <div class="row">
                            <div class="col-auto">
                                <select id="departmentFilter" class="form-control py-2">
                                    <option value="" disabled selected>Select Department</option>
                                    <option value="PRODUCTION SYSTEM & DEVELOPMEN">PRODUCTION SYSTEM & DEVELOPMENT</option>
                                    <option value="QA ENGINE COMPONENT">QA ENGINE COMPONENT</option>
                                    <option value="IT DEVELOPMENT">IT DEVELOPMENT</option>
                                    <option value="PRODUCTION ELECTRIC">PRODUCTION ELECTRIC</option>
                                    <option value="ENGINEERING & QUALITY ELECTRICAL COMPONENT">ENGINEERING & QUALITY ELECTRICAL COMPONENT</option>
                                    <option value="ENGINERING BODY">ENGINERING BODY</option>
                                    <option value="QA BODY COMPONENT">QA BODY COMPONENT</option>
                                    <option value="BODY COMPONENT">BODY COMPONENT</option>
                                    <option value="MAINTENANCE ELECTRIC">MAINTENANCE ELECTRIC</option>
                                    <option value="PPIC">PPIC</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button id="filterButton" class="btn btn-primary">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="employee-table">
                            <thead>
                                <tr class="text-center align-middle">
                                    <th>NPK</th>
                                    <th>Nama</th>
                                    <th>Department</th>
                                    <th>Occupation</th>
                                    <?php
                                    $tahun = date('Y');
                                    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulanSekarang, $tahun);
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedData as $npk => $npkData)
                                <tr>
                                    <td>{{ $npk }}</td>
                                    <td>{{ $npkData[0]->empnm }}</td>
                                    <td>{{ $npkData[0]->department }}</td>
                                    <td>{{ $npkData[0]->occupation }}</td>

                                    @php
                                    $alpCount = 0; // Initialize ALP counter
                                    @endphp

                                    @for ($hari = 1; $hari <= $jumlah_hari; $hari++) @php $rsccd='' ; $today=date('j'); $month=date('m'); if ($bulanSekarang==$month) { if ($hari <=$today) { foreach ($npkData as $data) { if (!is_null($data->schdt) && date('j', strtotime($data->schdt)) == $hari) {
                                        $rsccd = $data->rsccd;

                                        // Increment ALP count if rsccd is ALP
                                        if (trim($rsccd) == 'ALP') {
                                        $alpCount++;
                                        }

                                        break;
                                        }
                                        }
                                        }
                                        } else {
                                        foreach ($npkData as $data) {
                                        if (!is_null($data->schdt) && date('j', strtotime($data->schdt)) == $hari) {
                                        $rsccd = $data->rsccd;

                                        // Increment ALP count if rsccd is ALP
                                        if (trim($rsccd) == 'ALP') {
                                        $alpCount++;
                                        }

                                        break;
                                        }
                                        }
                                        }
                                        @endphp

                                        <td {!! in_array(trim($rsccd), ['HDR', 'TL1' , 'TL2' , 'TL3' ]) ? 'class="text-success"' : '' !!}>
                                            {!! in_array(trim($rsccd), ['HDR', 'TL1', 'TL2', 'TL3']) ? '<i class="fas fa-check"></i>' : '<span class="badge badge-warning">'. $rsccd .'</span>' !!}
                                        </td>
                                        @endfor

                                        <!-- Display ALP count in the "Note" column -->
                                        <td>ALP : {{ $alpCount }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#employee-table').DataTable({
            "paging": true,
            "pagingType": "simple_numbers",
            "scrollY": "400px",
            "scrollX": true,
            "scrollCollapse": true,
            "fixedHeader": true,
            "fixedColumns": {
                leftColumns: 4,
            }
        });
    });

    // Menambahkan event listener untuk tombol filter
    document.getElementById('filterButton').addEventListener('click', function() {
        var selectedMonth = document.getElementById('departmentFilter').value;
        window.location.href = '{{ route("departmentattendance") }}/' + selectedMonth;
    });

    // Mendapatkan bulan saat ini (0-11, dimulai dari Januari) dan tahun saat ini
    var currentDate = new Date();
    var currentMonth = new Date().getMonth() + 1;
    var currentYear = currentDate.getFullYear();

    // Mendapatkan jumlah hari dalam bulan saat ini
    var numberOfDaysInMonth = new Date(currentYear, currentMonth, 0).getDate();

    // Menentukan nama-nama hari untuk memberi warna pada hari-hari akhir pekan
    var dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    // Mendapatkan tabel dengan ID 'employee-table'
    var table = document.getElementById('employee-table');

    // Membuat elemen-elemen <th> dan menambahkannya ke tabel
    for (var day = 1; day <= numberOfDaysInMonth; day++) {
        var th = document.createElement('th');

        // Mengatur kelas CSS berdasarkan hari dalam seminggu (0 untuk Minggu, 6 untuk Sabtu)
        var dayOfWeek = new Date(currentYear, currentMonth - 1, day).getDay();
        var isWeekend = dayOfWeek === 0 || dayOfWeek === 6;

        th.className = isWeekend ? 'text-danger' : ''; // Tambahkan warna merah jika hari akhir pekan

        // Menambahkan elemen <th> ke dalam baris pertama tabel
        table.rows[0].appendChild(th);

        // Tambahkan kolom note setelah kolom terakhir (30 atau 31)
        if (day === numberOfDaysInMonth) {
            var noteTh = document.createElement('th');
            noteTh.textContent = 'Note';
            table.rows[0].appendChild(noteTh);
        }

        // Setel teks untuk semua elemen <th>, termasuk kolom tanggal dan kolom note
        th.textContent = day;
    }
</script>
@endpush
@endsection