export default function Table({
    listAction = [],
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
                        {listAction.length > 0 && ( 
                            <th scope="col" className="px-6 py-3">Action</th>
                        )}
                    </tr>
                </thead>
                <tbody>
                    {data.map((item, index) => (
                        <tr key={index} className="bg-white border-b hover:bg-gray-50">
                            {tableHeader.map((header, indexColumn) => (
                                <td 
                                    onClick={() => handleRowClick(item)} 
                                    key={indexColumn} 
                                    className={`px-6 py-4`}
                                >
                                    {
                                        header.formatData?
                                        header.formatData(parseHeaderKey(item,header.key)) 
                                        :parseHeaderKey(item,header.key)??"-"
                                    }
                                </td>
                            ))}
                            {listAction.length > 0 && (
                                <td className="px-6 py-4 flex">
                                    {listAction.map((action, index) => (
                                    <button key={index} onClick={() => action.action(item)} className="mx-1 text-blue-500 hover:text-blue-700">{action.label}</button>
                                    ))}
                                </td>
                            )}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

function parseHeaderKey(data, key) {
    const keys = key.split('.');
    let value = data;

    // Loop through the keys to access the nested property
    for (let i = 0; i < keys.length; i++) {
        if (value[keys[i]] !== undefined) {
            value = value[keys[i]];
        } else {
            return null; // Return null if the key doesn't exist
        }
    }
    
    return value;
}