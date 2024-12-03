import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import InputOfflineLocation from "./Partials/InputOfflineLocation";
import dayjs from "dayjs";
import clsx from "clsx";
import { Textarea } from "@headlessui/react";
import Loader from "@/Components/Loader";
import PrimaryButton from "@/Components/PrimaryButton";
import TicketForm from "./Partials/TicketForm";

export default function Form({ event, tickets }) {
    const { data, setData, post, errors, processing } = useForm({
        nama: event?.nama ?? '',
        poster: event?.poster ?? '',
        lokasi: event?.lokasi ?? 'offline',
        kota: event?.kota ?? '',
        alamat_lengkap: event?.alamat_lengkap ?? '',
        tautan_acara: event?.tautan_acara ?? '',
        jadwal_mulai: event?.jadwal_mulai ?? dayjs().add(1, 'week').format('YYYY-MM-DDTHH:mm'),
        jadwal_selesai: event?.jadwal_selesai ?? dayjs().add(1, 'week').add(1, 'hour').format('YYYY-MM-DDTHH:mm'),
        deskripsi: event?.deskripsi ?? '',
        _method: event ? 'put' : 'post',
    });

    const submit = (e) => {
        e.preventDefault();
        console.log(data);

        if (event) {
            post(route('events.update', event.id));
        } else {
            post(route('events.store'));
        }
    };

    return (
        <AuthenticatedLayout 
            header={<div className="text-xl font-semibold leading-tight text-gray-800">{event ? `Edit ` : "Create "}Event</div>}
        >
            <Head title={event ? `Edit ${event.nama}` : "Create Event"} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden p-4 bg-white shadow-sm sm:rounded-lg">
                        <section className="max-w-xl">
                            <header>
                                <h2 className="text-lg font-medium text-gray-900">Event Information</h2>
                                <p className="mt-1 text-sm text-gray-600">
                                    {event ? `Edit ${event.nama} information.` : "Create your event information."}
                                </p>
                            </header>

                            <form onSubmit={submit} className="mt-6 space-y-6">
                                <div>
                                    <InputLabel htmlFor="nama" value="Nama" />
                                    <TextInput 
                                        id="nama" 
                                        type="text" 
                                        className="mt-1 block w-full" 
                                        value={data.nama} 
                                        onChange={(e) => setData('nama', e.target.value)} 
                                        required
                                        autoComplete="nama"
                                    />
                                    <InputError message={errors.nama} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel value="Lokasi" />

                                    <div className="flex items-center gap-2">
                                        <TextInput 
                                            id="lokasi-offline" 
                                            type="radio" 
                                            className="mt-1 rounded-full" 
                                            checked={data.lokasi==='offline'}
                                            onChange={() => setData('lokasi', 'offline')}
                                        />
                                        <label htmlFor="lokasi-offline">Offline</label>
                                    </div>

                                    <div className="flex items-center gap-2">
                                        <TextInput 
                                            id="lokasi-online" 
                                            type="radio" 
                                            className="mt-1 rounded-full" 
                                            checked={data.lokasi==='online'}
                                            onChange={() => setData('lokasi', 'online')}
                                        />
                                        <label htmlFor="lokasi-online">Online</label>
                                    </div>

                                    <InputError message={errors.lokasi} className="mt-2" /> 
                                </div>

                                {data.lokasi==='offline' && (
                                    <InputOfflineLocation data={data} setData={setData} errors={errors} />
                                )}

                                {data.lokasi==='online' && (
                                    <div>
                                        <InputLabel htmlFor="tautan_acara" value="Tautan Acara" />
                                        <TextInput 
                                            id="tautan_acara" 
                                            type="text" 
                                            className="mt-1 block w-full" 
                                            value={data.tautan_acara}
                                            autoComplete="tautan_acara"
                                            placeholder="https://example.com"
                                            onChange={(e) => setData('tautan_acara', e.target.value)} 
                                        />
                                        <InputError message={errors.tautan_acara} className="mt-2" />
                                    </div>
                                )}

                                <div>
                                    <InputLabel htmlFor="jadwal_mulai" value="Jadwal Mulai" />
                                    <TextInput 
                                        id="jadwal_mulai" 
                                        type="datetime-local" 
                                        className="mt-1 block w-full"
                                        value={data.jadwal_mulai}
                                        onChange={(e) => setData('jadwal_mulai', e.target.value)}
                                        required
                                    />
                                    <InputError message={errors.jadwal_mulai} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="jadwal_selesai" value="Jadwal Selesai" />
                                    <TextInput 
                                        id="jadwal_selesai" 
                                        type="datetime-local" 
                                        className="mt-1 block w-full"
                                        value={data.jadwal_selesai}
                                        onChange={(e) => setData('jadwal_selesai', e.target.value)}
                                        required
                                    />
                                    <InputError message={errors.jadwal_selesai} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="deskripsi" value="Deskripsi" />
                                    <Textarea 
                                        id="deskripsi" 
                                        className={clsx(
                                            'mt-3 block w-full resize-none rounded-lg border-gray-300 bg-white/5 py-1.5 px-3 text-sm/6',
                                            'focus:outline-none data-[focus]:outline-2 data-[focus]:-outline-offset-2 data-[focus]:outline-white/25'
                                        )}
                                        value={data.deskripsi}
                                        onChange={(e) => setData('deskripsi', e.target.value)}
                                    />
                                    <InputError message={errors.deskripsi} className="mt-2" />
                                </div>

                                <div>
                                    <PrimaryButton type="submit" disabled={processing}>
                                        {processing ? <Loader /> : "Save"}
                                    </PrimaryButton>
                                </div>

                            </form>

                        </section>
                    </div>
                </div>
            </div>

            {event && (
                <TicketForm event={event} tickets={tickets} />
            )}
        </AuthenticatedLayout>
    );
}
