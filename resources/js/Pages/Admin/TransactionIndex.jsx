import Container from '@/Components/Container';
import Table from '@/Components/Table';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import dayjs from 'dayjs';

export default function TransactionIndex({ transactions, stats }) {
    return (
        <AuthenticatedLayout
            header={<Header />}
        >
            <Head title="Transaksi Terbaru" />
            <div className="p-8">
                <Container>
                    <div className="mb-6 flex gap-8">
                        <div className="bg-white rounded shadow p-4">
                            <div className="text-sm text-gray-500">Jumlah Transaksi</div>
                            <div className="text-2xl font-bold">{stats.count}</div>
                        </div>
                    </div>
                    <Table
                        listAction={[]}
                        tableHeader={[
                            { label: 'ID', key: 'id' },
                            { label: 'Event', key: 'event.nama' },
                            { label: 'Email', key: 'user.email' },
                            { label: 'Total', key: 'total_pembayaran' },
                            { label: 'Status', key: 'status' },
                            { label: 'Tanggal', key: 'created_at', formatData: (date) => dayjs(date).format('DD/MM/YYYY HH:mm:ss') },
                            
                        ]}
                        data={transactions}
                        handleRowClick={(item) => router.visit(route('transactions.show', item.id))}
                    />
                    
                    {/* <div className="overflow-x-auto">
                        <table className="min-w-full bg-white rounded shadow">
                            <thead>
                                <tr>
                                    <th className="px-4 py-2">ID</th>
                                    <th className="px-4 py-2">Event</th>
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
                                        <td className="px-4 py-2">{trx.event?.nama}</td>
                                        <td className="px-4 py-2">{trx.user?.name}</td>
                                        <td className="px-4 py-2">Rp{trx.total?.toLocaleString()}</td>
                                        <td className="px-4 py-2">{new Date(trx.created_at).toLocaleString()}</td>
                                        <td className="px-4 py-2">
                                            <Link href={route('transactions.show', trx.id)} className="text-blue-600 hover:underline">Detail</Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div> */}
                </Container>
            </div>
        </AuthenticatedLayout>
    );
}


const Header = () => {
    return (
        <div className="flex justify-between items-center">
            <h2 className="text-xl font-semibold leading-tight text-gray-800">Transaksi Terbaru</h2>
        </div>
    );
};