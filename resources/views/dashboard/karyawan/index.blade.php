<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Karyawan') }}
            </h2>
            <a href="{{ route('dashboard.karyawan.report') }}" target="_blank" class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition duration-150 ease-in-out">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Export PDF
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $edit ? 'Edit Karyawan' : 'Tambah Karyawan' }}
                    </h3>

                    <form method="POST" action="{{ $edit ? route('dashboard.karyawan.update', $edit) : route('dashboard.karyawan.store') }}" class="grid gap-4 md:grid-cols-4">
                        @csrf
                        @if ($edit)
                            @method('PUT')
                        @endif

                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input id="nama" name="nama" value="{{ old('nama', $edit->nama ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('nama') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tgl_lahir" class="block text-sm font-medium text-gray-700">Tgl Lahir</label>
                            <input id="tgl_lahir" type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $edit->tgl_lahir ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('tgl_lahir') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="gaji" class="block text-sm font-medium text-gray-700">Gaji</label>
                            <input id="gaji" type="number" step="0.01" min="0" name="gaji" value="{{ old('gaji', $edit->gaji ?? 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('gaji') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-end gap-2">
                            <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white">
                                {{ $edit ? 'Update' : 'Simpan' }}
                            </button>

                            @if ($edit)
                                <a href="{{ route('dashboard.karyawan.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Batal</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tgl Lahir</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Gaji</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($karyawan as $row)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $row->id }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $row->nama }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $row->tgl_lahir }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">Rp {{ number_format((float) $row->gaji, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('dashboard.karyawan.edit', $row) }}" class="font-medium text-indigo-600">Edit</a>
                                        <form method="POST" action="{{ route('dashboard.karyawan.destroy', $row) }}" class="inline" onsubmit="return confirm('Hapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="ml-3 font-medium text-red-600">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data karyawan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
