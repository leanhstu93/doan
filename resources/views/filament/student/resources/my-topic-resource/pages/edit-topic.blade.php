<x-filament-panels::page>
    @if($group && $topic)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Thông tin đề tài nhóm: {{ $group->group_code }}
                </div>
            </x-slot>

            @if($topic->status !== 'pending')
                <div class="p-4 mb-6 rounded-lg bg-gray-50">
                    <div class="flex items-center gap-3">
                        @php
                            $statusColor = $topic->status === 'approved' ? 'text-green-600' : 'text-red-600';
                            $statusLabel = [
                                'approved' => 'Đã duyệt',
                                'rejected' => 'Bị từ chối',
                            ][$topic->status] ?? $topic->status;
                        @endphp
                        @if($topic->status === 'approved')
                            <svg class="w-8 h-8 {{ $statusColor }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 2rem; height: 2rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @else
                            <svg class="w-8 h-8 {{ $statusColor }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 2rem; height: 2rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Trạng thái:</p>
                            <p class="font-semibold {{ $statusColor }}">{{ $statusLabel }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($canEdit)
                <form wire:submit.prevent="submit">
                    {{ $this->form }}

                    <div class="flex justify-end gap-3 mt-6">
                        <x-filament::button
                            type="button"
                            color="gray"
                            :href="url('student')"
                            tag="a"
                        >
                            Hủy
                        </x-filament::button>

                        <x-filament::button type="submit" color="primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1rem; height: 1rem; display: inline-block; vertical-align: middle;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Cập nhật đề tài
                        </x-filament::button>
                    </div>
                </form>
            @else
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-500">Tên đề tài (TV):</span>
                        <p class="mt-1 font-medium">{{ $topic->ten_de_tai_tv }}</p>
                    </div>

                    @if($topic->ten_de_tai_ta)
                        <div>
                            <span class="text-gray-500">Tên đề tài (TA):</span>
                            <p class="mt-1">{{ $topic->ten_de_tai_ta }}</p>
                        </div>
                    @endif

                    <div>
                        <span class="text-gray-500">GVHD:</span>
                        <p class="mt-1">{{ $topic->gvhd?->display_name ?? '-' }}</p>
                    </div>

                    @if($topic->status !== 'pending')
                        <div class="p-4 mt-6 rounded-lg bg-yellow-50">
                            <p class="text-sm text-yellow-700 flex items-center gap-2">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Đề tài đã được duyệt. Bạn không thể chỉnh sửa.</span>
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="py-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 3rem; height: 3rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Không tìm thấy thông tin đề tài.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
