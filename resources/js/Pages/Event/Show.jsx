import Dropdown from "@/Components/Dropdown";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import DeleteEventForm from "./Partials/DeleteEventForm";
import { useState } from "react";
import dayjs from "dayjs";
import AfterSubmissionModal from "./Partials/AfterSubmissionModal";
import DetailEventSection from "./Partials/DetailEventSection";

export default function Show({ event, tickets }) {
    return (
        <AuthenticatedLayout
            header={<Header event={event} />}
        >
            <Head title={event.nama} />

            <DetailEventSection event={event} tickets={tickets} />
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
            { event.status !== 'published' && (
                <>
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
                            {(event.status === 'draft' || event.status === 'rejected') && (
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
                </>
            )}

        </div>
    );
}
