@props(['binhLuans', 'baiHocId', 'lopHocId', 'role'])

<div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            Bình luận ({{ count($binhLuans) }})
        </h3>
    </div>
    
    <!-- Form thêm bình luận -->
    <div class="px-4 py-4 sm:px-6 border-b border-gray-200 bg-gray-50">
        <form action="{{ route($role.'.binh-luan.store') }}" method="POST" class="flex flex-col space-y-3">
            @csrf
            <input type="hidden" name="bai_hoc_id" value="{{ $baiHocId }}">
            <input type="hidden" name="lop_hoc_id" value="{{ $lopHocId }}">
            
            <div>
                <label for="noi_dung" class="block text-sm font-medium text-gray-700 mb-1">Thêm bình luận của bạn</label>
                <textarea id="noi_dung" name="noi_dung" rows="3" required 
                          class="shadow-sm focus:ring-red-500 focus:border-red-500 block w-full sm:text-sm border-gray-300 rounded-md"
                          placeholder="Viết bình luận của bạn..."></textarea>
            </div>
            
            <div class="text-right">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Gửi bình luận
                </button>
            </div>
        </form>
    </div>
    
    <!-- Danh sách bình luận -->
    <div class="divide-y divide-gray-200">
        @forelse($binhLuans as $binhLuan)
            <div class="px-4 py-4 sm:px-6">
                <div class="flex justify-between items-start">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-100">
                                <span class="font-medium text-gray-600 leading-none">
                                    {{ strtoupper(substr($binhLuan->nguoiDung->ho, 0, 1)) . strtoupper(substr($binhLuan->nguoiDung->ten, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ $binhLuan->nguoiDung->ho . ' ' . $binhLuan->nguoiDung->ten }}</h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'giao_vien')
                                        bg-blue-100 text-blue-800
                                    @elseif(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'tro_giang')
                                        bg-green-100 text-green-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    @if(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'giao_vien')
                                        Giáo viên
                                    @elseif(optional($binhLuan->nguoiDung->vaiTros->first())->ten == 'tro_giang')
                                        Trợ giảng
                                    @else
                                        Học viên
                                    @endif
                                </span>
                            </div>
                            <div class="mt-1 text-sm text-gray-700">
                                <p>{{ $binhLuan->noi_dung }}</p>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                <time datetime="{{ $binhLuan->tao_luc }}">{{ \Carbon\Carbon::parse($binhLuan->tao_luc)->format('d/m/Y H:i') }}</time>
                            </div>
                        </div>
                    </div>
                    
                    @if($binhLuan->nguoi_dung_id == session('nguoi_dung_id'))
                        <div>
                            <form action="{{ route($role.'.binh-luan.destroy', $binhLuan->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="px-4 py-12 text-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có bình luận nào</h3>
                <p class="mt-1 text-sm text-gray-500">Hãy là người đầu tiên chia sẻ ý kiến về bài học này.</p>
            </div>
        @endforelse
    </div>
</div> 