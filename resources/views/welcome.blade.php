<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>

    <script src="jquery.min.js"></script>
    <script src="resumable.min.js"></script>
</head>

<body>
    <div class="container pt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h5>Upload File</h5>
                    </div>

                    <div class="card-body">
                        <div id="upload-container" class="text-center">
                            <button id="browseFile" class="btn btn-primary">Brows File</button>
                        </div>
                        <div style="display: none" class="progress mt-3" style="height: 25px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"
                                style="width: 75%; height: 100%">75%</div>
                        </div>
                    </div>
                </div>

                <form action="{{route('import')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <button type="submit">okok</button>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        let browseFile = $('#browseFile');
        let resumable = new Resumable({
            target: '{{ route('upload') }}',
            query:{_token:'{{ csrf_token() }}'} ,
            fileType: ['csv'],
            chunkSize: 20*1024*1024,
            forceChunkSize: true,
            simultaneousUploads: 1,
            headers: {
                'Accept' : 'application/json'
            },
            testChunks: false,
            throttleProgressCallbacks: 1,
        });
    
        resumable.assignBrowse(browseFile[0]);
    
        resumable.on('fileAdded', function (file) { // trigger when file picked
            showProgress();
            resumable.upload() // to actually start uploading.
        });
    
        resumable.on('fileProgress', function (file) { // trigger when file progress update
            updateProgress(Math.floor(file.progress() * 100));
        });
    
        resumable.on('fileSuccess', function (file, response) { // trigger when file upload complete
            response = JSON.parse(response)
        });
    
        resumable.on('fileError', function (file, response) { // trigger when there is any error
            alert('file uploading error.')
        });
    
    
        let progress = $('.progress');
        function showProgress() {
            progress.find('.progress-bar').css('width', '0%');
            progress.find('.progress-bar').html('0%');
            progress.find('.progress-bar').removeClass('bg-success');
            progress.show();
        }
    
        function updateProgress(value) {
            progress.find('.progress-bar').css('width', `${value}%`)
            progress.find('.progress-bar').html(`${value}%`)
        }
    
        function hideProgress() {
            progress.hide();
        }
    </script>
</body>

</html>