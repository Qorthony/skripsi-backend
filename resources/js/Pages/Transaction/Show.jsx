import Container from '@/Components/Container';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';

export default function TransactionShow({ transaction }) {
    const collaborator = usePage().props.collaborator;

    const statusClass = {
        pending: "rounded-full px-2 bg-yellow-100 text-yellow-800",
        payment: "rounded-full px-2 bg-blue-100 text-blue-800",
        success: "rounded-full px-2 bg-green-100 text-green-800",
        failed: "rounded-full px-2 bg-red-100 text-red-800",
    };
    
    return (
        <AuthenticatedLayout
            header={<Header id={transaction.event.id} />}
        >
            <Head title={`Detail Transaksi #${transaction.id}`} />
            <div className="p-8">
                <Container>
                    <div className="bg-white rounded shadow p-6 mb-6">
                        <div className="mb-2"><b className='text-gray-500'>ID:</b> {transaction.id}</div>
                        <div className="mb-2">
                            <b className='text-gray-500'>Event:</b> 
                            <a href={collaborator?route('events.show.collaborator', {event:transaction.event_id, access_code:collaborator.kode_akses}) : route('events.show', transaction.event_id)} className='text-blue-600 hover:underline'>
                                {transaction.event?.nama}
                            </a> 
                        </div>
                        <div className="mb-2"><b className='text-gray-500'>Status:</b> <span className={statusClass[transaction.status]}> {transaction.status} </span></div>
                        <div className="mb-2">
                            <b className='text-gray-500'>User:</b> 
                            {transaction.user?.name} 
                            (<span className='text-gray-500'> 
                                {transaction.user?.email} 
                            </span>)
                        </div>
                        <div className="mb-2"><b className='text-gray-500'>Tanggal:</b> {new Date(transaction.created_at).toLocaleString()}</div>
                        {
                            transaction.status !=='success' 
                            && 
                            <div className="mb-2">
                                <b className='text-gray-500'>Batas Waktu Transaksi:</b> 
                                {new Date(transaction.batas_waktu).toLocaleString() }
                            </div>
                        } 
                    </div>
                    
                    <div className="bg-white rounded shadow p-6 mb-6">
                        <h2 className="text-xl font-semibold mb-2">Detail Pembayaran</h2>
                        <div className="mb-2"><b className='text-gray-500'>Metode Pembayaran:</b> {transaction.metode_pembayaran}</div>
                        <div className="mb-2"><b className='text-gray-500'>Total Harga:</b> Rp{transaction.total_harga?.toLocaleString()}</div>
                        <div className="mb-2"><b className='text-gray-500'>Biaya Layanan:</b> Rp{transaction.biaya_pembayaran?.toLocaleString()}</div>
                        <div className="mb-2"><b className='text-gray-500'>Total Pembayaran:</b> Rp{transaction.total_pembayaran?.toLocaleString()}</div>
                        <div className="mb-2"><b className='text-gray-500'>Waktu Pembayaran:</b> {transaction.waktu_pembayaran??"-"}</div>
                    </div>

                    <div className="bg-white rounded shadow p-6">
                        <h2 className="font-semibold mb-2">Tiket</h2>
                        <ul className="divide-y">
                            {transaction.event.tickets?.map((tix, idx) => {
                                return tix.transaction_items?.length > 0?
                                <li key={idx} className="py-2 flex justify-between">
                                    <span>{tix.nama}</span>
                                    <span>
                                        <span className='text-sm text-gray-400'> {tix.transaction_items?.length}x</span>  
                                        Rp{tix.harga?.toLocaleString()}
                                    </span>
                                </li>:""
                            })}
                        </ul>
                    </div>
                </Container>
            </div>
        </AuthenticatedLayout>
    );
}

const Header = ({id}) => {
    return (
        <div className="flex justify-between items-center">
            <div className="flex items-center">
                <a href={route('events.transactions.index', id)} className="text-sm text-gray-500 hover:text-blue-600">Transaksi Lainnya</a>
            </div>
            <h2 className="text-xl font-semibold leading-tight text-gray-800">Detail Transaksi</h2>
            
        </div>
    );
};