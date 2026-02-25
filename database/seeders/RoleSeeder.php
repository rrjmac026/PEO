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
            // User (Contractor)
            [
                'name'     => 'Juan Dela Cruz',
                'email'    => 'user@example.com',
                'role'     => 'contractor',
                'employee' => [
                    'employee_number' => 'EMP-002',
                    'position'        => 'Contractor',
                    'department'      => 'Construction',
                    'phone'           => '09000000002',
                    'office'          => 'Field Office',
                ],
            ],

            // Site Inspector
            [
                'name'     => 'Maria Santos',
                'email'    => 'site.inspector@example.com',
                'role'     => 'site_inspector',
                'employee' => [
                    'employee_number' => 'EMP-003',
                    'position'        => 'Site Inspector',
                    'department'      => 'Engineering',
                    'phone'           => '09000000003',
                    'office'          => 'Field Office',
                ],
            ],

            // Surveyor
            [
                'name'     => 'Carlos Reyes',
                'email'    => 'surveyor@example.com',
                'role'     => 'surveyor',
                'employee' => [
                    'employee_number' => 'EMP-004',
                    'position'        => 'Surveyor',
                    'department'      => 'Engineering',
                    'phone'           => '09000000004',
                    'office'          => 'Field Office',
                ],
            ],
            // MTQA
            [
                'name'     => 'Lucia Gomez',
                'email'    => 'mtqa@example.com',
                'role'     => 'mtqa',
                'employee' => [
                    'employee_number' => 'EMP-007',
                    'position'        => 'MTQA',
                    'department'      => 'Quality Assurance',
                    'phone'           => '09000000007',
                    'office'          => 'Head Office',
                ],
            ],

            // Resident Engineer
            [
                'name'     => 'Leizel S.Galleposo',
                'email'    => 'resident.engineer@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-005',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000005',
                    'office'          => 'District Office',
                ],
            ],
            
            //Engineer IV
            [
                'name'     => 'Randy Diaz',
                'email'    => 'engineeriv@example.com',
                'role'     => 'engineeriv',
                'employee' => [
                    'employee_number' => 'EMP-008',
                    'position'        => 'Engineer IV',
                    'department'      => 'Engineering',
                    'phone'           => '09000000008',
                    'office'          => 'Head Office',
                ],
            ],

            //Engineer III
            [
                'name'     => 'Sanita E. Maiza',
                'email'    => 'engineeriii@example.com',
                'role'     => 'engineeriii',
                'employee' => [
                    'employee_number' => 'EMP-009',
                    'position'        => 'Engineer III',
                    'department'      => 'Engineering',
                    'phone'           => '09000000009',
                    'office'          => 'Head Office',
                ],
            ],

            // Provincial Engineer
            [
                'name'     => 'Jose Pastor P. De La Cerna, III',
                'email'    => 'provincial.engineer@example.com',
                'role'     => 'provincial_engineer',
                'employee' => [
                    'employee_number' => 'EMP-006',
                    'position'        => 'Provincial Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000006',
                    'office'          => 'Provincial Office',
                ],
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'role'     => $data['role'],
                ]
            );

            // Create employee profile if not exists
            if (!$user->employee) {
                Employee::create([
                    'user_id'         => $user->id,
                    ...$data['employee'],
                ]);
            }
        }
    }
}