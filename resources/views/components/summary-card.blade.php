@props(['title', 'value', 'icon', 'color' => 'text-primary'])

<div class="card border-0 shadow-sm h-100">
    <div class="card-body d-flex align-items-center justify-content-between p-3">
        <div>
            <span class="text-muted small fw-bold text-uppercase d-block">{{ $title }}</span>
            <h3 class="fw-bold mb-0 mt-1">{{ $value }}</h3>
        </div>
        <div class="fs-1 {{ $color }}">
            <i class="bi bi-{{ $icon }}"></i>
        </div>
    </div>
</div>