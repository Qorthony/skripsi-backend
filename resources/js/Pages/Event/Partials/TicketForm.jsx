import DangerButton from "@/Components/DangerButton";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import Loader from "@/Components/Loader";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import TextInput from "@/Components/TextInput";
import { router, useForm } from "@inertiajs/react";
import dayjs from "dayjs";
import { useState } from "react";

export default function TicketForm({ event, tickets }) {
    const [activeForm, setActiveForm] = useState('');
    const [ticketEdit, setTicketEdit] = useState(null);
    const [processingDelete, setProcessingDelete] = useState(false);

    const openEditForm = (ticket) => {
        setTicketEdit(ticket);
        setActiveForm(ticket.harga == 0 || ticket.harga == null ? 'free' : 'paid');
    }

    const deleteTicket = (ticket) => {
        setProcessingDelete(true);
        router.delete(route('events.ticket.destroy', [ticket.id]), {
            preserveScroll: true,
            onSuccess: () => {
                setProcessingDelete(false);
            }
        });
    }

    return (
        <div className="pb-12">
            <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div className="overflow-hidden p-4 bg-white shadow-sm sm:rounded-lg">
                    <section className={`space-y-6`}>
                        <header>
                            <h2 className="text-lg font-medium text-gray-900">Ticket</h2>
                            <p className="mt-1 text-sm text-gray-600">
                                Create your event ticket.
                            </p>
                        </header>

                        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {tickets.map((ticket, index) => (
                                <div key={index} className="bg-white border rounded-lg shadow-sm p-4">
                                    <div className="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 className="text-lg font-medium text-gray-900">{ticket.nama}</h3>
                                            <p className="text-sm text-gray-500">Kuota: {ticket.kuota}</p>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-lg font-bold text-gray-900">
                                                {ticket.harga ? `Rp ${ticket.harga.toLocaleString('id-ID')}` : 'Gratis'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="space-y-2 text-sm text-gray-600">
                                        <p>Mulai: {dayjs(ticket.waktu_buka).format('DD/MM/YYYY HH:mm')}</p>
                                        <p>Tutup: {dayjs(ticket.waktu_tutup).format('DD/MM/YYYY HH:mm')}</p>
                                        {ticket.keterangan && (
                                            <p className="text-gray-500">{ticket.keterangan}</p>
                                        )}
                                    </div>
                                    <div className="mt-4 flex justify-end space-x-2">
                                        <SecondaryButton onClick={() => openEditForm(ticket)}>
                                            Edit
                                        </SecondaryButton>
                                        <DangerButton onClick={() => deleteTicket(ticket)} disabled={processingDelete}>
                                            {processingDelete ? <Loader /> : 'Delete'}
                                        </DangerButton>
                                    </div>
                                </div>
                            ))}
                        </div>


                        <div>
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                <PrimaryButton onClick={() => setActiveForm('paid')}>
                                    + Buat Tiket Berbayar
                                </PrimaryButton>
                                <PrimaryButton onClick={() => setActiveForm('free')}>
                                    + Buat Tiket Gratis
                                </PrimaryButton>
                            </div>
                        </div>

                        {activeForm === 'free' && 
                            <TicketItemForm 
                                type="free" 
                                event={event} 
                                ticketEdit={ticketEdit} 
                                setTicketEdit={setTicketEdit}
                                setActiveForm={setActiveForm}
                            />
                        }
                        {activeForm === 'paid' && 
                            <TicketItemForm 
                                type="paid" 
                                event={event} 
                                ticketEdit={ticketEdit}
                                setTicketEdit={setTicketEdit}
                                setActiveForm={setActiveForm}
                            />
                        }

                    </section>
                </div>
            </div>
        </div>
    );
}

const TicketItemForm = ({type, event, setActiveForm, ticketEdit, setTicketEdit}) => {
    const { data, setData, post, put, errors, processing } = useForm({
        nama: ticketEdit ? ticketEdit.nama : '',
        kuota: ticketEdit ? ticketEdit.kuota : '',
        harga: ticketEdit ? ticketEdit.harga : '',
        waktu_buka: ticketEdit ? ticketEdit.waktu_buka : dayjs().format('YYYY-MM-DDTHH:mm'),
        waktu_tutup: ticketEdit ? ticketEdit.waktu_tutup : dayjs(event.jadwal_mulai).subtract(1, 'hour').format('YYYY-MM-DDTHH:mm'),
        keterangan: ticketEdit ? ticketEdit.keterangan : '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (ticketEdit) {
            put(route('events.ticket.update', [ticketEdit.id]), {
                preserveScroll: true,
                onSuccess: () => {
                    setActiveForm('');
                    setTicketEdit(null);
                }
            });
        } else {
            post(route('events.ticket.store', event.id), {
                preserveScroll: true,
                onSuccess: () => {
                    setActiveForm('');
                    setTicketEdit(null);
                }
            });
        }
    };

    const handleCancel = () => {
        setActiveForm('');
        setTicketEdit(null);
    }

    return (
        <div className="space-y-6 max-w-xl">
            <div>
                <InputLabel required htmlFor="nama">Nama</InputLabel>
                <TextInput 
                    id="nama"
                    type="text"
                    autoFocus
                    className="mt-1 block w-full"
                    value={data.nama} 
                    onChange={(e) => setData({ ...data, nama: e.target.value })} 
                />
                <InputError message={errors.nama} />
            </div>

            <div>
                <InputLabel required htmlFor="kuota">Kuota</InputLabel>
                <TextInput 
                    id="kuota"
                    type="number"
                    className="mt-1 block w-full"
                    value={data.kuota} 
                    onChange={(e) => setData({ ...data, kuota: e.target.value })} 
                />
                <InputError message={errors.kuota} />
            </div>


            {type === 'paid' && (
                <div>
                    <InputLabel required htmlFor="harga">Harga</InputLabel>
                    <TextInput 
                        id="harga"
                        type="number"
                        className="mt-1 block w-full"
                        value={data.harga} 
                        onChange={(e) => setData({ ...data, harga: e.target.value })} 
                    />
                    <InputError message={errors.harga} />
                </div>
            )}

            <div>
                <InputLabel required htmlFor="waktu_buka">Waktu Buka</InputLabel>
                <TextInput 
                    id="waktu_buka"
                    type="datetime-local"
                    className="mt-1 block w-full"
                    min={dayjs().format('YYYY-MM-DDTHH:mm')}
                    max={dayjs(event.jadwal_mulai).format('YYYY-MM-DDTHH:mm')}
                    value={data.waktu_buka} 
                    onChange={(e) => setData({ ...data, waktu_buka: e.target.value })} 
                />
                <InputError message={errors.waktu_buka} />
            </div>

            <div>
                <InputLabel required htmlFor="waktu_tutup">Waktu Tutup</InputLabel>
                <TextInput 
                    id="waktu_tutup"
                    type="datetime-local"
                    className="mt-1 block w-full"
                    min={dayjs().format('YYYY-MM-DDTHH:mm')}
                    max={dayjs(event.jadwal_mulai).format('YYYY-MM-DDTHH:mm')}
                    value={data.waktu_tutup} 
                    onChange={(e) => setData({ ...data, waktu_tutup: e.target.value })} 
                />
                <InputError message={errors.waktu_tutup} />
            </div>

            <div>
                <InputLabel htmlFor="keterangan">Keterangan</InputLabel>
                <TextInput 
                    id="keterangan"
                    type="text"
                    className="mt-1 block w-full"
                    value={data.keterangan} 
                    onChange={(e) => setData({ ...data, keterangan: e.target.value })} 
                />
                <InputError message={errors.keterangan} />
            </div>

            <div className="flex space-x-2">
                <PrimaryButton onClick={handleSubmit} disabled={processing}>
                    {processing ? <Loader /> : 'Save'}
                </PrimaryButton>
                <SecondaryButton onClick={handleCancel}>
                    Cancel
                </SecondaryButton>
            </div>
        </div>
    );
}
