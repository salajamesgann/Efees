{{-- Payment Status Badge — shared between mobile cards and desktop table --}}
@props(['status' => null, 'size' => 'sm'])

@php
    $classes = $size === 'xs'
        ? 'px-2 py-0.5 text-[10px]'
        : 'px-2.5 py-0.5 text-xs';
@endphp

@if($status === 'approved' || $status === 'paid' || $status === null)
    <span class="inline-flex items-center gap-1 rounded-full font-bold bg-green-100 text-green-700 {{ $classes }}">
        <i class="fas fa-check-circle"></i> Paid
    </span>
@elseif($status === 'pending')
    <span class="inline-flex items-center gap-1 rounded-full font-bold bg-yellow-100 text-yellow-700 {{ $classes }}">
        <i class="fas fa-clock"></i> Pending
    </span>
@else
    <span class="inline-flex items-center gap-1 rounded-full font-bold bg-gray-100 text-gray-700 {{ $classes }}">
        {{ ucfirst($status ?? 'Unknown') }}
    </span>
@endif
