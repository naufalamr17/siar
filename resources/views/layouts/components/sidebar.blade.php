<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img src="{{ url('assets/img/S1AR.png') }}" alt="S1AR" class="img-fluid" style="max-width: 100px;">
        </div>

        <div class="sidebar-brand sidebar-brand-sm">
            <img src="{{ url('assets/img/S1AR.png') }}" alt="S1AR" class="img-fluid" style="max-width: 50px;">
        </div>

        @php
        $npk = auth()->user()->npk;

        $userInfo = DB::connection('mysql2')->select(DB::raw(
            "
            SELECT kehadiran2.empno, hirarki.hirar, MAX(hirarki.mutdt) AS mutdt, hirarkidesc.descr
            FROM kehadiran2
            LEFT JOIN hirarki ON kehadiran2.empno = hirarki.empno
            LEFT JOIN hirarkidesc ON hirarki.hirar = hirarkidesc.hirar
            WHERE kehadiran2.empno = $npk
            GROUP BY kehadiran2.empno, hirarki.hirar, hirarkidesc.descr
            ORDER BY mutdt DESC LIMIT 1;
            "
        ));

        if (!empty($userInfo)) {
        $npkDesc = $userInfo[0]->hirar; // Use array syntax

        $cleanedString = str_replace(' ', '', $npkDesc);

        // Hitung jumlah karakter
        $jumlahKarakter = strlen($cleanedString);

        // Tentukan jenis berdasarkan jumlah karakter
        if ($jumlahKarakter == 5) {
        $jenis = 'KDP';
        } elseif ($jumlahKarakter == 7) {
        $jenis = 'SPV';
        } elseif ($jumlahKarakter == 9) {
        $jenis = 'LDR/OPR';
        } elseif ($jumlahKarakter == 2 || $jumlahKarakter == 3) {
        $jenis = 'GMR';
        } else {
        $jenis = 'Jenis tidak dikenali'; // Atur jenis untuk kondisi lainnya
        }
        } else {
        // Handle the case where no results are returned
        $jenis = 'Jenis tidak dikenali';
        }

        $cleanedStringDept = str_replace(' ', '', $userInfo[0]->descr);
        $cleanedStringDeptFinal = substr($cleanedStringDept, 0, 3);
        $userInfoOccupation = $jenis;
        $userInfoDept = $cleanedStringDeptFinal;
        @endphp
        @if ($userInfoOccupation == 'GMR' or $userInfoOccupation == 'KDP' or $userInfoDept == 'HRD')
        <ul class="sidebar-menu">
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown {{ request()->is('dailyattendance*')||request()->is('weeklyattendance*')||request()->is('monthlyattendance*') ? 'text-primary' : '' }}"><i class="fas fa-th"></i>
                    <span>Attendance</span></a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="nav-link {{ request()->is('dailyattendance*') ? 'text-primary' : '' }}" href="/dailyattendance">Daily attendance</a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->is('monthlyattendance*') ? 'text-primary' : '' }}" href="/monthlyattendance">Monthly attendance</a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->is('historyattendance*') ? 'text-primary' : '' }}" href="/historyattendance">History attendance</a>
                    </li>
                </ul>
                @if ($userInfoDept == 'HRD')
                <a class="sidebar-menu {{ request()->is('holiday*') ? 'text-primary' : '' }}" href="/holiday"><i class="fas fa-calendar"></i><span>Master Holiday</span></a>
                @endif
                <a class="sidebar-menu" href="#"><i class="fas fa-edit"></i><span>Cuzia<span class="text-sm badge bg-secondary" style="width: 100px;">Coming Soon</span></span></a>
            </li>
        </ul>
        @else
        <ul class="sidebar-menu">
            <li>
                <a class="nav-link {{ request()->is('historyattendance*') || request()->is('/') ? 'text-primary' : '' }}" href="/historyattendance"><i class="fas fa-th"></i><span>History attendance</span></a>
                <a class="nav-link {{ request()->is('monthlyattendance*') ? 'text-primary' : '' }}" href="/monthlyattendance"><i class="fas fa-th"></i><span>Monthly attendance</span></a>
                <a class="sidebar-menu" href="#"><i class="fas fa-edit"></i><span>Cuzia<span class="text-sm badge bg-secondary" style="width: 100px;">Coming Soon</span></span></a>
            </li>
        </ul>
        @endif

    </aside>
</div>