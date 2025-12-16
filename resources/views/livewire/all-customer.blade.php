<div class="min-h-screen w-full bg-white text-gray-900 pt-0 p-8 relative overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
         <div class="flex items-center justify-between mb-4 pl-1">
           <h1 class="text-3xl font-medium tracking-tight">
                    <span class="text-[#5ca9ff]">Manage</span>
                    <span class="text-[#ac7bff]">Customers</span>
                </h1>

            <!-- Tombol Tambah Proyek Minimalis -->
             <div class="pt-3">
            <button wire:click="$set('showModal', true)" 
                class="text-white px-4 py-2 rounded-3xl shadow-sm flex items-center gap-2 text-xs font-medium hover:brightness-105 transition"
                style="background-color: #5ca9ff;">
                <i class="fas fa-plus"></i> Add Customer
            </button>
            </div>
        </div>

        <!-- Search + Filter -->
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative w-full sm:w-1/2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" wire:model.live="search" placeholder="Find customer..."
                    class="w-full pl-10 pr-3 py-2 rounded-full bg-white border border-gray-300 text-gray-900
                           focus:ring-1 focus:ring-blue-500 focus:border-transparent outline-none placeholder-gray-400 text-sm transition" />
            </div>

            <div class="flex gap-2">
                <button wire:click="$set('statusFilter', '')"
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $statusFilter === '' ? 'bg-[#5ca9ff] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    All Customer
                </button>
                <button wire:click="$set('statusFilter', 'aktif')"
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $statusFilter === 'aktif' ? 'bg-[#5ca9ff] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Active
                </button>
                <button wire:click="$set('statusFilter', 'tidak_aktif')"
                    class="px-3 py-1 rounded-full text-xs font-medium {{ $statusFilter === 'tidak_aktif' ? 'bg-[#5ca9ff] text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Not Active
                </button>
            </div>
        </div>

        <!-- List Customer Modern (Aktif vs Tidak Aktif) -->
        @if($customers->isEmpty())
            <div class="text-center text-xs italic py-10 text-gray-500 italic">No customers found.</div>
        @else
            <div>
                @foreach($customers as $c)
                    <div class="flex items-center justify-between transition px-4 py-3 cursor-pointer
                                {{ $c->status === 'aktif' ? 'bg-white shadow-sm hover:shadow-md' : 'bg-gray-50 text-gray-400 shadow-inner' }}">
                        
                        <!-- Foto profil -->
                        <div class="flex items-center gap-3">
                            <img src="{{ $c->foto ?? 'https://ui-avatars.com/api/?name='.urlencode($c->nama).'&background=007BFF&color=fff&size=64' }}" 
                                alt="{{ $c->nama }}" 
                                class="w-12 h-12 rounded-full object-cover border border-gray-200
                                        {{ $c->status !== 'aktif' ? 'opacity-60' : '' }}">

                            <!-- Info customer -->
                            <div class="flex flex-col">
                                <h3 class="text-sm font-medium truncate" title="{{ $c->nama }}
                                    {{ $c->status !== 'aktif' ? 'text-gray-500' : 'text-gray-800' }}">
                                    {{ $c->nama }}
                                </h3>
                                <p class="text-xs truncate" title="{{ $c->alamat }}">{{ $c->alamat }}</p>
                                <p class="text-xs">{{ $c->nomor_telepon }} | {{ $c->email }}</p>

                                @if($c->proyek->count())
    <div class="mt-1 flex flex-wrap gap-1">
        @foreach($c->proyek as $p)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium
                         bg-indigo-50 text-indigo-600 border border-indigo-200">
                {{ $p->nama_proyek }}
            </span>
        @endforeach
    </div>
@else
    <p class="text-[10px] italic text-gray-400 mt-1">
        No project
    </p>
@endif

                            </div>
                        </div>

                        <!-- Status + aksi Modern -->
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                        {{ $c->status === 'aktif' 
                                            ? 'bg-gradient-to-r from-green-400 to-teal-500 text-white shadow-sm' 
                                            : 'bg-gray-200 text-gray-700 shadow-inner' }}">
                                @if($c->status === 'aktif')
                                    <i class="fas fa-check-circle mr-1 text-white text-xs"></i>
                                    <span class="text-xs">Active</span>
                                @else
                                    <i class="fas fa-times-circle mr-1 text-gray-500 text-xs"></i>
                                    <span class="text-xs">Not Active</span>
                                @endif
                            </span>

                            <!-- Tombol aksi -->
                            <div class="flex gap-2">
                                <button wire:click.stop="edit({{ $c->id }})" 
                                        class="text-gray-500 hover:text-gray-700 text-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click.stop="delete({{ $c->id }})" 
                                        class="text-gray-500 hover:text-gray-700 text-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="border-gray-300">
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex justify-center">{{ $customers->links() }}</div>
        @endif



        <!-- Modal Add/Edit Customer -->
        @if($showModal)
            <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
                <div class="bg-white w-2/3 max-w-2xl shadow-2xl transform transition-transform duration-300 ease-out animate-fadeIn flex flex-col">
                    <div class="p-5 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-center" style="color: #9c62ff;">{{ $isEdit ? 'Update Customer' : 'Add Customer' }}</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" wire:model="nama" placeholder="Nama" class="border border-gray-300 rounded-full px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-xs">
                            <input type="text" wire:model="alamat" placeholder="Alamat" class="border border-gray-300 rounded-full px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-xs">
                            <input type="text" wire:model="nomor_telepon" placeholder="Nomor Telepon" class="border border-gray-300 rounded-full px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-xs">
                            <input type="email" wire:model="email" placeholder="Email" class="border border-gray-300 rounded-full px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-xs">
                            <input type="text" wire:model="catatan" placeholder="Catatan" class="border border-gray-300 rounded-full px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-xs">
                            <select wire:model="status" class="border border-gray-300 rounded-full px-3 py-2 text-gray-900 focus:ring-1 focus:ring-blue-500 focus:outline-none text-xs">
                                <option value="">-- Status --</option>
                                <option value="aktif">Active</option>
                                <option value="tidak_aktif">Not Active</option>
                            </select>
                        </div>
                        <div class="mt-5 flex justify-end gap-3">
                            @if($isEdit)
                                <button wire:click="update" class="bg-[#5ca9ff] hover:bg-blue-700 px-4 py-2 rounded-full shadow text-white text-xs font-medium transition">Update</button>
                            @else
                                <button wire:click="store" class="bg-[#5ca9ff] hover:bg-blue-700 px-4 py-2 rounded-full shadow text-white text-xs font-medium transition">Save</button>
                            @endif
                            <button wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-full shadow text-gray-800 text-xs font-medium transition">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        @keyframes fadeIn {0%{opacity:0;transform:scale(0.95);}100%{opacity:1;transform:scale(1);}}
        .animate-fadeIn{animation:fadeIn 0.3s forwards;}
    </style>
</div>
