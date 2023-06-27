let browseFile = $('#browse-file')
let csrfToken = $('meta[name="csrf-token"]').attr('content')
let progress = $('.progress')
let submitButton = $('#submit-button')
let closeButton = $('#close-modal')
let spinner = $('#spinner')
let input = $('#browse-file')
let removeFile = $('#remove-file')
let fileName = $('#file-name')

let resumable = new Resumable({
    target: 'http://127.0.0.1:8000',
    query: { _token: csrfToken },
    fileType: ['csv'],
    maxFiles: 1,
    chunkSize: 20 * 1024 * 1024,
    forceChunkSize: true,
    simultaneousUploads: 1,
    headers: {
        'Accept': 'application/json'
    },
    testChunks: false,
    throttleProgressCallbacks: 1,
})

resumable.assignBrowse(browseFile[0])

resumable.on('fileAdded', function (file) {
    showProgress()
    fileName.html(file.fileName)

    let data = file.file
    Papa.parse(data, {
        header: true,
        skipEmptyLines: false,
        complete: function (results) {
            let expectedHeaders = [
                'Name',
                'SKU',
                'Description',
                'Price',
                'Stock',
                'Status',
                'Type',
                'Vendor',
                'Created At'
            ];

            let csvHeaders = results.meta.fields;
            if (JSON.stringify(csvHeaders) === JSON.stringify(expectedHeaders)) {
                resumable.upload();
            } else {
                resumable.cancel();
                
                hideProgress()
                fileName.html('')
                input.attr('disabled', false)

                Toastify({
                    text: '⚠ Check headers of file .csv.',
                    duration: 6000,
                    gravity: 'bottom',
                    positionRight: true,
                    backgroundColor: '#b91c1c',
                }).showToast()
            }
        }
    });
})

resumable.on('fileProgress', function (file) {
    submitButton.attr('disabled', true)
    closeButton.attr('disabled', true)
    input.attr('disabled', true)
    updateProgress(Math.floor(file.progress() * 100))
})

resumable.on('fileSuccess', function (file) {
    hideProgress()
    submitButton.attr('disabled', false)
    closeButton.attr('disabled', false)
    input.attr('disabled', true)
    removeFile.html('Remove file')
    Toastify({
        text: '❤ Upload success.',
        duration: 6000,
        gravity: 'bottom',
        positionRight: true,
        backgroundColor: '#b91c1c',
    }).showToast()
})

resumable.on('fileError', function () {
    Toastify({
        text: '⚠ Error upload.',
        duration: 6000,
        gravity: 'bottom',
        positionRight: true,
        backgroundColor: '#b91c1c',
    }).showToast()
})

const showProgress = () => {
    progress.show()
}

const updateProgress = (value) => {
    progress.find('.progress-bar').css('width', `${value}%`)
    progress.find('.progress-bar').html(`${value}%`)
}

const hideProgress = () => {
    progress.hide();
}

submitButton.attr('disabled', true)

submitButton.click(function () {
    spinner.removeClass('hidden')
});

removeFile.click(function () {
    $.ajax({
        url: 'http://127.0.0.1:8000/remove-file',
        method: 'POST',
        dataType: 'JSON',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            if (response) {
                Toastify({
                    text: '❤ ' + response.message,
                    duration: 5000,
                    gravity: 'bottom',
                    positionRight: true,
                    backgroundColor: '#b91c1c',
                }).showToast();

                input.attr('disabled', false)
                removeFile.html('')
                fileName.html('')
                submitButton.attr('disabled', true)
            }
        },
        error: function (error) {
            if (error.responseJSON) {
                Toastify({
                    text: '⚠ ' + error.responseJSON.message,
                    duration: 6000,
                    gravity: 'bottom',
                    positionRight: true,
                    backgroundColor: '#b91c1c',
                }).showToast()
            }
        },
    })
})