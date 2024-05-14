<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>chatbot</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-sans antialiased bg-black text-white/50">
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <a href="#" class="text-white text-lg font-bold">Logo</a>
            </div>
            <div class="sm:block">
                <a href="/chatbot" class="text-gray-300 hover:text-white px-3 py-2">Home</a>
            </div>
        </div>
    </nav>
    <div class=" text-white  w-full p-2 md:p-8">
        @if (session('message'))
            <div class="alert text-white bg-green-500 text-center alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="text-white text-center md:text-2xl ">Here you can edit the details of the training data.</div>
        <div class="flex items-center justify-center  ">
            <form action="/update/{{ $item->id }}" method="POST"
                class="w-full md:w-3/4 bg-black text-white p-8 rounded-lg">
                @csrf
                @method('PUT')
                <label for="question" class="mb-2">Question:</label><br>
                <textarea name="question" id="question" cols="60" rows="3"
                    class="bg-black text-white border border-gray-300 rounded-md px-3 py-2 mb-4 w-full">{{ $item->question }}</textarea><br>
                <label for="response" class="mb-2">Response:</label><br>
                <textarea name="response" id="response" cols="60" rows="3"
                    class="bg-black text-white border border-gray-300 rounded-md px-3 py-2 mb-4 w-full">{{ $item->response }}</textarea><br>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update
                </button>
            </form>
        </div>
    </div>
</body>

</html>
