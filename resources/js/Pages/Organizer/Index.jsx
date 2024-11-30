import InputLabel from '@/Components/InputLabel';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import TextInput from '@/Components/TextInput';
import PrimaryButton from '@/Components/PrimaryButton';
import clsx from 'clsx';
import { Textarea, Transition } from '@headlessui/react';

export default function Index({ organizer }) {
    const { data, setData, post,patch, errors, processing, recentlySuccessful } =
        useForm({
            nama: organizer?.nama || '',
            alamat: organizer?.alamat || '',
            deskripsi: organizer?.deskripsi || '',
        });

    const submit = (e) => {
        e.preventDefault();

        if (organizer) {
            patch(route('organizer.update', organizer.id));
        } else {
            post(route('organizer.store'));
        }
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Organizer</h2>}
        >
            <Head title="Organizers" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden p-4 bg-white shadow-sm sm:rounded-lg">
                        <section className="max-w-xl">
                            <header>
                                <h2 className="text-lg font-medium text-gray-900">
                                    Organizer Profile
                                </h2>

                                <p className="mt-1 text-sm text-gray-600">
                                    {organizer ? 'Update' : 'Create'} your organizer profile information.
                                </p>
                            </header>

                            <form onSubmit={submit} className="mt-6 space-y-6">
                                <div>
                                    <InputLabel htmlFor="nama" value="Nama" required />

                                    <TextInput
                                        id="nama"
                                        className="mt-1 block w-full"
                                        value={data.nama}
                                        onChange={(e) => setData('nama', e.target.value)}
                                        required
                                        autoComplete="nama"
                                    />

                                    <InputError className="mt-2" message={errors.nama} />
                                </div>

                                <div>
                                    <InputLabel htmlFor="alamat" value="Alamat" required />

                                    <TextInput
                                        id="alamat"
                                        className="mt-1 block w-full"
                                        value={data.alamat}
                                        onChange={(e) => setData('alamat', e.target.value)}
                                        required
                                        autoComplete="alamat"
                                    />

                                    <InputError className="mt-2" message={errors.alamat} />
                                </div>

                                <div>
                                    <InputLabel htmlFor="deskripsi" value="Deskripsi" />

                                    <Textarea
                                        name="deskripsi"
                                        className={clsx(
                                            'mt-3 block w-full resize-none rounded-lg border-gray-300 bg-white/5 py-1.5 px-3 text-sm/6',
                                            'focus:outline-none data-[focus]:outline-2 data-[focus]:-outline-offset-2 data-[focus]:outline-white/25'
                                        )}
                                        value={data.deskripsi}
                                        onChange={(e) => setData('deskripsi', e.target.value)}
                                    />

                                    <InputError className="mt-2" message={errors.deskripsi} />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton type="submit" disabled={processing}>
                                        Save
                                    </PrimaryButton>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-gray-600">
                                            Saved.
                                        </p>
                                    </Transition>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
