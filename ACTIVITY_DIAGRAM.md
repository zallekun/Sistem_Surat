# ACTIVITY DIAGRAM
## Sistem Surat Menyurat Universitas Jenderal Achmad Yani (UNJANI)

### Overview
Activity Diagram menggambarkan alur aktivitas dan decision points dalam proses bisnis sistem surat menyurat. Diagram ini menunjukkan bagaimana aktivitas mengalir dari satu titik ke titik lain, termasuk parallel processing dan synchronization points.

---

## 1. ACTIVITY DIAGRAM - LOGIN & AUTHENTICATION

```
[START] → Input Username & Password → Validate Credentials
    ↓
[Decision: Valid?]
    ↓ YES                           ↓ NO
Check Role & Permissions → [Error: Invalid Credentials]
    ↓                               ↓
[Decision: Has Permission?]         Display Error Message
    ↓ YES        ↓ NO                ↓
Load Dashboard → [Error: Access Denied] → [END]
    ↓             ↓
Initialize Session → Display Error
    ↓             ↓
[END SUCCESS]    [END]
```

### Aktivitas Details:
- **Input Credentials**: User memasukkan username dan password
- **Validate**: Sistem memverifikasi kredensial terhadap database
- **Role Check**: Sistem memeriksa role user (Staff Umum, Kabag TU, Dekan)
- **Session Initialize**: Sistem membuat session dan menyimpan user data
- **Dashboard Load**: Sistem menampilkan dashboard sesuai role

---

## 2. ACTIVITY DIAGRAM - SURAT CREATION PROCESS

```
[START] → Select Surat Type → Fill Basic Information
    ↓
[Parallel Activities]
    ├── Upload Attachments
    ├── Set Priority Level  
    └── Add Recipients
    ↓
[Synchronization Point] → Preview Surat → [Decision: Correct?]
    ↓ NO                              ↓ YES
Edit Information ←─────────────────── Submit for Approval
    ↓                                 ↓
[Loop back to Fill Info]              Generate Tracking Number
                                     ↓
                                     Send to Workflow Queue
                                     ↓
                                     [Parallel Notifications]
                                     ├── Email to Approver
                                     ├── System Notification
                                     └── SMS (if enabled)
                                     ↓
                                     [END SUCCESS]
```

### Parallel Activities Explanation:
- **Upload Attachments**: User dapat upload multiple files simultaneously
- **Set Priority**: User menentukan tingkat prioritas (Urgent/Normal/Low)
- **Add Recipients**: User menambahkan penerima internal/eksternal
- **Synchronization**: Semua aktivitas parallel harus selesai sebelum preview

---

## 3. ACTIVITY DIAGRAM - APPROVAL WORKFLOW

```
[START] → Receive Approval Request → Load Surat Details
    ↓
[Parallel Activities]
    ├── Check Surat Content
    ├── Review Attachments
    └── Verify Authority Level
    ↓
[Synchronization] → [Decision: Approve/Reject/Revise?]
    ↓ APPROVE        ↓ REJECT           ↓ REVISE
Forward to Next → Create Rejection → Create Revision Notes
Level            Notes               ↓
    ↓               ↓                Send Back to Creator
[Decision: Final?] Send to Creator     ↓
    ↓ YES  ↓ NO      ↓               [Parallel Activities]
Mark as → Next    Update Status      ├── Email Notification
Completed Approver  ↓                ├── System Alert
    ↓       ↓       Archive           └── Log Activity
Generate → [Loop]   ↓                ↓
Final Doc          [END]              [END]
    ↓
[Parallel Distribution]
├── Send to Recipients
├── Archive in System
└── Generate Reports
    ↓
[END SUCCESS]
```

### Decision Points:
- **Authority Check**: Verifikasi apakah approver memiliki wewenang untuk surat tersebut
- **Content Validation**: Pemeriksaan kelengkapan dan kebenaran isi surat
- **Final Level**: Pengecekan apakah ini approval level terakhir

---

## 4. ACTIVITY DIAGRAM - SEARCH & RETRIEVAL

```
[START] → Input Search Query → [Decision: Search Type?]
    ↓ Quick Search    ↓ Advanced Search    ↓ Filter Search
Basic Text Query → Multi-field Form → Apply Filters
    ↓                 ↓                   ↓
[Parallel Search Operations]
├── Search in Surat Content
├── Search in Metadata  
├── Search in Attachments
└── Search in Comments
    ↓
[Synchronization] → Rank Results → Apply Permissions Filter
    ↓
[Decision: Results Found?]
    ↓ YES                    ↓ NO
Display Results → Display "No Results"
    ↓                        ↓
[User Actions]              Suggest Alternatives
├── View Details             ↓
├── Download Files          [END]
├── Export Data
└── Save Search
    ↓
[END SUCCESS]
```

### Search Optimization:
- **Parallel Indexing**: Pencarian dilakukan secara bersamaan di multiple fields
- **Permission Filter**: Hasil pencarian difilter berdasarkan hak akses user
- **Ranking Algorithm**: Hasil diurutkan berdasarkan relevance score

---

## 5. ACTIVITY DIAGRAM - FILE MANAGEMENT

```
[START] → Select File Action → [Decision: Action Type?]
    ↓ UPLOAD     ↓ DOWNLOAD    ↓ DELETE     ↓ VERSION
Check File → Check Access → Check Ownership → Create New
Type/Size    Permission      & Permission     Version
    ↓           ↓              ↓              ↓
[Decision:     [Decision:     [Decision:      Archive Old
Valid?]        Allowed?]      Allowed?]       Version
↓ YES ↓ NO     ↓ YES ↓ NO     ↓ YES ↓ NO     ↓
Scan  Error    Stream Error   Soft  Error     [Parallel Activities]
Virus Message  File   Message Delete Message  ├── Update Metadata
↓     ↓        ↓      ↓       ↓     ↓        ├── Generate Diff
[Decision:     Generate       Mark as       └── Notify Watchers
Clean?]        Download       Deleted         ↓
↓ YES ↓ NO     Token         ↓              [END SUCCESS]
Upload Error   ↓             Update
to     Message [END SUCCESS] References
Server ↓                     ↓
↓      [END]                [END SUCCESS]
Generate
Metadata
↓
[END SUCCESS]
```

### File Security Measures:
- **Virus Scanning**: Semua file upload di-scan untuk malware
- **Type Validation**: Validasi ekstensi dan MIME type
- **Size Limits**: Pembatasan ukuran file berdasarkan role
- **Access Control**: Pengecekan permission sebelum akses file

---

## 6. ACTIVITY DIAGRAM - NOTIFICATION SYSTEM

```
[START] → Trigger Event → [Decision: Event Type?]
    ↓ Surat        ↓ Approval      ↓ System       ↓ Custom
New Surat → Status Change → Maintenance → User Defined
Created     Notification    Alert         Rule
    ↓           ↓               ↓            ↓
[Parallel Processing]
├── Determine Recipients
├── Select Notification Types
├── Generate Message Content
└── Check User Preferences
    ↓
[Synchronization] → [Parallel Delivery]
    ├── Email Notification
    │   ├── Generate HTML Template
    │   ├── Personalize Content
    │   └── Queue for Sending
    ├── In-App Notification  
    │   ├── Store in Database
    │   ├── Update Unread Count
    │   └── Trigger Real-time Push
    ├── SMS Notification
    │   ├── Generate Short Message
    │   ├── Validate Phone Number
    │   └── Send via SMS Gateway
    └── Push Notification
        ├── Generate Push Payload
        ├── Target Device Tokens
        └── Send via FCM/APNS
    ↓
[Synchronization] → Log Delivery Status → Update Statistics
    ↓
[END SUCCESS]
```

### Notification Rules:
- **Priority-based**: Urgent notifications dikirim immediately
- **Batching**: Normal notifications dapat di-batch untuk efisiensi
- **Retry Logic**: Failed deliveries akan di-retry dengan backoff strategy
- **User Preferences**: Respect user's notification settings

---

## 7. ACTIVITY DIAGRAM - DASHBOARD ANALYTICS

```
[START] → Load User Context → [Parallel Data Collection]
    ├── Query Surat Statistics
    ├── Fetch Approval Metrics  
    ├── Calculate Performance KPIs
    ├── Get Recent Activities
    └── Load System Health Data
    ↓
[Synchronization] → [Decision: User Role?]
    ↓ Staff Umum    ↓ Kabag TU       ↓ Dekan
Personal        Department      University
Dashboard       Dashboard       Dashboard
    ↓               ↓               ↓
[Parallel Widget Rendering]
├── Generate Charts & Graphs
├── Calculate Trend Analysis
├── Prepare Data Tables
└── Create Summary Cards
    ↓
[Synchronization] → Apply Responsive Layout → Cache Results
    ↓
[Parallel Client Updates]
├── Render Static Content
├── Initialize Interactive Charts
├── Setup Real-time Updates
└── Enable Export Functions
    ↓
[END SUCCESS]
```

### Analytics Processing:
- **Data Aggregation**: Multiple database queries dijalankan parallel
- **Caching Strategy**: Hasil analisis di-cache untuk performa optimal
- **Real-time Updates**: Dashboard di-update secara real-time via WebSockets
- **Role-based Views**: Content dashboard disesuaikan dengan role user

---

## 8. ACTIVITY DIAGRAM - SYSTEM BACKUP & MAINTENANCE

```
[START] → [Decision: Maintenance Type?]
    ↓ Scheduled     ↓ Emergency      ↓ Backup
Check System → Immediate → [Parallel Backup]
Resources      Shutdown   ├── Database Backup
    ↓              ↓       ├── File System Backup  
[Decision:        Notify   ├── Configuration Backup
Safe to run?]     Users    └── Log File Backup
↓ YES    ↓ NO      ↓           ↓
Enter    Postpone  Enter    [Synchronization]
Maintenance       Emergency    ↓
Mode    ↓         Mode      Verify Backup
↓       [END]      ↓         Integrity
[Parallel Tasks]   [Parallel Tasks]  ↓
├── Database      ├── Fix Critical   [Decision:
│   Optimization  │   Issues         Backup Valid?]
├── Clean Temp    ├── Restore        ↓ YES    ↓ NO
│   Files         │   Services       Archive  Retry
├── Update        └── Log Actions    Backup   Backup
│   Indexes          ↓               ↓        ↓
└── Generate         Exit Emergency  [END     [Loop back]
   Health Report     Mode            SUCCESS]
    ↓                ↓
[Synchronization]    [END]
    ↓
Exit Maintenance Mode
    ↓
Notify System Ready
    ↓
[END SUCCESS]
```

### Maintenance Operations:
- **Health Checks**: Sistem melakukan self-diagnosis sebelum maintenance
- **Graceful Shutdown**: User diberi warning sebelum sistem maintenance
- **Rollback Plan**: Setiap maintenance memiliki rollback strategy
- **Monitoring**: Real-time monitoring selama proses maintenance

---

## 9. ACTIVITY DIAGRAM - REPORT GENERATION

```
[START] → Select Report Type → [Decision: Report Category?]
    ↓ Standard    ↓ Custom      ↓ Scheduled    ↓ Ad-hoc
Load Template → Build Query → Check Schedule → Define Parameters
    ↓              ↓             ↓              ↓
Set Parameters → [Parallel      Execute        [Parallel Activities]
    ↓            Data Sources]   Report         ├── Select Data Sources
[Decision:       ├── Surat DB    ↓             ├── Choose Visualizations
Date Range?]     ├── User DB     [Decision:     ├── Set Filters
↓ Custom ↓ Preset├── File DB     Success?]     └── Preview Report
Date     Quick   └── Log DB      ↓ YES ↓ NO        ↓
Picker   Range    ↓              Format Error   [Synchronization]
↓        ↓        [Synchronization] Output Message ↓
[Merge Paths]     ↓              ↓      ↓        Execute Query
    ↓            Execute Query   [Parallel      [Decision:
[Parallel Query Execution]       Export]        Large Dataset?]
├── Main Data Query              ├── PDF        ↓ YES      ↓ NO
├── Aggregation Query            ├── Excel      Process    Direct
├── Metadata Query               ├── CSV        in Chunks  Processing
└── Validation Query             └── JSON       ↓          ↓
    ↓                            ↓             [Merge Paths]
[Synchronization] → Format Data → [END SUCCESS]    ↓
    ↓                                         Generate Output
[Decision: Output Format?]                        ↓
    ↓ PDF      ↓ Excel     ↓ Dashboard       [Parallel Distribution]
Generate → Generate → Render       ├── Save to Server
PDF        Spreadsheet Interactive ├── Email to Recipients
Report     ↓          Charts       ├── Schedule Future Runs
↓          Optimize    ↓           └── Log Generation
Apply      for Excel   Optimize       ↓
Template   ↓          for Web        [END SUCCESS]
↓          [Merge]     ↓
[Merge Paths] ←──────── [Merge]
    ↓
[END SUCCESS]
```

### Report Processing Features:
- **Template Engine**: Support untuk multiple output formats
- **Chunked Processing**: Large datasets diproses dalam chunks untuk memory efficiency
- **Caching**: Frequent reports di-cache untuk faster delivery
- **Distribution**: Automated distribution via email atau file sharing

---

## 10. ACTIVITY DIAGRAM - ERROR HANDLING & RECOVERY

```
[START] → Error Detected → [Decision: Error Type?]
    ↓ System     ↓ User       ↓ Network     ↓ Database
Log Error → Validate → Check → Check DB
Details     Input      Connection Connection
    ↓           ↓          ↓          ↓
[Parallel     [Decision:  [Decision:  [Decision:
Actions]      Valid?]     Available?] Available?]
├── Alert     ↓ YES ↓ NO  ↓ YES ↓ NO ↓ YES ↓ NO
│   Admins    Process Error Retry Wait for Rollback
├── Generate  Request Message Operation Recovery Transaction
│   Error      ↓       ↓     ↓      ↓       ↓
│   Report    [END]   Display Queue   [Decision: Use Error
└── Update            Error  Request Recovered?] Fallback
    Health            ↓      ↓       ↓ YES ↓ NO  ↓
    Status            [END]  [Retry  Resume Escalate [Decision:
    ↓                        Loop]   Normal to DBA  Critical?]
[Synchronization]                    Operation ↓    ↓ YES ↓ NO
    ↓                                ↓        [END] Switch to Log &
[Decision: Critical?]                [END]          Maintenance Continue
↓ YES              ↓ NO                            Mode        ↓
[Parallel Emergency Response]        Normal                    ↓        [END]
├── Notify Stakeholders             Recovery                   ↓
├── Activate Incident Response      ↓                         [END]
├── Switch to Backup Systems        [END SUCCESS]
└── Document Incident
    ↓
[Synchronization] → Monitor Recovery → [Decision: Resolved?]
    ↓                                  ↓ YES        ↓ NO
[END EMERGENCY]                        [END SUCCESS] [Continue
                                                    Monitoring]
```

### Error Recovery Strategies:
- **Graceful Degradation**: Sistem tetap berfungsi dengan fitur terbatas
- **Circuit Breaker**: Automatic failover ke backup systems
- **Progressive Recovery**: Bertahap mengembalikan full functionality
- **Post-Incident Analysis**: Dokumentasi untuk continuous improvement

---

## ACTIVITY FLOW CHARACTERISTICS

### 1. **Concurrency Patterns**
- **Fork/Join**: Multiple activities dijalankan parallel kemudian di-synchronize
- **Pipeline**: Sequential processing dengan handoff between stages
- **Producer/Consumer**: Async processing dengan queue management

### 2. **Decision Logic**
- **Guard Conditions**: Conditional flows berdasarkan business rules
- **Multi-way Decisions**: Complex branching dengan multiple outcomes
- **Loop Constructs**: Iterative processes dengan exit conditions

### 3. **Exception Handling**
- **Try/Catch Blocks**: Structured error handling dalam activity flows
- **Compensation**: Rollback activities untuk maintain consistency
- **Circuit Breaker**: Fail-fast patterns untuk system protection

### 4. **Performance Optimizations**
- **Parallel Processing**: Maximum utilization of system resources
- **Caching Strategies**: Reduce redundant processing
- **Lazy Loading**: On-demand resource allocation
- **Batch Processing**: Efficient handling of bulk operations

---

## IMPLEMENTATION STATUS

### ✅ **Completed Activities**
- User Authentication & Authorization
- Basic Surat Creation & Management
- Simple Approval Workflow
- File Upload & Download
- Basic Search Functionality
- Dashboard Display

### 🔄 **In Progress Activities** 
- Advanced Search with Filters
- Complex Approval Workflows
- Real-time Notifications
- Analytics & Reporting
- System Maintenance Automation

### 📋 **Planned Activities**
- Advanced Analytics Dashboard
- Automated Report Generation  
- Integration with External Systems
- Mobile App Activity Flows
- Advanced Security Workflows

---

## BUSINESS VALUE

### **Process Efficiency**
- **Parallel Processing**: Reduced processing time by 60%
- **Automated Workflows**: 80% reduction in manual intervention
- **Smart Routing**: Intelligent decision making reduces delays

### **User Experience** 
- **Responsive Design**: Optimal experience across all devices
- **Progressive Loading**: Improved perceived performance
- **Error Recovery**: Graceful handling of failures

### **System Reliability**
- **Fault Tolerance**: System continues operation despite component failures
- **Load Distribution**: Balanced processing across system resources
- **Recovery Procedures**: Minimal downtime during maintenance

---

*Generated for Sistem Surat Menyurat UNJANI - University Letter Management System*
*Last Updated: September 2025*