import Container from '@/Components/Container';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function TransactionList({ event, transactions, stats }) {
    return (
        <AuthenticatedLayout
            header={<Header/>}
        >
            <Head title={`Transaksi Event: ${event.nama}`} />
            <div className="p-8">
                <Container>
                    <h1 className="text-2xl font-bold mb-4">
                        Transaksi Event:  
                        <a href={route('events.show', event.id)} className='text-blue-600 hover:underline'>
                            {event.nama}
                        </a> 
                    </h1>
                    <div className="mb-6 flex gap-8 flex-wrap">
                        <div className="bg-white rounded shadow p-4">
                            <div className="text-sm text-gray-500">Jumlah Transaksi</div>
                            <div className="text-2xl font-bold">{stats.count}</div>
                        </div>
                        <div className="bg-white rounded shadow p-4">
                            <div className="text-sm text-gray-500">Total Pendapatan</div>
                            <div className="text-2xl font-bold">Rp {stats.total_income?.toLocaleString()}</div>
                        </div>
                    </div>
                    <div className="mb-6">
                        <h2 className="font-semibold mb-2">Tiket Terjual per Kategori</h2>
                        <ul className="grid gap-2 md:grid-cols-2">
                            {stats.tickets_by_category?.map((cat, idx) => (
                                <li key={idx} className="bg-gray-50 rounded px-4 py-2 flex justify-between">
                                    <span>
                                        {cat.category} 
                                        <span className="ms-1 text-sm text-gray-500">
                                            (Rp {cat.price?.toLocaleString()})
                                        </span> 
                                    </span>
                                    <span>{cat.count} tiket</span>
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="min-w-full bg-white rounded shadow">
                            <thead>
                                <tr>
                                    <th className="px-4 py-2">ID</th>
                                    <th className="px-4 py-2">Email</th>
                                    <th className="px-4 py-2">User</th>
                                    <th className="px-4 py-2">Total</th>
                                    <th className="px-4 py-2">Tanggal</th>
                                    <th className="px-4 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {transactions.map(trx => (
                                    <tr key={trx.id} className="border-t">
                                        <td className="px-4 py-2">{trx.id}</td>
                                        <td className="px-4 py-2">{trx.user?.email}</td>
                                        <td className="px-4 py-2">{trx.user?.name}</td>
                                        <td className="px-4 py-2">Rp{trx.total_harga?.toLocaleString()}</td>
                                        <td className="px-4 py-2">{new Date(trx.created_at).toLocaleString()}</td>
                                        <td className="px-4 py-2">
                                            <Link href={route('transactions.show', trx.id)} className="text-blue-600 hover:underline">Detail</Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </Container>
            </div>
        </AuthenticatedLayout>
    );
}

const Header = () => {
    return (
        <div className="flex justify-between items-center">
            <h2 className="text-xl font-semibold leading-tight text-gray-800">Daftar Transaksi</h2>
        </div>
    );
};
