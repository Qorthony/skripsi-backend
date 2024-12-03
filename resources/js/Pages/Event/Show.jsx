import Dropdown from "@/Components/Dropdown";
import PrimaryButton from "@/Components/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";

export default function Show({ event }) {
    return (
        <AuthenticatedLayout
            header={<Header event={event} />}
        >
            <Head title={event.nama} />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <header>
                                <h2 className="text-lg font-medium text-gray-900">{event.nama}</h2>
                                <p className="mt-1 w-fit text-sm text-gray-600 bg-gray-300 rounded-full px-2">
                                    {event.status}
                                </p>
                            </header>
                            
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
                                        <p className="text-sm text-gray-900">{event.deskripsi}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

const Header = ({ event }) => {
    return (
        <div className="flex justify-between items-center">
            <h2 className="text-xl font-semibold leading-tight text-gray-800">Detail Event</h2>
            <Dropdown>
                <Dropdown.Trigger>
                    <PrimaryButton>
                        Actions
                        <svg
                            className="-me-0.5 ms-2 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fillRule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clipRule="evenodd"
                            />
                        </svg>
                    </PrimaryButton>
                </Dropdown.Trigger>
                <Dropdown.Content>
                    <Dropdown.Link href={route('events.edit', event.id)}>Edit Event</Dropdown.Link>
                </Dropdown.Content>
            </Dropdown>
        </div>
    );
}
