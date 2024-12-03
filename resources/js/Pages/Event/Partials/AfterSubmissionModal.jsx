import SecondaryButton from "@/Components/SecondaryButton";

export default function AfterSubmissionModal({ onClose }) {
    return (
        <div className="p-4 space-y-4 flex flex-col items-center justify-center h-full">
            <h1 className="text-2xl font-bold">Event berhasil diajukan</h1>
            <p className="text-sm text-gray-600">Silakan tunggu konfirmasi dari admin</p>
            <SecondaryButton onClick={onClose}>Tutup</SecondaryButton>
        </div>
    );
}
