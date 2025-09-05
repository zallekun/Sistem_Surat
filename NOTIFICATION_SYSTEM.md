# Notification System Documentation

Sistem notifikasi lengkap untuk aplikasi Surat Menyurat UNJANI yang mendukung notifikasi internal dan email.

## 📋 Fitur Utama

- **Notifikasi Internal**: Notifikasi real-time dalam aplikasi
- **Email Notifications**: Pengiriman email untuk notifikasi penting
- **Workflow Notifications**: Notifikasi untuk setiap perubahan status surat
- **Deadline Reminders**: Pengingat dan peringatan deadline
- **User Settings**: Pengaturan preferensi notifikasi per user
- **Priority System**: Sistem prioritas (LOW, NORMAL, HIGH, URGENT)
- **Real-time Updates**: Update notifikasi setiap 30 detik

## 🗄️ Database Tables

### notifications
- `id` - Primary key
- `user_id` - Foreign key ke users
- `surat_id` - Foreign key ke surat (nullable)
- `type` - Jenis notifikasi (WORKFLOW, SYSTEM, REMINDER, DEADLINE)
- `title` - Judul notifikasi
- `message` - Isi pesan
- `action_url` - URL untuk action button (nullable)
- `is_read` - Status sudah dibaca
- `is_email_sent` - Status email sudah dikirim
- `priority` - Prioritas notifikasi
- `metadata` - Data tambahan dalam JSON
- `created_at, updated_at, read_at` - Timestamps

### users (kolom tambahan)
- `notification_settings` - Pengaturan notifikasi dalam JSON

## 🔧 Core Components

### NotificationModel
**Location:** `app/Models/NotificationModel.php`

**Key Methods:**
- `getUnreadCount($userId)` - Hitung notifikasi belum dibaca
- `getRecentNotifications($userId, $limit)` - Ambil notifikasi terbaru
- `markAsRead($notificationId, $userId)` - Tandai sudah dibaca
- `createWorkflowNotification()` - Buat notifikasi workflow
- `createSystemNotification()` - Buat notifikasi sistem

### NotificationService
**Location:** `app/Services/NotificationService.php`

**Key Methods:**
- `notifyWorkflowAction()` - Kirim notifikasi untuk aksi workflow
- `sendDeadlineReminders()` - Kirim reminder deadline
- `sendEmailNotification()` - Kirim notifikasi email
- `generateEmailTemplate()` - Generate template email

### NotificationController
**Location:** `app/Controllers/NotificationController.php`

**Endpoints:**
- `GET /notifications` - Halaman utama notifikasi
- `GET /notifications/recent` - API untuk notifikasi terbaru
- `POST /notifications/mark-read/{id}` - Tandai dibaca
- `GET /notifications/settings` - Pengaturan notifikasi
- `POST /notifications/test` - Test notifikasi

## 📧 Email Configuration

**File:** `app/Config/Email.php`

```php
public string $fromEmail = 'no-reply@unjani.ac.id';
public string $fromName = 'Sistem Surat Menyurat UNJANI';
public string $protocol = 'smtp';
public string $SMTPHost = 'smtp.gmail.com'; // Sesuaikan dengan server
public int $SMTPPort = 587;
public string $SMTPCrypto = 'tls';
```

## 🔄 Workflow Integration

Notifikasi otomatis dikirim untuk setiap perubahan status surat:

### Status Transitions & Recipients

1. **DRAFT → SUBMITTED**
   - Recipients: Staff Umum
   - Priority: NORMAL

2. **SUBMITTED → UNDER_REVIEW**
   - Recipients: Staff Umum
   - Priority: NORMAL

3. **UNDER_REVIEW → APPROVED_L1**
   - Recipients: Kabag TU
   - Priority: NORMAL

4. **APPROVED_L1 → APPROVED_L2**
   - Recipients: Dekan/Wakil Dekan (berdasarkan divisi)
   - Priority: NORMAL

5. **APPROVED_L2 → READY_DISPOSISI**
   - Recipients: Sekretaris
   - Priority: NORMAL

6. **READY_DISPOSISI → IN_PROCESS**
   - Recipients: Berdasarkan kategori surat
   - Priority: NORMAL

7. **Any Status → REJECTED**
   - Recipients: Pembuat surat
   - Priority: HIGH

## ⚙️ User Notification Settings

Setiap user dapat mengatur preferensi notifikasi:

```json
{
    "email_enabled": true,
    "email_priorities": ["HIGH", "URGENT"],
    "workflow_notifications": true,
    "system_notifications": true,
    "reminder_notifications": true,
    "deadline_notifications": true
}
```

## 🕐 Deadline System

### Automatic Reminders
- **Upcoming Deadlines**: 3 hari sebelum deadline (HIGH priority)
- **Overdue Letters**: Setelah melewati deadline (URGENT priority)

### CLI Command
```bash
php spark notification:reminders
php spark notification:reminders --cleanup --days=30
```

## 🎨 Frontend Features

### Sidebar Notification Badge
- Real-time update setiap 30 detik
- Menampilkan jumlah notifikasi belum dibaca

### Notification Page
- Filter berdasarkan jenis (WORKFLOW, SYSTEM, REMINDER, DEADLINE)
- Mark as read individual atau semua
- Hapus notifikasi
- Link ke detail surat

### Settings Page
- Toggle email notifications
- Pilih prioritas untuk email
- Aktifkan/nonaktifkan jenis notifikasi

## 🚀 Deployment Notes

1. **Database Migration**
   ```bash
   php spark migrate
   ```

2. **Cron Job untuk Deadline Reminders**
   ```cron
   # Setiap hari jam 9 pagi
   0 9 * * * cd /path/to/project && php spark notification:reminders
   ```

3. **Email Configuration**
   - Update SMTP credentials di `.env`:
   ```env
   email.SMTPHost = your-smtp-host
   email.SMTPUser = your-email
   email.SMTPPass = your-password
   ```

## 🐛 Troubleshooting

### Email tidak terkirim
1. Periksa konfigurasi SMTP
2. Periksa log di `writable/logs/`
3. Pastikan firewall tidak memblokir port SMTP

### Notifikasi tidak muncul
1. Periksa pengaturan user di halaman settings
2. Pastikan JavaScript tidak diblokir browser
3. Periksa console browser untuk error

### Performance Issues
1. Jalankan cleanup notifikasi lama secara rutin
2. Consider indexing pada tabel notifications
3. Optimalkan query jika ada N+1 problems

## 📊 Monitoring

### Database Queries untuk Monitoring
```sql
-- Notifikasi belum dibaca per user
SELECT u.nama, COUNT(*) as unread_count 
FROM notifications n 
JOIN users u ON u.id = n.user_id 
WHERE n.is_read = 0 
GROUP BY u.id;

-- Email yang gagal dikirim
SELECT * FROM notifications 
WHERE is_email_sent = 0 AND created_at < NOW() - INTERVAL 1 HOUR;

-- Statistik notifikasi per jenis
SELECT type, COUNT(*) as count 
FROM notifications 
WHERE created_at >= CURDATE() 
GROUP BY type;
```

## 🔮 Future Enhancements

- [ ] Push notifications untuk mobile
- [ ] WhatsApp integration
- [ ] Notification scheduling
- [ ] Advanced filtering dan search
- [ ] Notification templates customization
- [ ] Multi-language support