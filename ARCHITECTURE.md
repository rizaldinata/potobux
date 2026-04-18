# Panduan Clean Architecture Aplikasi Potobux

Project **Potobux** menggunakan pendekatan **Clean Architecture** (berbasis *Separation of Concerns*) untuk menjaga agar kode lebih modular, mudah ditest (testable), mudah dikelola, dan tidak terlalu coupling (terikat) dengan framework Laravel secara langsung. 

Struktur folder utama di dalam direktori `app/` telah direstrukturisasi menjadi 4 lapis (Layer) berikut:

---

## 1. Presentation Layer (`app/Presentation/`)
Layer ini bertanggung jawab sebagai gerbang awal / jalur antarmuka dari luar aplikasi. Segala hal yang berurusan dengan protokol (seperti HTTP Request, Console Command, atau HTTP Response) berada di sini.
**Penting:** Layer ini **tidak boleh** memiliki logika bisnis secara langsung. Tugas utamanya hanyalah menerima input, memvalidasinya, melempar request ke Application Layer, dan mengembalikan response.
- `Http/Controllers`: Tempat menerima request dari user. Controller memanggil *UseCases* atau *Services* yang berada di lapisan Application. (Controller ini memiliki `ResponseTrait` sebagai standarisasi response).
- `Http/Requests`: Validasi Form (Request Validation).
- `Http/Resources`: Transformasi data output (API Resource) sebelum dikembalikan ke user.

## 2. Application Layer (`app/Application/`)
Lapisan Orkestrasi atau *"Application Business Rules"*. Di sinilah alur kerja aplikasi (Use Cases) didefinisikan. Layer ini berinteraksi dengan layer Interface/Repository (abstraksi) di Domain layer untuk mengambil atau menyimpan sebuah data tanpa perlu tahu bagaimana persisnya query ke database dieksekusi.
- `Services/` (atau `UseCases/`): Berisi modul logika aplikasi yang mengorkestrasi satu fitur bisnis dari awal hingga akhir. (Contoh: `CreateUserRegistrationService`).
- `DTOs/` (Data Transfer Objects): Objek sederhana berisi mapping data (biasanya immutable) yang bertugas mentransfer data atau Payload secara murni antar lapisan (biasanya Controller ke lapisan Services).

## 3. Domain Layer (`app/Domain/`)
Lapisan Core bisnis *"Enterprise Business Rules"*. Ini adalah layer paling tinggi dan tidak boleh bergantung (dependency) pada lapisan mana pun di atasnya (Presentation, Application, atau Infrastructure).
- `Models/`: Memuat Domain Entities atau Eloquent Models inti sistem (e.g. `User`).
- `Enums/`: Menyimpan deklarasi Enumeration statis yang mendeskripsikan tipe-tipe spesifik logic bisnis.
- `Interfaces/`: Menyediakan Contract untuk implementation. Application layer menggunakan Contracts ini. (Contoh: `BaseRepositoryInterface`).

## 4. Infrastructure Layer (`app/Infrastructure/`)
Layer terendah dan paling luar. Layer ini bertanggungjawab atas **implementasi teknikal** untuk berinteraksi dengan dunia luar atau eksternal framework (Database, Email, Third Party APIs, dsb).
- `Repositories/`: Tempat menaruh implementasi sesungguhnya dari `Interfaces` yang ada di ranah Domain. Semua logika yang menyentuh query database / Eloquent dieksekusi di sini. (Contoh: `BaseRepository` untuk reusabilitas standarisasi CRUD).
- `Providers/`: Berisi Laravel Service Providers, tempat di mana Dependency Injection (DI) dilakukan, yaitu me-mapping/binding Interface (Domain Layer) ke Repository (Infrastructure Layer).
- `ThirdParty/`: Berisi utilitas untuk pemanggilan SDK, API midtrans, atau layanan notifikasi eksternal.

---

## 🔁 Flow Eksekusi Standar (Garis Besar)

Jika ada sebuah request dari Endpoint (contoh: `POST /users`):
1. **Routing** mengarahkan request ke `App\Presentation\Http\Controllers\UserController@store`.
2. **Controller** meng-inject / menggunakan kelas `FormRequest` untuk memvalidasi body parameter yang masuk.
3. **Controller** lalu mengirim data tervalidasi ini (biasanya diubah menjadi **DTO**) ke Application Layer, misalnya ke `UserService` atau `CreateUserAction`.
4. **Service (Application Layer)** memiliki bisnis logic (contoh: mengecek jika email blacklisted, mengirimkan email welcome, dll). Untuk menyimpan User, Service akan memanggil `UserRepositoryInterface->create()`. *(Perhatikan: Service tidak memanggil Model ORM secara langsung melainkan melalui Interface)*.
5. **Repository (Infrastructure Layer)** menerima instruksi `create()`, dan melakukan eksekusi query *Eloquent ORM* ke Database. Model kemudian dikembalikan ke Service.
6. **Service** mengembalikan hasil pemrosesan (bisa berupa Entity Object atau DTO response) kembali ke **Controller**.
7. Terakhir, **Controller** membungkus datanya dalam **API Resource** lalu mengembalikan HTTP Response JSON (menggunakan fitur `ResponseTrait`).

---

## 🚫 Peraturan Aturan Main (Clean Architecture Guidelines)
- **Dependency Rule:** Ketergantungan/Dependency **harus selalu mengarah ke dalam** (menuju Domain Layer).
  - Domain TIDAK BOLEH mengimpor class dari layer Application / Presentation / Infrastructure.
  - Application BOLEH mengimpor Domain, TAPI tidak boleh mengimpor dari Presentation / Infrastructure.
  - Presentation boleh mengimpor Application dan Domain.
- **ORM Independence:** Sebisa mungkin Controller tidak melakukan statement Eloquent (seperti `User::where(...)`). Semuanya dilakukan di Repositories (Infrastructure Layer).
- **Fat Model, Skinny Controller? SALAH!** Jangan buat logic raksasa di Model atau Controller. Letakkan semua Application logic orchestration berpusat di Application Layer (`Services` atau `UseCases`).
