import PrimaryButton from '@/Components/PrimaryButton';
import Table from '@/Components/Table';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Index({ events }) {
    const deleteAction = (item) => {
        console.log('delete', item);
    }
    const editAction = (item) => {
        console.log('edit', item);
    }
    const listAction = [
        {
            label: 'Edit',
            action: (item) => editAction(item)
        },
        {
            label: 'Delete',
            action: (item) => deleteAction(item)
        }
    ];

    const tableHeader = [
        { label: 'Nama', key: 'nama' },
        { label: 'Lokasi', key: 'lokasi' },
        { label: 'Jadwal Mulai', key: 'jadwal_mulai' },
        { label: 'Status', key: 'status' },
    ];

    return (
        <AuthenticatedLayout
            header={<Header />}
        >
            <Head title="Events" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <header>
                                <h2 className="text-lg font-medium text-gray-900">Events</h2>
                                <p className="mt-1 text-sm text-gray-600">
                                    List of your events.
                                </p>
                            </header>
                            <div className="mt-4">
                                <Table 
                                    listAction={listAction} 
                                    tableHeader={tableHeader} 
                                    data={events} 
                                    handleRowClick={(item) => console.log('row click', item)} 
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

const Header = () => {
    return (
        <div className="flex justify-between items-center">
            <h2 className="text-xl font-semibold leading-tight text-gray-800">Events</h2>
            <PrimaryButton onClick={() => router.visit(route('events.create'))}>Create Event</PrimaryButton>
        </div>
    );
};
