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
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this item?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        function editItem(id) {
            document.getElementById('edit-form-' + id).submit();
        }
    </script>
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
            <div class="alert text-center bg-green-500 text-white alert-success">
                {{ session('message') }}
            </div>
        @endif
        <div class="text-white text-center md:text-2xl ">This is the training data of the chatbot. Here you get the
            chance to refine the training data and add more</div>
        <div class="bg-black md-p-8 overflow-y-auto h-screen mx-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider max-w-xs">
                            Question
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider max-w-xs">
                            Response
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @foreach ($data as $item)
                        <tr class="bg-gray-800">
                            <td class="px-6 py-4 whitespace-wrap text-sm text-gray-300 max-w-xs">
                                <div class="max-h-24 overflow-y-auto">{{ $item->question }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-wrap text-sm text-gray-300 max-w-xs">
                                <div class="max-h-24 overflow-y-auto">{{ $item->response }}</div>
                            </td>
                            <td class="px-6 flex py-4 whitespace-wrap text-sm text-gray-300">
                                <button class="text-red-600 hover:text-red-900"
                                    onclick="confirmDelete({{ $item->id }})">
                                    Delete
                                </button>
                                <form id="delete-form-{{ $item->id }}" action="/delete/{{ $item->id }}"
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button class="text-blue-600 hover:text-blue-900 ml-2"
                                    onclick="editItem({{ $item->id }})">
                                    Edit
                                </button>
                                <form id="edit-form-{{ $item->id }}" action="edit/{{ $item->id }}"
                                    method="GET" style="display: none;">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="my-4">
            <a href="/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add more
                training data</a>
        </div>
    </div>
</body>

</html>
