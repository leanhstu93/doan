@if(isset($url) && $type === 'pdf')
    <div style="width: 100%; height: 70vh; min-height: 500px;">
        <iframe src="{{ $url }}" style="width: 100%; height: 100%; border: none;"></iframe>
    </div>
@else
    <div class="text-center py-8">
        <div class="mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </div>

        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            {{ $fileName ?? 'File' }}
        </h4>

        <p class="text-sm text-gray-500 mb-4">
            Loại file: {{ strtoupper($fileType ?? 'Unknown') }}<br>
            @if(isset($fileSize))
                Dung lượng: {{ number_format($fileSize / 1024, 2) }} KB
            @endif
        </p>

        @if(isset($downloadUrl))
            <a href="{{ $downloadUrl }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Tải xuống để xem
            </a>
        @endif
    </div>
@endif
