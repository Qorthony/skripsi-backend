import DangerButton from "@/Components/DangerButton";
import Loader from "@/Components/Loader";
import Modal from "@/Components/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import { Textarea } from "@headlessui/react";
import { router } from "@inertiajs/react";
import clsx from "clsx";
import { useState } from "react";

export default function RejectModal({ show, onClose, event }) {
    const [reason, setReason] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleReject = () => {
        if (reason.trim() === '') {
            return;
        }

        setIsLoading(true);
        router.post(route('admin.event-submission.reject', event.id), {
            reason: reason,
            onSuccess: () => {
                onClose();
            },
            onFinish: () => {
                setIsLoading(false);
            }
        });
    }

    return (
        <Modal show={show} onClose={onClose}>
            <div className="p-4">
                <h1 className="text-lg font-semibold leading-tight text-gray-800">Reject Event Submission</h1>
                <p className="text-sm text-gray-600">Tuliskan alasan penolakan event submission</p>
                <div className="mt-4">
                    <Textarea 
                        rows={5} 
                        className={clsx(
                            'mt-3 block w-full resize-none rounded-lg border-gray-300 bg-white/5 py-1.5 px-3 text-sm/6',
                            'focus:outline-none data-[focus]:outline-2 data-[focus]:-outline-offset-2 data-[focus]:outline-white/25'
                        )} 
                        value={reason}
                        onChange={(e) => setReason(e.target.value)}
                    />
                </div>
                <div className="mt-4 flex justify-end gap-2">
                    <SecondaryButton onClick={onClose} disabled={isLoading}>Cancel</SecondaryButton>
                    <DangerButton onClick={handleReject} disabled={reason.trim() === '' || isLoading}>
                        {isLoading ? <Loader /> : 'Submit'}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    );
}
