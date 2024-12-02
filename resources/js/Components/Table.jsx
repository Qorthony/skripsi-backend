export default function Table({
    listAction,
    tableHeader,
    data,
    handleRowClick
}) {

    return (
        <div className="overflow-x-auto">
            <table className="table-auto w-full text-sm text-left rtl:text-right text-gray-600">
                <thead className="text-sm text-gray-600 uppercase bg-gray-100">
                    <tr>
                        {tableHeader.map((item, index) => (
                            <th scope="col" key={index} className="px-6 py-3">{item.label}</th>
                        ))}
                        <th scope="col" className="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    {data.map((item, index) => (
                        <tr key={index} className="bg-white border-b hover:bg-gray-50">
                            {tableHeader.map((header, indexColumn) => (
                                <td onClick={() => handleRowClick(item)} key={indexColumn} className="px-6 py-4">{item[header.key]}</td>
                            ))}
                            <td className="px-6 py-4 flex">
                                {listAction.map((action, index) => (
                                    <button key={index} onClick={() => action.action(item)} className="mx-1 text-blue-500 hover:text-blue-700">{action.label}</button>
                                ))}
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}