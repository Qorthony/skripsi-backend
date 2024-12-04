import DangerButton from "@/Components/DangerButton";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import DetailEventSection from "@/Pages/Event/Partials/DetailEventSection";
import { Head, router } from "@inertiajs/react";
import { useState } from "react";
import RejectModal from "./Partials/RejectModal";

export default function Detail({ event, tickets }) {
    return (
        <AuthenticatedLayout
            header={<Header event={event} />}
        >
            <Head title="Event Submission" />

            <DetailEventSection event={event} tickets={tickets} />
        </AuthenticatedLayout>
    );
}

const Header = ({ event }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [showRejectModal, setShowRejectModal] = useState(false);

    const approveAction = (e) => {
        e.preventDefault();
        setIsLoading(true);
        router.post(route('admin.event-submission.approve', event.id), {
            onSuccess: () => {
                setIsLoading(false);
            }
        });
    }

    const rejectAction = (e) => {
        e.preventDefault();
        setShowRejectModal(true);
    }
    return (
        <div className="flex justify-between items-center">
            <h2 className="text-xl font-semibold leading-tight text-gray-800">Detail Event Submission</h2>
            {event.status === 'in_review' && (
                <>
                    <div className="flex items-center gap-2">
                        <PrimaryButton onClick={approveAction} disabled={isLoading}>Approve</PrimaryButton>
                        <DangerButton onClick={rejectAction} disabled={isLoading}>Reject</DangerButton>
                    </div>
                    <RejectModal show={showRejectModal} onClose={() => setShowRejectModal(false)} event={event} />
                </>
            )}

        </div>
    );
}
