import Dropdown from "@/Components/Dropdown";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import DeleteEventForm from "./Partials/DeleteEventForm";
import { useState } from "react";
import dayjs from "dayjs";
import AfterSubmissionModal from "./Partials/AfterSubmissionModal";

export default function Show({ event, tickets }) {
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
        </AuthenticatedLayout>
    );
}

const Header = ({ event }) => {
    const [showModal, setShowModal] = useState(false);
    const [showAfterSubmissionModal, setShowAfterSubmissionModal] = useState(false);

    const confirmUserDeletion = (e) => {
        e.preventDefault();
        setShowModal(true);
    }

    const handleSubmitEvent = (e) => {
        e.preventDefault();
        router.post(route('events.publish', event.id));
        setShowAfterSubmissionModal(true);
    }

    const handleCancelPublish = (e) => {
        e.preventDefault();
        router.post(route('events.cancelPublish', event.id));
    }

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
                    {event.status === 'draft' && (
                        <>
                            <Dropdown.Link onClick={handleSubmitEvent}>Ajukan Event</Dropdown.Link>
                            <Dropdown.Link href={route('events.edit', event.id)}>Edit Event</Dropdown.Link>
                            <Dropdown.Link onClick={confirmUserDeletion}>Delete Event</Dropdown.Link>
                        </>
                    )}
                    {event.status === 'in_review' && (
                        <>
                            <Dropdown.Link onClick={handleCancelPublish}>Batalkan Pengajuan</Dropdown.Link>
                        </>
                    )}
                </Dropdown.Content>
            </Dropdown>

            <Modal show={showModal} onClose={() => setShowModal(false)}>
                <DeleteEventForm 
                    className="max-w-md" 
                    closeModal={() => setShowModal(false)} 
                    deleteItem={event} 
                />
            </Modal>

            <Modal show={showAfterSubmissionModal} onClose={() => setShowAfterSubmissionModal(false)}>
                <AfterSubmissionModal onClose={() => setShowAfterSubmissionModal(false)} />
            </Modal>
        </div>
    );
}
