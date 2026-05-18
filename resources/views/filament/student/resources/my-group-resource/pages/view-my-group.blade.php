<x-filament-panels::page>
    @if($group)
        <!-- Group Info Card -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Thông tin nhóm: {{ $group->group_code }}
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Topic Info -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Đề tài
                    </h3>
                    @if($group->topic)
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-500">Tên đề tài (Tiếng Việt):</span>
                                <p class="font-medium text-gray-900 mt-1">{{ $group->topic->ten_de_tai_tv }}</p>
                            </div>
                            @if($group->topic->ten_de_tai_ta)
                                <div>
                                    <span class="text-sm text-gray-500">Tên đề tài (Tiếng Anh):</span>
                                    <p class="font-medium text-gray-900 mt-1">{{ $group->topic->ten_de_tai_ta }}</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-yellow-600 bg-yellow-50 p-3 rounded-lg">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>Chưa có đề tài. Vui lòng nhập đề tài trong menu "Đề tài của tôi".</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Lecturer Info -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Giảng viên
                    </h3>
                    <div class="space-y-4">
                        <!-- GVHD -->
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Giảng viên hướng dẫn (GVHD)</p>
                                @if($group->topic && $group->topic->gvhd)
                                    <p class="font-medium text-gray-900">{{ $group->topic->gvhd->degree ?? '' }} {{ $group->topic->gvhd->full_name }}</p>
                                    @if($group->topic->gvhd->email)
                                        <p class="text-sm text-gray-500">{{ $group->topic->gvhd->email }}</p>
                                    @endif
                                @else
                                    <p class="text-gray-400 italic">Chưa phân công GVHD</p>
                                @endif
                            </div>
                        </div>

                        <!-- GVPB -->
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-secondary-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Giảng viên phản biện (GVPB)</p>
                                @if($group->topic && $group->topic->gvpb)
                                    <p class="font-medium text-gray-900">{{ $group->topic->gvpb->degree ?? '' }} {{ $group->topic->gvpb->full_name }}</p>
                                    @if($group->topic->gvpb->email)
                                        <p class="text-sm text-gray-500">{{ $group->topic->gvpb->email }}</p>
                                    @endif
                                @else
                                    <p class="text-gray-400 italic">Chưa phân công GVPB</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Group Members -->
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Thành viên nhóm
                </div>
            </x-slot>

            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MSSV</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Họ và tên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($group->members as $index => $member)
                            <tr class="{{ $member->student->id === $student->id ? 'bg-primary-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $member->student->mssv }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $member->student->full_name }}
                                    @if($member->student->id === $student->id)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800">
                                            Bạn
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $member->is_leader ? 'Trưởng nhóm' : 'Thành viên' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

    @else
        <x-filament::section>
            <div class="py-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 3rem; height: 3rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Bạn chưa được phân vào nhóm nào.</p>
                <p class="mt-2 text-sm">Vui lòng liên hệ Admin để được hỗ trợ.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
