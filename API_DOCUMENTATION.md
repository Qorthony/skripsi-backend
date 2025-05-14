# API Documentation

## Daftar Isi
1. [Autentikasi](#autentikasi)
2. [User](#user)
3. [Events](#events)
4. [Transactions](#transactions)
5. [Ticket Issued](#ticket-issued)
6. [Resales](#resales)
7. [Checkins](#checkins)
8. [Midtrans Integration](#midtrans-integration)

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
    "message": "OTP code has been sent to your email"
  }
  ```
- **Response Error (404)**:
  ```json
  {
    "status": "error",
    "message": "Email not found or already verified"
  }
  ```

### Verifikasi OTP Registrasi
Endpoint untuk memverifikasi kode OTP yang dikirimkan saat registrasi.

- **URL**: `/api/register/verifyOtp`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "email": "string, required, email format",
    "otp_code": "string, required"
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "OTP code is valid",
    "user": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "email_verified_at": "timestamp",
      "role": "participant",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    },
    "token": "string"
  }
  ```
- **Response Error (401)**:
  ```json
  {
    "status": "error",
    "message": "Invalid OTP code"
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
    "message": "OTP code has been sent to your email",
    "user": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "email_verified_at": "timestamp",
      "role": "participant atau organizer",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```
- **Response Error (401)**:
  ```json
  {
    "status": "error",
    "message": "Unauthorized"
  }
  ```
  atau
  ```json
  {
    "status": "error",
    "message": "Unauthorized role"
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
    "message": "OTP code has been sent to your email"
  }
  ```
- **Response Error (404)**:
  ```json
  {
    "status": "error",
    "message": "Email not found"
  }
  ```

### Verifikasi OTP Login
Endpoint untuk memverifikasi kode OTP yang dikirimkan saat login.

- **URL**: `/api/login/verifyOtp`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**:
  ```json
  {
    "email": "string, required, email format",
    "otp_code": "string, required"
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "OTP code is valid",
    "user": {
      "id": "uuid",
      "name": "string",
      "email": "string",
      "email_verified_at": "timestamp",
      "role": "participant atau organizer",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    },
    "token": "string"
  }
  ```
- **Response Error (401)**:
  ```json
  {
    "status": "error",
    "message": "Invalid OTP code"
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
    "message": "User logged out successfully"
  }
  ```

## User

### Get User Data
Endpoint untuk mendapatkan data pengguna yang sedang login.

- **URL**: `/api/user`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response Success (200)**:
  ```json
  {
    "id": "uuid",
    "name": "string",
    "email": "string",
    "email_verified_at": "timestamp",
    "role": "participant atau organizer",
    "created_at": "timestamp",
    "updated_at": "timestamp"
  }
  ```

## Events

### List Events
Endpoint untuk mendapatkan daftar event.

- **URL**: `/api/events`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Event list",
    "data": [
      {
        "id": "uuid",
        "nama": "string",
        "lokasi": "string",
        "jadwal_mulai": "timestamp",
        "jadwal_selesai": "timestamp",
        "status": "string",
        "tickets": [...]
      }
    ]
  }
  ```

### Show Event Detail
Endpoint untuk mendapatkan detail event.

- **URL**: `/api/events/{id}`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID event
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Event detail",
    "data": {
      "id": "uuid",
      "nama": "string",
      "lokasi": "string",
      "jadwal_mulai": "timestamp",
      "jadwal_selesai": "timestamp",
      "status": "string",
      "tickets": [...]
    }
  }
  ```

## Transactions

### List Transactions
Endpoint untuk mendapatkan daftar transaksi.

- **URL**: `/api/transactions`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Query Parameters**:
  - `event_id` - (optional/required untuk organizer) ID event
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "List of transactions",
    "data": [
      {
        "id": "uuid",
        "user_id": "uuid",
        "event_id": "uuid",
        "metode_pembayaran": "string",
        "total_harga": "number",
        "status": "string",
        "created_at": "timestamp",
        "updated_at": "timestamp",
        "event": {...},
        "transactionItems": [...]
      }
    ]
  }
  ```

### Create Transaction
Endpoint untuk membuat transaksi baru.

- **URL**: `/api/transactions`
- **Metode**: `POST`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Body Parameters (Primary Ticket)**:
  ```json
  {
    "event_id": "uuid, required",
    "ticket_items": [
      {
        "ticket_id": "uuid, required",
        "jumlah": "number, required"
      }
    ]
  }
  ```
- **Body Parameters (Secondary Ticket)**:
  ```json
  {
    "ticket_source": "secondary",
    "resale_id": "uuid, required"
  }
  ```
- **Response Success (201)**:
  ```json
  {
    "status": "success",
    "message": "Transaction created",
    "data": {
      "id": "uuid",
      "user_id": "uuid",
      "event_id": "uuid",
      "metode_pembayaran": null,
      "total_harga": "number",
      "status": "pending",
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "transactionItems": [...],
      "event": {...}
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
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Transaction detail",
    "data": {
      "id": "uuid",
      "user_id": "uuid",
      "event_id": "uuid",
      "metode_pembayaran": "string",
      "total_harga": "number",
      "status": "string",
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "event": {...},
      "transactionItems": [...]
    }
  }
  ```

### Update Transaction
Endpoint untuk mengupdate transaksi (melakukan pembayaran).

- **URL**: `/api/transactions/{id}`
- **Metode**: `PUT`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID transaksi
- **Body Parameters**:
  ```json
  {
    "metode_pembayaran": "string, required for paid transactions",
    "ticket_issueds": [
      {
        "id": "uuid",
        "email_penerima": "string, email format"
      }
    ]
  }
  ```
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Transaction updated",
    "data": {
      "id": "uuid",
      "user_id": "uuid",
      "event_id": "uuid",
      "metode_pembayaran": "string",
      "total_harga": "number",
      "status": "payment",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```

### Mark Transaction as Expired
Endpoint untuk menandai transaksi sebagai expired.

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
    "data": {
      "id": "uuid",
      "user_id": "uuid",
      "event_id": "uuid",
      "status": "failed",
      "batas_waktu": "timestamp",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
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
Endpoint untuk mengupdate tiket yang diterbitkan.

- **URL**: `/api/ticket-issued/{id}`
- **Metode**: `PUT` or `PATCH`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID tiket
- **Body Parameters**: Parameter update tiket
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
      "kode_tiket": "uuid",
      "status": "active",
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "transaction_item": {...},
      "resale": {...},
      "checkins": [...],
      "user": {...}
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
Endpoint untuk mendapatkan daftar tiket resale untuk suatu event.

- **URL**: `/api/events/{event_id}/resales`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `event_id` - ID event
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "List of resale tickets",
    "data": [
      {
        "id": "uuid",
        "ticket_issued_id": "uuid",
        "harga": "number",
        "status": "available",
        "created_at": "timestamp",
        "updated_at": "timestamp",
        "ticket_issued": {
          "id": "uuid",
          "transaction_item_id": "uuid",
          "kode_tiket": "uuid",
          "status": "resale",
          "created_at": "timestamp",
          "updated_at": "timestamp",
          "transaction_item": {...}
        }
      }
    ]
  }
  ```

### Show Resale Detail
Endpoint untuk mendapatkan detail tiket resale.

- **URL**: `/api/events/{event_id}/resales/{id}`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: 
  - `event_id` - ID event
  - `id` - ID resale
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Resale ticket detail",
    "data": {
      "id": "uuid",
      "ticket_issued_id": "uuid",
      "harga": "number",
      "status": "available",
      "created_at": "timestamp",
      "updated_at": "timestamp",
      "ticket_issued": {
        "id": "uuid",
        "transaction_item_id": "uuid",
        "kode_tiket": "uuid",
        "status": "resale",
        "created_at": "timestamp",
        "updated_at": "timestamp",
        "transaction_item": {...}
      }
    }
  }
  ```

### Cancel Resale Ticket
Endpoint untuk membatalkan tiket yang dijual kembali.

- **URL**: `/api/events/{event_id}/resales/{id}/cancel`
- **Metode**: `PATCH`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: 
  - `event_id` - ID event
  - `id` - ID resale
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Resale ticket cancelled successfully",
    "data": {
      "id": "uuid",
      "ticket_issued_id": "uuid",
      "status": "cancelled",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```
- **Response Error (403)**:
  ```json
  {
    "status": "error",
    "message": "You are not authorized to cancel this resale ticket"
  }
  ```
- **Response Error (400)**:
  ```json
  {
    "status": "error", 
    "message": "This resale ticket cannot be cancelled"
  }
  ```

### Update Resale
Endpoint untuk mengupdate tiket resale.

- **URL**: `/api/events/{event_id}/resales/{id}`
- **Metode**: `PUT` or `PATCH`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: 
  - `event_id` - ID event
  - `id` - ID resale
- **Body Parameters**: Parameter update resale
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Resale ticket updated",
    "data": {
      "id": "uuid",
      "ticket_issued_id": "uuid",
      "harga": "number",
      "status": "string",
      "created_at": "timestamp",
      "updated_at": "timestamp"
    }
  }
  ```

### Delete Resale
Endpoint untuk menghapus tiket resale.

- **URL**: `/api/events/{event_id}/resales/{id}`
- **Metode**: `DELETE`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: 
  - `event_id` - ID event
  - `id` - ID resale
- **Response Success (200)**:
  ```json
  {
    "status": "success",
    "message": "Resale ticket deleted successfully" 
  }
  ```

## Checkins

### List Checkins
Endpoint untuk mendapatkan daftar checkin.

- **URL**: `/api/checkins`
- **Metode**: `GET`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **Response**: Daftar checkin yang terkait dengan pengguna

### Update Checkin
Endpoint untuk mengupdate status checkin.

- **URL**: `/api/checkins/{id}`
- **Metode**: `PUT`
- **Auth Required**: Ya
- **Headers**: `Authorization: Bearer {token}`
- **URL Parameters**: `id` - ID checkin
- **Body Parameters**: Parameter update checkin
- **Response**: Status checkin yang diupdate

## Midtrans Integration

### Handle Notifikasi Pembayaran
Endpoint untuk menerima notifikasi dari Midtrans.

- **URL**: `/api/midtrans/notification`
- **Metode**: `POST`
- **Auth Required**: Tidak
- **Body Parameters**: Format sesuai dengan notifikasi Midtrans
- **Response**: Status pemrosesan notifikasi

### Get Transaction Information
Endpoint untuk mendapatkan informasi transaksi dari Midtrans.

- **URL**: `/api/midtrans/transaction/{orderId}`
- **Metode**: `GET`
- **Auth Required**: Tidak
- **URL Parameters**: `orderId` - ID order transaksi
- **Response**: Data transaksi dari Midtrans