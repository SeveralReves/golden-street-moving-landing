<div class="admin__agenda-item">
    <span class="admin__agenda-time">{{ $event->start_at->format('g:i A') }}</span>
    <div>
        <strong>{{ $event->customer_name ?: $event->title }}</strong>
        <span class="admin__muted admin__truncate">{{ $event->origin_address }} → {{ $event->destination_address }}</span>
        <span class="admin__tag">{{ $event->crew_size }} {{ __('movers') }}</span>
    </div>
</div>
