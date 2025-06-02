import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import Table from "@/Components/Table";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";

export default function Index({ event, collaborators }) {
    console.log('collaborators:', collaborators);
      const tableHeader = [
        { label: 'Nama', key: 'nama' },
        { label: 'Email', key: 'email' },
        { 
            label: 'Kode Akses', 
            key: 'kode_akses',
            formatData: (value) => (
                <span className="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                    {value.substring(0, 8)}...
                </span>
            )
        },
        { 
            label: 'Link Akses', 
            key: 'access_link',
            formatData: (value) => (
                <a 
                    href={value} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="text-blue-600 hover:text-blue-800 text-sm"
                >
                    Buka Link
                </a>
            )
        },
    ];

    const handleEdit = (collaborator) => {
        router.visit(route('events.collaborators.edit', [event.id, collaborator.id]));
    };

    const handleDelete = (collaborator) => {
        if (confirm('Apakah Anda yakin ingin menghapus kolaborator ini?')) {
            router.delete(route('events.collaborators.destroy', [event.id, collaborator.id]), {
                preserveScroll: true,
            });
        }
    };    const handleAccess = (collaborator) => {
        router.visit(collaborator.access_link);
    };

    const handleResendInvitation = (collaborator) => {
        if (confirm('Kirim ulang email undangan ke ' + collaborator.email + '?')) {
            router.post(route('events.collaborators.resend-invitation', [event.id, collaborator.id]), {}, {
                preserveScroll: true,
            });
        }
    };

    const rowActions = [
        {
            label: 'Akses',
            action: handleAccess,
        },
        {
            label: 'Kirim Ulang Email',
            action: handleResendInvitation,
        },
        {
            label: 'Edit',
            action: handleEdit
        },
        {
            label: 'Hapus',
            action: handleDelete
        },
    ];

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Kolaborator - {event.nama}
                    </h2>
                    <PrimaryButton
                        onClick={() => router.visit(route('events.collaborators.create', event.id))}
                    >
                        Tambah Kolaborator
                    </PrimaryButton>
                </div>
            }
        >
            <Head title={`Kolaborator - ${event.nama}`} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="mb-6">
                                <SecondaryButton
                                    onClick={() => router.visit(route('events.show', event.id))}
                                >
                                    ← Kembali ke Event
                                </SecondaryButton>
                            </div>

                            {collaborators.length === 0 ? (
                                <div className="text-center py-8">
                                    <p className="text-gray-600">Belum ada kolaborator untuk event ini.</p>
                                    <PrimaryButton
                                        className="mt-4"
                                        onClick={() => router.visit(route('events.collaborators.create', event.id))}
                                    >
                                        Tambah Kolaborator Pertama
                                    </PrimaryButton>
                                </div>
                            ) : (
                                <Table
                                    tableHeader={tableHeader}
                                    data={collaborators}
                                    listAction={rowActions}
                                />
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
