import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import Table from '@/Components/Table';

export default function Index({ auth, event, attendanceData, stats }) {
    // const handleCheckin = (ticket) => {
    //     if (confirm('Apakah Anda yakin ingin melakukan check-in untuk peserta ini?')) {
    //         router.post(route('events.attendance.checkin', { 
    //             event: event.id, 
    //             ticketIssued: ticket.id 
    //         }));
    //     }
    // };

    // const handleCheckout = (ticket) => {
    //     if (confirm('Apakah Anda yakin ingin melakukan check-out untuk peserta ini?')) {
    //         router.post(route('events.attendance.checkout', { 
    //             event: event.id, 
    //             ticketIssued: ticket.id 
    //         }));
    //     }
    // };

    // const viewHistory = (ticket) => {
    //     router.visit(route('events.attendance.history', { 
    //         event: event.id, 
    //         ticketIssued: ticket.id 
    //     }));
    // };
    const collaborator = usePage().props.collaborator;

    const tableHeader = [
        {
            label: 'Email Peserta',
            key: 'email_penerima',
        },
        {
            label: 'Nama User',
            key: 'user.name',
        },
        {
            label: 'Jenis Tiket',
            key: 'ticket_name',
        },
        {
            label: 'Status Kehadiran',
            key: 'is_checked_in',
            formatData: (value, row) => (
                <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                    value 
                        ? 'bg-green-100 text-green-800' 
                        : row.total_checkins > 0 
                            ? 'bg-yellow-100 text-yellow-800'
                            : 'bg-gray-100 text-gray-800'
                }`}>
                    {value ? 'Hadir' : row.total_checkins > 0 ? 'Sudah Keluar' : 'Belum Hadir'}
                </span>
            ),
        },
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
        // {
        //     label: 'Total Kunjungan',
        //     key: 'total_checkins',
        // },
    ];

    // const listAction = [
    //     {
    //         label: 'Check-in',
    //         action: (ticket) => handleCheckin(ticket),
    //         condition: (ticket) => !ticket.is_checked_in,
    //     },
    //     {
    //         label: 'Check-out',
    //         action: (ticket) => handleCheckout(ticket),
    //         condition: (ticket) => ticket.is_checked_in,
    //     },
    //     {
    //         label: 'Riwayat',
    //         action: (ticket) => viewHistory(ticket),
    //     },
    // ].filter(action => !action.condition || attendanceData.some(ticket => action.condition(ticket)));

    // Custom list action yang mempertimbangkan kondisi per item
    // const customListAction = attendanceData.length > 0 ? [
    //     {
    //         label: 'Aksi',
    //         action: () => {}, // dummy action
    //     }
    // ] : [];

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            Daftar Kehadiran - {event.nama}
                        </h2>
                        <p className="text-sm text-gray-600 mt-1">
                            Kelola kehadiran peserta event
                        </p>
                    </div>
                    <div className="flex space-x-3">
                        <Link href={collaborator? route('events.show.collaborator', {event: event.id, access_code: collaborator.kode_akses}) : route('events.show', event.id)}>
                            <SecondaryButton>Kembali ke Event</SecondaryButton>
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title={`Daftar Kehadiran - ${event.nama}`} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Statistics Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <div className="text-2xl font-bold text-gray-900">{stats.total_tickets}</div>
                                <div className="text-sm text-gray-600">Total Tiket</div>
                            </div>
                        </div>
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <div className="text-2xl font-bold text-green-600">{stats.checked_in}</div>
                                <div className="text-sm text-gray-600">Sedang Hadir</div>
                            </div>
                        </div>
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <div className="text-2xl font-bold text-gray-600">{stats.never_checked_in}</div>
                                <div className="text-sm text-gray-600">Belum Pernah Hadir</div>
                            </div>
                        </div>
                    </div>

                    {/* Attendance Table */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            {attendanceData.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <table className="table-auto w-full text-sm text-left rtl:text-right text-gray-600">
                                        <thead className="text-sm text-gray-600 uppercase bg-gray-100">
                                            <tr>
                                                {tableHeader.map((item, index) => (
                                                    <th scope="col" key={index} className="px-6 py-3">{item.label}</th>
                                                ))}
                                                {/* <th scope="col" className="px-6 py-3">Aksi</th> */}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {attendanceData.map((ticket, index) => (
                                                <tr key={index} className="bg-white border-b hover:bg-gray-50">
                                                    {tableHeader.map((header, indexColumn) => (
                                                        <td key={indexColumn} className="px-6 py-4">
                                                            {header.formatData 
                                                                ? header.formatData(
                                                                    header.key.includes('.') 
                                                                        ? header.key.split('.').reduce((obj, key) => obj?.[key], ticket)
                                                                        : ticket[header.key],
                                                                    ticket
                                                                  )
                                                                : (header.key.includes('.') 
                                                                    ? header.key.split('.').reduce((obj, key) => obj?.[key], ticket)
                                                                    : ticket[header.key]) ?? "-"
                                                            }
                                                        </td>
                                                    ))}
                                                    {/* <td className="px-6 py-4">
                                                        <div className="flex space-x-2">
                                                            {!ticket.is_checked_in && (
                                                                <button
                                                                    onClick={() => handleCheckin(ticket)}
                                                                    className="text-blue-600 hover:text-blue-900 text-sm"
                                                                >
                                                                    Check-in
                                                                </button>
                                                            )}
                                                            {ticket.is_checked_in && (
                                                                <button
                                                                    onClick={() => handleCheckout(ticket)}
                                                                    className="text-orange-600 hover:text-orange-900 text-sm"
                                                                >
                                                                    Check-out
                                                                </button>
                                                            )}
                                                            <button
                                                                onClick={() => viewHistory(ticket)}
                                                                className="text-gray-600 hover:text-gray-900 text-sm"
                                                            >
                                                                Riwayat
                                                            </button>
                                                        </div>
                                                    </td> */}
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            ) : (
                                <div className="text-center py-8">
                                    <p className="text-gray-500 mb-4">Belum ada tiket yang diterbitkan untuk event ini.</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
