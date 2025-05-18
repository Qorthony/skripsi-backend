import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm } from "@inertiajs/react";
import InputOfflineLocation from "./Partials/InputOfflineLocation";
import dayjs from "dayjs";
import clsx from "clsx";
import { Textarea } from "@headlessui/react";
import Loader from "@/Components/Loader";
import PrimaryButton from "@/Components/PrimaryButton";
import TicketForm from "./Partials/TicketForm";
import { useEffect, useState, useRef } from "react";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";
import Cropper from 'react-easy-crop';
import getCroppedImg from '@/utils/cropImage';

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

    const [posterFile, setPosterFile] = useState(null);
    const [posterUrl, setPosterUrl] = useState(event?.poster ? `/storage/${event.poster}` : null);
    const [crop, setCrop] = useState({ x: 0, y: 0 });
    const [zoom, setZoom] = useState(1);
    const [croppedAreaPixels, setCroppedAreaPixels] = useState(null);
    const [showCrop, setShowCrop] = useState(false);
    const inputFileRef = useRef();

    useEffect(() => {
        if (event) {
            setTimeout(() => {
                
                scrollTo({
                    top: document.body.scrollHeight,
                behavior: 'smooth',
                });
            }, 500);
        }
    }, [data._method]);
    

    const submit = (e) => {
        e.preventDefault();

        if (event) {
            post(route('events.update', event.id), {
                preserveScroll: true,
            });
        } else {
            post(route('events.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    setData('_method', 'put');
                },
            });
        }
    };

    const onSelectPoster = (e) => {
        const file = e.target.files[0];
        if (file) {
            setPosterFile(file);
            setPosterUrl(URL.createObjectURL(file));
            setShowCrop(true);
        }
    };

    const onCropComplete = (croppedArea, croppedAreaPixels) => {
        setCroppedAreaPixels(croppedAreaPixels);
    };

    const handleCropPoster = async () => {
        const croppedBlob = await getCroppedImg(posterUrl, croppedAreaPixels);
        setData('poster', new File([croppedBlob], 'poster.jpg', { type: 'image/jpeg' }));
        setPosterUrl(URL.createObjectURL(croppedBlob));
        setShowCrop(false);
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
                            <form onSubmit={submit} className="mt-6 space-y-6" encType="multipart/form-data">
                                {/* Poster Upload & Crop */}
                                <div>
                                    <InputLabel htmlFor="poster" value="Poster Event" />
                                    <input
                                        id="poster"
                                        type="file"
                                        accept="image/*"
                                        className="block mt-1"
                                        ref={inputFileRef}
                                        onChange={onSelectPoster}
                                    />
                                    {posterUrl && (
                                        <div className="mt-2">
                                            <img src={posterUrl} alt="Poster Preview" className="max-h-48 rounded" />
                                        </div>
                                    )}
                                    <InputError message={errors.poster} className="mt-2" />
                                </div>
                                {showCrop && posterUrl && (
                                    <>
                                        <div className="relative w-full h-72 bg-gray-100 mt-2">
                                            <Cropper
                                                image={posterUrl}
                                                crop={crop}
                                                zoom={zoom}
                                                aspect={4/3}
                                                onCropChange={setCrop}
                                                onZoomChange={setZoom}
                                                onCropComplete={onCropComplete}
                                            />
                                            
                                        </div>
                                        <div className="flex gap-2 mt-2">
                                            <PrimaryButton type="button" onClick={handleCropPoster}>Crop & Pakai Poster</PrimaryButton>
                                            <SecondaryButton type="button" onClick={() => setShowCrop(false)}>Batal</SecondaryButton>
                                        </div>
                                    </>
                                )}

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

            {
                event && tickets.length > 0 && (
                    <div className="pb-12">
                        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                            <div className="overflow-hidden p-4 bg-white shadow-sm sm:rounded-lg">
                                <section className={`space-y-6`}>
                                    <div className="mt-4 flex justify-start space-x-2">
                                        <PrimaryButton
                                            onClick={()=>router.visit(route('events.show', event.id))}
                                        >
                                            Selesai
                                        </PrimaryButton>
                                        <SecondaryButton
                                            onClick={() => router.visit(route('events.index'))}
                                        >
                                            Lihat events lainnya
                                        </SecondaryButton>
                                        
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                )
            }

        </AuthenticatedLayout>
    );
}
