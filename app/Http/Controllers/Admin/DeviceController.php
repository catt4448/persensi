<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $devices = $query->orderBy('device_id')->paginate(15)->withQueryString();

        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string|max:100|unique:devices,device_id',
            'name' => 'nullable|string|max:255',
            'token' => 'nullable|string|max:100|unique:devices,token',
            'is_active' => 'sometimes|boolean',
        ], [
            'device_id.required' => 'Device ID wajib diisi',
            'device_id.unique' => 'Device ID sudah digunakan',
            'token.unique' => 'Token sudah digunakan',
        ]);

        $deviceId = strtoupper($request->device_id);
        $token = $request->token ?: Str::random(40);

        Device::create([
            'device_id' => $deviceId,
            'name' => $request->name,
            'token' => $token,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.devices.index')
            ->with('success', 'Perangkat berhasil ditambahkan');
    }

    public function edit(Device $device)
    {
        return view('admin.devices.edit', compact('device'));
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'device_id' => 'required|string|max:100|unique:devices,device_id,' . $device->id,
            'name' => 'nullable|string|max:255',
            'token' => 'nullable|string|max:100|unique:devices,token,' . $device->id,
            'is_active' => 'sometimes|boolean',
        ], [
            'device_id.required' => 'Device ID wajib diisi',
            'device_id.unique' => 'Device ID sudah digunakan',
            'token.unique' => 'Token sudah digunakan',
        ]);

        $device->device_id = strtoupper($request->device_id);
        $device->name = $request->name;
        $device->is_active = $request->boolean('is_active', false);

        if ($request->filled('token')) {
            $device->token = $request->token;
        }

        $device->save();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Perangkat berhasil diperbarui');
    }

    public function destroy(Device $device)
    {
        try {
            $device->delete();
            return back()->with('success', 'Perangkat berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus perangkat: ' . $e->getMessage());
        }
    }
}
