<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Undangan Kolaborator Event</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .event-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .access-code {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 15px 0;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Undangan Kolaborator Event</h1>
    </div>
    
    <div class="content">
        <h2>Halo {{ $collaborator->nama }}!</h2>
        
        <p>Anda telah ditambahkan sebagai <strong>kolaborator</strong> untuk event <strong>"{{ $event->nama }}"</strong>.</p>
        
        <p>Sebagai kolaborator, Anda dapat:</p>
        <ul>
            <li>Mengakses dashboard event khusus</li>
            <li>Melihat data transaksi dan penjualan tiket</li>
            <li>Memantau statistik event</li>
        </ul>

        <div class="event-details">
            <h3>Detail Event:</h3>
            <p><strong>Nama Event:</strong> {{ $event->nama }}</p>
            <p><strong>Tanggal:</strong> {{ $event->start_date->format('d M Y H:i') }} - {{ $event->end_date->format('d M Y H:i') }}</p>
            <p><strong>Lokasi:</strong> {{ $event->lokasi }}</p>
            @if($event->deskripsi)
            <p><strong>Deskripsi:</strong> {{ $event->deskripsi }}</p>
            @endif
        </div>

        <div style="text-align: center;">
            <a href="{{ $accessLink }}" class="button">Akses Dashboard Event</a>
        </div>

        <div class="access-code">
            <div>Kode Akses Anda:</div>
            <div>{{ $collaborator->kode_akses }}</div>
        </div>

        <p><strong>⚠️ Penting:</strong></p>
        <ul>
            <li>Link akses di atas adalah khusus untuk Anda</li>
            <li>Jangan bagikan link atau kode akses ini kepada orang lain</li>
            <li>Simpan email ini untuk referensi di masa mendatang</li>
        </ul>

        <p>Jika Anda memiliki pertanyaan atau mengalami kesulitan mengakses dashboard, silakan hubungi penyelenggara event.</p>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis. Terima kasih telah bergabung sebagai kolaborator!</p>
        <p>© {{ date('Y') }} Platform Event Management</p>
    </div>
</body>
</html>
