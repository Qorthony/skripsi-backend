import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';

export default function Edit({ auth, event, gatekeeper }) {
    const { data, setData, put, processing, errors } = useForm({
        nama: gatekeeper.nama,
        email: gatekeeper.email,
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('events.gatekeepers.update', { event: event.id, gatekeeper: gatekeeper.id }));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            Edit Gate Keeper - {event.nama}
                        </h2>
                        <p className="text-sm text-gray-600 mt-1">
                            Edit informasi gate keeper {gatekeeper.nama}
                        </p>
                    </div>
                    <Link href={route('events.gatekeepers.index', event.id)}>
                        <SecondaryButton>Kembali</SecondaryButton>
                    </Link>
                </div>
            }
        >
            <Head title={`Edit Gate Keeper - ${gatekeeper.nama}`} />

            <div className="py-12">
                <div className="mx-auto max-w-2xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <div className="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <h3 className="font-semibold text-blue-800 mb-2">Informasi Kode Akses</h3>
                                <p className="text-sm text-blue-700">
                                    <strong>Kode Akses:</strong> 
                                    <span className="font-mono ml-2 bg-white px-2 py-1 rounded">
                                        {gatekeeper.kode_akses}
                                    </span>
                                </p>
                                <p className="text-xs text-blue-600 mt-1">
                                    Kode akses ini digunakan gate keeper untuk mengakses fitur tertentu
                                </p>
                            </div>

                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="nama" value="Nama Gate Keeper" />
                                    <TextInput
                                        id="nama"
                                        type="text"
                                        name="nama"
                                        value={data.nama}
                                        className="mt-1 block w-full"
                                        autoComplete="name"
                                        onChange={(e) => setData('nama', e.target.value)}
                                        placeholder="Masukkan nama gate keeper"
                                    />
                                    <InputError message={errors.nama} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="email" value="Email Gate Keeper" />
                                    <TextInput
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        className="mt-1 block w-full"
                                        autoComplete="email"
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="Masukkan email gate keeper"
                                    />
                                    <InputError message={errors.email} className="mt-2" />
                                </div>

                                <div className="flex items-center justify-end space-x-4">
                                    <Link href={route('events.gatekeepers.index', event.id)}>
                                        <SecondaryButton type="button">
                                            Batal
                                        </SecondaryButton>
                                    </Link>
                                    <PrimaryButton disabled={processing}>
                                        {processing ? 'Menyimpan...' : 'Update Gate Keeper'}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
