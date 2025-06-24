<!DOCTYPE html>
<html>
<head>
    <title>Verify Email</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4 text-center">Verify Your Email Address</h2>
            
            <div class="mb-4 text-sm text-gray-600">
                Thanks for updating your email! Please verify your new email address by clicking on the link
                we just sent to your email. If you didn't receive the email, we will gladly send you another.
            </div>

            @if (session('success'))
                <div class="mb-4 text-green-600">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <div class="mt-4 flex justify-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Resend Verification Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>