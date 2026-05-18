<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Thông tin nhóm --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-2 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary-600">
                    <path fill-rule="evenodd" d="M8.25 6a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zm7.5 3a3 3 0 00-3 3v6.75a3 3 0 003 3h7.5a3 3 0 003-3v-6.75a3 3 0 00-3-3h-7.5zm-9.75 3a3 3 0 00-3 3v6.75a3 3 0 003 3h6.75a3 3 0 003-3v-6.75a3 3 0 00-3-3H6.375zM12 12.75a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
                </svg>
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">Thông tin nhóm</h3>
            </div>
            <div class="p-6">
                @if($group)
                    <div class="space-y-6">
                        {{-- Thông tin chung --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Mã nhóm</div>
                                <div class="font-semibold text-lg text-primary-600">{{ $group->group_code }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Năm học</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $group->academicYear?->name ?? '-' }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Thành viên</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $group->members->count() }} sinh viên</div>
                            </div>
                        </div>

                        {{-- Danh sách thành viên --}}
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-400">
                                    <path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 4.5h-2.25a3 3 0 00-2.238.98c-.12.144-.25.28-.387.407A3.001 3.001 0 007.5 6h-3.375A3.375 3.375 0 003 9.375V18a3 3 0 003 3h6.75a3 3 0 003-3V6.75a3 3 0 00-3-3h-.75z" clip-rule="evenodd"/>
                                </svg>
                                Danh sách thành viên
                            </h4>
                            <div class="space-y-2">
                                @foreach($group->members as $member)
                                    <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-sm">
                                            {{ substr($member->student->ten, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $member->student->full_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $member->student->mssv }}
                                            </div>
                                        </div>
                                        @if($member->is_leader)
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300 text-xs font-medium">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3">
                                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Trưởng nhóm
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="py-10 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-gray-400">
                                <path fill-rule="evenodd" d="M8.25 6a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zm7.5 3a3 3 0 00-3 3v6.75a3 3 0 003 3h7.5a3 3 0 003-3v-6.75a3 3 0 00-3-3h-7.5zm-9.75 3a3 3 0 00-3 3v6.75a3 3 0 003 3h6.75a3 3 0 003-3v-6.75a3 3 0 00-3-3H6.375zM12 12.75a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium dark:text-gray-400">Bạn chưa có nhóm thực hiện</p>
                        <p class="mt-2 text-sm text-gray-400">Bạn vẫn có thể đăng ký đề tài cá nhân hoặc liên hệ Admin để ghép nhóm.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Thông tin đề tài --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-2 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary-600">
                    <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM12.75 12a.75.75 0 00-1.5 0v4.94l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3a.75.75 0 001.06 0l3-3a.75.75 0 00-1.06-1.06l-1.72 1.72V12z" clip-rule="evenodd"/>
                    <path d="M14.25 5.25a5.25 5.25 0 00-5.25 5.25v-1.125A3.375 3.375 0 0112.375 6h1.5V5.25z"/>
                </svg>
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">Thông tin đề tài</h3>
            </div>
            <div class="p-6">
                @if($topic)
                    <div class="space-y-5">
                        {{-- Tên đề tài --}}
                        <div class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-100 dark:border-primary-800">
                            <div class="text-xs text-primary-600 dark:text-primary-400 font-medium mb-1 uppercase tracking-wide">Tên đề tài (Tiếng Việt)</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $topic->ten_de_tai_tv }}</div>
                        </div>

                        @if($topic->ten_de_tai_ta)
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tên đề tài (Tiếng Anh)</div>
                                <div class="text-gray-700 dark:text-gray-300 italic">{{ $topic->ten_de_tai_ta }}</div>
                            </div>
                        @endif

                        {{-- Thông tin khác --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3">
                                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd"/>
                                    </svg>
                                    Giảng viên HD
                                </div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $topic->gvhd?->display_name ?? 'Chưa phân công' }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Trạng thái</div>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Đang chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Bị từ chối',
                                    ];
                                    $color = $statusColors[$topic->status] ?? 'gray';
                                @endphp
                                <span @class([
                                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                    'bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300' => $color === 'warning',
                                    'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300' => $color === 'success',
                                    'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300' => $color === 'danger',
                                    'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $color === 'gray',
                                ])>
                                    {{ $statusLabels[$topic->status] ?? $topic->status }}
                                </span>
                            </div>
                        </div>

                        @if($topic->status === 'pending')
                            <div class="flex items-center gap-2 p-3 bg-info-50 dark:bg-info-900/20 rounded-lg border border-info-100 dark:border-info-800">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-info-500 flex-shrink-0">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.064 1.756l-1.075 3.939-.99-1.033c-.628-.653-1.65-.653-2.278 0l-.99 1.033-1.075-3.939c-.373-1.293.918-2.329 2.064-1.756l.66.33zm7.294 1.442c0 .414-.336.75-.75.75h-.75v3.75a.75.75 0 01-1.5 0v-3.75h-1.5a.75.75 0 010-1.5h3a.75.75 0 01.75.75z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-info-700 dark:text-info-300">Đề tài đang chờ duyệt. Bạn có thể chỉnh sửa trước khi được duyệt.</span>
                            </div>
                            <div class="pt-2">
                                <a href="{{ url('student/my-topics/edit') }}" class="fi-btn fi-btn-size-md fi-btn-color-primary fi-color-custom fi-color-primary inline-flex items-center justify-center gap-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 bg-primary-600 text-white hover:bg-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                        <path d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32L19.513 8.199z"/>
                                    </svg>
                                    Chỉnh sửa đề tài
                                </a>
                            </div>
                        @elseif($topic->status === 'approved')
                            <div class="flex items-center gap-2 p-3 bg-success-50 dark:bg-success-900/20 rounded-lg border border-success-100 dark:border-success-800">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-success-500 flex-shrink-0">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-success-700 dark:text-success-300">Đề tài đã được phê duyệt. Bạn không thể chỉnh sửa.</span>
                            </div>
                        @elseif($topic->status === 'rejected')
                            <div class="flex items-center gap-2 p-3 bg-danger-50 dark:bg-danger-900/20 rounded-lg border border-danger-100 dark:border-danger-800">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-danger-500 flex-shrink-0">
                                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm text-danger-700 dark:text-danger-300">Đề tài đã bị từ chối. Vui lòng liên hệ GVHD để biết thêm chi tiết.</span>
                            </div>
                        @endif
                    </div>
                @elseif($group)
                    <div class="py-10 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-gray-400">
                                <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM12.75 12a.75.75 0 00-1.5 0v4.94l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3a.75.75 0 001.06 0l3-3a.75.75 0 00-1.06-1.06l-1.72 1.72V12z" clip-rule="evenodd"/>
                                <path d="M14.25 5.25a5.25 5.25 0 00-5.25 5.25v-1.125A3.375 3.375 0 0112.375 6h1.5V5.25z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium mb-1 dark:text-gray-400">Nhóm chưa có đề tài</p>
                        <p class="text-sm text-gray-400 mb-4">Hãy nhập đề tài để bắt đầu làm đồ án</p>
                        <a href="{{ url('student/my-topics/create') }}" class="fi-btn fi-btn-size-md fi-btn-color-primary fi-color-custom fi-color-primary inline-flex items-center justify-center gap-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 bg-primary-600 text-white hover:bg-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd"/>
                            </svg>
                            Nhập đề tài
                        </a>
                    </div>
                @else
                    <div class="py-10 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-gray-400">
                                <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM12.75 12a.75.75 0 00-1.5 0v4.94l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3a.75.75 0 001.06 0l3-3a.75.75 0 00-1.06-1.06l-1.72 1.72V12z" clip-rule="evenodd"/>
                                <path d="M14.25 5.25a5.25 5.25 0 00-5.25 5.25v-1.125A3.375 3.375 0 0112.375 6h1.5V5.25z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium mb-1 dark:text-gray-400">Bạn có thể đăng ký đề tài cá nhân</p>
                        <p class="text-sm text-gray-400 mb-4">Hoặc liên hệ Admin để ghép nhóm trước khi đăng ký đề tài.</p>
                        <a href="{{ url('student/my-topics/create') }}" class="fi-btn fi-btn-size-md fi-btn-color-primary fi-color-custom fi-color-primary inline-flex items-center justify-center gap-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-200 bg-primary-600 text-white hover:bg-primary-500 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd"/>
                            </svg>
                            Nhập đề tài cá nhân
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Trạng thái nộp file --}}
        @if($topic && $topic->status === 'approved')
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lg:col-span-2">
            <div class="fi-section-header flex items-center gap-2 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary-600">
                    <path fill-rule="evenodd" d="M3.75 3A2.25 2.25 0 016 .75h3.75v19.5H6a2.25 2.25 0 01-2.25-2.25V3zM9.75 3v19.5h9.75a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0019.5 3H9.75z" clip-rule="evenodd"/>
                </svg>
                <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">Trạng thái nộp file</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($submissionPhases as $phase)
                        <div @class([
                            'relative p-4 rounded-lg border',
                            'border-primary-200 bg-primary-50 dark:border-primary-800 dark:bg-primary-900/20' => $phase['is_open'],
                            'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800' => !$phase['is_open'],
                        ])>
                            {{-- Header --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 @class([
                                        'font-semibold text-sm',
                                        'text-primary-900 dark:text-primary-300' => $phase['is_open'],
                                        'text-gray-700 dark:text-gray-300' => !$phase['is_open'],
                                    ])>
                                        {{ $phase['name'] }}
                                    </h4>
                                    @if($phase['is_not_yet_open'])
                                        <span class="inline-flex items-center gap-1 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3">
                                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zM12.75 6a.75.75 0 00-1.5 0v6c0 .199.079.39.22.53l2.25 2.25a.75.75 0 101.06-1.06l-2.03-2.03V6z" clip-rule="evenodd"/>
                                            </svg>
                                            Chưa mở
                                        </span>
                                    @elseif($phase['is_closed'])
                                        <span class="inline-flex items-center gap-1 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3">
                                                <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd"/>
                                            </svg>
                                            Đã đóng
                                        </span>
                                    @elseif($phase['is_open'])
                                        <span class="inline-flex items-center gap-1 mt-1 text-xs text-primary-600 dark:text-primary-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3">
                                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                                            </svg>
                                            Đang mở
                                        </span>
                                    @endif
                                </div>

                                {{-- Status Badge --}}
                                @if($phase['submission'])
                                    <span @class([
                                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                        'bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-300' => $phase['status_color'] === 'warning',
                                        'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300' => $phase['status_color'] === 'success',
                                        'bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300' => $phase['status_color'] === 'danger',
                                        'bg-info-100 text-info-700 dark:bg-info-900 dark:text-info-300' => $phase['status_color'] === 'info',
                                        'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' => $phase['status_color'] === 'gray',
                                    ])>
                                        {{ $phase['status_label'] }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        Chưa nộp
                                    </span>
                                @endif
                            </div>

                            {{-- Description --}}
                            @if($phase['description'])
                                <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $phase['description'] }}</p>
                            @endif

                            {{-- File Info --}}
                            @if($phase['submission'])
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-gray-400 flex-shrink-0">
                                            <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM12.75 12a.75.75 0 00-1.5 0v4.94l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3a.75.75 0 001.06 0l3-3a.75.75 0 00-1.06-1.06l-1.72 1.72V12z" clip-rule="evenodd"/>
                                            <path d="M14.25 5.25a5.25 5.25 0 00-5.25 5.25v-1.125A3.375 3.375 0 0112.375 6h1.5V5.25z"/>
                                        </svg>
                                        <span class="truncate text-gray-700 dark:text-gray-300">{{ $phase['submission']->original_name }}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Nộp ngày: {{ $phase['submission']->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    @if($phase['submission']->note)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Ghi chú: {{ $phase['submission']->note }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div @class([
                                'pt-2 border-t',
                                'border-primary-200 dark:border-primary-800' => $phase['is_open'],
                                'border-gray-200 dark:border-gray-700' => !$phase['is_open'],
                            ])>
                                @if($phase['is_open'])
                                    @if($phase['submission'] && in_array($phase['status'], ['pending', 'rejected']))
                                        {{-- Nộp lại: chuyển đến trang tạo với phase_id --}}
                                        <a href="{{ \App\Filament\Student\Resources\FileSubmissionResource::getUrl('edit', ['record' => $phase['submission']->id]) }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary w-full inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 bg-primary-600 text-white hover:bg-primary-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.903-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.919z" clip-rule="evenodd"/>
                                            </svg>
                                            Nộp lại
                                        </a>
                                    @elseif(!$phase['submission'])
                                        {{-- Nộp file: chuyển đến trang tạo với phase_id --}}
                                        <a href="{{ \App\Filament\Student\Resources\FileSubmissionResource::getUrl('create', ['phase_id' => $phase['id']]) }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary w-full inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 bg-primary-600 text-white hover:bg-primary-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd"/>
                                            </svg>
                                            Nộp file
                                        </a>
                                    @else
                                        {{-- Xem chi tiết: chuyển đến trang danh sách file nộp --}}
                                        <a href="{{ \App\Filament\Student\Resources\FileSubmissionResource::getUrl('view', ['record' => $phase['submission']->id]) }}" class="fi-btn fi-btn-size-sm fi-btn-color-primary w-full inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 bg-primary-600 text-white hover:bg-primary-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                                                <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Xem chi tiết
                                        </a>
                                    @endif
                                @else
                                    <button disabled class="fi-btn fi-btn-size-sm fi-btn-color-gray w-full inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed">
                                        @if($phase['is_not_yet_open'])
                                            Chưa mở
                                        @elseif($phase['is_closed'])
                                            Đã đóng
                                        @else
                                            Không khả dụng
                                        @endif
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @php
                    $hasOpenPhase = collect($submissionPhases)->contains('is_open', true);
                @endphp

                @if($hasOpenPhase)
                    <div class="mt-4 p-3 bg-info-50 dark:bg-info-900/20 rounded-lg border border-info-100 dark:border-info-800">
                        <div class="flex items-center gap-2 text-sm text-info-700 dark:text-info-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.064 1.756l-1.075 3.939-.99-1.033c-.628-.653-1.65-.653-2.278 0l-.99 1.033-1.075-3.939c-.373-1.293.918-2.329 2.064-1.756l.66.33zm7.294 1.442c0 .414-.336.75-.75.75h-.75v3.75a.75.75 0 01-1.5 0v-3.75h-1.5a.75.75 0 010-1.5h3a.75.75 0 01.75.75z" clip-rule="evenodd"/>
                            </svg>
                            <span>Có giai đoạn đang mở để nộp file. Bạn có thể nộp file hoặc thay thế file đã nộp (nếu chưa được duyệt).</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</x-filament-panels::page>
