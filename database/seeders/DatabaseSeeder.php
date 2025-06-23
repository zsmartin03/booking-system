<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessWorkingHour;
use App\Models\Employee;
use App\Models\EmployeeWorkingHour;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Admin User
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
            'phone_number' => '1234567890',
        ]);

        // Service Providers
        $providers = [
            [
                'name' => 'Provider',
                'email' => 'provider@example.com',
                'phone' => '2345678901'
            ],
            [
                'name' => 'Provider Two',
                'email' => 'provider2@example.com',
                'phone' => '3456789012'
            ],
            [
                'name' => 'Provider Three',
                'email' => 'provider3@example.com',
                'phone' => '4567890123'
            ],
            [
                'name' => 'Provider Four',
                'email' => 'provider4@example.com',
                'phone' => '5678901234'
            ],
            [
                'name' => 'Provider Five',
                'email' => 'provider5@example.com',
                'phone' => '6789012345'
            ]
        ];

        $providerUsers = [];
        foreach ($providers as $provider) {
            $providerUsers[] = User::create([
                'name' => $provider['name'],
                'email' => $provider['email'],
                'password' => bcrypt('password'),
                'role' => 'provider',
                'email_verified_at' => Carbon::now(),
                'phone_number' => $provider['phone'],
            ]);
        }

        // Businesses with their services
        $businessData = [
            [
                'provider_index' => 0,
                'business' => [
                    'name' => 'Hair Salon',
                    'description' => 'Professional hair styling and beauty services',
                    'address' => '123 Main St, Downtown',
                    'phone_number' => '555-0001',
                    'email' => 'info@hairsalon.com',
                ],
                'services' => [
                    ['name' => 'Haircut', 'description' => 'Professional haircut service', 'price' => 4500, 'duration' => 45],
                    ['name' => 'Hair Coloring', 'description' => 'Hair color service', 'price' => 8000, 'duration' => 120],
                    ['name' => 'Hair Wash', 'description' => 'Shampoo and conditioning', 'price' => 2500, 'duration' => 30],
                    ['name' => 'Hair Highlights', 'description' => 'Hair highlighting service', 'price' => 9500, 'duration' => 150],
                    ['name' => 'Hair Treatment', 'description' => 'Deep conditioning treatment', 'price' => 3500, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Hair Stylist One', 'email' => 'stylist1@hairsalon.com', 'bio' => 'Professional hair stylist'],
                    ['name' => 'Hair Stylist Two', 'email' => 'stylist2@hairsalon.com', 'bio' => 'Color specialist'],
                ]
            ],
            [
                'provider_index' => 1,
                'business' => [
                    'name' => 'Auto Repair Shop',
                    'description' => 'Complete automotive repair and maintenance',
                    'address' => '456 Industrial St, Business District',
                    'phone_number' => '555-0002',
                    'email' => 'service@autorepair.com',
                ],
                'services' => [
                    ['name' => 'Oil Change', 'description' => 'Vehicle oil change service', 'price' => 3500, 'duration' => 30],
                    ['name' => 'Brake Service', 'description' => 'Brake inspection and repair', 'price' => 5000, 'duration' => 45],
                    ['name' => 'Tire Service', 'description' => 'Tire rotation and balancing', 'price' => 4000, 'duration' => 40],
                    ['name' => 'Engine Diagnostic', 'description' => 'Computer diagnostic service', 'price' => 7500, 'duration' => 60],
                    ['name' => 'AC Repair', 'description' => 'Air conditioning repair service', 'price' => 8500, 'duration' => 90],
                ],
                'employees' => [
                    ['name' => 'Mechanic One', 'email' => 'mechanic1@autorepair.com', 'bio' => 'Certified automotive mechanic'],
                    ['name' => 'Mechanic Two', 'email' => 'mechanic2@autorepair.com', 'bio' => 'Electrical systems specialist'],
                ]
            ],
            [
                'provider_index' => 2,
                'business' => [
                    'name' => 'Dental Clinic',
                    'description' => 'Comprehensive dental services',
                    'address' => '789 Health Plaza, Medical Center',
                    'phone_number' => '555-0003',
                    'email' => 'appointments@dentalclinic.com',
                ],
                'services' => [
                    ['name' => 'Dental Cleaning', 'description' => 'Professional teeth cleaning', 'price' => 8000, 'duration' => 60],
                    ['name' => 'Dental Checkup', 'description' => 'Comprehensive dental examination', 'price' => 6000, 'duration' => 45],
                    ['name' => 'Teeth Whitening', 'description' => 'Professional whitening treatment', 'price' => 15000, 'duration' => 90],
                    ['name' => 'Dental Filling', 'description' => 'Cavity filling service', 'price' => 12000, 'duration' => 75],
                    ['name' => 'Root Canal', 'description' => 'Root canal consultation', 'price' => 10000, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Dentist One', 'email' => 'dentist1@dentalclinic.com', 'bio' => 'General dentist'],
                    ['name' => 'Dentist Two', 'email' => 'dentist2@dentalclinic.com', 'bio' => 'Cosmetic dentistry specialist'],
                ]
            ],
            [
                'provider_index' => 3,
                'business' => [
                    'name' => 'Fitness Gym',
                    'description' => 'Personal training and fitness services',
                    'address' => '321 Fitness Blvd, Sports Complex',
                    'phone_number' => '555-0004',
                    'email' => 'trainers@fitnessgym.com',
                ],
                'services' => [
                    ['name' => 'Personal Training', 'description' => 'One-on-one personal training session', 'price' => 6000, 'duration' => 60],
                    ['name' => 'Nutrition Consultation', 'description' => 'Personalized nutrition planning', 'price' => 7500, 'duration' => 45],
                    ['name' => 'Fitness Assessment', 'description' => 'Complete fitness evaluation', 'price' => 5000, 'duration' => 90],
                    ['name' => 'Group Training', 'description' => 'Small group training session', 'price' => 3000, 'duration' => 45],
                    ['name' => 'Sports Massage', 'description' => 'Therapeutic sports massage', 'price' => 8000, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Personal Trainer One', 'email' => 'trainer1@fitnessgym.com', 'bio' => 'Certified personal trainer'],
                    ['name' => 'Personal Trainer Two', 'email' => 'trainer2@fitnessgym.com', 'bio' => 'Strength training specialist'],
                ]
            ],
            [
                'provider_index' => 4,
                'business' => [
                    'name' => 'Beauty Spa',
                    'description' => 'Full-service beauty and wellness spa',
                    'address' => '654 Beauty Lane, Spa District',
                    'phone_number' => '555-0005',
                    'email' => 'book@beautyspa.com',
                ],
                'services' => [
                    ['name' => 'Facial Treatment', 'description' => 'Deep cleansing facial treatment', 'price' => 7000, 'duration' => 75],
                    ['name' => 'Manicure', 'description' => 'Complete nail care service', 'price' => 3500, 'duration' => 45],
                    ['name' => 'Pedicure', 'description' => 'Foot care treatment', 'price' => 4500, 'duration' => 60],
                    ['name' => 'Eyebrow Shaping', 'description' => 'Professional eyebrow shaping', 'price' => 2500, 'duration' => 30],
                    ['name' => 'Body Massage', 'description' => 'Relaxing full body massage', 'price' => 9000, 'duration' => 90],
                    ['name' => 'Makeup Service', 'description' => 'Professional makeup application', 'price' => 6500, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Esthetician One', 'email' => 'esthetician1@beautyspa.com', 'bio' => 'Licensed esthetician'],
                    ['name' => 'Massage Therapist One', 'email' => 'therapist1@beautyspa.com', 'bio' => 'Certified massage therapist'],
                ]
            ]
        ];

        // Create businesses, services, and employees
        foreach ($businessData as $data) {
            $business = Business::create([
                'user_id' => $providerUsers[$data['provider_index']]->id,
                ...$data['business']
            ]);

            // Create business working hours (Monday to Friday, 9 AM to 6 PM)
            $workDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

            // Some businesses also work weekends
            if (in_array($business->name, ['Hair Salon', 'Beauty Spa'])) {
                $workDays[] = 'saturday';
            }

            foreach ($workDays as $day) {
                BusinessWorkingHour::create([
                    'business_id' => $business->id,
                    'day_of_week' => $day,
                    'start_time' => $business->name === 'Auto Repair Shop' ? '08:00:00' : '09:00:00',
                    'end_time' => $business->name === 'Fitness Gym' ? '21:00:00' : '18:00:00',
                ]);
            }

            // Create services
            $services = [];
            foreach ($data['services'] as $serviceData) {
                $services[] = Service::create([
                    'business_id' => $business->id,
                    ...$serviceData,
                    'active' => true,
                ]);
            }

            // Create employees
            foreach ($data['employees'] as $index => $employeeData) {
                $employeeUser = User::create([
                    'name' => $employeeData['name'],
                    'email' => $employeeData['email'],
                    'password' => bcrypt('password'),
                    'role' => 'employee',
                    'email_verified_at' => Carbon::now(),
                    'phone_number' => '555' . str_pad($business->id . $index, 7, '0', STR_PAD_LEFT),
                ]);

                $employee = Employee::create([
                    'business_id' => $business->id,
                    'user_id' => $employeeUser->id,
                    'name' => $employeeData['name'],
                    'email' => $employeeData['email'],
                    'bio' => $employeeData['bio'],
                    'active' => true,
                ]);

                // Create employee working hours (same as business for now)
                foreach ($workDays as $day) {
                    EmployeeWorkingHour::create([
                        'employee_id' => $employee->id,
                        'day_of_week' => $day,
                        'start_time' => $business->name === 'Auto Repair Shop' ? '08:00:00' : '09:00:00',
                        'end_time' => $business->name === 'Fitness Gym' ? '21:00:00' : '18:00:00',
                    ]);
                }

                // Assign all services to all employees (you can modify this logic)
                foreach ($services as $service) {
                    $service->employees()->attach($employee->id);
                }
            }
        }

        // Create some client users
        $clients = [
            ['name' => 'Client', 'email' => 'client@example.com', 'phone' => '7890123456'],
            ['name' => 'Client Two', 'email' => 'client2@example.com', 'phone' => '8901234567'],
            ['name' => 'Client Three', 'email' => 'client3@example.com', 'phone' => '9012345678'],
            ['name' => 'Client Four', 'email' => 'client4@example.com', 'phone' => '0123456789'],
            ['name' => 'Client Five', 'email' => 'client5@example.com', 'phone' => '1234509876'],
        ];

        foreach ($clients as $client) {
            User::create([
                'name' => $client['name'],
                'email' => $client['email'],
                'password' => bcrypt('password'),
                'role' => 'client',
                'email_verified_at' => Carbon::now(),
                'phone_number' => $client['phone'],
            ]);
        }
    }
}
