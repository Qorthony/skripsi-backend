import DangerButton from "@/Components/DangerButton";
import SecondaryButton from "@/Components/SecondaryButton";
import { router } from "@inertiajs/react";
import { useState } from "react";

export default function DeleteEventForm({ className, closeModal, deleteItem }) {
    const [isLoading, setIsLoading] = useState(false);
    const confirmUserDeletion = () => {
        console.log('confirmUserDeletion');
        setIsLoading(true);
        router.delete(route('events.destroy', deleteItem.id), {
            onSuccess: () => {
                closeModal();
            },
            onFinish: () => {
                setIsLoading(false);
            }
        });
    }
    return (
        <section className={`space-y-6 p-6 ${className}`}>
            <header>
                <h2 className="text-lg font-medium text-gray-900">Delete Event</h2>
            </header>

            <div>
                <p>Are you sure you want to delete this event?</p>
            </div>

            <div className="flex gap-2">
                <SecondaryButton onClick={closeModal} disabled={isLoading}>
                    Cancel
                </SecondaryButton>
                <DangerButton onClick={confirmUserDeletion} disabled={isLoading}>
                    Delete Event
                </DangerButton>
            </div>
        </section>
    );
}
