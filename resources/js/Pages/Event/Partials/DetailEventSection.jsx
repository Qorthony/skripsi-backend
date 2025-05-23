import dayjs from "dayjs";

export default function DetailEventSection({ event, tickets }) {
    return (
        <div className="py-12">
            <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div className="p-6">

                        <header className="flex flex-col items-center justify-between">
                            <h2 className="text-2xl font-bold text-gray-800">{event.nama}</h2>
                            <p className="mt-1 w-fit text-sm text-gray-600 bg-gray-300 rounded-full px-2">
                                {event.status}
                            </p>
                        </header>

                        {/* Poster Event */}
                        {event.poster && (
                            <div className="mt-4 justify-center flex">
                                <img
                                    src={event.poster}
                                    alt="Poster Event"
                                    className="max-h-64 rounded shadow border"
                                />
                            </div>
                        )}

                        {event.status === 'rejected' && (
                            <div className="mt-4 border border-gray-200 py-4 px-4 rounded-lg">
                                <p className="text-lg font-medium text-gray-900">Alasan Penolakan :</p>
                                <p className="mt-1 text-sm text-gray-600">{event.alasan_penolakan}</p>
                            </div>
                        )}

                        <div className="mt-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-sm text-gray-600">Lokasi</p>
                                    <p className="text-sm text-gray-900">{event.lokasi}</p>
                                </div>
                                {event.lokasi === 'offline' && (
                                    <div>
                                        <p className="text-sm text-gray-600">Kota dan Alamat</p>
                                        <p className="text-sm text-gray-900">{event.kota} - {event.alamat_lengkap}</p>
                                    </div>
                                )}
                                {event.lokasi === 'online' && (
                                    <div>
                                        <p className="text-sm text-gray-600">Tautan Acara</p>
                                        <p className="text-sm text-gray-900">
                                            <a href={event.tautan_acara} target="_blank" rel="noopener noreferrer">
                                                {event.tautan_acara}
                                            </a>
                                        </p>
                                    </div>
                                )}
                                <div>
                                    <p className="text-sm text-gray-600">Jadwal Mulai</p>
                                    <p className="text-sm text-gray-900">{event.jadwal_mulai}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-600">Jadwal Selesai</p>
                                    <p className="text-sm text-gray-900">{event.jadwal_selesai}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-600">Deskripsi</p>
                                    <p className="text-sm text-gray-900">{event.deskripsi ? event.deskripsi : 'Belum ada deskripsi'}</p>
                                </div>
                            </div>
                        </div>

                        <div className="mt-6">
                            <h3 className="text-lg font-medium text-gray-900">Tiket</h3>
                            {tickets.length == 0 && (
                                <p className="text-sm text-gray-600">Belum ada tiket yang terdaftar</p>
                            )}
                            <div className="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                {tickets.map((ticket, index) => (
                                    <div key={index} className="bg-white border rounded-lg shadow-sm p-4">
                                        <div className="flex justify-between items-start mb-4">
                                            <div>
                                                <h3 className="text-lg font-medium text-gray-900">{ticket.nama}</h3>
                                                <p className="text-sm text-gray-500">Kuota: {ticket.kuota}</p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-lg font-bold text-gray-900">
                                                    {ticket.harga ? `Rp ${ticket.harga.toLocaleString('id-ID')}` : 'Gratis'}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="space-y-2 text-sm text-gray-600">
                                            <p>Mulai: {dayjs(ticket.waktu_buka).format('DD/MM/YYYY HH:mm')}</p>
                                            <p>Tutup: {dayjs(ticket.waktu_tutup).format('DD/MM/YYYY HH:mm')}</p>
                                            {ticket.keterangan && (
                                                <p className="text-gray-500">{ticket.keterangan}</p>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    );
}
