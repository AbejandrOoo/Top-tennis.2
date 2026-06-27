@if($errors->any())
    <div class="mb-6 rounded-xl overflow-hidden"
         style="border:1px solid #fca5a5; background:#fef2f2;">
        <div class="flex items-center gap-2 px-4 py-2.5 border-b" style="border-color:#fca5a5; background:#fee2e2;">
            <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm font-bold text-red-700">
                {{ $errors->count() === 1 ? 'Hay un error en el formulario' : 'Hay '.$errors->count().' errores en el formulario' }}
            </p>
        </div>
        <ul class="px-4 py-3 space-y-1">
            @foreach($errors->all() as $error)
                <li class="flex items-start gap-2 text-sm text-red-700">
                    <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-red-400 shrink-0"></span>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif
