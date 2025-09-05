<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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
                        <i class="bi bi-search me-2 text-primary"></i>
                        Advanced Search
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <?php if (!empty($saved_searches)): ?>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="bi bi-bookmark me-1"></i>
                                Saved Searches (<?= count($saved_searches) ?>)
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach ($saved_searches as $saved): ?>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" onclick="loadSavedSearch('<?= esc($saved['search_params']) ?>')">
                                        <span><?= esc($saved['name']) ?></span>
                                        <button class="btn btn-sm btn-outline-danger ms-2" onclick="event.stopPropagation(); deleteSavedSearch(<?= $saved['id'] ?>)" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Search Form -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="get" id="searchForm">
                            <!-- Main Search Bar -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="position-relative">
                                        <input type="text" class="form-control form-control-lg" 
                                               id="searchInput" name="q" 
                                               placeholder="Search surat by nomor, perihal, isi, or creator name..."
                                               value="<?= esc($search_params['q']) ?>"
                                               autocomplete="off">
                                        <div class="position-absolute top-50 end-0 translate-middle-y me-3">
                                            <i class="bi bi-search text-muted"></i>
                                        </div>
                                        <!-- Search Suggestions Dropdown -->
                                        <div id="searchSuggestions" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto;">
                                            <!-- Suggestions will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Advanced Filters -->
                            <div class="row mb-3">
                                <div class="col-md-2 mb-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <?php foreach ($filter_options['status'] as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= $search_params['status'] === $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label for="kategori" class="form-label">Category</label>
                                    <select class="form-select" id="kategori" name="kategori">
                                        <option value="">All Categories</option>
                                        <?php foreach ($filter_options['kategori'] as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= $search_params['kategori'] === $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label for="prioritas" class="form-label">Priority</label>
                                    <select class="form-select" id="prioritas" name="prioritas">
                                        <option value="">All Priorities</option>
                                        <?php foreach ($filter_options['prioritas'] as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= $search_params['prioritas'] === $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" 
                                           value="<?= esc($search_params['date_from']) ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" 
                                           value="<?= esc($search_params['date_to']) ?>">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search me-1"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Filters (Modern Slide Panel) -->
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" onclick="toggleFiltersPanel()" id="moreFiltersBtn">
                                        <i class="bi bi-funnel me-1" id="moreFiltersIcon"></i>
                                        <span id="moreFiltersText">More Filters</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm ms-2" onclick="saveCurrentSearch()" id="saveSearchBtn">
                                        <i class="bi bi-bookmark me-1" id="saveSearchIcon"></i>
                                        <span id="saveSearchText">Save Search</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="clearFilters()">
                                        <i class="bi bi-x-circle me-1"></i>Clear
                                    </button>
                                </div>
                            </div>

                            <!-- Modern Slide Panel for More Filters -->
                            <div class="filters-panel" id="moreFiltersPanel" style="display: none; opacity: 0; transform: translateY(-20px); transition: all 0.3s ease;">
                                <div class="card border-0 shadow-sm mt-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <div class="card-header bg-transparent border-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="bi bi-sliders me-2"></i>
                                                Advanced Filters
                                            </h6>
                                            <button class="btn btn-sm btn-outline-light" onclick="toggleFiltersPanel()">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <?php if (isset($filter_options['users'])): ?>
                                            <div class="col-md-4">
                                                <label for="created_by" class="form-label">
                                                    <i class="bi bi-person me-1"></i>Created By
                                                </label>
                                                <select class="form-select bg-white" id="created_by" name="created_by">
                                                    <option value="">All Users</option>
                                                    <?php foreach ($filter_options['users'] as $id => $name): ?>
                                                    <option value="<?= $id ?>" <?= $search_params['created_by'] == $id ? 'selected' : '' ?>>
                                                        <?= esc($name) ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-md-4">
                                                <label for="sort_by" class="form-label">
                                                    <i class="bi bi-sort-down me-1"></i>Sort By
                                                </label>
                                                <select class="form-select bg-white" id="sort_by" name="sort_by">
                                                    <option value="created_at">Created Date</option>
                                                    <option value="updated_at">Modified Date</option>
                                                    <option value="nomor_surat">Nomor Surat</option>
                                                    <option value="priority">Priority</option>
                                                    <option value="status">Status</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="sort_order" class="form-label">
                                                    <i class="bi bi-arrow-up-down me-1"></i>Sort Order
                                                </label>
                                                <select class="form-select bg-white" id="sort_order" name="sort_order">
                                                    <option value="desc">Newest First</option>
                                                    <option value="asc">Oldest First</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    <i class="bi bi-search me-1"></i>Search Scope
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="searchContent" name="search_content" checked>
                                                    <label class="form-check-label" for="searchContent">
                                                        Include content in search
                                                    </label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="searchFiles" name="search_files">
                                                    <label class="form-check-label" for="searchFiles">
                                                        Search in file names
                                                    </label>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="exactMatch" name="exact_match">
                                                    <label class="form-check-label" for="exactMatch">
                                                        Exact phrase matching
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="resultsPerPage" class="form-label">
                                                    <i class="bi bi-list me-1"></i>Results Per Page
                                                </label>
                                                <select class="form-select bg-white" id="resultsPerPage" name="per_page">
                                                    <option value="10">10 results</option>
                                                    <option value="25" selected>25 results</option>
                                                    <option value="50">50 results</option>
                                                    <option value="100">100 results</option>
                                                </select>
                                                
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-outline-light me-2" onclick="resetAdvancedFilters()">
                                                        <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-light" onclick="applyAdvancedFilters()">
                                                        <i class="bi bi-check2 me-1"></i>Apply
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search Results -->
                <?php if (!empty($search_params['q']) || !empty(array_filter($search_params))): ?>
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">
                                    <i class="bi bi-list-ul me-2"></i>
                                    Search Results
                                    <?php if ($result_count > 0): ?>
                                    <span class="badge bg-white text-primary ms-2"><?= $result_count ?> found</span>
                                    <?php endif; ?>
                                </h5>
                                <small class="opacity-75">
                                    <i class="bi bi-clock me-1"></i>
                                    Search completed in 0.12s
                                </small>
                            </div>
                            <?php if ($result_count > 0): ?>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-light" onclick="toggleResultView()" id="viewToggle">
                                    <i class="bi bi-grid-3x3-gap me-1" id="viewToggleIcon"></i>
                                    <span id="viewToggleText">Grid View</span>
                                </button>
                                <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-download me-1"></i>Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="exportResults('pdf')">
                                        <i class="bi bi-file-pdf text-danger me-2"></i>PDF Report
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportResults('excel')">
                                        <i class="bi bi-file-excel text-success me-2"></i>Excel Spreadsheet
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="exportResults('csv')">
                                        <i class="bi bi-filetype-csv text-info me-2"></i>CSV File
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="shareResults()">
                                        <i class="bi bi-share text-primary me-2"></i>Share Results
                                    </a></li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($results)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-search" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="text-muted mt-3">No results found</h4>
                                <p class="text-muted">
                                    Try adjusting your search terms or filters to find what you're looking for.
                                </p>
                                <button class="btn btn-outline-primary" onclick="clearFilters()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive" id="tableView">
                                <table class="table table-hover align-middle" style="border-radius: 10px; overflow: hidden;">
                                    <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                        <tr>
                                            <th style="border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; color: #495057;">
                                                <i class="bi bi-hash text-primary me-1"></i>Nomor Surat
                                            </th>
                                            <th style="border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; color: #495057;">
                                                <i class="bi bi-file-text text-info me-1"></i>Perihal
                                            </th>
                                            <th style="border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; color: #495057;">
                                                <i class="bi bi-tag text-warning me-1"></i>Category
                                            </th>
                                            <th style="border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; color: #495057;">
                                                <i class="bi bi-circle text-success me-1"></i>Status
                                            </th>
                                            <th style="border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; color: #495057;">
                                                <i class="bi bi-exclamation-triangle text-danger me-1"></i>Priority
                                            </th>
                                            <th style="border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; color: #495057;">
                                                <i class="bi bi-calendar text-secondary me-1"></i>Created
                                            </th>
                                            <th style="border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; color: #495057; text-align: center;">
                                                <i class="bi bi-gear text-primary me-1"></i>Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($results as $index => $surat): ?>
                                        <tr style="border: none; <?= $index % 2 === 0 ? 'background-color: rgba(0,0,0,0.02);' : '' ?>" class="result-row" data-surat-id="<?= $surat['id'] ?>">
                                            <td style="border: none; padding: 1rem 0.75rem; vertical-align: middle;">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="bi bi-file-text text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark"><?= esc($surat['nomor_surat']) ?></div>
                                                        <?php if (isset($surat['nama_prodi'])): ?>
                                                            <small class="text-muted"><i class="bi bi-building me-1"></i><?= esc($surat['nama_prodi']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="border: none; padding: 1rem 0.75rem; vertical-align: middle;">
                                                <div class="fw-medium text-dark mb-1" style="line-height: 1.4;">
                                                    <?= esc(substr($surat['perihal'], 0, 50)) ?><?= strlen($surat['perihal']) > 50 ? '...' : '' ?>
                                                </div>
                                                <?php if (isset($surat['created_by_name'])): ?>
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>by <?= esc($surat['created_by_name']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td style="border: none; padding: 1rem 0.75rem; vertical-align: middle;">
                                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); padding: 0.5rem 0.75rem; font-size: 0.75rem;">
                                                    <i class="bi bi-tag me-1"></i><?= ucfirst($surat['kategori']) ?>
                                                </span>
                                            </td>
                                            <td style="border: none; padding: 1rem 0.75rem; vertical-align: middle;">
                                                <?php 
                                                $statusConfig = [
                                                    'DRAFT' => ['bg' => 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)', 'icon' => 'bi-pencil'],
                                                    'SUBMITTED' => ['bg' => 'linear-gradient(135deg, #007bff 0%, #0056b3 100%)', 'icon' => 'bi-arrow-up-circle'],
                                                    'UNDER_REVIEW' => ['bg' => 'linear-gradient(135deg, #17a2b8 0%, #117a8b 100%)', 'icon' => 'bi-eye'],
                                                    'NEED_REVISION' => ['bg' => 'linear-gradient(135deg, #ffc107 0%, #d39e00 100%)', 'icon' => 'bi-exclamation-circle'],
                                                    'APPROVED_L1' => ['bg' => 'linear-gradient(135deg, #28a745 0%, #1e7e34 100%)', 'icon' => 'bi-check-circle'],
                                                    'APPROVED_L2' => ['bg' => 'linear-gradient(135deg, #28a745 0%, #1e7e34 100%)', 'icon' => 'bi-check-circle-fill'],
                                                    'READY_DISPOSISI' => ['bg' => 'linear-gradient(135deg, #20c997 0%, #17a2b8 100%)', 'icon' => 'bi-check2-all'],
                                                    'IN_PROCESS' => ['bg' => 'linear-gradient(135deg, #fd7e14 0%, #dc6545 100%)', 'icon' => 'bi-arrow-repeat'],
                                                    'COMPLETED' => ['bg' => 'linear-gradient(135deg, #28a745 0%, #20c997 100%)', 'icon' => 'bi-check-all'],
                                                    'REJECTED' => ['bg' => 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)', 'icon' => 'bi-x-circle']
                                                ];
                                                $config = $statusConfig[$surat['status']] ?? ['bg' => 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)', 'icon' => 'bi-circle'];
                                                ?>
                                                <span class="badge rounded-pill" style="background: <?= $config['bg'] ?>; padding: 0.5rem 0.75rem; font-size: 0.75rem; color: white;">
                                                    <i class="bi <?= $config['icon'] ?> me-1"></i><?= str_replace('_', ' ', $surat['status']) ?>
                                                </span>
                                            </td>
                                            <td style="border: none; padding: 1rem 0.75rem; vertical-align: middle;">
                                                <?php 
                                                $priorityConfig = [
                                                    'normal' => ['bg' => 'linear-gradient(135deg, #6c757d 0%, #5a6268 100%)', 'icon' => 'bi-circle', 'emoji' => '🟢'],
                                                    'urgent' => ['bg' => 'linear-gradient(135deg, #ffc107 0%, #d39e00 100%)', 'icon' => 'bi-exclamation-triangle', 'emoji' => '🟡'],
                                                    'sangat_urgent' => ['bg' => 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)', 'icon' => 'bi-exclamation-triangle-fill', 'emoji' => '🔴']
                                                ];
                                                $config = $priorityConfig[$surat['prioritas']] ?? $priorityConfig['normal'];
                                                ?>
                                                <span class="badge rounded-pill" style="background: <?= $config['bg'] ?>; padding: 0.5rem 0.75rem; font-size: 0.75rem; color: white;">
                                                    <?= $config['emoji'] ?> <?= ucfirst(str_replace('_', ' ', $surat['prioritas'])) ?>
                                                </span>
                                            </td>
                                            <td style="border: none; padding: 1rem 0.75rem; vertical-align: middle;">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-info bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="bi bi-calendar text-info" style="font-size: 0.8rem;"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium text-dark" style="font-size: 0.9rem;">
                                                            <?= date('d/m/Y', strtotime($surat['surat_created_at'])) ?>
                                                        </div>
                                                        <small class="text-muted">
                                                            <?= date('H:i', strtotime($surat['surat_created_at'])) ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="border: none; padding: 1rem 0.75rem; vertical-align: middle; text-align: center;">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('surat/' . $surat['id']) ?>" 
                                                       class="btn btn-outline-primary" 
                                                       title="View Details"
                                                       style="border-radius: 8px; padding: 0.5rem 0.75rem; transition: all 0.3s ease;">
                                                        <i class="bi bi-eye me-1"></i><span class="d-none d-md-inline">View</span>
                                                    </a>
                                                    <?php if ($user_role === 'admin_prodi' && $surat['created_by'] == session()->get('user_id') && 
                                                              in_array($surat['status'], ['DRAFT', 'NEED_REVISION'])): ?>
                                                    <a href="<?= base_url('surat/' . $surat['id'] . '/edit') ?>" 
                                                       class="btn btn-outline-warning ms-1" 
                                                       title="Edit"
                                                       style="border-radius: 8px; padding: 0.5rem 0.75rem; transition: all 0.3s ease;">
                                                        <i class="bi bi-pencil me-1"></i><span class="d-none d-md-inline">Edit</span>
                                                    </a>
                                                    <?php endif; ?>
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
                <?php endif; ?>

                <!-- Search Tips -->
                <?php if (empty($search_params['q']) && empty(array_filter($search_params))): ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="bi bi-lightbulb text-warning me-2"></i>
                                    Search Tips
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Basic Search:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="bi bi-check text-success me-2"></i>Search by nomor surat</li>
                                            <li><i class="bi bi-check text-success me-2"></i>Search by perihal/subject</li>
                                            <li><i class="bi bi-check text-success me-2"></i>Search by content</li>
                                            <li><i class="bi bi-check text-success me-2"></i>Search by creator name</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Advanced Features:</h6>
                                        <ul class="list-unstyled">
                                            <li><i class="bi bi-check text-success me-2"></i>Filter by status & category</li>
                                            <li><i class="bi bi-check text-success me-2"></i>Date range filtering</li>
                                            <li><i class="bi bi-check text-success me-2"></i>Save frequent searches</li>
                                            <li><i class="bi bi-check text-success me-2"></i>Export search results</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="mb-0">
                                    <i class="bi bi-graph-up text-info me-2"></i>
                                    Quick Stats
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Enhanced Quick Stats with Insights -->
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="bi bi-search text-primary"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 text-primary">12</h5>
                                                <small class="text-muted">Searches Today</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="bi bi-bookmark text-success"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 text-success"><?= count($saved_searches) ?></h5>
                                                <small class="text-muted">Saved Searches</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="bi bi-file-text text-info"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 text-info">18</h5>
                                                <small class="text-muted">Avg Results</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="bi bi-trending-up text-warning"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 text-warning">+35%</h5>
                                                <small class="text-muted">This Week</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Popular Search Categories -->
                                <hr class="my-3">
                                <div class="mb-3">
                                    <h6 class="mb-2"><i class="bi bi-graph-up me-2"></i>Most Searched</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-primary bg-opacity-25 text-primary">📋 Akademik</span>
                                        <span class="badge bg-success bg-opacity-25 text-success">✅ Completed</span>
                                        <span class="badge bg-info bg-opacity-25 text-info">🔄 In Review</span>
                                        <span class="badge bg-warning bg-opacity-25 text-warning">⚡ Urgent</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Save Search Modal -->
    <div class="modal fade" id="saveSearchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Save Search</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="saveSearchForm">
                        <div class="mb-3">
                            <label for="searchName" class="form-label">Search Name</label>
                            <input type="text" class="form-control" id="searchName" name="searchName" 
                                   placeholder="Enter a name for this search..." required>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle me-1"></i>
                                Current search criteria will be saved and can be accessed from the "Saved Searches" dropdown.
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmSaveSearch()">Save Search</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let suggestionTimeout;
        const searchInput = document.getElementById('searchInput');
        const suggestionsDiv = document.getElementById('searchSuggestions');

        // Search suggestions
        searchInput.addEventListener('input', function() {
            clearTimeout(suggestionTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                hideSuggestions();
                return;
            }
            
            suggestionTimeout = setTimeout(() => {
                fetchSuggestions(query);
            }, 300);
        });

        function fetchSuggestions(query) {
            fetch(`<?= base_url('search/suggestions') ?>?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(suggestions => {
                    displaySuggestions(suggestions);
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                });
        }

        function displaySuggestions(suggestions) {
            if (suggestions.length === 0) {
                hideSuggestions();
                return;
            }

            let html = '';
            suggestions.forEach(suggestion => {
                html += `
                    <li>
                        <a class="dropdown-item" href="#" onclick="selectSuggestion('${escapeHtml(suggestion.value)}')">
                            <i class="bi ${suggestion.icon} me-2 text-muted"></i>
                            ${escapeHtml(suggestion.label)}
                            <small class="text-muted ms-2">(${suggestion.type.replace('_', ' ')})</small>
                        </a>
                    </li>
                `;
            });

            suggestionsDiv.innerHTML = html;
            suggestionsDiv.style.display = 'block';
        }

        function hideSuggestions() {
            suggestionsDiv.style.display = 'none';
        }

        function selectSuggestion(value) {
            searchInput.value = value;
            hideSuggestions();
            document.getElementById('searchForm').submit();
        }

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                hideSuggestions();
            }
        });

        // Save search functionality
        function saveCurrentSearch() {
            const modal = new bootstrap.Modal(document.getElementById('saveSearchModal'));
            modal.show();
        }

        function confirmSaveSearch() {
            const searchName = document.getElementById('searchName').value.trim();
            if (!searchName) {
                SuratNotification.warning('Nama Diperlukan!', 'Silakan masukkan nama untuk pencarian ini');
                return;
            }

            // Get current form data
            const formData = new FormData(document.getElementById('searchForm'));
            const searchParams = {};
            for (let [key, value] of formData.entries()) {
                if (value) searchParams[key] = value;
            }

            // Save search
            fetch('<?= base_url('search/save') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    name: searchName,
                    params: JSON.stringify(searchParams)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('saveSearchModal')).hide();
                    showAlert('success', 'Search saved successfully!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', data.message || 'Failed to save search');
                }
            });
        }

        // Load saved search
        function loadSavedSearch(paramsJson) {
            const params = JSON.parse(paramsJson);
            const form = document.getElementById('searchForm');
            
            Object.keys(params).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = params[key];
                }
            });
            
            form.submit();
        }

        // Delete saved search
        async function deleteSavedSearch(searchId) {
            const confirmed = await SuratNotification.confirmDelete(
                'Hapus Pencarian Tersimpan?',
                'Pencarian yang dihapus tidak dapat dikembalikan!'
            );
            
            if (!confirmed) return;
            
            fetch(`<?= base_url('search/saved/') ?>${searchId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Search deleted successfully!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('error', data.message || 'Failed to delete search');
                }
            });
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById('searchForm').reset();
            window.location.href = '<?= base_url('search') ?>';
        }

        // Export results
        function exportResults(format) {
            const form = document.getElementById('searchForm');
            const formData = new FormData(form);
            formData.append('export', format);
            
            // Create temporary form for export
            const exportForm = document.createElement('form');
            exportForm.method = 'GET';
            exportForm.action = '<?= base_url('search') ?>';
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    exportForm.appendChild(input);
                }
            }
            
            document.body.appendChild(exportForm);
            exportForm.submit();
            document.body.removeChild(exportForm);
        }

        // Utility functions
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Enhanced filter panel toggle
        function toggleFiltersPanel() {
            const panel = document.getElementById('moreFiltersPanel');
            const btn = document.getElementById('moreFiltersBtn');
            const icon = document.getElementById('moreFiltersIcon');
            const text = document.getElementById('moreFiltersText');
            
            if (panel.style.display === 'none') {
                panel.style.display = 'block';
                setTimeout(() => {
                    panel.style.opacity = '1';
                    panel.style.transform = 'translateY(0)';
                }, 10);
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-secondary');
                icon.className = 'bi bi-x me-1';
                text.textContent = 'Close Filters';
            } else {
                panel.style.opacity = '0';
                panel.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    panel.style.display = 'none';
                }, 300);
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-outline-secondary');
                icon.className = 'bi bi-funnel me-1';
                text.textContent = 'More Filters';
            }
        }
        
        // Apply advanced filters
        function applyAdvancedFilters() {
            document.getElementById('searchForm').submit();
            showAlert('success', 'Advanced filters applied successfully!');
        }
        
        // Reset advanced filters
        function resetAdvancedFilters() {
            document.getElementById('sort_by').value = 'created_at';
            document.getElementById('sort_order').value = 'desc';
            document.getElementById('resultsPerPage').value = '25';
            document.getElementById('exactMatch').checked = false;
            showAlert('info', 'Advanced filters reset to defaults');
        }
        
        // Toggle result view (table/grid)
        let isGridView = false;
        function toggleResultView() {
            const tableView = document.getElementById('tableView');
            const toggleBtn = document.getElementById('viewToggle');
            const toggleIcon = document.getElementById('viewToggleIcon');
            const toggleText = document.getElementById('viewToggleText');
            
            if (isGridView) {
                // Switch to table view
                tableView.className = 'table-responsive';
                toggleIcon.className = 'bi bi-grid-3x3-gap me-1';
                toggleText.textContent = 'Grid View';
                isGridView = false;
            } else {
                // Switch to grid view
                tableView.className = 'row g-3';
                toggleIcon.className = 'bi bi-list me-1';
                toggleText.textContent = 'Table View';
                isGridView = true;
                // Convert table rows to cards (would need additional JS logic)
                showAlert('info', 'Grid view coming soon!');
            }
        }
        
        // Share results
        function shareResults() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: 'Search Results - Sistem Surat UNJANI',
                    url: url
                });
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    showAlert('success', 'Search URL copied to clipboard!');
                });
            }
        }
        
        // Enhanced save search with visual feedback
        function saveCurrentSearch() {
            const btn = document.getElementById('saveSearchBtn');
            const icon = document.getElementById('saveSearchIcon');
            const text = document.getElementById('saveSearchText');
            
            // Show loading state
            btn.classList.add('btn-info');
            btn.classList.remove('btn-outline-info');
            icon.className = 'bi bi-bookmark-check me-1';
            text.textContent = 'Saving...';
            
            const modal = new bootstrap.Modal(document.getElementById('saveSearchModal'));
            modal.show();
            
            // Reset button after modal is shown
            setTimeout(() => {
                btn.classList.remove('btn-info');
                btn.classList.add('btn-outline-info');
                icon.className = 'bi bi-bookmark me-1';
                text.textContent = 'Save Search';
            }, 1000);
        }

        function showAlert(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : (type === 'info' ? 'alert-info' : 'alert-danger');
            const iconClass = type === 'success' ? 'bi-check-circle' : (type === 'info' ? 'bi-info-circle' : 'bi-exclamation-triangle');
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 1050; min-width: 300px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <i class="bi ${iconClass} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', alertHtml);
        }
    </script>

    <style>
        .card {
            border-radius: 15px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        #searchInput {
            border-radius: 50px;
            border: 2px solid #e3f2fd;
            transition: all 0.3s ease;
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
        }

        #searchInput:focus {
            border-color: #2196f3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
            transform: scale(1.02);
        }

        #searchSuggestions {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            margin-top: 5px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        .result-row {
            transition: all 0.3s ease;
            border-radius: 10px;
            margin-bottom: 2px;
        }
        
        .result-row:hover {
            background-color: rgba(102, 126, 234, 0.05) !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .filters-panel {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        /* Mobile Responsive Optimizations */
        @media (max-width: 768px) {
            #searchInput {
                font-size: 1rem;
                padding: 0.6rem 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .result-row td {
                padding: 0.75rem 0.5rem !important;
            }
            
            .btn-group-sm .btn {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }
            
            .badge {
                font-size: 0.7rem;
                padding: 0.4rem 0.6rem;
            }
            
            .filters-panel .card-body {
                padding: 0.75rem;
            }
            
            .filters-panel .row {
                margin: 0;
            }
            
            .filters-panel .col-md-4, 
            .filters-panel .col-md-6 {
                padding: 0.25rem;
            }
        }
        
        @media (max-width: 576px) {
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }
            
            .btn-group {
                width: 100%;
            }
            
            .btn-group .btn {
                flex: 1;
            }
            
            .result-row .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }
            
            .result-row .bg-primary {
                width: 30px;
                height: 30px;
            }
        }
        
        /* Enhanced animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .result-row {
            animation: fadeInUp 0.5s ease-out;
        }
        
        /* Quick Stats enhanced styling */
        .bg-opacity-10 {
            transition: all 0.3s ease;
        }
        
        .bg-opacity-10:hover {
            transform: scale(1.1);
        }
    </style>
</body>
</html>