<?php

namespace App\Enums;

// app/Enums/UserRole.php
enum Role: string
{
    case Admin              = 'admin';
    case Contractor         = 'contractor';
    case SiteInspector      = 'site_inspector';
    case Surveyor           = 'surveyor';
    case ResidentEngineer   = 'resident_engineer';
    case Mtqa               = 'mtqa';
    case EngineerIII        = 'engineeriii';
    case EngineerIV         = 'engineeriv';
    case ProvincialEngineer = 'provincial_engineer';

    // All roles that go through the reviewer routes
    public static function reviewerRoles(): array
    {
        return [
            self::SiteInspector->value,
            self::Surveyor->value,
            self::ResidentEngineer->value,
            self::Mtqa->value,
            self::EngineerIII->value,
            self::EngineerIV->value,
            self::ProvincialEngineer->value,
        ];
    }

    // Maps position title keywords → role
    public static function fromPositionTitle(?string $title): self
    {
        if (!$title) return self::Contractor;

        $lower = mb_strtolower(trim($title));

        return match(true) {
            str_contains($lower, 'provincial engineer') => self::ProvincialEngineer,
            str_contains($lower, 'engineer iv')         => self::EngineerIV,
            str_contains($lower, 'engineer iii')        => self::EngineerIII,
            str_contains($lower, 'resident engineer')   => self::ResidentEngineer,
            str_contains($lower, 'site inspector')      => self::SiteInspector,
            str_contains($lower, 'surveyor')            => self::Surveyor,
            str_contains($lower, 'mtqa')                => self::Mtqa,
            str_contains($lower, 'contractor')          => self::Contractor,
            default                                     => self::Contractor,
        };
    }
}