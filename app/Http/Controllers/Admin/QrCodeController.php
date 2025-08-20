<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQrCodeRequest;
use App\Models\QrCode;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class QrCodeController extends Controller
{
    /**
     * Display a listing of QR codes.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $officeLocationId = $request->get('office_location_id');

        $qrCodes = QrCode::query()
            ->with('officeLocation')
            ->when($search, function ($query, $search) {
                return $query->where('code', 'like', "%{$search}%")
                    ->orWhereHas('officeLocation', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($status === 'active', function ($query) {
                return $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                return $query->where('is_active', false);
            })
            ->when($status === 'expired', function ($query) {
                return $query->where('expires_at', '<', now());
            })
            ->when($officeLocationId, function ($query, $officeLocationId) {
                return $query->where('office_location_id', $officeLocationId);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $officeLocations = OfficeLocation::active()->get();

        return Inertia::render('admin/qr-codes/index', [
            'qrCodes' => $qrCodes,
            'officeLocations' => $officeLocations,
            'filters' => $request->only(['search', 'status', 'office_location_id']),
        ]);
    }

    /**
     * Show the form for creating a new QR code.
     */
    public function create()
    {
        $officeLocations = OfficeLocation::active()->get();
        
        return Inertia::render('admin/qr-codes/create', [
            'officeLocations' => $officeLocations,
        ]);
    }

    /**
     * Store a newly created QR code.
     */
    public function store(StoreQrCodeRequest $request)
    {
        $validated = $request->validated();

        $qrCode = QrCode::create([
            'code' => QrCode::generateCode(),
            'office_location_id' => $validated['office_location_id'],
            'expires_at' => Carbon::parse($validated['expires_at']),
            'is_active' => true,
        ]);

        return redirect()->route('admin.qr-codes.show', $qrCode)
            ->with('success', 'QR Code generated successfully.');
    }

    /**
     * Display the specified QR code.
     */
    public function show(QrCode $qrCode)
    {
        $qrCode->load(['officeLocation', 'attendances' => function ($query) {
            $query->with('employee')->latest()->take(10);
        }]);

        // Generate QR code URL for display
        $qrCodeUrl = route('attendance.index') . '?code=' . $qrCode->code;

        return Inertia::render('admin/qr-codes/show', [
            'qrCode' => $qrCode,
            'qrCodeUrl' => $qrCodeUrl,
            'isExpired' => $qrCode->expires_at->isPast(),
            'isValid' => $qrCode->isValid(),
        ]);
    }

    /**
     * Update the specified QR code status.
     */
    public function update(Request $request, QrCode $qrCode)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $qrCode->update([
            'is_active' => $request->is_active,
        ]);

        $status = $request->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.qr-codes.show', $qrCode)
            ->with('success', "QR Code {$status} successfully.");
    }

    /**
     * Remove the specified QR code.
     */
    public function destroy(QrCode $qrCode)
    {
        $qrCode->delete();
        
        return redirect()->route('admin.qr-codes.index')
            ->with('success', 'QR Code deleted successfully.');
    }


}