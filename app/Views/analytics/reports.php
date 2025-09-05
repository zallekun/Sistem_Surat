<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>"
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include sidebar -->
            <?= $this->include('partials/sidebar') ?>

            <!-- Main content -->
            <main class="col-md-9 col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-file-text me-2 text-primary"></i>
                        Reports & Export
                    </h1>
                </div>

                <!-- Quick Export Cards -->
                <div class="row mb-4 g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 report-card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-white d-flex flex-column" style="padding: 1.25rem;">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">
                                        <i class="bi bi-file-pdf me-2"></i>Executive Summary
                                    </h5>
                                    <p class="card-text mb-3 opacity-75 small">
                                        Comprehensive overview of all surat activities and performance metrics
                                    </p>
                                </div>
                                <div class="mt-auto">
                                    <button class="btn btn-light btn-sm report-btn" onclick="exportReport('executive')">
                                        <i class="bi bi-download me-1"></i>Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 report-card h-100" style="background: linear-gradient(135deg, #FF6B6B 20%, #4ECDC4 100%);">
                            <div class="card-body text-white d-flex flex-column" style="padding: 1.25rem;">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">
                                        <i class="bi bi-graph-up me-2"></i>Performance Report
                                    </h5>
                                    <p class="card-text mb-3 opacity-75 small">
                                        Detailed analysis of workflow performance and bottlenecks
                                    </p>
                                </div>
                                <div class="mt-auto">
                                    <button class="btn btn-light btn-sm report-btn" onclick="exportReport('performance')">
                                        <i class="bi bi-download me-1"></i>Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 report-card h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body text-white d-flex flex-column" style="padding: 1.25rem;">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">
                                        <i class="bi bi-table me-2"></i>Data Export
                                    </h5>
                                    <p class="card-text mb-3 opacity-75 small">
                                        Raw data export for further analysis in Excel or other tools
                                    </p>
                                </div>
                                <div class="mt-auto">
                                    <button class="btn btn-light btn-sm report-btn" onclick="exportReport('data')">
                                        <i class="bi bi-file-excel me-1"></i>Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Report Generator -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-gear text-primary me-2"></i>
                                    Custom Report Generator
                                </h5>
                                <small class="text-muted">Generate customized reports with specific parameters</small>
                            </div>
                            <div class="card-body">
                                <form id="customReportForm">
                                    <!-- Primary Controls Grid -->
                                    <div class="report-grid mb-4">
                                        <div class="report-grid-item">
                                            <label for="reportType" class="form-label fw-semibold">
                                                <i class="bi bi-file-text text-primary me-1"></i>Report Type
                                            </label>
                                            <select class="form-select" id="reportType" name="reportType">
                                                <option value="summary">📊 Executive Summary</option>
                                                <option value="detailed">📈 Detailed Analysis</option>
                                                <option value="workflow">⚡ Workflow Performance</option>
                                                <option value="user">👥 User Activity</option>
                                            </select>
                                        </div>
                                        
                                        <div class="report-grid-item">
                                            <label for="dateFrom" class="form-label fw-semibold">
                                                <i class="bi bi-calendar-check text-success me-1"></i>Date From
                                            </label>
                                            <input type="date" class="form-control" id="dateFrom" name="dateFrom" 
                                                   value="<?= date('Y-m-01') ?>">
                                        </div>
                                        
                                        <div class="report-grid-item">
                                            <label for="dateTo" class="form-label fw-semibold">
                                                <i class="bi bi-calendar-x text-danger me-1"></i>Date To
                                            </label>
                                            <input type="date" class="form-control" id="dateTo" name="dateTo" 
                                                   value="<?= date('Y-m-t') ?>">
                                        </div>
                                        
                                        <div class="report-grid-item">
                                            <label for="format" class="form-label fw-semibold">
                                                <i class="bi bi-file-arrow-down text-info me-1"></i>Export Format
                                            </label>
                                            <select class="form-select" id="format" name="format">
                                                <option value="pdf">📄 PDF Report</option>
                                                <option value="excel">📊 Excel Spreadsheet</option>
                                                <option value="csv">📋 CSV Data</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Secondary Options -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold mb-3">
                                                <i class="bi bi-toggles text-warning me-1"></i>Include Options
                                            </label>
                                            <div class="options-grid">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="includeCharts" name="includeCharts" checked>
                                                    <label class="form-check-label" for="includeCharts">
                                                        📊 Charts & Visualizations
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="includeDetails" name="includeDetails" checked>
                                                    <label class="form-check-label" for="includeDetails">
                                                        📋 Detailed Data Tables
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="includeRecommendations" name="includeRecommendations">
                                                    <label class="form-check-label" for="includeRecommendations">
                                                        💡 Recommendations
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="additionalNotes" class="form-label fw-semibold">
                                                <i class="bi bi-chat-text text-secondary me-1"></i>Additional Notes
                                            </label>
                                            <textarea class="form-control" id="additionalNotes" name="additionalNotes" 
                                                      rows="4" placeholder="Add any specific notes, requirements, or custom instructions for this report..."></textarea>
                                        </div>
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <div class="text-muted small">
                                            <i class="bi bi-info-circle me-1"></i>Report generation may take 30-60 seconds
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-primary btn-lg generate-btn">
                                                <i class="bi bi-gear me-2"></i>Generate Custom Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Reports History -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-clock-history text-info me-2"></i>
                                    Recent Reports
                                </h5>
                                <small class="text-muted">Your recently generated reports</small>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="mt-2 mb-0">No reports generated yet</p>
                                    <small>Generated reports will appear here for easy re-download</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Statistics -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-file-text text-primary" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-primary">0</h4>
                                <small class="text-muted">Reports Generated</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-download text-success" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-success">0</h4>
                                <small class="text-muted">Downloads</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-calendar text-info" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-info"><?= date('M') ?></h4>
                                <small class="text-muted">Current Month</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-warning">100%</h4>
                                <small class="text-muted">Uptime</small>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function exportReport(type) {
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating...';
            button.disabled = true;

            // Simulate report generation
            setTimeout(() => {
                // For now, redirect to the PDF export endpoint
                // In future, this can be enhanced with actual report generation
                window.location.href = `<?= base_url('analytics/export/pdf') ?>?type=${type}`;
                
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
                
                // Show success message
                showAlert('success', `${type.charAt(0).toUpperCase() + type.slice(1)} report generated successfully!`);
            }, 2000);
        }

        document.getElementById('customReportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const reportData = Object.fromEntries(formData);
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';
            submitBtn.disabled = true;
            
            // Simulate custom report generation
            setTimeout(() => {
                // Reset form
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                showAlert('success', 'Custom report generated successfully!');
                console.log('Report Data:', reportData);
            }, 3000);
        });

        function resetForm() {
            document.getElementById('customReportForm').reset();
            document.getElementById('dateFrom').value = '<?= date('Y-m-01') ?>';
            document.getElementById('dateTo').value = '<?= date('Y-m-t') ?>';
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    </script>

    <style>
        /* Enhanced Report Cards */
        .report-card {
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        
        .report-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }
        
        .report-btn {
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .report-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* Custom Report Generator Grid */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .report-grid-item {
            padding: 0.5rem;
        }
        
        .options-grid {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .form-check-label {
            font-weight: 500;
            color: #495057;
        }
        
        .form-check-input:checked {
            background-color: var(--unjani-primary);
            border-color: var(--unjani-primary);
        }
        
        /* Enhanced Form Controls */
        .form-select, .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: var(--unjani-primary);
            box-shadow: 0 0 0 0.2rem rgba(158, 190, 245, 0.25);
        }
        
        /* Generate Button Enhancement */
        .generate-btn {
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--unjani-primary) 0%, #667eea 100%);
            border: none;
            transition: all 0.3s ease;
        }
        
        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(158, 190, 245, 0.4);
            background: linear-gradient(135deg, #667eea 0%, var(--unjani-primary) 100%);
        }
        
        /* Card Consistent Styling */
        .card {
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
        }
        
        .card:hover:not(.report-card) {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
        
        /* Statistics Cards */
        .card.text-center {
            transition: all 0.3s ease;
        }
        
        .card.text-center:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        /* Improved Spacing */
        .g-4 > * {
            margin-bottom: 1rem;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .report-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .report-card {
                margin-bottom: 1rem;
            }
            
            .generate-btn {
                width: 100%;
                margin-top: 1rem;
            }
        }
        
        /* Better Recent Reports Section */
        .text-center.py-4 {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            border: 2px dashed #dee2e6;
        }
        
        /* Enhanced Icons */
        .bi {
            transition: all 0.2s ease;
        }
        
        .card:hover .bi {
            transform: scale(1.1);
        }
    </style>
</body>
</html>