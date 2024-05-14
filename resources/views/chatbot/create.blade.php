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
<body class="font-sans antialiased dark:bg-black dark:text-white/50">
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
    <div class=" text-white bg-black w-full p-2 md:p-8">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="text-white text-center md:text-2xl ">Answer this question from your teammate. You response will be
            posted as a reply on pumble and both question and reponse will be stored as new training data.</div>
        <div class="flex items-center justify-center  ">
            <form action="/store" method="POST" class="w-full md:w-3/4 bg-black text-white p-8 rounded-lg">
                @csrf
                <label for="message_id" class="mb-2">Message ID:</label><br>
                <input type="text" id="message_id" readonly name="message_id" placeholder="Readonly field" value="{{ $messageId }}"
                    class="bg-black text-white border border-gray-300 rounded-md px-3 py-2 mb-4 w-full"><br>
                <label for="question" class="mb-2">Question:</label><br>
                <textarea name="question" id="question" cols="60" rows="3"
                    class="bg-black text-white border border-gray-300 rounded-md px-3 py-2 mb-4 w-full">{{ $question }}</textarea><br>
                <label for="response" class="mb-2">Response:</label><br>
                <textarea name="response" id="response" cols="60" rows="3"
                    class="bg-black text-white border border-gray-300 rounded-md px-3 py-2 mb-4 w-full"></textarea><br>
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>
