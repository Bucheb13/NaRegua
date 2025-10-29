<div {{ $attributes->merge(['class' => 'hidden']) }}>
    {{-- Modal placeholder para evitar erro de componente ausente no deploy --}}
    {{ $slot }}
</div>
