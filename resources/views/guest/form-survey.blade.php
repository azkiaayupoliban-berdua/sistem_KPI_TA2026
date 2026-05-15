<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survei Kepuasan Layanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fcfcfd; }

        /* Style untuk Rating Bintang */
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 0.5rem;
        }
        .star-rating input { display: none; }
        .star-rating label {
            font-size: 2.5rem;
            color: #e5e7eb;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #fbbf24;
            transform: scale(1.1);
        }
    </style>
</head>
<body class="antialiased">

    <div class="max-w-2xl mx-auto py-12 px-6">
        {{-- Header Section --}}
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-50 rounded-3xl mb-4">
                <i class="fa-solid fa-star text-2xl text-indigo-500"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Beri Ulasan Layanan</h2>
            <p class="text-gray-500 mt-2">Halo **{{ $nama_tamu }}**, masukan Anda membantu kami menjadi lebih baik.</p>

            @if(isset($durasi_menit))
                <div class="mt-6 inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100 px-4 py-1.5 rounded-2xl">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-emerald-700 uppercase tracking-wider">
                        Selesai dalam {{ $durasi_menit }} Menit
                    </span>
                </div>
            @endif
        </div>

        <form action="{{ route('survey.store') }}" method="POST" class="space-y-12">
            @csrf
            <input type="hidden" name="nomor_kunjungan" value="{{ $kunjungan->nomor_kunjungan }}">

            {{-- LOOP ASPEK --}}
            @foreach($aspek_survey as $aspek)
                <div class="mb-10">
                    <h3 class="text-indigo-500 font-black uppercase tracking-widest text-[10px] mb-6 text-center">
                        --- {{ $aspek->nama_aspek }} ---
                    </h3>

                    @foreach($aspek->pertanyaan as $item)
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 mb-6 shadow-sm hover:shadow-md transition-all">
                            <p class="text-gray-800 font-bold text-center mb-8 leading-relaxed">
                                "{{ $item->pertanyaan }}"
                            </p>

                            <div class="star-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star-{{ $item->id }}-{{ $i }}" name="jawaban[{{ $item->id }}]" value="{{ $i }}" required>
                                    <label for="star-{{ $item->id }}-{{ $i }}">
                                        <i class="fa-solid fa-star"></i>
                                    </label>
                                @endfor
                            </div>
                            <div class="flex justify-between mt-4 px-10 text-[9px] font-bold text-gray-300 uppercase tracking-tighter">
                                <span>Sangat Buruk</span>
                                <span>Sangat Puas</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            {{-- BAGIAN KRITIK & SARAN --}}
            <div class="mb-8">
                <h3 class="text-indigo-500 font-black uppercase tracking-widest text-[10px] mb-4 text-center">
                    --- Masukan Tambahan ---
                </h3>
                <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <label for="catatan" class="block text-gray-800 font-bold text-center mb-4">
                        Kritik, Saran, atau Kesan Anda?
                    </label>
                    <textarea
                        name="catatan"
                        id="catatan"
                        rows="4"
                        class="w-full px-5 py-4 rounded-2xl bg-gray-50 border-none focus:ring-2 focus:ring-indigo-100 text-sm text-gray-600 placeholder-gray-400 outline-none"
                        placeholder="Tuliskan masukan Anda di sini (opsional)..."
                    ></textarea>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="group relative w-full inline-flex items-center justify-center px-8 py-5 font-black text-white transition-all bg-indigo-600 rounded-[2.5rem] hover:bg-indigo-700 shadow-xl shadow-indigo-100 active:scale-95">
                    <span class="relative flex items-center gap-2 uppercase tracking-widest text-sm">
                        Kirim Ulasan Sekarang <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition-transform ml-2"></i>
                    </span>
                </button>

                {{-- <a href="{{ url('/') }}" class="block text-center mt-8 text-gray-400 font-bold text-xs hover:text-indigo-500 transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Beranda
                </a> --}}
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        title: 'Terima Kasih!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'Kembali ke Beranda',
        background: '#ffffff',
        allowOutsideClick: false, // User tidak bisa klik di luar pop-up untuk menutup
        customClass: {
            popup: 'rounded-[2.5rem]',
            confirmButton: 'rounded-full px-10 py-3 font-bold uppercase tracking-widest text-[10px]'
        }
    }).then((result) => {
        // Logika pemindahan halaman setelah klik tombol 'Kembali ke Beranda'
        if (result.isConfirmed) {
            window.location.href = "{{ url('/') }}";
        }
    });
</script>
@endif
</body>
</html>
