import { Head } from '@inertiajs/react';
import React, { use, useEffect } from 'react';

function isAndroid() {
    return /Android/i.test(navigator.userAgent);
}

const MobileLink = ({intentLink,downloadLink}) => {

    const handleOpenApp = () => {
        window.location.href = intentLink;
    };

    useEffect(() => {
        // Coba buka aplikasi Android saat komponen dimount
        if (isAndroid()) {
            window.location.href = intentLink;
        }
    }, []);

    if (isAndroid()) {
        return (
            <Head title='Link to Mobile App'>
                <div className="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-50 to-white px-4">
                    <div className="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md text-center">
                        <h1 className="text-2xl font-bold mb-6">Buka di Aplikasi Android</h1>
                        <button
                            onClick={handleOpenApp}
                            className="w-full py-3 mb-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold rounded-lg shadow transition duration-200"
                        >
                            Lanjutkan
                        </button>
                        <div className="mt-2">
                            <span className="text-gray-500 text-sm block mb-1">Belum punya aplikasinya?</span>
                            <a
                                href={downloadLink}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="inline-block text-blue-600 hover:text-blue-800 font-medium underline"
                            >
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            </Head>
        );
    }

    // Jika bukan Android (desktop atau iOS)
    return (
        <Head title='Link to Mobile App'>
            <div className="flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-blue-50 to-white px-4">
                <div className="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md text-center">
                    <h1 className="text-2xl font-bold mb-6">Download Aplikasi Android</h1>
                    <a
                        href={downloadLink}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-block text-blue-600 hover:text-blue-800 font-medium underline text-lg"
                    >
                        Download
                    </a>
                </div>
            </div>
        </Head>
    );
};

export default MobileLink;
