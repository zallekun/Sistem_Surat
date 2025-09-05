<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        Search Analytics
                    </h1>
                </div>

                <div class="row mb-4">
                    <!-- Top Searches -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="bi bi-trophy text-warning me-2"></i>
                                    Top Search Terms (Last 30 Days)
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($analytics['top_searches'])): ?>
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-search" style="font-size: 3rem; opacity: 0.3;"></i>
                                        <p class="mt-2 mb-0">No search data available yet</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Search Term</th>
                                                    <th>Search Count</th>
                                                    <th>Popularity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($analytics['top_searches'] as $index => $search): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <strong><?= esc($search['search_term']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary"><?= $search['search_count'] ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar" role="progressbar" 
                                                                 style="width: <?= ($search['search_count'] / $analytics['top_searches'][0]['search_count']) * 100 ?>%">
                                                                <?= round(($search['search_count'] / $analytics['top_searches'][0]['search_count']) * 100, 1) ?>%
                                                            </div>
                                                        </div>
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

                    <!-- Search Trends -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="bi bi-graph-up text-info me-2"></i>
                                    Search Trends
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="searchTrendsChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-search text-primary" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-primary"><?= array_sum(array_column($analytics['top_searches'], 'search_count')) ?></h4>
                                <small class="text-muted">Total Searches</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-list text-success" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-success"><?= count($analytics['top_searches']) ?></h4>
                                <small class="text-muted">Unique Terms</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-calendar text-info" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-info"><?= array_sum(array_column($analytics['search_trends'], 'daily_searches')) ?></h4>
                                <small class="text-muted">Searches This Month</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-0 shadow-sm">
                            <div class="card-body">
                                <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                                <h4 class="mt-2 mb-0 text-warning">
                                    <?= count($analytics['search_trends']) > 0 ? round(array_sum(array_column($analytics['search_trends'], 'daily_searches')) / count($analytics['search_trends']), 1) : 0 ?>
                                </h4>
                                <small class="text-muted">Daily Average</small>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Search Trends Chart
        const trendsData = <?= json_encode($analytics['search_trends']) ?>;
        const labels = trendsData.map(item => {
            const date = new Date(item.search_date);
            return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
        });
        const data = trendsData.map(item => item.daily_searches);
        
        new Chart(document.getElementById('searchTrendsChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Searches',
                    data: data,
                    borderColor: '#17a2b8',
                    backgroundColor: 'rgba(23, 162, 184, 0.1)',
                    tension: 0.4,
                    fill: true
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
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>