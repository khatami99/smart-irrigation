<div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
    <h2 class="text-lg font-bold text-slate-800 tracking-tight">Dataset Historis (per 15 hari)</h2>
    <span class="px-3 py-1 bg-white border border-slate-200 text-[10px] font-bold text-slate-500 rounded-full shadow-sm">
        Total: {{ $tableData->total() }} Data
    </span>
</div>

<div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="text-slate-400 text-[11px] uppercase tracking-widest border-b border-slate-100">
                <th class="px-6 py-4 font-bold">Tanggal</th>
                <th class="px-6 py-4 text-center font-bold">ETo</th>
                <th class="px-6 py-4 text-center font-bold">ETc</th>
                <th class="px-6 py-4 text-center font-bold">Hujan (mm)</th>
                <th class="px-6 py-4 text-center font-bold">Kebutuhan</th>
                <th class="px-6 py-4 text-center font-bold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
            @foreach($tableData as $item)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 font-semibold text-slate-700">{{ $item->tanggal }}</td>
                    <td class="px-6 py-4 text-center">{{ number_format($item->eto, 1) }}</td>
                    <td class="px-6 py-4 text-center">{{ number_format($item->etc, 1) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black italic">
                            {{ $item->curah_hujan }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg italic">
                            {{ $item->kebutuhan_air }} L
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Edit --}}
                            <a href="{{ route('irrigation.edit', $item) }}"
                               class="px-3 py-1.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg text-xs font-semibold transition">
                                Edit
                            </a>
                            {{-- Tombol Hapus --}}
                            <form method="POST" action="{{ route('irrigation.destroy', $item) }}"
                                  onsubmit="return confirm('Hapus data tanggal {{ $item->tanggal }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 bg-red-50 text-red-500 hover:bg-red-100 rounded-lg text-xs font-semibold transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
    {{ $tableData->links() }}
</div>
