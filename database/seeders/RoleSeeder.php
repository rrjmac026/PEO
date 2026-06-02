<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Juan Dela Cruz',              'email' => 'lolzka12345@gmail.com',                      'role' => 'contractor',           'position_title' => 'Contractor',          'department' => 'Construction', 'office' => 'Field Office'],
            ['name' => 'Maria Santos',                'email' => 'site.inspector@example.com',                  'role' => 'site_inspector',       'position_title' => 'Site Inspector',      'department' => 'Engineering',  'office' => 'Field Office'],
            ['name' => 'Carlos Reyes',                'email' => 'surveyor@example.com',                        'role' => 'surveyor',             'position_title' => 'Surveyor',            'department' => 'Engineering',  'office' => 'Field Office'],
            ['name' => 'Randy P. Diaz',               'email' => 'namisanchez123@gmail.com',                    'role' => 'mtqa',                 'position_title' => 'MTQA',                'department' => 'Quality Assurance', 'office' => 'Head Office'],
            ['name' => 'Leizel S. Galleposo',         'email' => 'resident.engineer@example.com',               'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Apolinario C. Pesisano',      'email' => 'resident.engineer.pesisano@example.com',      'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Josephine C. Fernandez',      'email' => 'resident.engineer.fernandez@example.com',     'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Jose Henry T. Sonsona',       'email' => 'resident.engineer.sonsona@example.com',       'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Ferdinand U. Sanico',         'email' => 'resident.engineer.fsanico@example.com',       'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Jewel Ann A. Calubia',        'email' => 'resident.engineer.calubia@example.com',       'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Jona Criezl L. De La Cruz',  'email' => 'resident.engineer.delacruz@example.com',      'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Alexander U. Sanico',         'email' => 'resident.engineer.asanico@example.com',       'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Romel B. Cadion',             'email' => 'resident.engineer.cadion@example.com',        'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Romeo R. Tianga',             'email' => 'resident.engineer.tianga@example.com',        'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Aaron Daniel P. Alvez',       'email' => 'macalutasreyramesesjudeiii@gmail.com',        'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Roman Kristopher M. Aranas',  'email' => 'resident.engineer.aranas@example.com',        'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Alexander P. Asok',           'email' => 'resident.engineer.asok@example.com',          'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Glen Mart Paluga',            'email' => 'resident.engineer.paluga@example.com',        'role' => 'resident_engineer',    'position_title' => 'Resident Engineer',   'department' => 'Engineering',  'office' => 'District Office'],
            ['name' => 'Grace D. Cañete',             'email' => 'obaobrosejane@gmail.com',                     'role' => 'engineeriv',           'position_title' => 'Engineer IV',         'department' => 'Engineering',  'office' => 'Head Office'],
            ['name' => 'Sanita E. Maiza',             'email' => 'engineeriii@example.com',                     'role' => 'engineeriii',          'position_title' => 'Engineer III',        'department' => 'Engineering',  'office' => 'Head Office'],
            ['name' => 'Atillana B. Mangubat',        'email' => 'engineeriii2@example.com',                    'role' => 'engineeriii',          'position_title' => 'Engineer III',        'department' => 'Engineering',  'office' => 'Head Office'],
            ['name' => 'Jose Pastor P. De La Cerna, III', 'email' => '1901102366@student.buksu.edu.ph',         'role' => 'provincial_engineer',  'position_title' => 'Provincial Engineer', 'department' => 'Engineering',  'office' => 'Provincial Office'],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password'), 'role' => $data['role']]
            );

            if (! $user->employee) {
                // Derive first/last name from full name
                $parts = explode(' ', $data['name'], 2);

                Employee::create([
                    'user_id'        => $user->id,
                    'first_name'     => $parts[0],
                    'last_name'      => $parts[1] ?? '',
                    'position_title' => $data['position_title'],
                    'department'     => $data['department'],
                    'office'         => $data['office'],
                ]);
            }
        }
    }
}