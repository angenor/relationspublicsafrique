<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Partenaire;
use App\Models\User;

/**
 * Accès au CRUD des partenaires réservé aux administrateurs (FR-024, R7).
 */
class PartenairePolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->type === 'admin';
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Partenaire $partenaire): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Partenaire $partenaire): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Partenaire $partenaire): bool
    {
        return $this->isAdmin($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
