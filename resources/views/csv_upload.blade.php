@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="card shadow-lg border-primary">
                <div class="card-header bg-primary text-white"><strong>Upload CSV File</strong></div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form id="uploadForm" action="{{ route('csv.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="file" name="csv_file" id="csv_file" class="form-control" required accept=".csv">
                            @error('csv_file')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Upload</button>
                    </form>
                    <div id="progressSection" class="my-3 d-none">
                        <div class="progress">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar"
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div class="card shadow border-info mb-4">
                <div class="card-header bg-info text-white"><strong>CSV File Upload Log</strong></div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle" id="uploadLogTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Time Upload</th>
                                    <th>File Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($uploads as $upload)
                                    <tr>
                                        <td>{{ $upload->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>{{ $upload->file_name }}</td>
                                        <td>{{ $upload->status }}</td>
                                        <td>
                                            <a class="btn btn-success btn-sm" href="{{ route('csv.download', $upload->id) }}">
                                                <i class="bi bi-download"></i> Download
                                            </a>
                                            <form action="{{ route('csv.delete', $upload->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No uploads yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        var form = this;
        var formData = new FormData(form);
        var progressSection = document.getElementById('progressSection');
        var progressBar = progressSection.querySelector('.progress-bar');
        progressSection.classList.remove('d-none');
        e.preventDefault();
        var xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                var percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressBar.innerText = percentComplete + '%';
                progressBar.setAttribute('aria-valuenow', percentComplete);
            }
        };
        xhr.onload = function () {
            progressBar.style.width = '100%';
            progressBar.innerText = 'Completed!';
            setTimeout(function(){ window.location.reload(); }, 700);
        };
        xhr.onerror = function() {
            progressSection.classList.add('d-none');
            alert('Upload error. Please try again.');
        };
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('X-CSRF-TOKEN', form.querySelector('input[name=_token]').value);
        xhr.send(formData);
    });
</script>
@endsection
