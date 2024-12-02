import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Index() {
    return (
        <AuthenticatedLayout
            header={<Header />}
        >
            <Head title="Events" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">Events</div>
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
