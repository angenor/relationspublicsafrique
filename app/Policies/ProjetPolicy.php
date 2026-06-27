<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Projet;
use App\Models\User;

/**
 * Accès au CRUD des projets réservé aux administrateurs (FR-024).
 * Les éditeurs accèdent au panneau Filament (annuaire) mais pas aux projets.
 */
class ProjetPolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->type === 'admin';
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Projet $projet): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Projet $projet): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Projet $projet): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function restore(User $user, Projet $projet): bool
    {
        return $this->isAdmin($user);
    }

    public function forceDelete(User $user, Projet $projet): bool
    {
        return $this->isAdmin($user);
    }
}
