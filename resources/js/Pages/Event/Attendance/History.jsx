import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import SecondaryButton from '@/Components/SecondaryButton';
import Table from '@/Components/Table';

export default function History({ auth, event, ticketIssued, checkins }) {
    const tableHeader = [
        {
            label: 'Waktu Check-in',
            key: 'checked_in_at',
            formatData: (value) => value ? new Date(value).toLocaleString('id-ID') : '-',
        },
        {
            label: 'Waktu Check-out',
            key: 'checked_out_at',
            formatData: (value) => value ? new Date(value).toLocaleString('id-ID') : '-',
        },
        {
            label: 'Durasi Kehadiran',
            key: 'duration',
            formatData: (value, row) => {
                if (!row.checked_in_at) return '-';
                
                const checkinTime = new Date(row.checked_in_at);
                const checkoutTime = row.checked_out_at ? new Date(row.checked_out_at) : new Date();
                const duration = checkoutTime - checkinTime;
                
                const hours = Math.floor(duration / (1000 * 60 * 60));
                const minutes = Math.floor((duration % (1000 * 60 * 60)) / (1000 * 60));
                
                if (hours > 0) {
                    return `${hours} jam ${minutes} menit`;
                } else {
                    return `${minutes} menit`;
                }
            },
        },
        {
            label: 'Status',
            key: 'checked_out_at',
            formatData: (value) => (
                <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    value 
                        ? 'bg-gray-100 text-gray-800' 
                        : 'bg-green-100 text-green-800'
                }`}>
                    {value ? 'Selesai' : 'Sedang Hadir'}
                </span>
            ),
        },
    ];

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            Riwayat Kehadiran
                        </h2>
                        <p className="text-sm text-gray-600 mt-1">
                            {ticketIssued.email_penerima} - {event.nama}
                        </p>
                    </div>
                    <div className="flex space-x-3">
                        <Link href={route('events.attendance.index', event.id)}>
                            <SecondaryButton>Kembali ke Daftar Kehadiran</SecondaryButton>
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title={`Riwayat Kehadiran - ${ticketIssued.email_penerima}`} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Ticket Information */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">Informasi Tiket</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Email Penerima</dt>
                                    <dd className="text-sm text-gray-900">{ticketIssued.email_penerima}</dd>
                                </div>
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Nama User</dt>
                                    <dd className="text-sm text-gray-900">{ticketIssued.user?.name || '-'}</dd>
                                </div>
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Jenis Tiket</dt>
                                    <dd className="text-sm text-gray-900">{ticketIssued.transactionItem?.ticket?.nama}</dd>
                                </div>
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Waktu Penerbitan</dt>
                                    <dd className="text-sm text-gray-900">
                                        {new Date(ticketIssued.waktu_penerbitan).toLocaleString('id-ID')}
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Status Tiket</dt>
                                    <dd className="text-sm text-gray-900">
                                        <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                            ticketIssued.status === 'active' 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-gray-100 text-gray-800'
                                        }`}>
                                            {ticketIssued.status === 'active' ? 'Aktif' : ticketIssued.status}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">Total Kunjungan</dt>
                                    <dd className="text-sm text-gray-900">{checkins.length} kali</dd>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Checkin History */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">Riwayat Check-in</h3>
                            {checkins.length > 0 ? (
                                <Table
                                    data={checkins}
                                    tableHeader={tableHeader}
                                    listAction={[]}
                                />
                            ) : (
                                <div className="text-center py-8">
                                    <p className="text-gray-500">Belum ada riwayat check-in untuk tiket ini.</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
