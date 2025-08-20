import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

export default function Dashboard(): React.JSX.Element {
    const currentTime = new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });

    const currentDate = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });

    return (
        <AppShell>
            <Head title="Dashboard - Sistem Absensi QR" />
            
            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div className="text-center space-y-2">
                    <h1 className="text-3xl font-bold text-gray-900">📊 Dashboard Absensi</h1>
                    <p className="text-gray-600">Selamat datang di sistem absensi QR Code</p>
                    <div className="text-sm text-gray-500">
                        {currentDate} • {currentTime}
                    </div>
                </div>

                {/* Quick Actions */}
                <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card className="border-2 border-blue-200 hover:border-blue-300 transition-colors">
                        <CardHeader className="pb-4">
                            <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                                <span className="text-2xl">📱</span>
                            </div>
                            <CardTitle className="text-lg">Absensi</CardTitle>
                            <CardDescription>Scan QR untuk absensi</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button asChild className="w-full">
                                <Link href="/attendance">
                                    Buka Scanner
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>

                    <Card className="border-2 border-green-200 hover:border-green-300 transition-colors">
                        <CardHeader className="pb-4">
                            <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-2">
                                <span className="text-2xl">📊</span>
                            </div>
                            <CardTitle className="text-lg">Laporan</CardTitle>
                            <CardDescription>Lihat laporan kehadiran</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button variant="outline" asChild className="w-full">
                                <Link href="/attendance/report">
                                    Buka Laporan
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>

                    <Card className="border-2 border-purple-200 hover:border-purple-300 transition-colors">
                        <CardHeader className="pb-4">
                            <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-2">
                                <span className="text-2xl">👥</span>
                            </div>
                            <CardTitle className="text-lg">Karyawan</CardTitle>
                            <CardDescription>Kelola data karyawan</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button variant="outline" asChild className="w-full">
                                <Link href="/admin/employees">
                                    Kelola Data
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>

                    <Card className="border-2 border-orange-200 hover:border-orange-300 transition-colors">
                        <CardHeader className="pb-4">
                            <div className="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-2">
                                <span className="text-2xl">🔲</span>
                            </div>
                            <CardTitle className="text-lg">QR Code</CardTitle>
                            <CardDescription>Kelola QR Code kantor</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <Button variant="outline" asChild className="w-full">
                                <Link href="/admin/qr-codes">
                                    Kelola QR
                                </Link>
                            </Button>
                        </CardContent>
                    </Card>
                </div>

                {/* Today's Summary */}
                <div className="grid md:grid-cols-3 gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center space-x-2">
                                <span>📅</span>
                                <span>Hari Ini</span>
                            </CardTitle>
                            <CardDescription>Ringkasan kehadiran hari ini</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Status:</span>
                                    <Badge className="bg-green-100 text-green-700">Belum Absen</Badge>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Check-in:</span>
                                    <span className="text-sm font-medium">-</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Check-out:</span>
                                    <span className="text-sm font-medium">-</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center space-x-2">
                                <span>📊</span>
                                <span>Statistik Bulan Ini</span>
                            </CardTitle>
                            <CardDescription>Ringkasan kehadiran bulan ini</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Total Hadir:</span>
                                    <span className="text-sm font-medium">0 hari</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Terlambat:</span>
                                    <span className="text-sm font-medium">0 hari</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Total Jam:</span>
                                    <span className="text-sm font-medium">0 jam</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center space-x-2">
                                <span>🏢</span>
                                <span>Info Kantor</span>
                            </CardTitle>
                            <CardDescription>Informasi lokasi dan jam kerja</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Jam Masuk:</span>
                                    <span className="text-sm font-medium">08:00</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Jam Pulang:</span>
                                    <span className="text-sm font-medium">17:00</span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-600">Lokasi:</span>
                                    <span className="text-sm font-medium">Jakarta</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Features Overview */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center space-x-2">
                            <span>🚀</span>
                            <span>Fitur Sistem Absensi</span>
                        </CardTitle>
                        <CardDescription>
                            Fitur-fitur unggulan sistem absensi QR Code
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div className="text-center p-4 bg-blue-50 rounded-lg">
                                <div className="text-3xl mb-2">📱</div>
                                <h3 className="font-semibold text-sm">QR Code Scanning</h3>
                                <p className="text-xs text-gray-600 mt-1">Absensi dengan scan QR</p>
                            </div>
                            <div className="text-center p-4 bg-green-50 rounded-lg">
                                <div className="text-3xl mb-2">🌍</div>
                                <h3 className="font-semibold text-sm">GPS Verification</h3>
                                <p className="text-xs text-gray-600 mt-1">Verifikasi lokasi otomatis</p>
                            </div>
                            <div className="text-center p-4 bg-purple-50 rounded-lg">
                                <div className="text-3xl mb-2">📊</div>
                                <h3 className="font-semibold text-sm">Real-time Reports</h3>
                                <p className="text-xs text-gray-600 mt-1">Laporan waktu nyata</p>
                            </div>
                            <div className="text-center p-4 bg-orange-50 rounded-lg">
                                <div className="text-3xl mb-2">👥</div>
                                <h3 className="font-semibold text-sm">Multi-Role System</h3>
                                <p className="text-xs text-gray-600 mt-1">Admin, HRD, Karyawan</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Quick Links */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center space-x-2">
                            <span>🔗</span>
                            <span>Menu Utama</span>
                        </CardTitle>
                        <CardDescription>
                            Akses cepat ke fitur-fitur utama sistem
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <Button asChild variant="outline" className="h-auto p-4">
                                <Link href="/attendance" className="flex flex-col items-center space-y-2">
                                    <span className="text-2xl">📱</span>
                                    <span>Scan QR Absensi</span>
                                    <span className="text-xs text-gray-500">Check-in & Check-out</span>
                                </Link>
                            </Button>

                            <Button asChild variant="outline" className="h-auto p-4">
                                <Link href="/attendance/report" className="flex flex-col items-center space-y-2">
                                    <span className="text-2xl">📊</span>
                                    <span>Laporan Kehadiran</span>
                                    <span className="text-xs text-gray-500">Harian, Mingguan, Bulanan</span>
                                </Link>
                            </Button>

                            <Button asChild variant="outline" className="h-auto p-4">
                                <Link href="/profile" className="flex flex-col items-center space-y-2">
                                    <span className="text-2xl">👤</span>
                                    <span>Profil Saya</span>
                                    <span className="text-xs text-gray-500">Edit informasi pribadi</span>
                                </Link>
                            </Button>

                            <Button asChild variant="outline" className="h-auto p-4">
                                <Link href="/admin/employees" className="flex flex-col items-center space-y-2">
                                    <span className="text-2xl">👥</span>
                                    <span>Kelola Karyawan</span>
                                    <span className="text-xs text-gray-500">Admin & HRD</span>
                                </Link>
                            </Button>

                            <Button asChild variant="outline" className="h-auto p-4">
                                <Link href="/admin/qr-codes" className="flex flex-col items-center space-y-2">
                                    <span className="text-2xl">🔲</span>
                                    <span>Kelola QR Code</span>
                                    <span className="text-xs text-gray-500">Generate & Manage</span>
                                </Link>
                            </Button>

                            <Button asChild variant="outline" className="h-auto p-4">
                                <Link href="/settings" className="flex flex-col items-center space-y-2">
                                    <span className="text-2xl">⚙️</span>
                                    <span>Pengaturan</span>
                                    <span className="text-xs text-gray-500">Konfigurasi sistem</span>
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppShell>
    );
}