<x-filament-panels::page>
    @if($group)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Danh sách file đã nộp - Nhóm: {{ $group->group_code }}
                </div>
            </x-slot>

            {{ $this->table }}
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
