@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
    $statusLabels = [
        'pending' => 'New',
        'in_review' => 'In review',
        'schedule' => 'Scheduled',
        'closed' => 'Closed',
        'cancelled' => 'Cancelled',
    ];
    $delta = fn ($n) => ($n >= 0 ? '+' : '−') . abs($n);
@endphp

<x-app-layout>
    <x-slot name="header">{{ $greeting }}, {{ Auth::user()->name }}</x-slot>
    <x-slot name="subheader">{{ now()->format('l, F j') }}</x-slot>

    <section class="admin__stats">
        <article class="admin__stat">
            <span class="admin__stat-label">{{ __('New leads today') }}</span>
            <strong class="admin__stat-value">{{ $stats['leadsToday'] }}</strong>
            <span class="admin__stat-delta {{ $stats['leadsTodayDelta'] < 0 ? 'is-down' : '' }}">{{ $delta($stats['leadsTodayDelta']) }} {{ __('vs yesterday') }}</span>
        </article>
        <article class="admin__stat">
            <span class="admin__stat-label">{{ __('Leads this week') }}</span>
            <strong class="admin__stat-value">{{ $stats['leadsWeek'] }}</strong>
            <span class="admin__stat-delta {{ $stats['leadsWeekDelta'] < 0 ? 'is-down' : '' }}">{{ $delta($stats['leadsWeekDelta']) }} {{ __('vs last week') }}</span>
        </article>
        <article class="admin__stat">
            <span class="admin__stat-label">{{ __('Upcoming jobs') }}</span>
            <strong class="admin__stat-value">{{ $stats['upcomingJobs'] }}</strong>
            <span class="admin__stat-delta is-neutral">{{ $stats['jobsToday'] }} {{ __('today') }}</span>
        </article>
        <article class="admin__stat">
            <span class="admin__stat-label">{{ __('Estimated value (month)') }}</span>
            <strong class="admin__stat-value">${{ number_format($stats['monthEstimate'], 0) }}</strong>
            <span class="admin__stat-delta is-neutral">{{ __('Sum of lead estimates') }}</span>
        </article>
    </section>

    <div class="admin__grid">
        <section class="admin__card admin__card--wide">
            <header class="admin__card-head">
                <h2>{{ __('Lead pipeline') }}</h2>
                <a href="{{ route('dashboard.leads') }}">{{ __('View all') }} →</a>
            </header>

            @if ($recentQuotes->isEmpty())
                <p class="admin__empty">{{ __('No leads yet.') }}</p>
            @else
                <div class="admin__table-scroll">
                    <table class="admin__table">
                        <thead>
                            <tr>
                                <th>{{ __('Client') }}</th>
                                <th>{{ __('Move') }}</th>
                                <th>{{ __('Estimate') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentQuotes as $quote)
                                <tr>
                                    <td>
                                        <a href="{{ route('dashboard.leads') }}#quote-{{ $quote->id }}" class="admin__client">{{ $quote->name }}</a>
                                        <span class="admin__muted">{{ $quote->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <span class="admin__route">
                                            {{ collect([ucfirst((string) $quote->move_type), $quote->bedrooms ? $quote->bedrooms.' bd' : null])->filter()->implode(' · ') }}
                                        </span>
                                        <span class="admin__muted admin__truncate">{{ $quote->origin_address }} → {{ $quote->destination_address }}</span>
                                    </td>
                                    <td>{{ $quote->estimate_total ? '$'.number_format($quote->estimate_total, 0) : '—' }}</td>
                                    <td><span class="admin__chip admin__chip--{{ $quote->status }}">{{ $statusLabels[$quote->status] ?? $quote->status }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="admin__card">
            <header class="admin__card-head">
                <h2>{{ __('Next schedules') }}</h2>
                <a href="{{ route('dashboard.calendar') }}">{{ __('Open calendar') }} →</a>
            </header>

            <h3 class="admin__agenda-day">{{ __('Today') }}</h3>
            @forelse ($agendaToday as $event)
                @include('partials.agenda-item', ['event' => $event])
            @empty
                <p class="admin__empty admin__empty--compact">{{ __('Nothing scheduled today.') }}</p>
            @endforelse

            @foreach ($agendaNext as $date => $events)
                <h3 class="admin__agenda-day">{{ \Illuminate\Support\Carbon::parse($date)->isTomorrow() ? __('Tomorrow') : \Illuminate\Support\Carbon::parse($date)->format('l, M j') }}</h3>
                @foreach ($events as $event)
                    @include('partials.agenda-item', ['event' => $event])
                @endforeach
            @endforeach
        </section>
    </div>

    <section class="admin__card">
        <header class="admin__card-head">
            <h2>{{ __('New leads · last 6 weeks') }}</h2>
            <a href="{{ route('dashboard.leads') }}">{{ __('All leads') }} →</a>
        </header>
        <div class="admin__chart">
            @foreach ($weekly as $week)
                <div class="admin__bar {{ $week['current'] ? 'is-current' : '' }}">
                    <span class="admin__bar-value">{{ $week['count'] }}</span>
                    <span class="admin__bar-fill" style="height: {{ max(4, round($week['count'] / $weeklyMax * 100)) }}%"></span>
                    <span class="admin__bar-label">{{ $week['label'] }}</span>
                </div>
            @endforeach
        </div>
    </section>
</x-app-layout>
