import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';

export default function Create({ auth, event }) {
    const { data, setData, post, processing, errors } = useForm({
        nama: '',
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('events.gatekeepers.store', event.id));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            Tambah Gate Keeper - {event.nama}
                        </h2>
                        <p className="text-sm text-gray-600 mt-1">
                            Tambahkan gate keeper baru untuk event ini
                        </p>
                    </div>
                    <Link href={route('events.gatekeepers.index', event.id)}>
                        <SecondaryButton>Kembali</SecondaryButton>
                    </Link>
                </div>
            }
        >
            <Head title={`Tambah Gate Keeper - ${event.nama}`} />

            <div className="py-12">
                <div className="mx-auto max-w-2xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
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
                                    <p className="text-sm text-gray-600 mt-1">
                                        Kode akses akan digenerate otomatis setelah gate keeper dibuat
                                    </p>
                                </div>

                                <div className="flex items-center justify-end space-x-4">
                                    <Link href={route('events.gatekeepers.index', event.id)}>
                                        <SecondaryButton type="button">
                                            Batal
                                        </SecondaryButton>
                                    </Link>
                                    <PrimaryButton disabled={processing}>
                                        {processing ? 'Menyimpan...' : 'Simpan Gate Keeper'}
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
