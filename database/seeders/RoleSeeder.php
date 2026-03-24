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
                'email'    => 'prototypecapstone@gmail.com',
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
                'email'    => 'macalutasreyramesesjudeiii@gmail.com',
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
                'name'     => 'Randy P. Diaz',
                'email'    => 'namisanchez123@gmail.com',
                'role'     => 'mtqa',
                'employee' => [
                    'employee_number' => 'EMP-007',
                    'position'        => 'MTQA',
                    'department'      => 'Quality Assurance',
                    'phone'           => '09000000007',
                    'office'          => 'Head Office',
                ],
            ],

            // Resident Engineers
            [
                'name'     => 'Leizel S. Galleposo',
                'email'    => '1901102366@student.buksu.edu.ph',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-005',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000005',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Apolinario C. Pesisano',
                'email'    => 'resident.engineer.pesisano@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-001',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000010',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Josephine C. Fernandez',
                'email'    => 'resident.engineer.fernandez@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-002',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000011',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Jose Henry T. Sonsona',
                'email'    => 'resident.engineer.sonsona@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-003',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000012',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Ferdinand U. Sanico',
                'email'    => 'resident.engineer.fsanico@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-004',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000013',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Jewel Ann A. Calubia',
                'email'    => 'resident.engineer.calubia@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-005',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000014',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Jona Criezl L. De La Cruz',
                'email'    => 'resident.engineer.delacruz@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-006',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000015',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Alexander U. Sanico',
                'email'    => 'resident.engineer.asanico@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-007',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000016',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Romel B. Cadion',
                'email'    => 'resident.engineer.cadion@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-008',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000017',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Romeo R. Tianga',
                'email'    => 'resident.engineer.tianga@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-009',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000018',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Aaron Daniel P. Alvez',
                'email'    => 'resident.engineer.alvez@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-010',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000019',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Roman Kristopher M. Aranas',
                'email'    => 'resident.engineer.aranas@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-011',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000020',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Alexander P. Asok',
                'email'    => 'resident.engineer.asok@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-012',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000021',
                    'office'          => 'District Office',
                ],
            ],
            [
                'name'     => 'Glen Mart Paluga',
                'email'    => 'resident.engineer.paluga@example.com',
                'role'     => 'resident_engineer',
                'employee' => [
                    'employee_number' => 'EMP-RE-013',
                    'position'        => 'Resident Engineer',
                    'department'      => 'Engineering',
                    'phone'           => '09000000022',
                    'office'          => 'District Office',
                ],
            ],

            // Engineer IV
            [
                'email'    => 'engineeriv@example.com',
                'role'     => 'engineeriv',
                'name'     => 'Grace D. Cañete',
                'employee' => [
                    'employee_number' => 'EMP-008',
                    'position'        => 'Engineer IV',
                    'department'      => 'Engineering',
                    'phone'           => '09000000008',
                    'office'          => 'Head Office',
                ],
            ],

            // Engineer III
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

            // Engineer III
            [
                'name'     => 'Atillana B. Mangubat',
                'email'    => 'engineeriii2@example.com',
                'role'     => 'engineeriii',
                'employee' => [
                    'employee_number' => 'EMP-091',
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