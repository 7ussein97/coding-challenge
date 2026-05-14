<div wire:poll.5000ms>
{{-- Leaderboard Table --}}
<div class="lb-card mb-4">
    <div class="table-responsive">
        <table class="table lb-table mb-0">
            <thead>
                <tr>
                    <th style="width:60px">Rank</th>
                    <th>Team</th>
                    <th style="width:120px" class="text-center">Solved</th>
                    <th>Last Solved</th>
                    <th style="width:160px">Last Solve Time</th>
                    <th style="width:100px" class="text-center">Attempts</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaderboard as $i => $entry)
                @php $rank = $i + 1; @endphp
                <tr class="{{ $rank === 1 ? 'top-row-1' : ($rank === 2 ? 'top-row-2' : ($rank === 3 ? 'top-row-3' : '')) }}">
                    <td>
                        <div class="rank-badge {{ $rank === 1 ? 'rank-1' : ($rank === 2 ? 'rank-2' : ($rank === 3 ? 'rank-3' : 'rank-other')) }}">
                            @if($rank === 1) <i class="fas fa-crown"></i>
                            @elseif($rank === 2) 2
                            @elseif($rank === 3) 3
                            @else {{ $rank }}
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="team-name">{{ $entry['name'] }}</div>
                        @if(isset($firstSolvers) && in_array($entry['name'], $firstSolvers))
                            <span class="first-solver-badge mt-1 d-inline-block">
                                <i class="fas fa-star me-1"></i>First solver
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($entry['solved_count'] > 0)
                            <span class="solved-count">{{ $entry['solved_count'] }}</span>
                            <span class="text-muted small"> / {{ $questions->count() }}</span>
                        @else
                            <span class="no-solve">0 / {{ $questions->count() }}</span>
                        @endif
                    </td>
                    <td class="text-muted small">
                        {{ $entry['last_solved_question'] ?? '—' }}
                    </td>
                    <td>
                        @if($entry['last_solve_time'])
                            <div class="last-time">{{ \Carbon\Carbon::parse($entry['last_solve_time'])->format('H:i:s') }}</div>
                            <div style="font-size:.7rem;color:#475569;">{{ \Carbon\Carbon::parse($entry['last_solve_time'])->format('M d, Y') }}</div>
                        @else
                            <span class="no-solve">—</span>
                        @endif
                    </td>
                    <td class="text-center text-muted small">{{ $entry['total_attempts'] }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-5" style="color:#475569;">
                    <i class="fas fa-users fa-2x d-block mb-2 opacity-25"></i>
                    No teams registered yet.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- First Solvers Panel --}}
@if(count($firstSolvers) > 0)
<div class="lb-card p-4">
    <h6 class="fw-bold mb-3" style="color:#fbbf24;">
        <i class="fas fa-star me-2"></i>First Solvers
    </h6>
    <div class="row g-2">
        @foreach($questions as $q)
            @if(isset($firstSolvers[$q->id]))
            <div class="col-auto">
                <div style="background:#2d3548;border-radius:8px;padding:8px 14px;font-size:.82rem;">
                    <span style="color:#64748b;">{{ $q->display_title }}:</span>
                    <strong style="color:#fbbf24;">{{ $firstSolvers[$q->id] }}</strong>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
@endif
</div>
