@if ($errors->any())
    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif
