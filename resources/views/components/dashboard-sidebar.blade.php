@if ($role == 'hoc_vien')
    <x-nav-link route="hoc-vien.dashboard" active="{{ $active == 'dashboard' }}" icon="home">
        Dashboard
    </x-nav-link>
    <x-nav-link route="hoc-vien.lop-hoc.index" active="{{ $active == 'lop-hoc' }}" icon="book-open">
        Lớp học
    </x-nav-link>
    <x-nav-link route="hoc-vien.khoa-hoc.index" active="{{ $active == 'khoa-hoc' }}" icon="academic-cap">
        Khóa học
    </x-nav-link>
    <x-nav-link route="hoc-vien.ket-qua.index" active="{{ $active == 'ket-qua' }}" icon="chart-bar">
        Kết quả học tập
    </x-nav-link>
    <x-nav-link route="hoc-vien.profile.index" active="{{ $active == 'profile' }}" icon="user">
        Thông tin cá nhân
    </x-nav-link>
@endif 