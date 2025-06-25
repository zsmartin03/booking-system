<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500/80 to-red-600/80 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-red-500 hover:to-red-600 active:from-red-600/90 active:to-red-700/90 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 backdrop-blur-sm']) }}>
    {{ $slot }}
</button>
