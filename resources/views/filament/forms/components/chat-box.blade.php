<div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center gap-2 mb-2">
        <x-heroicon-m-sparkles class="w-5 h-5 text-primary-500" />
        <span class="font-bold text-sm uppercase tracking-wider text-gray-500">Respuesta de Gemini</span>
    </div>
    <div class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 italic">
        {{ $getState() ?? 'Esperando consulta... Escribe algo abajo y presiona el avión.' }}
    </div>
</div>