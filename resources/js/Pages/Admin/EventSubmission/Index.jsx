import Table from "@/Components/Table";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";

export default function Index({ eventSubmissions }) {
    const approveAction = (item) => {
        console.log('approve', item);
    }
    const rejectAction = (item) => {
        console.log('reject', item);
    }

    const goToDetailAction = (item) => {
        console.log('go to detail', item);
        router.visit(route('admin.event-submission.show', item.id));
    }

    const tableHeader = [
        { label: 'Nama', key: 'nama' },
        { label: 'Lokasi', key: 'lokasi' },
        { label: 'Jadwal Mulai', key: 'jadwal_mulai' },
        { label: 'Status', key: 'status' },
    ];

    return (
        <AuthenticatedLayout
            header="Event Submission"
        >
            <Head title="Event Submission" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <header>
                            <h2 className="text-lg font-medium text-gray-900">Event Submission</h2>
                            <p className="mt-1 text-sm text-gray-600">
                                List of event submissions.
                            </p>
                        </header>

                        <div className="mt-4">
                            <Table
                                tableHeader={tableHeader}
                                data={eventSubmissions}
                                handleRowClick={goToDetailAction}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
