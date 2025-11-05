import { dashboard, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import AppLogoIconTools from '@/components/app-logo-icon-tools';
import { motion } from 'framer-motion';


const FeatureIcon = ({ children }: { children: React.ReactNode }) => (
    <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
        {children}
    </div>
);

const Feature = ({
    title,
    description,
    icon,
}: {
    title: string;
    description: string;
    icon: React.ReactNode;
}) => (
    <motion.div
        className="text-center"
        variants={{
            initial: { opacity: 0, y: 30 },
            animate: { opacity: 1, y: 0, transition: { duration: 0.6, ease: 'easeOut' } }
        }}
        whileHover={{ y: -5, transition: { duration: 0.3 } }}
    >
        <FeatureIcon>{icon}</FeatureIcon>
        <h3 className="mb-2 text-xl font-bold text-gray-900 dark:text-white">
            {title}
        </h3>
        <p className="text-gray-600 dark:text-gray-400">{description}</p>
    </motion.div>
);

const Problem = ({ title }: { title: string }) => (
    <motion.div
        className="rounded-lg bg-gray-100 p-6 dark:bg-gray-800"
        variants={{
            initial: { opacity: 0, y: 30 },
            animate: { opacity: 1, y: 0, transition: { duration: 0.6, ease: 'easeOut' } }
        }}
        whileHover={{ scale: 1.02, transition: { duration: 0.3 } }}
    >
        <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
            {title}
        </h3>
    </motion.div>
);

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;
    const { t } = useLaravelReactI18n();

    return (
        <>
            <Head title="Witamy w FixFlow">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700"
                    rel="stylesheet"
                />
            </Head>
            <div className="flex min-h-screen flex-col bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                <header className="w-full bg-white p-6 dark:bg-gray-950 lg:p-8">
                    <div className="mx-auto flex max-w-7xl items-center justify-between">
                        <motion.div
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.6, ease: 'easeOut' }}
                        >
                            <Link href="/" className="flex items-center gap-2">
                                <AppLogoIconTools className="h-6 w-6 text-primary" />
                                <span className="text-lg font-semibold text-gray-900 dark:text-white">
                                    FixFlow
                                </span>
                            </Link>
                        </motion.div>
                        <motion.nav
                            className="flex items-center gap-4"
                            initial={{ opacity: 0, x: 20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{
                                duration: 0.6,
                                ease: 'easeOut',
                                delay: 0.2,
                            }}
                        >
                            {auth.user ? (
                                <Link
                                    href={dashboard()}
                                    className="rounded-md px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-indigo-500 dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    Panel
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={login()}
                                        className="rounded-md px-4 py-2 text-sm font-medium text-gray-700 ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-primary dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                    >
                                        {t('login')}
                                    </Link>
                                    <Link
                                        href={register()}
                                        className="rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition hover:bg-primary/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                                    >
                                        {t('register')}
                                    </Link>
                                </>
                            )}
                        </motion.nav>
                    </div>
                </header>

                <main>
                    {/* Hero Section */}
                    <section className="relative flex h-[40rem] items-center overflow-hidden">
                        <div className="absolute inset-0 z-0">
                            <img
                                src="/img/hero1.jpg"
                                alt="Mechanik pracujący przy samochodzie"
                                className="h-full w-full object-cover"
                            />
                            <div className="absolute inset-0 bg-black/60" />
                        </div>
                        <div className="relative z-10 mx-auto max-w-7xl px-4 text-center lg:px-8">
                            <div className="flex flex-col items-center">
                                <motion.h1
                                    className="text-4xl font-bold tracking-tight text-white sm:text-6xl"
                                    initial={{ opacity: 0, y: 30 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{
                                        duration: 0.8,
                                        ease: 'easeOut',
                                    }}
                                >
                                    Zoptymalizuj pracę swojego warsztatu z
                                    FixFlow
                                </motion.h1>
                                <motion.p
                                    className="mt-6 max-w-2xl text-lg leading-8 text-gray-300"
                                    initial={{ opacity: 0, y: 30 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{
                                        duration: 0.8,
                                        ease: 'easeOut',
                                        delay: 0.2,
                                    }}
                                >
                                    Precyzyjne śledzenie czasu pracy, analiza
                                    rentowności i efektywności zespołu w jednym
                                    miejscu. Pożegnaj papierową dokumentację i
                                    podejmuj decyzje oparte na danych.
                                </motion.p>
                                <motion.div
                                    className="mt-10 flex items-center justify-center gap-x-6"
                                    initial={{ opacity: 0, y: 30 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{
                                        duration: 0.8,
                                        ease: 'easeOut',
                                        delay: 0.4,
                                    }}
                                >
                                    <motion.div
                                        whileHover={{ scale: 1.05 }}
                                        whileTap={{ scale: 0.95 }}
                                    >
                                        <Link
                                            href={register()}
                                            className="rounded-md bg-primary px-6 py-3 text-base font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                                        >
                                            Rozpocznij teraz
                                        </Link>
                                    </motion.div>
                                </motion.div>
                            </div>
                        </div>
                    </section>

                    {/* Problem Section */}
                    <section className="bg-gray-100 py-24 sm:py-32 dark:bg-gray-950">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <motion.div
                                className="mx-auto max-w-2xl text-center"
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, margin: '-50px' }}
                                transition={{ duration: 0.6, ease: 'easeOut' }}
                            >
                                <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                                    Czy Twój warsztat boryka się z tymi
                                    problemami?
                                </h2>
                                <p className="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                                    Ręczne śledzenie czasu pracy jest
                                    nieefektywne, podatne na błędy i utrudnia
                                    analizę kluczowych danych dla Twojego
                                    biznesu.
                                </p>
                            </motion.div>
                            <motion.div
                                className="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-8 lg:mx-0 lg:max-w-none lg:grid-cols-3"
                                initial="initial"
                                whileInView="animate"
                                viewport={{ once: true, margin: '-50px' }}
                                variants={{
                                    animate: {
                                        transition: {
                                            staggerChildren: 0.1
                                        }
                                    }
                                }}
                            >
                                <Problem title="Niedokładna analiza rentowności" />
                                <Problem title="Brak wglądu w wydajność zespołu" />
                                <Problem title="Uciążliwy dostęp do historii napraw" />
                            </motion.div>
                        </div>
                    </section>

                    {/* Features Section */}
                    <section className="bg-white py-24 sm:py-32 dark:bg-gray-900">
                        <div className="mx-auto max-w-7xl px-6 lg:px-8">
                            <motion.div
                                className="mx-auto max-w-2xl text-center"
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true, margin: '-50px' }}
                                transition={{ duration: 0.6, ease: 'easeOut' }}
                            >
                                <h2 className="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                                    Odkryj kluczowe funkcje FixFlow
                                </h2>
                                <p className="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                                    Wszystko, czego potrzebujesz, aby przenieść
                                    zarządzanie warsztatem na wyższy poziom.
                                </p>
                            </motion.div>
                            <motion.div
                                className="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-4"
                                initial="initial"
                                whileInView="animate"
                                viewport={{ once: true, margin: '-50px' }}
                                variants={{
                                    animate: {
                                        transition: {
                                            staggerChildren: 0.15
                                        }
                                    }
                                }}
                            >
                                <Feature
                                    title="Zarządzanie Zleceniami"
                                    description="Twórz, aktualizuj i śledź statusy zleceń w czasie rzeczywistym."
                                    icon={
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="h-6 w-6 text-primary"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C6.095 4.01 5.25 4.973 5.25 6.108V18.75c0 1.282.806 2.37 1.908 2.599l.11.022c.567.11.936.636.936 1.206v2.625a1.125 1.125 0 001.125 1.125h3.375c.621 0 1.125-.504 1.125-1.125V22.5c0-.57.368-1.096.936-1.206l.11-.022c1.102-.23 1.908-1.317 1.908-2.599V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08"
                                            />
                                        </svg>
                                    }
                                />
                                <Feature
                                    title="Rejestracja Czasu Pracy"
                                    description="Proste i szybkie rejestrowanie czasu pracy mechaników na dowolnym urządzeniu."
                                    icon={
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="h-6 w-6 text-primary"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    }
                                />
                                <Feature
                                    title="Baza Klientów i Pojazdów"
                                    description="Kompletna historia napraw i serwisów dla każdego pojazdu w Twoim warsztacie."
                                    icon={
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="h-6 w-6 text-primary"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V14.25m-17.25 4.5v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H3.375c-.621 0-1.125.504-1.125 1.125v1.5a2.25 2.25 0 002.25 2.25h1.5a3.375 3.375 0 003.375 3.375H9m1.5-9H18a2.25 2.25 0 002.25-2.25v-1.5a3.375 3.375 0 00-3.375-3.375h-1.5a2.25 2.25 0 00-2.25 2.25v1.5m1.5-9a2.25 2.25 0 00-2.25 2.25v1.5"
                                            />
                                        </svg>
                                    }
                                />
                                <Feature
                                    title="Zaawansowane Raportowanie"
                                    description="Generuj raporty wydajności, aby podejmować lepsze decyzje biznesowe."
                                    icon={
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="h-6 w-6 text-primary"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"
                                            />
                                        </svg>
                                    }
                                />
                            </motion.div>
                        </div>
                    </section>
                </main>

                <footer className="bg-white dark:bg-gray-950">
                    <div className="mx-auto max-w-7xl px-6 py-12 lg:px-8">
                        <div className="mt-8 border-t border-gray-900/10 pt-8 dark:border-white/10">
                            <p className="text-center text-xs leading-5 text-gray-500 dark:text-gray-400">
                                © {new Date().getFullYear()} FixFlow. Wszelkie
                                prawa zastrzeżone.
                            </p>
                        </div>
                </div>
                </footer>
            </div>
        </>
    );
}
