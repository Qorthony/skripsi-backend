import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import Table from '@/Components/Table';

export default function Index({ auth, event, gatekeepers }) {
    const handleDelete = (gatekeeper) => {
        if (confirm('Apakah Anda yakin ingin menghapus gate keeper ini?')) {
            router.delete(route('events.gatekeepers.destroy', { event: event.id, gatekeeper: gatekeeper.id }));        }
    };

    const tableHeader = [
        {
            label: 'Nama',
            key: 'nama',
        },
        {
            label: 'Email',
            key: 'email',
        },
        // {
        //     label: 'Kode Akses',
        //     key: 'kode_akses',
        //     formatData: (value) => (
        //         <span className="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
        //             {value}
        //         </span>
        //     ),
        // },
    ];

    const listAction = [
        {
            label: 'Edit',
            action: (gatekeeper) => {
                router.visit(route('events.gatekeepers.edit', { 
                    event: event.id, 
                    gatekeeper: gatekeeper.id 
                }));
            },
        },
        {
            label: 'Hapus',
            action: (gatekeeper) => handleDelete(gatekeeper),
        },
    ];

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            Gate Keepers - {event.nama}
                        </h2>
                        <p className="text-sm text-gray-600 mt-1">
                            Kelola gate keeper untuk event ini
                        </p>
                    </div>
                    <div className="flex space-x-3">
                        <Link href={route('events.show', event.id)}>
                            <SecondaryButton>Kembali ke Event</SecondaryButton>
                        </Link>
                        <Link href={route('events.gatekeepers.create', event.id)}>
                            <PrimaryButton>Tambah Gate Keeper</PrimaryButton>
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title={`Gate Keepers - ${event.nama}`} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            {gatekeepers.length > 0 ? (
                                <Table
                                    data={gatekeepers}
                                    tableHeader={tableHeader}
                                    listAction={listAction}
                                />
                            ) : (
                                <div className="text-center py-8">
                                    <p className="text-gray-500 mb-4">Belum ada gate keeper untuk event ini.</p>
                                    <Link href={route('events.gatekeepers.create', event.id)}>
                                        <PrimaryButton>Tambah Gate Keeper Pertama</PrimaryButton>
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
