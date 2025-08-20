import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    auth?: {
        user?: User;
    };
    [key: string]: unknown;
}

export default function Welcome({ auth }: Props) {
    return (
        <>
            <Head title="Sistem Absensi QR Code - Kelola Kehadiran Karyawan dengan Mudah" />
            
            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
                {/* Header */}
                <header className="container mx-auto px-4 py-6">
                    <nav className="flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <div className="w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                                <span className="text-white font-bold text-lg">📱</span>
                            </div>
                            <div>
                                <h1 className="text-xl font-bold text-gray-900">AttendQR</h1>
                                <p className="text-xs text-gray-600">Smart Attendance System</p>
                            </div>
                        </div>
                        
                        <div className="flex items-center space-x-4">
                            {auth?.user ? (
                                <>
                                    <Link
                                        href="/dashboard"
                                        className="text-gray-600 hover:text-gray-900 transition-colors"
                                    >
                                        Dashboard
                                    </Link>
                                    <Button asChild>
                                        <Link href="/attendance">
                                            📱 Scan QR
                                        </Link>
                                    </Button>
                                </>
                            ) : (
                                <>
                                    <Button variant="ghost" asChild>
                                        <Link href="/login">
                                            Masuk
                                        </Link>
                                    </Button>
                                    <Button asChild>
                                        <Link href="/register">
                                            Daftar
                                        </Link>
                                    </Button>
                                </>
                            )}
                        </div>
                    </nav>
                </header>

                {/* Hero Section */}
                <main className="container mx-auto px-4 py-12">
                    <div className="text-center mb-16">
                        <div className="inline-flex items-center space-x-2 bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium mb-6">
                            <span>🚀</span>
                            <span>Sistem Absensi Modern dengan Teknologi QR Code</span>
                        </div>
                        
                        <h1 className="text-5xl font-bold text-gray-900 mb-6">
                            📱 Sistem Absensi QR Code
                            <span className="block text-blue-600 mt-2">untuk Karyawan</span>
                        </h1>
                        
                        <p className="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                            Kelola kehadiran karyawan dengan mudah menggunakan teknologi QR Code dan verifikasi GPS. 
                            Sistem terintegrasi untuk Admin, HRD, dan Karyawan dengan laporan real-time.
                        </p>

                        {!auth?.user && (
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Button size="lg" asChild className="text-lg px-8 py-6">
                                    <Link href="/register">
                                        🎯 Mulai Sekarang - Gratis
                                    </Link>
                                </Button>
                                <Button variant="outline" size="lg" asChild className="text-lg px-8 py-6">
                                    <Link href="/login">
                                        🔑 Masuk ke Akun
                                    </Link>
                                </Button>
                            </div>
                        )}
                    </div>

                    {/* Features Grid */}
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                        <Card className="border-2 hover:border-blue-300 transition-colors">
                            <CardHeader>
                                <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                                    <span className="text-2xl">📱</span>
                                </div>
                                <CardTitle>Scan QR Code</CardTitle>
                                <CardDescription>
                                    Absensi mudah dengan scan QR code menggunakan smartphone karyawan
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ul className="space-y-2 text-sm text-gray-600">
                                    <li className="flex items-center">✅ Check-in dan Check-out otomatis</li>
                                    <li className="flex items-center">✅ QR Code unik untuk setiap lokasi</li>
                                    <li className="flex items-center">✅ Waktu real-time</li>
                                </ul>
                            </CardContent>
                        </Card>

                        <Card className="border-2 hover:border-green-300 transition-colors">
                            <CardHeader>
                                <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                                    <span className="text-2xl">🌍</span>
                                </div>
                                <CardTitle>Verifikasi GPS</CardTitle>
                                <CardDescription>
                                    Pastikan karyawan berada di lokasi kantor saat melakukan absensi
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ul className="space-y-2 text-sm text-gray-600">
                                    <li className="flex items-center">✅ Validasi radius lokasi</li>
                                    <li className="flex items-center">✅ Anti-fraud location spoofing</li>
                                    <li className="flex items-center">✅ Multiple office locations</li>
                                </ul>
                            </CardContent>
                        </Card>

                        <Card className="border-2 hover:border-purple-300 transition-colors">
                            <CardHeader>
                                <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                                    <span className="text-2xl">👥</span>
                                </div>
                                <CardTitle>3 Role Pengguna</CardTitle>
                                <CardDescription>
                                    Sistem role yang lengkap untuk berbagai kebutuhan organisasi
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-2">
                                    <Badge variant="outline" className="mr-2">👨‍💼 Admin</Badge>
                                    <Badge variant="outline" className="mr-2">👩‍💼 HRD</Badge>
                                    <Badge variant="outline">👨‍💻 Karyawan</Badge>
                                </div>
                            </CardContent>
                        </Card>

                        <Card className="border-2 hover:border-orange-300 transition-colors">
                            <CardHeader>
                                <div className="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                                    <span className="text-2xl">📊</span>
                                </div>
                                <CardTitle>Laporan Lengkap</CardTitle>
                                <CardDescription>
                                    Berbagai jenis laporan kehadiran untuk analisis dan evaluasi
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ul className="space-y-2 text-sm text-gray-600">
                                    <li className="flex items-center">📈 Laporan Harian</li>
                                    <li className="flex items-center">📈 Laporan Mingguan</li>
                                    <li className="flex items-center">📈 Laporan Bulanan</li>
                                    <li className="flex items-center">📈 Laporan per Karyawan</li>
                                </ul>
                            </CardContent>
                        </Card>

                        <Card className="border-2 hover:border-red-300 transition-colors">
                            <CardHeader>
                                <div className="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                                    <span className="text-2xl">⚡</span>
                                </div>
                                <CardTitle>Real-time Monitoring</CardTitle>
                                <CardDescription>
                                    Pantau kehadiran karyawan secara real-time dengan notifikasi
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ul className="space-y-2 text-sm text-gray-600">
                                    <li className="flex items-center">⚡ Status kehadiran live</li>
                                    <li className="flex items-center">⚡ Notifikasi keterlambatan</li>
                                    <li className="flex items-center">⚡ Dashboard analytics</li>
                                </ul>
                            </CardContent>
                        </Card>

                        <Card className="border-2 hover:border-indigo-300 transition-colors">
                            <CardHeader>
                                <div className="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                                    <span className="text-2xl">🔒</span>
                                </div>
                                <CardTitle>Keamanan Tinggi</CardTitle>
                                <CardDescription>
                                    Sistem keamanan berlapis untuk melindungi data kehadiran
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <ul className="space-y-2 text-sm text-gray-600">
                                    <li className="flex items-center">🔒 QR Code dengan expiry</li>
                                    <li className="flex items-center">🔒 Audit trail lengkap</li>
                                    <li className="flex items-center">🔒 Role-based access</li>
                                </ul>
                            </CardContent>
                        </Card>
                    </div>

                    {/* How It Works */}
                    <div className="bg-white rounded-2xl p-8 shadow-sm border mb-16">
                        <h2 className="text-3xl font-bold text-center text-gray-900 mb-12">
                            🔄 Cara Kerja Sistem
                        </h2>
                        
                        <div className="grid md:grid-cols-4 gap-8">
                            <div className="text-center">
                                <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl">1️⃣</span>
                                </div>
                                <h3 className="font-semibold text-gray-900 mb-2">Buka Aplikasi</h3>
                                <p className="text-sm text-gray-600">Karyawan membuka aplikasi di smartphone</p>
                            </div>
                            
                            <div className="text-center">
                                <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl">2️⃣</span>
                                </div>
                                <h3 className="font-semibold text-gray-900 mb-2">Scan QR Code</h3>
                                <p className="text-sm text-gray-600">Scan QR code yang tersedia di kantor</p>
                            </div>
                            
                            <div className="text-center">
                                <div className="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl">3️⃣</span>
                                </div>
                                <h3 className="font-semibold text-gray-900 mb-2">Verifikasi GPS</h3>
                                <p className="text-sm text-gray-600">Sistem memverifikasi lokasi karyawan</p>
                            </div>
                            
                            <div className="text-center">
                                <div className="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span className="text-2xl">4️⃣</span>
                                </div>
                                <h3 className="font-semibold text-gray-900 mb-2">Absensi Berhasil</h3>
                                <p className="text-sm text-gray-600">Data kehadiran tersimpan otomatis</p>
                            </div>
                        </div>
                    </div>

                    {/* User Roles */}
                    <div className="text-center mb-16">
                        <h2 className="text-3xl font-bold text-gray-900 mb-12">
                            👥 Tiga Role Pengguna
                        </h2>
                        
                        <div className="grid md:grid-cols-3 gap-8">
                            <Card className="border-2 border-blue-200">
                                <CardHeader>
                                    <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span className="text-3xl">👨‍💻</span>
                                    </div>
                                    <CardTitle className="text-blue-700">Karyawan</CardTitle>
                                    <CardDescription>
                                        Melakukan absensi harian dengan mudah
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm text-left">
                                        <li>✅ Scan QR untuk check-in/out</li>
                                        <li>✅ Lihat riwayat absensi</li>
                                        <li>✅ Laporan kehadiran pribadi</li>
                                        <li>✅ Notifikasi reminder</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            <Card className="border-2 border-green-200">
                                <CardHeader>
                                    <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span className="text-3xl">👨‍💼</span>
                                    </div>
                                    <CardTitle className="text-green-700">Admin</CardTitle>
                                    <CardDescription>
                                        Mengelola sistem dan konfigurasi
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm text-left">
                                        <li>✅ Kelola data karyawan</li>
                                        <li>✅ Generate QR Code</li>
                                        <li>✅ Atur lokasi kantor</li>
                                        <li>✅ Monitor sistem</li>
                                    </ul>
                                </CardContent>
                            </Card>

                            <Card className="border-2 border-purple-200">
                                <CardHeader>
                                    <div className="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <span className="text-3xl">👩‍💼</span>
                                    </div>
                                    <CardTitle className="text-purple-700">HRD</CardTitle>
                                    <CardDescription>
                                        Mengelola SDM dan laporan kehadiran
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <ul className="space-y-2 text-sm text-left">
                                        <li>✅ Laporan kehadiran lengkap</li>
                                        <li>✅ Analytics kinerja karyawan</li>
                                        <li>✅ Export data untuk payroll</li>
                                        <li>✅ Manage employee performance</li>
                                    </ul>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    {/* CTA Section */}
                    {!auth?.user && (
                        <div className="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-center text-white">
                            <h2 className="text-3xl font-bold mb-4">
                                🚀 Siap Modernisasi Sistem Absensi Anda?
                            </h2>
                            <p className="text-xl mb-8 opacity-90">
                                Bergabunglah dengan perusahaan modern yang menggunakan teknologi QR Code untuk efisiensi maksimal
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Button size="lg" variant="secondary" asChild className="text-lg px-8 py-6">
                                    <Link href="/register">
                                        🎯 Daftar Gratis Sekarang
                                    </Link>
                                </Button>
                                <Button size="lg" variant="outline" asChild className="text-lg px-8 py-6 border-white text-white hover:bg-white hover:text-blue-600">
                                    <Link href="/login">
                                        🔑 Masuk ke Sistem
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    )}
                </main>

                {/* Footer */}
                <footer className="border-t bg-white/50 backdrop-blur-sm mt-16">
                    <div className="container mx-auto px-4 py-8">
                        <div className="text-center text-gray-600">
                            <div className="flex items-center justify-center space-x-2 mb-4">
                                <span className="text-2xl">📱</span>
                                <span className="font-semibold">AttendQR - Smart Attendance System</span>
                            </div>
                            <p className="text-sm">
                                Sistem absensi modern dengan teknologi QR Code dan verifikasi GPS untuk efisiensi maksimal.
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}