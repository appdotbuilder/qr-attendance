import React, { useState, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import { AppShell } from '@/components/app-shell';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { router } from '@inertiajs/react';

interface Employee {
    id: number;
    name: string;
    department: string;
    position: string;
}

interface Attendance {
    id: number;
    date: string;
    check_in_time?: string;
    check_out_time?: string;
    status: string;
    office_location?: {
        name: string;
    };
}

interface Props {
    employee: Employee;
    todayAttendance?: Attendance;
    recentAttendances: Attendance[];
    canCheckIn: boolean;
    canCheckOut: boolean;
    [key: string]: unknown;
}

export default function AttendanceScanner({ 
    employee, 
    todayAttendance, 
    recentAttendances, 
    canCheckIn, 
    canCheckOut 
}: Props) {
    const [isScanning, setIsScanning] = useState(false);
    const [scanError, setScanError] = useState('');
    const [scanSuccess, setScanSuccess] = useState('');
    const [location, setLocation] = useState<{latitude: number, longitude: number} | null>(null);
    const [locationError, setLocationError] = useState('');

    // Get user's location
    useEffect(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    setLocation({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                    });
                },
                (error) => {
                    setLocationError('Location access required for attendance. Please enable GPS.');
                    console.error('Location error:', error);
                }
            );
        } else {
            setLocationError('Geolocation is not supported by this browser.');
        }
    }, []);

    const handleQRCodeScan = async (type: 'check_in' | 'check_out') => {
        if (!location) {
            setScanError('Location not available. Please enable GPS and refresh.');
            return;
        }

        // For demo purposes, we'll use a prompt to get QR code
        // In a real app, this would use camera scanning
        const qrCode = prompt('Enter QR Code (or scan with camera):');
        
        if (!qrCode) {
            return;
        }

        setIsScanning(true);
        setScanError('');
        setScanSuccess('');

        try {
            const response = await fetch(route('attendance.store'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({
                    qr_code: qrCode,
                    type: type,
                    latitude: location.latitude,
                    longitude: location.longitude,
                }),
            });

            const data = await response.json();

            if (response.ok) {
                setScanSuccess(data.message);
                // Refresh page data
                router.reload({ only: ['todayAttendance', 'recentAttendances', 'canCheckIn', 'canCheckOut'] });
            } else {
                setScanError(data.error || 'Failed to process attendance.');
            }
        } catch (error) {
            setScanError('Network error. Please try again.');
            console.error('Attendance error:', error);
        } finally {
            setIsScanning(false);
        }
    };

    const formatTime = (timeString: string) => {
        if (!timeString) return '-';
        return new Date(timeString).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const formatDate = (dateString: string) => {
        return new Date(dateString).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'present':
                return <Badge className="bg-green-100 text-green-700">Hadir</Badge>;
            case 'late':
                return <Badge className="bg-orange-100 text-orange-700">Terlambat</Badge>;
            case 'absent':
                return <Badge className="bg-red-100 text-red-700">Absen</Badge>;
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    return (
        <AppShell>
            <Head title="Absensi QR Scanner" />
            
            <div className="container mx-auto p-6 space-y-6">
                {/* Header */}
                <div className="text-center space-y-2">
                    <h1 className="text-3xl font-bold text-gray-900">📱 Absensi QR Scanner</h1>
                    <p className="text-gray-600">Scan QR Code untuk melakukan check-in atau check-out</p>
                    <div className="text-sm text-gray-500">
                        {employee?.name} • {employee?.department} • {employee?.position}
                    </div>
                </div>

                {/* Location Status */}
                {locationError && (
                    <Alert className="border-red-200 bg-red-50">
                        <AlertDescription className="text-red-700">
                            🚨 {locationError}
                        </AlertDescription>
                    </Alert>
                )}

                {location && (
                    <Alert className="border-green-200 bg-green-50">
                        <AlertDescription className="text-green-700">
                            ✅ GPS Location detected: {location.latitude.toFixed(6)}, {location.longitude.toFixed(6)}
                        </AlertDescription>
                    </Alert>
                )}

                {/* Scan Results */}
                {scanError && (
                    <Alert className="border-red-200 bg-red-50">
                        <AlertDescription className="text-red-700">
                            ❌ {scanError}
                        </AlertDescription>
                    </Alert>
                )}

                {scanSuccess && (
                    <Alert className="border-green-200 bg-green-50">
                        <AlertDescription className="text-green-700">
                            ✅ {scanSuccess}
                        </AlertDescription>
                    </Alert>
                )}

                {/* Today's Status */}
                <Card className="border-2">
                    <CardHeader>
                        <CardTitle className="flex items-center space-x-2">
                            <span>📅</span>
                            <span>Absensi Hari Ini</span>
                        </CardTitle>
                        <CardDescription>
                            {formatDate(new Date().toISOString())}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {todayAttendance ? (
                            <div className="grid md:grid-cols-3 gap-4">
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-green-600">
                                        {todayAttendance.check_in_time ? formatTime(todayAttendance.check_in_time) : '-'}
                                    </div>
                                    <div className="text-sm text-gray-600">Check-in</div>
                                </div>
                                <div className="text-center">
                                    <div className="text-2xl font-bold text-blue-600">
                                        {todayAttendance.check_out_time ? formatTime(todayAttendance.check_out_time) : '-'}
                                    </div>
                                    <div className="text-sm text-gray-600">Check-out</div>
                                </div>
                                <div className="text-center">
                                    {getStatusBadge(todayAttendance.status)}
                                    <div className="text-sm text-gray-600 mt-1">Status</div>
                                </div>
                            </div>
                        ) : (
                            <div className="text-center py-8 text-gray-500">
                                <span className="text-4xl">📝</span>
                                <p className="mt-2">Belum melakukan absensi hari ini</p>
                            </div>
                        )}
                    </CardContent>
                </Card>

                {/* Action Buttons */}
                <div className="grid md:grid-cols-2 gap-4">
                    <Button
                        size="lg"
                        className="h-24 text-lg"
                        onClick={() => handleQRCodeScan('check_in')}
                        disabled={!canCheckIn || isScanning || !location}
                    >
                        <div className="text-center">
                            <div className="text-2xl mb-1">🔵</div>
                            <div>{isScanning ? 'Processing...' : 'Check-In'}</div>
                            <div className="text-sm opacity-80">Masuk Kantor</div>
                        </div>
                    </Button>

                    <Button
                        size="lg"
                        variant="outline"
                        className="h-24 text-lg"
                        onClick={() => handleQRCodeScan('check_out')}
                        disabled={!canCheckOut || isScanning || !location}
                    >
                        <div className="text-center">
                            <div className="text-2xl mb-1">🔴</div>
                            <div>{isScanning ? 'Processing...' : 'Check-Out'}</div>
                            <div className="text-sm opacity-80">Pulang Kantor</div>
                        </div>
                    </Button>
                </div>

                {/* Instructions */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center space-x-2">
                            <span>📋</span>
                            <span>Cara Menggunakan</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <ol className="space-y-2 text-sm">
                            <li className="flex items-start space-x-2">
                                <span className="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs">1</span>
                                <span>Pastikan GPS/Location aktif di perangkat Anda</span>
                            </li>
                            <li className="flex items-start space-x-2">
                                <span className="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs">2</span>
                                <span>Berada di dalam radius kantor yang telah ditentukan</span>
                            </li>
                            <li className="flex items-start space-x-2">
                                <span className="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs">3</span>
                                <span>Klik tombol Check-In atau Check-Out</span>
                            </li>
                            <li className="flex items-start space-x-2">
                                <span className="flex-shrink-0 w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs">4</span>
                                <span>Scan QR Code yang tersedia di kantor</span>
                            </li>
                        </ol>
                    </CardContent>
                </Card>

                {/* Recent Attendances */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center space-x-2">
                            <span>📊</span>
                            <span>Riwayat Absensi Terkini</span>
                        </CardTitle>
                        <CardDescription>
                            5 absensi terakhir Anda
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        {recentAttendances.length > 0 ? (
                            <div className="space-y-3">
                                {recentAttendances.map((attendance, index) => (
                                    <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <div className="font-medium">
                                                {new Date(attendance.date).toLocaleDateString('id-ID', {
                                                    weekday: 'short',
                                                    day: '2-digit',
                                                    month: 'short',
                                                })}
                                            </div>
                                            <div className="text-sm text-gray-600">
                                                {attendance.office_location?.name}
                                            </div>
                                        </div>
                                        <div className="text-right">
                                            <div className="text-sm">
                                                {attendance.check_in_time ? formatTime(attendance.check_in_time) : '-'} - {attendance.check_out_time ? formatTime(attendance.check_out_time) : '-'}
                                            </div>
                                            {getStatusBadge(attendance.status)}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-8 text-gray-500">
                                <span className="text-4xl">📅</span>
                                <p className="mt-2">Belum ada riwayat absensi</p>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AppShell>
    );
}