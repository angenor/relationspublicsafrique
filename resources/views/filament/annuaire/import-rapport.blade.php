<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
        <div><strong>Lues :</strong> {{ $rapport->lues }}</div>
        <div><strong>Créées :</strong> {{ $rapport->creees }}</div>
        <div><strong>Mises à jour :</strong> {{ $rapport->misesAJour }}</div>
        <div><strong>Ignorées :</strong> {{ $rapport->ignorees }}</div>
        <div><strong>Erreurs :</strong> {{ count($rapport->erreurs) }}</div>
    </div>

    @if (count($rapport->erreurs) > 0)
        <table class="min-w-full text-sm">
            <thead>
                <tr>
                    <th class="px-2 py-1 text-left">Ligne</th>
                    <th class="px-2 py-1 text-left">Colonne</th>
                    <th class="px-2 py-1 text-left">Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rapport->erreurs as $erreur)
                    <tr class="border-t">
                        <td class="px-2 py-1">{{ $erreur['ligne'] }}</td>
                        <td class="px-2 py-1">{{ $erreur['colonne'] ?? '—' }}</td>
                        <td class="px-2 py-1">{{ $erreur['message'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
