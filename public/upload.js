let browseFile = $('#browse-file')
let csrfToken = $('meta[name="csrf-token"]').attr('content')
let progress = $('.progress')
let submitButton = $('#submit-button')
let closeButton = $('#close-modal')
let spinner = $('#spinner')

submitButton.attr('disabled', true)

submitButton.click(function(){
    spinner.removeClass('hidden')
});

let resumable = new Resumable({
    target: 'http://127.0.0.1:8000',
    query: { _token: csrfToken },
    fileType: ['csv'],
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

resumable.on('fileAdded', function () {
    showProgress()
    resumable.upload()
})

resumable.on('fileProgress', function (file) {
    submitButton.attr('disabled', true)
    closeButton.attr('disabled', true)
    updateProgress(Math.floor(file.progress() * 100))
})

resumable.on('fileSuccess', function (response) {
    submitButton.attr('disabled', false)
    closeButton.attr('disabled', false)
    console.log(response);
})

resumable.on('fileError', function (response) {
    console.log(response);
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