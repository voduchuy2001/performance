<script src="js/resumable.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/papaparse.min.js"></script>
<script src="js/toastify-js.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
@if(session()->has('messages'))
    <script>
        Toastify({
            text: '❤ {{ session('messages') }}',
            duration: 5000,
            gravity: 'bottom',
            positionRight: true,
            backgroundColor: '#15803d',
        }).showToast();
    </script>
@endif
@yield('js')