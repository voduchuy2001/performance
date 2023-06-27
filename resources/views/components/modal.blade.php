<div class="flex bg-gray-200 overflow-x-hidden overflow-y-auto ease-in-out z-10 absolute top-0 right-0 bottom-0 left-0 items-center hidden"
    id="modal">
    <div role="alert" class="container mx-auto w-11/12 md:w-2/3 max-w-lg">
        <div class="relative py-8 px-5 md:px-10 bg-white shadow-md rounded border border-gray-400">
            <button id="close-modal"
                class="close-modal cursor-pointer absolute top-0 right-0 mt-4 mr-5 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out rounded focus:ring-2 focus:outline-none focus:ring-gray-600"
                aria-label="close modal" role="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="20" height="20"
                    viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" />
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <h1 class="text-gray-800 font-lg font-bold tracking-normal leading-tight mb-4">CSV Input Form</h1>
            <div class="flex items-center justify-center w-full mb-2 mt-4">
                <label for="browse-file"
                    class="flex flex-col items-center justify-center w-full h-40 mb-6 border-2 border-gray-300 border-dashed rounded-sm cursor-pointer bg-gray-50">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        @include('components.icons.upload-icon')

                        <p class="mb-2 text-sm text-gray-500">
                            <span class="font-semibold">
                                Click here to upload CSV file
                            </span>
                        </p>

                        <p id="file-name"></p>
                        <p id="remove-file" class="text-red-700 cursor-pointer text-sm"></p>
                    </div>

                    <div style="display: none" class="flex progress items-center mb-6">
                        <div class="w-72 bg-gray-200 rounded-full">
                            <div class="progress-bar bg-indigo-700 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full"
                                role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <input accept=".csv" type="file" class="hidden" id="browse-file" required />
                </label>
            </div>

            <form action="{{route('import')}}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="flex items-center justify-center w-full">
                    <button type="submit" id="submit-button"
                        class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-700 transition duration-150 ease-in-out hover:bg-indigo-600 bg-indigo-700 rounded text-white px-8 py-2 text-xs sm:text-sm">
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('js')
<script src="js/upload.js"></script>
<script src="js/toggle.js"></script>
@endsection