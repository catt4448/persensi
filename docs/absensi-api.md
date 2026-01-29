# API Absensi (Arduino/ESP32)

## Base URL
`http://<HOST>/api`

## Endpoint
### POST `/absensi/scan`
Melakukan absensi berdasarkan UID kartu RFID.

**Request Body (JSON)**
```json
{
  "uid": "04A1B2C3D4",
  "device_id": "ESP32-01",
  "token": "DEVICE_TOKEN"
}
```

**Response 200**
```json
{
  "status": "success",
  "message": "Absensi berhasil",
  "data": {
    "mahasiswa": {
      "id": 12,
      "nim": "2023001",
      "nama": "Budi",
      "kelas": "AA"
    },
    "sesi": {
      "id": 7,
      "nama_sesi": "Presensi IoT",
      "kelas": "AA",
      "tanggal": "2026-01-23",
      "jam_mulai": "08:00",
      "jam_selesai": "09:40"
    },
    "kehadiran": {
      "status": "hadir",
      "waktu_hadir": "2026-01-23 08:15:10"
    }
  }
}
```

**Response Error (contoh)**
```json
{
  "status": "error",
  "message": "Kartu tidak terdaftar"
}
```

**Response Error (device)**
```json
{
  "status": "error",
  "message": "Device tidak terdaftar atau token salah"
}
```

## Catatan Perilaku
- UID dinormalisasi ke format heksadesimal uppercase.
- Device wajib terdaftar dan token harus sesuai (status aktif).
- Sesi yang dianggap aktif: `status = aktif`, `tanggal = hari ini`, dan `jam_mulai <= sekarang <= jam_selesai`.
- Jika sudah absen (status `hadir/terlambat`), API mengembalikan error 409.

## Contoh cURL
```bash
curl -X POST http://localhost:8000/api/absensi/scan \
  -H "Content-Type: application/json" \
  -d '{"uid":"04A1B2C3D4","device_id":"ESP32-01","token":"DEVICE_TOKEN"}'
```

