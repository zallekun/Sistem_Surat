<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">
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
                        <i class="bi bi-graph-up me-2 text-primary"></i>
                        Executive Analytics Dashboard
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar-range me-1"></i>
                                Last 6 Months
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Last Month</a></li>
                                <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                                <li><a class="dropdown-item" href="#">Last 6 Months</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                        <a href="<?= base_url('analytics/export/pdf') ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-download me-1"></i>Export PDF
                        </a>
                    </div>
                </div>

                <!-- Executive KPI Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card kpi-card border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="card-body text-white">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="card-title mb-1 opacity-75">Total Surat</p>
                                        <h2 class="mb-0 fw-bold"><?= number_format($kpis['total_surat']) ?></h2>
                                        <small class="opacity-75">
                                            <i class="bi bi-envelope"></i> All Time
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-file-text" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card kpi-card border-0 h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <div class="card-body text-white">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="card-title mb-1 opacity-75">Completed This Month</p>
                                        <h2 class="mb-0 fw-bold"><?= number_format($kpis['completed_this_month']) ?></h2>
                                        <small class="opacity-75">
                                            <i class="bi bi-check-circle"></i> <?= date('M Y') ?>
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-check2-circle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card kpi-card border-0 h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body text-white">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="card-title mb-1 opacity-75">Avg Processing Time</p>
                                        <h2 class="mb-0 fw-bold"><?= $kpis['avg_processing_days'] ?></h2>
                                        <small class="opacity-75">
                                            <i class="bi bi-clock"></i> Days Average
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-hourglass-split" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card kpi-card border-0 h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <div class="card-body text-white">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="card-title mb-1 opacity-75">Completion Rate</p>
                                        <h2 class="mb-0 fw-bold"><?= $kpis['completion_rate'] ?>%</h2>
                                        <small class="opacity-75">
                                            <i class="bi bi-graph-up"></i> Success Rate
                                        </small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-percent" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Monthly Trends Chart -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-graph-up text-primary me-2"></i>
                                    Monthly Trends
                                </h5>
                                <small class="text-muted">Surat creation and completion trends over time</small>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyTrendsChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Workflow Status Distribution -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-pie-chart text-success me-2"></i>
                                    Workflow Status
                                </h5>
                                <small class="text-muted">Current status distribution</small>
                            </div>
                            <div class="card-body">
                                <canvas id="workflowStatusChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance & Activity Row -->
                <div class="row mb-4">
                    <!-- Top Performers -->
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-trophy text-warning me-2"></i>
                                    Top Performers (30 Days)
                                </h5>
                                <small class="text-muted">Most active users this month</small>
                            </div>
                            <div class="card-body">
                                <?php if (empty($top_performers)): ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="mt-2 mb-0">No activity data available</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>User</th>
                                                    <th>Role</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($top_performers, 0, 5) as $performer): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($performer['nama']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?= ucwords(str_replace('_', ' ', $performer['role'])) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"><?= $performer['total_actions'] ?></span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Performance -->
                    <div class="col-lg-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent border-0">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-speedometer text-info me-2"></i>
                                    Processing Performance
                                </h5>
                                <small class="text-muted">Average processing time per stage</small>
                            </div>
                            <div class="card-body">
                                <canvas id="processingChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Approvals Alert -->
                <?php if ($kpis['pending_approvals'] > 0): ?>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-warning border-0" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="alert-heading mb-1">Action Required</h5>
                                    <p class="mb-2">You have <strong><?= $kpis['pending_approvals'] ?> surat(s)</strong> waiting for approval.</p>
                                    <a href="<?= base_url('surat?status=SUBMITTED,APPROVED_L1,APPROVED_L2') ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-arrow-right me-1"></i>Review Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Monthly Trends Chart
        const monthlyData = <?= json_encode($monthly_trends) ?>;
        const monthlyLabels = monthlyData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
        });
        
        new Chart(document.getElementById('monthlyTrendsChart'), {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Created',
                    data: monthlyData.map(item => item.total_created),
                    borderColor: '#6c5ce7',
                    backgroundColor: 'rgba(108, 92, 231, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#6c5ce7',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }, {
                    label: 'Completed',
                    data: monthlyData.map(item => item.completed),
                    borderColor: '#00b894',
                    backgroundColor: 'rgba(0, 184, 148, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#00b894',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Workflow Status Pie Chart
        const workflowData = <?= json_encode($workflow_stats) ?>;
        const statusLabels = workflowData.map(item => item.status.replace('_', ' '));
        const statusCounts = workflowData.map(item => item.count);
        
        new Chart(document.getElementById('workflowStatusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: [
                        '#00b894', // Approved/Completed - Green
                        '#74b9ff', // Submitted/In Progress - Blue
                        '#fdcb6e', // Pending/Waiting - Yellow
                        '#e17055', // Urgent/High Priority - Orange
                        '#6c5ce7', // Draft/New - Purple
                        '#fd79a8', // Review/Revision - Pink
                        '#636e72', // Cancelled/Rejected - Gray
                        '#a29bfe'  // Other - Light Purple
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Processing Performance Bar Chart  
        new Chart(document.getElementById('processingChart'), {
            type: 'bar',
            data: {
                labels: statusLabels,
                datasets: [{
                    label: 'Avg Days',
                    data: workflowData.map(item => parseFloat(item.avg_days || 0).toFixed(1)),
                    backgroundColor: 'rgba(116, 185, 255, 0.7)',
                    borderColor: '#74b9ff',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
    </script>

    <style>
        /* Enhanced KPI Grid Layout */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .kpi-card-wrapper {
            min-height: 140px;
        }
        
        .kpi-card {
            transition: all 0.3s ease;
            backdrop-filter: blur(15px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-height: 140px;
            cursor: pointer;
        }
        
        .kpi-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
        }
        
        .kpi-title {
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .kpi-value {
            font-size: 2.2rem;
            line-height: 1;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .kpi-subtitle {
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .kpi-icon {
            font-size: 2.8rem;
            opacity: 0.3;
            transition: all 0.3s ease;
        }
        
        .kpi-card:hover .kpi-icon {
            opacity: 0.5;
            transform: scale(1.1);
        }
        
        /* Enhanced Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            align-items: stretch;
        }
        
        .chart-card {
            backdrop-filter: blur(15px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        /* Enhanced Performance Grid */
        .performance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            align-items: stretch;
        }
        
        .performance-card {
            backdrop-filter: blur(15px);
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .performance-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        /* Enhanced Typography */
        .section-title {
            font-weight: 600;
            font-size: 1.05rem;
            color: #2d3436;
        }
        
        /* Enhanced Table Styling */
        .analytics-table {
            font-size: 0.95rem;
        }
        
        .analytics-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-top: none;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
        }
        
        .analytics-row {
            transition: all 0.2s ease;
        }
        
        .analytics-row:nth-child(even) {
            background-color: rgba(248, 249, 250, 0.5);
        }
        
        .analytics-row:hover {
            background-color: rgba(108, 92, 231, 0.05);
            transform: scale(1.01);
        }
        
        .analytics-cell {
            padding: 0.75rem;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.05);
        }
        
        .performer-rank {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        /* Enhanced Badge Styling */
        .role-badge {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            border: none;
            border-radius: 9999px;
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .actions-badge {
            background: linear-gradient(135deg, #00b894, #00cec9);
            border: none;
            border-radius: 9999px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        /* Enhanced Button Styling */
        .export-btn {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            border: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .export-btn:hover {
            background: linear-gradient(135deg, #5f4fcf, #8c7ae6);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.3);
        }
        
        .analytics-dropdown {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .analytics-dropdown:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Enhanced Alert Styling */
        .alert {
            border-radius: 1rem;
            border: none;
            backdrop-filter: blur(10px);
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .kpi-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 0.75rem;
            }
            
            .charts-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .performance-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            
            .kpi-card {
                min-height: 120px;
            }
            
            .kpi-value {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</body>
</html>