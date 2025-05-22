# API Documentation

## Daftar Isi
1. [Autentikasi](#autentikasi)
2. [User](#user)
3. [Events](#events)
4. [Transactions](#transactions)
5. [Ticket Issued](#ticket-issued)
6. [Resales](#resales)
7. [Organizer](#organizer)
8. [Midtrans Integration](#midtrans-integration)

## Endpoint List

### Public Endpoints
- `GET /api` — Welcome message
- `POST /api/register` — Register user
- `POST /api/register/resendOtp` — Resend OTP for registration
- `POST /api/register/verifyOtp` — Verify OTP for registration
- `POST /api/login` — Login user
- `POST /api/login/resendOtp` — Resend OTP for login
- `POST /api/login/verifyOtp` — Verify OTP for login
- `POST /api/midtrans/notification` — Midtrans payment notification
- `GET /api/midtrans/transaction/{orderId}` — Get transaction status from Midtrans

### Protected Endpoints (auth:sanctum)
- `POST /api/logout` — Logout user
- `GET /api/user` — Get current user profile

#### Events
- `GET /api/events` — List events
- `GET /api/events/{id}` — Event detail

#### Transactions
- `GET /api/transactions` — List transactions
- `POST /api/transactions` — Create transaction
- `GET /api/transactions/{id}` — Transaction detail
- `PUT|PATCH /api/transactions/{id}` — Update transaction
- `PATCH /api/transactions/{id}/expired` — Set transaction as expired

#### Ticket Issued
- `GET /api/ticket-issued` — List ticket issued (user)
- `GET /api/ticket-issued/{id}` — Ticket issued detail (user)
- `PUT|PATCH /api/ticket-issued/{id}` — Update ticket issued (activate/resale)
- `GET /api/ticket-issued/{id}/checkin` — Show ticket issued info (with kode_tiket, only if status active)

#### Resales
- `GET /api/resales` — List resales
- `GET /api/resales/{id}` — Resale detail
- `PUT|PATCH /api/resales/{id}` — Update resale

#### Organizer (prefix `/organizer`)
- `GET /api/organizer/events` — List events by organizer
- `GET /api/organizer/events/{event}` — Event detail (organizer)
- `GET /api/organizer/events/{event}/transactions` — List transactions for event (organizer)
- `GET /api/organizer/events/{event}/participants` — List participants for event (organizer)
- `POST /api/organizer/events/{event}/checkin` — Checkin participant (organizer)

## Autentikasi

### Register
Endpoint untuk mendaftarkan pengguna baru.

- **URL**: `/api/register`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "name": "string, required",
    "email": "string, required, email format"
  }
  ```
- **Response Success (201)**:
  ```json
  {
    "status": "success",
    "message": "OTP code has been sent to your email",
    "user": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "role": "participant",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```
- **Response Error (409)**:
  ```json
  {
    "status": "error",
    "message": "Email already registered"
  }
  ```

### Resend OTP for Register
Endpoint untuk mengirim ulang kode OTP saat registrasi.

- **URL**: `/api/register/resendOtp`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "email": "string, required, email format"
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "OTP code has been resent to your email"
  }
  ```

### Verify OTP for Register
Endpoint untuk memverifikasi kode OTP saat registrasi.

- **URL**: `/api/register/verifyOtp`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "email": "string, required, email format",
    "otp": "string, required, 6 digits"
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "OTP verified successfully",
    "token": "string, bearer token",
    "user": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "role": "participant",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```

### Login
Endpoint untuk login pengguna.

- **URL**: `/api/login`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "email": "string, required, email format"
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "OTP code has been sent to your email"
  }
  ```

### Resend OTP for Login
Endpoint untuk mengirim ulang kode OTP saat login.

- **URL**: `/api/login/resendOtp`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "email": "string, required, email format"
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "OTP code has been resent to your email"
  }
  ```

### Verify OTP for Login
Endpoint untuk memverifikasi kode OTP saat login.

- **URL**: `/api/login/verifyOtp`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "email": "string, required, email format",
    "otp": "string, required, 6 digits"
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "OTP verified successfully",
    "token": "string, bearer token",
    "user": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "role": "string", // "participant", "organizer", "admin"
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "organizer": { // hanya jika role = "organizer"
        "id": "uuid",
        "user_id": "uuid",
        "nama": "string",
        "deskripsi": "string",
        "logo": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
      }
    }
  }
  ```

### Logout
Endpoint untuk logout pengguna.

- **URL**: `/api/logout`
- **Metode**: `POST`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Logged out successfully"
  }
  ```

## User

### Get Current User
Endpoint untuk mendapatkan data pengguna yang sedang login.

- **URL**: `/api/user`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response**: Data pengguna yang sedang login

## Events

### List Events
Endpoint untuk mendapatkan daftar event.

- **URL**: `/api/events`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `search` - Pencarian berdasarkan nama event
  - `category` - Filter berdasarkan kategori
  - `status` - Filter berdasarkan status (draft, published, canceled)
  - `location` - Filter berdasarkan lokasi
- **Response**: Daftar event

### Show Event Detail
Endpoint untuk mendapatkan detail event.

- **URL**: `/api/events/{id}`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID event
- **Response**: Detail event

## Transactions

### List Transactions
Endpoint untuk mendapatkan daftar transaksi pengguna.

- **URL**: `/api/transactions`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response**: Daftar transaksi pengguna yang login

### Create Transaction
Endpoint untuk membuat transaksi baru.

- **URL**: `/api/transactions`
- **Metode**: `POST`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Body Parameters**:
  ```json
  {
    "items": [
      {
        "ticket_id": "uuid",
        "quantity": "integer"
      }
    ]
  }
  ```
- **Response Success (201)**:
  ```json
  {
    "status": "success",
    "message": "Transaction created successfully",
    "data": {
      "id": "uuid",
      "user_id": "uuid",
      "status": "pending",
      "jumlah_bayar": "number",
      "kode_unik": "integer",
      "waktu_kedaluwarsa": "datetime",
      "snap_token": "string",
      "snap_url": "string",
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "items": [...]
    }
  }
  ```

### Show Transaction Detail
Endpoint untuk mendapatkan detail transaksi.

- **URL**: `/api/transactions/{id}`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID transaksi
- **Response**: Detail transaksi

### Update Transaction
Endpoint untuk mengupdate status transaksi.

- **URL**: `/api/transactions/{id}`
- **Metode**: `PUT` or `PATCH`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID transaksi
- **Body Parameters**:
  ```json
  {
    "status": "string"
  }
  ```
- **Response**: Transaksi yang diupdate

### Mark Transaction as Expired
Endpoint untuk menandai transaksi sebagai kedaluwarsa.

- **URL**: `/api/transactions/{id}/expired`
- **Metode**: `PATCH`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID transaksi
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Transaction marked as expired",
    "data": {...}
  }
  ```
- **Response Error (400)**:
  ```json
  {
    "status": "error",
    "message": "Transaction cannot be expired because it is already completed"
  }
  ```
  atau
  ```json
  {
    "status": "error",
    "message": "Transaction has not yet expired"
  }
  ```

## Ticket Issued

### List Ticket Issued
Endpoint untuk mendapatkan daftar tiket yang diterbitkan.

- **URL**: `/api/ticket-issued`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response**: Daftar tiket yang diterbitkan untuk pengguna yang login

### Show Ticket Issued Detail
Endpoint untuk mendapatkan detail tiket yang diterbitkan.

- **URL**: `/api/ticket-issued/{id}`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID tiket
- **Response**: Detail tiket yang diterbitkan

### Update Ticket Issued
Endpoint untuk mengupdate tiket yang diterbitkan (aktivasi atau resale).

- **URL**: `/api/ticket-issued/{id}`
- **Metode**: `PUT` or `PATCH`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID tiket
- **Body Parameters**: 
  - Untuk aktivasi:
    ```json
    {}
    ```
  - Untuk resale:
    ```json
    {
      "action": "resale",
      "harga_jual": "number"
    }
    ```
- **Response**: Tiket yang diupdate

### Check Ticket Issued (Show Info + kode_tiket)
Endpoint untuk menampilkan info ticket issued beserta kode_tiket berdasarkan id. Hanya ticket issued dengan status "active" yang dapat diakses.

- **URL**: `/api/ticket-issued/{id}/checkin`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID ticket issued
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Ticket issued found",
    "data": {
      "id": "uuid",
      "transaction_item_id": "uuid",
      "kode_tiket": "uuid", // kode tiket yang biasanya hidden
      "status": "active",
      "email": "string",
      "waktu_penerbitan": "timestamp",
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "transaction_item": {
        "id": "uuid",
        "transaction_id": "uuid",
        "ticket_id": "uuid",
        "quantity": "integer",
        "created_at": "timestamp",
        "updated_at": "timestamp",
        "transaction": {
          "id": "uuid",
          "user_id": "uuid",
          "event_id": "uuid",
          "status": "string",
          "jumlah_bayar": "number",
          "created_at": "timestamp",
          "updated_at": "timestamp",
          "event": {
            "id": "uuid",
            "organizer_id": "uuid",
            "nama": "string",
            "deskripsi": "string",
            "lokasi": "string",
            "jadwal_mulai": "datetime",
            "jadwal_selesai": "datetime",
            "created_at": "timestamp",
            "updated_at": "timestamp"
          }
        }
      },
      "resale": {
        "id": "uuid",
        "ticket_issued_id": "uuid",
        "harga_jual": "number",
        "status": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
      },
      "checkins": [
        {
          "id": "uuid",
          "ticket_issued_id": "uuid",
          "user_id": "uuid",
          "checked_in_at": "timestamp",
          "checked_out_at": "timestamp|null",
          "created_at": "timestamp",
          "updated_at": "timestamp"
        }
      ],
      "user": {
        "id": "uuid",
        "name": "string",
        "email": "string",
        "role": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp"
      }
    }
  }
  ```
- **Response Error (404)**:
  ```json
  {
    "status": "error",
    "message": "Ticket issued tidak ditemukan"
  }
  ```
- **Response Error (403)**:
  ```json
  {
    "status": "error",
    "message": "Ticket issued tidak aktif"
  }
  ```

## Resales

### List Resales
Endpoint untuk mendapatkan daftar tiket resale.

- **URL**: `/api/resales`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response**: Daftar tiket resale

### Show Resale Detail
Endpoint untuk mendapatkan detail tiket resale.

- **URL**: `/api/resales/{id}`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID resale
- **Response**: Detail tiket resale

### Update Resale
Endpoint untuk mengupdate tiket resale.

- **URL**: `/api/resales/{id}`
- **Metode**: `PUT` or `PATCH`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID resale
- **Body Parameters**: Parameter update resale
- **Response**: Tiket resale yang diupdate

## Organizer

### List Organizer Events
Endpoint untuk mendapatkan daftar event yang dibuat oleh organizer.

- **URL**: `/api/organizer/events`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `ongoing`: Jika parameter ini ada, hanya menampilkan event yang belum dimulai dan diurutkan berdasarkan jadwal_mulai (asc)
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "data": [
      {
        "id": "uuid",
        "organizer_id": "uuid", 
        "nama": "string",
        "deskripsi": "string",
        "lokasi": "string",
        "jadwal_mulai": "datetime",
        "jadwal_selesai": "datetime",
        "status": "string", // "draft", "published", "canceled"
        "created_at": "timestamp",
        "updated_at": "timestamp",
        "tickets": [
          {
            "id": "uuid",
            "event_id": "uuid",
            "nama": "string",
            "deskripsi": "string",
            "harga": "number",
            "kuota": "integer",
            "created_at": "timestamp",
            "updated_at": "timestamp"
          }
        ]
      }
    ]
  }
  ```

### Show Organizer Event Detail
Endpoint untuk mendapatkan detail event yang dibuat oleh organizer.

- **URL**: `/api/organizer/events/{event}`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `event` - ID event
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "data": {
      "id": "uuid",
      "organizer_id": "uuid",
      "nama": "string",
      "deskripsi": "string",
      "lokasi": "string",
      "jadwal_mulai": "datetime",
      "jadwal_selesai": "datetime",
      "status": "string", // "draft", "published", "canceled"
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "tickets": [
        {
          "id": "uuid",
          "event_id": "uuid",
          "nama": "string",
          "deskripsi": "string",
          "harga": "number",
          "kuota": "integer",
          "created_at": "timestamp",
          "updated_at": "timestamp"
        }
      ]
    }
  }
  ```
- **Response Error (403)**:
  ```json
  {
    "status": "error",
    "message": "Unauthorized" // Jika bukan organizer event ini
  }
  ```

### List Event Transactions
Endpoint untuk mendapatkan daftar transaksi untuk event tertentu.

- **URL**: `/api/organizer/events/{event}/transactions`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `event` - ID event
- **Query Parameters**:
  - `status` - Filter berdasarkan status transaksi (pending, success, failed, expired)
  - `order` - Urutan berdasarkan created_at (asc, desc). Default: desc
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "data": {
      "event": {
        "id": "uuid",
        "organizer_id": "uuid",
        "nama": "string",
        "poster": "string|null",
        "lokasi": "string",
        "kota": "string",
        "alamat_lengkap": "string",
        "jadwal_mulai": "datetime",
        "jadwal_selesai": "datetime",
        "deskripsi": "string",
        "status": "string",
        "alasan_penolakan": "string|null",
        "created_at": "timestamp",
        "updated_at": "timestamp",
        "deleted_at": "timestamp|null"
      },
      "stats": {
        "total_penghasilan": "number|string",
        "total_tiket_terjual": "integer"
      },
      "transactions": [
        {
          "id": "uuid",
          "user_id": "uuid",
          "event_id": "uuid",
          "jumlah_tiket": "integer",
          "total_harga": "number",
          "batas_waktu": "timestamp",
          "status": "string",
          "metode_pembayaran": "string|null",
          "kode_pembayaran": "string|null",
          "detail_pembayaran": {
            "bank": "string",
            "va_number": "string"
          },
          "waktu_pembayaran": "timestamp|null",
          "biaya_pembayaran": "number|null",
          "total_pembayaran": "number|null",
          "created_at": "timestamp",
          "updated_at": "timestamp",
          "resale_id": "uuid|null",
          "user": {
            "id": "uuid",
            "name": "string",
            "email": "string",
            "email_verified_at": "timestamp|null",
            "role": "string",
            "created_at": "timestamp",
            "updated_at": "timestamp"
          },
          "event": {
            "id": "uuid",
            "organizer_id": "uuid",
            "nama": "string",
            "poster": "string|null",
            "lokasi": "string",
            "kota": "string",
            "alamat_lengkap": "string",
            "jadwal_mulai": "datetime",
            "jadwal_selesai": "datetime",
            "deskripsi": "string",
            "status": "string",
            "alasan_penolakan": "string|null",
            "created_at": "timestamp",
            "updated_at": "timestamp",
            "deleted_at": "timestamp|null"
          },
          "transaction_items": [
            {
              "id": "uuid",
              "transaction_id": "uuid",
              "ticket_id": "uuid",
              "nama": "string",
              "deskripsi": "string|null",
              "harga_satuan": "number",
              "jumlah": "integer",
              "total_harga": "number",
              "created_at": "timestamp",
              "updated_at": "timestamp",
              "ticket": {
                "id": "uuid",
                "event_id": "uuid",
                "nama": "string",
                "harga": "number",
                "kuota": "integer",
                "waktu_buka": "datetime|null",
                "waktu_tutup": "datetime|null",
                "keterangan": "string|null",
                "created_at": "timestamp",
                "updated_at": "timestamp",
                "deleted_at": "timestamp|null"
              },
              "ticket_issueds": [
                {
                  "id": "uuid",
                  "user_id": "uuid|null",
                  "transaction_item_id": "uuid",
                  "email_penerima": "string|null",
                  "waktu_penerbitan": "timestamp|null",
                  "status": "string",
                  "created_at": "timestamp",
                  "updated_at": "timestamp"
                }
              ]
            }
          ]
        }
      ]
    }
  }
  ```
- **Response Error (403)**:
  ```json
  {
    "status": "error",
    "message": "Unauthorized" // Jika bukan organizer event ini
  }
  ```

### List Event Participants
Endpoint untuk mendapatkan daftar peserta untuk event tertentu.

- **URL**: `/api/organizer/events/{event}/participants`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `event` - ID event
- **Query Parameters**: 
  - `search` - Pencarian berdasarkan nama user (jika tersedia), email tiket issued
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "data": {
      "event": {
        "id": "uuid",
        "organizer_id": "uuid",
        "nama": "string",
        "poster": "string|null",
        "lokasi": "string",
        "kota": "string",
        "alamat_lengkap": "string",
        "jadwal_mulai": "datetime",
        "jadwal_selesai": "datetime",
        "deskripsi": "string",
        "status": "string",
        "alasan_penolakan": "string|null",
        "created_at": "timestamp",
        "updated_at": "timestamp",
        "deleted_at": "timestamp|null"
      },
      "stats": {
        "total_peserta": "integer",
        "total_checkin": "integer"
      },
      "participants": [
        {
          "id": "uuid",
          "user_id": "uuid",
          "transaction_item_id": "uuid",
          "email_penerima": "string",
          "waktu_penerbitan": "timestamp|null",
          "status": "string",
          "created_at": "timestamp",
          "updated_at": "timestamp",
          "transaction_item": {
            "id": "uuid",
            "transaction_id": "uuid",
            "ticket_id": "uuid",
            "nama": "string",
            "deskripsi": "string|null",
            "harga_satuan": "number",
            "jumlah": "integer",
            "total_harga": "number",
            "created_at": "timestamp",
            "updated_at": "timestamp",
            "transaction": {
              "id": "uuid",
              "user_id": "uuid",
              "event_id": "uuid",
              "jumlah_tiket": "integer",
              "total_harga": "number",
              "batas_waktu": "timestamp",
              "status": "string",
              "metode_pembayaran": "string|null",
              "kode_pembayaran": "string|null",
              "detail_pembayaran": {
                "bank": "string",
                "va_number": "string"
              },
              "waktu_pembayaran": "timestamp|null",
              "biaya_pembayaran": "number|null",
              "total_pembayaran": "number|null",
              "created_at": "timestamp",
              "updated_at": "timestamp",
              "resale_id": "uuid|null",
              "user": {
                "id": "uuid",
                "name": "string",
                "email": "string",
                "email_verified_at": "timestamp|null",
                "role": "string",
                "created_at": "timestamp",
                "updated_at": "timestamp"
              }
            },
            "ticket": {
              "id": "uuid",
              "event_id": "uuid",
              "nama": "string",
              "harga": "number",
              "kuota": "integer",
              "waktu_buka": "datetime|null",
              "waktu_tutup": "datetime|null",
              "keterangan": "string|null",
              "created_at": "timestamp",
              "updated_at": "timestamp",
              "deleted_at": "timestamp|null"
            }
          },
          "checkins": [
            {
              "id": "uuid",
              "ticket_issued_id": "uuid",
              "user_id": "uuid",
              "checked_in_at": "timestamp",
              "checked_out_at": "timestamp|null",
              "created_at": "timestamp",
              "updated_at": "timestamp"
            }
          ],
          "user": {
            "id": "uuid",
            "name": "string",
            "email": "string",
            "email_verified_at": "timestamp|null",
            "role": "string",
            "created_at": "timestamp",
            "updated_at": "timestamp"
          }
        }
      ]
    }
  }
  ```
- **Response Error (403)**:
  ```json
  {
    "status": "error",
    "message": "Unauthorized" // Jika bukan organizer event ini
  }
  ```

### Checkin Participant
Endpoint untuk melakukan checkin peserta event.

- **URL**: `/api/organizer/events/{event}/checkin`
- **Metode**: `POST`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `event` - ID event
- **Body Parameters**:
  ```json
  {
    "kode_tiket": "string" // kode tiket yang akan di-checkin
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Checkin berhasil"
  }
  ```
- **Response Error (404)**:
  ```json
  {
    "status": "error",
    "message": "Kode tiket tidak ditemukan"
  }
  ```
- **Response Error (400)**:
  ```json
  {
    "status": "error",
    "message": "Ticket not valid"
  }
  ```
- **Response Error (400)**:
  ```json
  {
    "status": "error",
    "message": "Ticket already checkin"
  }
  ```
- **Response Error (403)**:
  ```json
  {
    "status": "error",
    "message": "Unauthorized" // Jika bukan organizer event ini
  }
  ```
- **Response Error (500)**:
  ```json
  {
    "status": "error",
    "message": "Checkin gagal",
    "error": "error message"
  }
  ```

## Midtrans Integration

### Midtrans Payment Notification
Endpoint untuk menerima notifikasi pembayaran dari Midtrans.

- **URL**: `/api/midtrans/notification`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**: Parameter notifikasi dari Midtrans
- **Response**: Status notifikasi

### Get Midtrans Transaction
Endpoint untuk mendapatkan status transaksi dari Midtrans.

- **URL**: `/api/midtrans/transaction/{orderId}`
- **Metode**: `GET`
- **Auth Required**: Tidak
- **URL Parameters**: `orderId` - ID order
- **Response**: Status transaksi dari Midtrans
