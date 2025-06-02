import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import TextInput from "@/Components/TextInput";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm, router } from "@inertiajs/react";

export default function Edit({ event, collaborator }) {
    const { data, setData, put, processing, errors } = useForm({
        nama: collaborator.nama,
        email: collaborator.email,
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('events.collaborators.update', [event.id, collaborator.id]));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Edit Kolaborator - {event.nama}
                </h2>
            }
        >
            <Head title={`Edit Kolaborator - ${event.nama}`} />

            <div className="py-12">
                <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="mb-6">
                                <SecondaryButton
                                    onClick={() => router.visit(route('events.collaborators.index', event.id))}
                                >
                                    ← Kembali ke Daftar Kolaborator
                                </SecondaryButton>
                            </div>

                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="nama" value="Nama" />
                                    <TextInput
                                        id="nama"
                                        type="text"
                                        name="nama"
                                        value={data.nama}
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData('nama', e.target.value)}
                                        required
                                        autoFocus
                                    />
                                    <InputError message={errors.nama} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="email" value="Email" />
                                    <TextInput
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                    />
                                    <InputError message={errors.email} className="mt-2" />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>
                                        Update
                                    </PrimaryButton>
                                    <SecondaryButton
                                        type="button"
                                        onClick={() => router.visit(route('events.collaborators.index', event.id))}
                                    >
                                        Batal
                                    </SecondaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
