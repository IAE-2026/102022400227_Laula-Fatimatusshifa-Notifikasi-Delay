# README.md

````markdown
# Notification Delay Service

Service ini merupakan bagian dari proyek Service-Based Architecture untuk mata kuliah Integration of Enterprise Applications (IAE).

## Informasi Service

- **Nama Service:** Notification Delay Service
- **Repository:** 102022400227_Laula-Notification-Delay
- **NIM:** 102022400227
- **Mahasiswa:** Laula Fatimatusshifa
- **Framework:** Laravel 10
- **Database:** MySQL
- **Port Aplikasi:** 8027
- **Port MySQL:** 3307
- **Port phpMyAdmin:** 8087

## Deskripsi

Service ini bertanggung jawab untuk:

- Menyimpan notifikasi keterlambatan perjalanan.
- Mengirim notifikasi delay kepada penumpang.
- Menampilkan status perjalanan.
- Menampilkan detail notifikasi.

## Endpoint REST API

### GET /api/v1/trips/{id}/status
Mengambil status perjalanan.

### POST /api/v1/delay-notifikasi
Membuat notifikasi delay baru.

### POST /api/v1/delay-notifikasi/send
Mengirim notifikasi delay.

### GET /api/v1/delay-notifikasi/{id}
Menampilkan detail notifikasi.

## Security

Semua endpoint REST dilindungi dengan API Key melalui header:

```http
X-IAE-KEY: 102022400227
````

## Standard Integration Contract

### Success Response

```json
{
  "status": "success",
  "message": "Operation successful",
  "data": {},
  "meta": {
    "service_name": "Notification-Delay",
    "api_version": "v1"
  }
}
```

### Error Response

```json
{
  "status": "error",
  "message": "Unauthorized",
  "errors": null
}
```

## Swagger/OpenAPI

Dokumentasi API tersedia di:

```text
http://localhost:8027/api/documentation
```

## GraphQL

### Endpoint

```text
http://localhost:8027/graphql
```

### Contoh Query

```graphql
{
  tripStatus(id: 1) {
    trip_id
    status
  }
}
```

### Contoh Response

```json
{
  "data": {
    "tripStatus": {
      "trip_id": "1",
      "status": "delayed"
    }
  }
}
```

## Struktur Database

### Table: delay_notifications

| Field               | Type               |
| ------------------- | ------------------ |
| id                  | bigint             |
| trip_id             | string             |
| route_name          | string             |
| delay_minutes       | integer            |
| delay_reason        | text               |
| passenger_name      | string             |
| passenger_email     | string             |
| notification_status | string             |
| sent_at             | timestamp nullable |
| created_at          | timestamp          |
| updated_at          | timestamp          |

## Docker

### Menjalankan Project

```bash
docker compose up --build -d
```

### Menjalankan Migration

```bash
docker compose exec app php artisan migrate
```

### Menjalankan Test

```bash
docker compose exec app php artisan test
```

## Environment Variables

Salin file `.env.example` menjadi `.env`.

Variabel penting:

```env
APP_URL=http://localhost:8027
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=notification_delay_db
DB_USERNAME=root
DB_PASSWORD=root
IAE_API_KEY=102022400227
```

## Testing

Project ini memiliki automated tests untuk:

* API Key protection
* REST API success response
* GraphQL query
* Swagger accessibility

### Hasil Test

```text
Tests: 6 passed (9 assertions)
```

## Inter-Service Communication

Service ini dapat berkomunikasi dengan service lain melalui HTTP API, terutama untuk menerima informasi status perjalanan dari service Rute & Jadwal.

## Repository Contents

* Dockerfile
* docker-compose.yml
* README.md
* .env.example
* Swagger/OpenAPI
* GraphQL schema
* Database migration
* Automated tests
* AI_PROMPT_HISTORY.md

## Author

Laula Fatimatusshifa
102022400227

```
```
