<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Name Processor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Main Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-text me-2"></i>
                            <h4 class="mb-0">CSV Name Processor</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Upload Form -->
                        <form id="uploadForm" action="/process" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <div class="drop-zone" id="dropZone">
                                    <i class="bi bi-cloud-upload fs-1 mb-2"></i>
                                    <h5>Drag & Drop your CSV file here</h5>
                                    <p class="text-muted mb-2">or</p>
                                    <label class="btn btn-outline-primary mb-3" for="csv_file">
                                        Choose File
                                    </label>
                                    <input type="file" class="d-none" id="csv_file" name="csv_file" accept=".csv"
                                        required>
                                    <div id="selectedFileName" class="text-muted small"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-gear me-2"></i>Process Names
                                </button>
                            </div>
                        </form>

                        <!-- Results -->
                        <div id="results" class="mt-4" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Processing Results</h5>
                                <button class="btn btn-sm btn-outline-secondary" id="exportBtn">
                                    <i class="bi bi-download me-1"></i>Export
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>First Name</th>
                                            <th>Initial</th>
                                            <th>Last Name</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsTable"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Spinner -->
                        <div id="loadingSpinner" class="text-center mt-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Processing your file...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const fileInput = document.getElementById('csv_file');
        const dropZone = document.getElementById('dropZone');
        const selectedFileName = document.getElementById('selectedFileName');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const resultsDiv = document.getElementById('results');
        const resultsTable = document.getElementById('resultsTable');
        const uploadForm = document.getElementById('uploadForm');
        const exportBtn = document.getElementById('exportBtn');

        fileInput.addEventListener('change', function() {
            if (this.files[0]) {
                selectedFileName.textContent = `Selected file: ${this.files[0].name}`;
                dropZone.style.borderColor = '#0d6efd';
            }
        });

        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#0d6efd';
            this.style.background = '#f1f3f5';
        });
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ccc';
            this.style.background = '#f8f9fa';
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            const file = e.dataTransfer.files[0];
            if (file && file.name.endsWith('.csv')) {
                fileInput.files = e.dataTransfer.files;
                selectedFileName.textContent = `Selected file: ${file.name}`;
                this.style.borderColor = '#0d6efd';
            } else {
                alert('Please upload a CSV file');
            }
            this.style.background = '#f8f9fa';
        });

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            loadingSpinner.style.display = 'block';
            resultsDiv.style.display = 'none';

            fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(async response => {
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(`Server error (${response.status}): ${text}`);
                    }
                    return response.json();
                })
                .then(data => {
                    resultsTable.innerHTML = '';

                    if (data && Array.isArray(data.people)) {
                        data.people.forEach(person => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                    <td>${person.title || '-'}</td>
                    <td>${person.first_name || '-'}</td>
                    <td>${person.initial || '-'}</td>
                    <td>${person.last_name || '-'}</td>
                `;
                            resultsTable.appendChild(row);
                        });

                        resultsDiv.style.display = 'block';
                    } else {
                        throw new Error('Invalid response format');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'An error occurred while processing the file.');
                })
                .finally(() => {
                    loadingSpinner.style.display = 'none';
                });
        });

        // export timeeee
        exportBtn.addEventListener('click', function() {
            const table = document.querySelector('table');
            const rows = Array.from(table.querySelectorAll('tr'));

            let csvContent = "data:text/csv;charset=utf-8,";

            rows.forEach(row => {
                const cells = Array.from(row.querySelectorAll('th, td'));
                const rowData = cells.map(cell => cell.textContent).join(',');
                csvContent += rowData + "\r\n";
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'processed_names.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        })
    </script>
</body>

</html>

<style>
    .drop-zone {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .drop-zone:hover {
        border-color: #0d6efd;
        background: #f1f3f5;
    }

    .result-card {
        transition: all 0.3s ease;
    }

    .result-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
