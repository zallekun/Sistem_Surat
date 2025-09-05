# Class Diagram - Sistem Surat Menyurat UNJANI

## 🏗️ System Architecture Overview

### MVC Pattern Implementation
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Controllers │────│   Models    │────│    Views    │
│   (Logic)   │    │   (Data)    │    │   (UI)      │
└─────────────┘    └─────────────┘    └─────────────┘
```

## 📊 Core Entity Classes

### 1. User Management Classes

```php
┌─────────────────────────────────┐
│            UserModel            │
├─────────────────────────────────┤
│ + id: int                       │
│ + nama: string                  │
│ + email: string                 │
│ + nip: string                   │
│ + role: enum                    │
│ + fakultas_id: int              │
│ + prodi_id: int                 │
│ + password: string              │
│ + avatar: string                │
│ + alamat: text                  │
│ + telepon: string               │
│ + whatsapp: string              │
│ + last_login: datetime          │
│ + is_active: boolean            │
├─────────────────────────────────┤
│ + authenticate()                │
│ + getUsersByRole()              │
│ + updateProfile()               │
│ + changePassword()              │
│ + incrementWorkload()           │
│ + decrementWorkload()           │
│ + getWorkloadStats()            │
└─────────────────────────────────┘
```

### 2. Surat Management Classes

```php
┌─────────────────────────────────┐
│           SuratModel            │
├─────────────────────────────────┤
│ + id: int                       │
│ + nomor_surat: string           │
│ + tanggal_surat: date           │
│ + perihal: text                 │
│ + kategori: enum                │
│ + prioritas: enum               │
│ + tujuan: string                │
│ + keterangan: text              │
│ + status: enum                  │
│ + prodi_id: int                 │
│ + created_by: int               │
│ + current_approver: int         │
├─────────────────────────────────┤
│ + createSurat()                 │
│ + updateSurat()                 │
│ + submitSurat()                 │
│ + updateStatus()                │
│ + getSuratByUser()              │
│ + getSuratByStatus()            │
│ + getDetailWithWorkflow()       │
│ + generateNomorSurat()          │
└─────────────────────────────────┘
           │
           │ 1:N
           ▼
┌─────────────────────────────────┐
│         LampiranModel           │
├─────────────────────────────────┤
│ + id: int                       │
│ + surat_id: int                 │
│ + nama_file: string             │
│ + nama_asli: string             │
│ + ukuran_file: int              │
│ + mime_type: string             │
│ + versi: int                    │
│ + keterangan: text              │
│ + uploaded_by: int              │
│ + is_final: boolean             │
├─────────────────────────────────┤
│ + uploadFile()                  │
│ + downloadFile()                │
│ + previewFile()                 │
│ + deleteFile()                  │
│ + getFileHistory()              │
│ + updateVersion()               │
└─────────────────────────────────┘
```

### 3. Workflow Management Classes

```php
┌─────────────────────────────────┐
│       ApprovalModel             │
├─────────────────────────────────┤
│ + id: int                       │
│ + surat_id: int                 │
│ + level: int                    │
│ + user_id: int                  │
│ + status_approval: enum         │
│ + tanggal_approval: datetime    │
│ + alasan_reject: text           │
│ + catatan: text                 │
│ + ip_address: string            │
├─────────────────────────────────┤
│ + getApprovalChain()            │
│ + createApprovalChain()         │
│ + updateApprovalStatus()        │
│ + getPendingApprovals()         │
└─────────────────────────────────┘
           │
           │ 1:N
           ▼
┌─────────────────────────────────┐
│      SuratWorkflowModel         │
├─────────────────────────────────┤
│ + id: int                       │
│ + surat_id: int                 │
│ + from_status: string           │
│ + to_status: string             │
│ + action_by: int                │
│ + action_type: enum             │
│ + keterangan: text              │
│ + ip_address: string            │
│ + user_agent: string            │
│ + created_at: datetime          │
├─────────────────────────────────┤
│ + logWorkflow()                 │
│ + getWorkflowHistory()          │
│ + getLatestWorkflow()           │
│ + getProcessingTime()           │
│ + getWorkflowStats()            │
│ + getUserActivitySummary()      │
└─────────────────────────────────┘
```

### 4. Organizational Structure Classes

```php
┌─────────────────────────────────┐
│         FakultasModel           │
├─────────────────────────────────┤
│ + id: int                       │
│ + nama_fakultas: string         │
│ + kode_fakultas: string         │
│ + dekan_id: int                 │
│ + is_active: boolean            │
├─────────────────────────────────┤
│ + getAllFakultas()              │
│ + getFakultasById()             │
└─────────────────────────────────┘
           │
           │ 1:N
           ▼
┌─────────────────────────────────┐
│           ProdiModel            │
├─────────────────────────────────┤
│ + id: int                       │
│ + nama_prodi: string            │
│ + kode_prodi: string            │
│ + fakultas_id: int              │
│ + kaprodi_id: int               │
│ + jenjang: enum                 │
│ + is_active: boolean            │
├─────────────────────────────────┤
│ + getProdiByFakultas()          │
│ + getAllProdi()                 │
│ + getProdiById()                │
└─────────────────────────────────┘
           │
           │ 1:N
           ▼
┌─────────────────────────────────┐
│          DivisiModel            │
├─────────────────────────────────┤
│ + id: int                       │
│ + nama_divisi: string           │
│ + kode_divisi: string           │
│ + parent_id: int                │
│ + kepala_divisi_id: int         │
│ + level: int                    │
│ + is_active: boolean            │
├─────────────────────────────────┤
│ + getDivisiHierarchy()          │
│ + getAllDivisi()                │
│ + getDivisiById()               │
└─────────────────────────────────┘
```

### 5. Notification System Classes

```php
┌─────────────────────────────────┐
│       NotificationModel         │
├─────────────────────────────────┤
│ + id: int                       │
│ + user_id: int                  │
│ + surat_id: int                 │
│ + type: enum                    │
│ + title: string                 │
│ + message: text                 │
│ + is_read: boolean              │
│ + read_at: datetime             │
│ + data: json                    │
│ + created_at: datetime          │
├─────────────────────────────────┤
│ + createNotification()          │
│ + markAsRead()                  │
│ + getUnreadNotifications()      │
│ + getNotificationHistory()      │
│ + deleteNotification()          │
│ + markAllAsRead()               │
└─────────────────────────────────┘
```

### 6. Disposisi Management Classes

```php
┌─────────────────────────────────┐
│        DisposisiModel           │
├─────────────────────────────────┤
│ + id: int                       │
│ + surat_id: int                 │
│ + dari_user_id: int             │
│ + ke_user_id: int               │
│ + catatan: text                 │
│ + batas_waktu: date             │
│ + prioritas: enum               │
│ + status: enum                  │
│ + created_at: datetime          │
│ + completed_at: datetime        │
├─────────────────────────────────┤
│ + createAutoDisposisi()         │
│ + createManualDisposisi()       │
│ + updateStatus()                │
│ + getDisposisiByUser()          │
│ + getPendingDisposisi()         │
│ + getDisposisiHistory()         │
└─────────────────────────────────┘
```

### 7. User Delegation Classes

```php
┌─────────────────────────────────┐
│     UserDelegationsModel        │
├─────────────────────────────────┤
│ + id: int                       │
│ + delegator_id: int             │
│ + delegate_id: int              │
│ + start_date: date              │
│ + end_date: date                │
│ + permissions: json             │
│ + is_active: boolean            │
│ + created_by: int               │
├─────────────────────────────────┤
│ + createDelegation()            │
│ + getActiveDelegations()        │
│ + revokeDelegation()            │
│ + checkDelegationPermission()   │
└─────────────────────────────────┘
```

## 🎮 Controller Classes

### 1. Authentication Controller

```php
┌─────────────────────────────────┐
│       AuthController            │
├─────────────────────────────────┤
│ - userModel: UserModel          │
│ - session: Session              │
├─────────────────────────────────┤
│ + login()                       │
│ + authenticate()                │
│ + logout()                      │
│ + profile()                     │
│ + updateProfile()               │
│ + uploadAvatar()                │
│ + changePassword()              │
└─────────────────────────────────┘
```

### 2. Surat Management Controller

```php
┌─────────────────────────────────┐
│        SuratController          │
├─────────────────────────────────┤
│ - suratModel: SuratModel        │
│ - workflowModel: SuratWorkflow  │
│ - lampiranModel: LampiranModel  │
├─────────────────────────────────┤
│ + index()                       │
│ + create()                      │
│ + store()                       │
│ + show()                        │
│ + edit()                        │
│ + update()                      │
│ + submit()                      │
│ + bulkSubmit()                  │
└─────────────────────────────────┘
```

### 3. Workflow Management Controller

```php
┌─────────────────────────────────┐
│      WorkflowController         │
├─────────────────────────────────┤
│ - workflowModel: SuratWorkflow  │
│ - approvalModel: ApprovalModel  │
│ - notificationService: Service  │
├─────────────────────────────────┤
│ + approve()                     │
│ + reject()                      │
│ + revise()                      │
│ + dispose()                     │
│ + complete()                    │
│ + history()                     │
│ + timeline()                    │
└─────────────────────────────────┘
```

### 4. Dashboard & Analytics Controller

```php
┌─────────────────────────────────┐
│     DashboardController         │
├─────────────────────────────────┤
│ - suratModel: SuratModel        │
│ - userModel: UserModel          │
│ - workflowModel: SuratWorkflow  │
├─────────────────────────────────┤
│ + index()                       │
│ + getKPIMetrics()               │
│ + getChartData()                │
│ + getRecentActivity()           │
│ + getUserStats()                │
└─────────────────────────────────┘
```

### 5. Search & Analytics Controller

```php
┌─────────────────────────────────┐
│      SearchController           │
├─────────────────────────────────┤
│ - suratModel: SuratModel        │
│ - searchModel: SearchModel      │
├─────────────────────────────────┤
│ + index()                       │
│ + search()                      │
│ + suggestions()                 │
│ + saveSearch()                  │
│ + exportResults()               │
│ + analytics()                   │
└─────────────────────────────────┘
```

## 🛠️ Service Classes

### 1. Notification Service

```php
┌─────────────────────────────────┐
│     NotificationService         │
├─────────────────────────────────┤
│ - notificationModel: Model      │
│ - emailService: EmailService    │
│ - whatsappService: WhatsApp     │
├─────────────────────────────────┤
│ + notifyWorkflowAction()        │
│ + sendEmailNotification()       │
│ + sendWhatsAppMessage()         │
│ + createWebNotification()       │
│ + broadcastNotification()       │
└─────────────────────────────────┘
```

### 2. File Management Service

```php
┌─────────────────────────────────┐
│      FileService               │
├─────────────────────────────────┤
│ - lampiranModel: LampiranModel  │
│ - storageConfig: Config         │
├─────────────────────────────────┤
│ + uploadFile()                  │
│ + downloadFile()                │
│ + previewFile()                 │
│ + deleteFile()                  │
│ + validateFile()                │
│ + compressImage()               │
│ + generateThumbnail()           │
└─────────────────────────────────┘
```

## 🔄 Design Patterns Implemented

### 1. MVC Pattern
- **Models**: Data layer dengan business logic
- **Views**: Presentation layer dengan UI components  
- **Controllers**: Logic layer dengan request handling

### 2. Repository Pattern
- UserModel sebagai repository untuk User entities
- SuratModel sebagai repository untuk Surat entities
- Separation of concerns antara data access dan business logic

### 3. Service Pattern
- NotificationService untuk centralized notification handling
- FileService untuk file operations
- Reusable service components

### 4. Observer Pattern
- Event-driven notifications
- Workflow state change observers
- Real-time updates

### 5. Strategy Pattern
- Different approval strategies based on surat category
- Multiple notification channels (email, web, whatsapp)
- File handling strategies by type

## 📊 Class Relationships Summary

```
UserModel (1) ←→ (N) SuratModel
SuratModel (1) ←→ (N) LampiranModel
SuratModel (1) ←→ (N) SuratWorkflowModel
SuratModel (1) ←→ (N) ApprovalModel
UserModel (1) ←→ (N) NotificationModel
SuratModel (1) ←→ (N) DisposisiModel
UserModel (1) ←→ (N) UserDelegationsModel
FakultasModel (1) ←→ (N) ProdiModel
ProdiModel (1) ←→ (N) UserModel
```

## 🎯 Key Features Implemented

- ✅ **Role-Based Access Control** dengan proper inheritance
- ✅ **Workflow State Management** dengan state transitions
- ✅ **File Management** dengan versioning support
- ✅ **Notification System** dengan multiple channels
- ✅ **Audit Trail** untuk semua critical operations
- ✅ **Search & Analytics** dengan advanced filtering
- ✅ **Dashboard Metrics** dengan real-time data

Semua classes telah diimplementasi mengikuti **SOLID principles** dan **CodeIgniter 4 best practices**.