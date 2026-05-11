@php
    /** @var \App\Models\HistoriqueProfil $entree */
    $diff = is_array($entree->diff) ? $entree->diff : [];
    $hasDiff = ! empty($diff);
@endphp

<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500">Date</div>
            <div class="font-medium">{{ optional($entree->created_at)->format('d/m/Y H:i:s') }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500">Auteur</div>
            <div class="font-medium">{{ optional($entree->user)->name ?? 'Système' }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wide text-gray-500">Action</div>
            <div class="font-medium">{{ $entree->action }}</div>
        </div>
        @if ($entree->ip)
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500">IP</div>
                <div class="font-mono text-xs">{{ $entree->ip }}</div>
            </div>
        @endif
        @if ($entree->user_agent)
            <div class="md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500">User agent</div>
                <div class="font-mono text-xs truncate" title="{{ $entree->user_agent }}">{{ $entree->user_agent }}</div>
            </div>
        @endif
        @if ($entree->motif)
            <div class="md:col-span-3">
                <div class="text-xs uppercase tracking-wide text-gray-500">Motif</div>
                <div>{{ $entree->motif }}</div>
            </div>
        @endif
    </div>

    @if ($hasDiff)
        <div class="overflow-x-auto border rounded-md">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold">Champ</th>
                        <th class="px-3 py-2 text-left font-semibold text-red-600">Avant</th>
                        <th class="px-3 py-2 text-left font-semibold text-green-700">Après</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($diff as $champ => $change)
                        @php
                            $avant = is_array($change) ? ($change['avant'] ?? null) : null;
                            $apres = is_array($change) ? ($change['apres'] ?? null) : null;
                            $renderValue = static function ($value): string {
                                if (is_null($value)) {
                                    return '∅';
                                }
                                if (is_scalar($value)) {
                                    return (string) $value;
                                }
                                return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                            };
                        @endphp
                        <tr>
                            <td class="px-3 py-2 align-top font-mono text-xs">{{ $champ }}</td>
                            <td class="px-3 py-2 align-top text-red-700 whitespace-pre-wrap">{{ $renderValue($avant) }}</td>
                            <td class="px-3 py-2 align-top text-green-800 whitespace-pre-wrap">{{ $renderValue($apres) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-sm text-gray-500 italic">Aucun champ détaillé dans cette entrée.</div>
    @endif
</div>
