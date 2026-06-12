## analisis pemilihan endpoint

endpoint yang saya pilih adalah post /api/v1/delay-notifikasi/send karena endpoint ini merupakan bagian paling penting dari notification delay service. endpoint ini tidak hanya menyimpan atau menampilkan data, tetapi benar-benar menjalankan proses pengiriman notifikasi keterlambatan kepada penumpang.

dibandingkan endpoint lainnya, endpoint ini memiliki proses yang paling lengkap. saat dijalankan, sistem akan mengambil data notifikasi dari database, mengubah status notifikasi menjadi **sent**, mengirim data audit ke sistem pusat menggunakan soap, lalu mengirim informasi ke rabbitmq agar service lain juga mengetahui bahwa notifikasi telah berhasil dikirim.

tanpa endpoint ini, notifikasi yang sudah dibuat hanya akan tersimpan di database dan tidak pernah diproses lebih lanjut. karena itu, endpoint ini menjadi langkah terakhir yang memastikan seluruh proses notifikasi berjalan dengan baik.

saya memilih endpoint ini karena dapat menunjukkan hampir semua fitur utama yang ada pada service saya, mulai dari penggunaan database, autentikasi token, integrasi soap audit, hingga rabbitmq dalam satu alur proses. selain itu, endpoint ini juga merupakan endpoint dengan proses yang paling kompleks dibandingkan endpoint lainnya sehingga paling cocok untuk digunakan.