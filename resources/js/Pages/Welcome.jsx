import { Head, Link } from '@inertiajs/react';

export default function Welcome({ auth, laravelVersion, phpVersion, canLogin, canRegister, appDescription, features }) {
    const handleImageError = () => {
        document
            .getElementById('screenshot-container')
            ?.classList.add('!hidden');
        document.getElementById('docs-card')?.classList.add('!row-span-1');
        document
            .getElementById('docs-card-content')
            ?.classList.add('!flex-row');
        document.getElementById('background')?.classList.add('!hidden');
    };

    return (
        <>
            <Head title="Welcome" />
            <div className="bg-white text-black/80 min-h-screen">
                <div className="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                    <div className="relative w-full max-w-2xl px-6 lg:max-w-4xl">
                        <header className="flex flex-col items-center gap-2 py-10">
                            <div className="flex justify-center">
                                <img src="/images/logo.png" alt="Event Organizer Logo" className="h-40 w-40 object-cover" />
                            </div>
                            <h1 className="text-3xl font-bold text-center mt-0">Event Organizer Platform</h1>
                            <p className="text-center text-base max-w-xl mt-2">{appDescription}</p>
                            <nav className="flex gap-4 mt-4">
                                {auth?.user ? (
                                    <Link
                                        href={route('dashboard')}
                                        className="rounded-md px-4 py-2 bg-[#FF2D20] text-white font-semibold shadow hover:bg-[#e52a1a] transition"
                                    >
                                        Dashboard
                                    </Link>
                                ) : (
                                    <>
                                        {canLogin && (
                                            <Link
                                                href={route('login')}
                                                className="rounded-md px-4 py-2 bg-[#FF2D20] text-white font-semibold shadow hover:bg-[#e52a1a] transition"
                                            >
                                                Log in
                                            </Link>
                                        )}
                                        {canRegister && (
                                            <Link
                                                href={route('register')}
                                                className="rounded-md px-4 py-2 bg-white text-[#FF2D20] font-semibold border border-[#FF2D20] shadow hover:bg-[#FF2D20]/10 transition"
                                            >
                                                Register
                                            </Link>
                                        )}
                                    </>
                                )}
                            </nav>
                        </header>
                        <main className="mt-10">
                            <section className="mb-10">
                                <h2 className="text-xl font-semibold mb-4 text-center">Fitur Utama</h2>
                                <ul className="grid gap-3 md:grid-cols-2">
                                    {features && features.map((feature, idx) => (
                                        <li key={idx} className="flex items-center gap-2 bg-white rounded-lg px-4 py-3 shadow">
                                            <span className="inline-block w-2 h-2 rounded-full bg-[#FF2D20]" />
                                            <span>{feature}</span>
                                        </li>
                                    ))}
                                </ul>
                            </section>
                            <footer className="py-8 text-center text-sm text-black/60">
                                &copy; {new Date().getFullYear()} Event Organizer Platform
                            </footer>
                        </main>
                    </div>
                </div>
            </div>
        </>
    );
}
