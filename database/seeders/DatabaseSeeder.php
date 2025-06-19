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
                'name' => 'Sarah Johnson',
                'email' => 'sarah@hairsalon.com',
                'phone' => '2345678901'
            ],
            [
                'name' => 'Mike Thompson',
                'email' => 'mike@autorepair.com',
                'phone' => '3456789012'
            ],
            [
                'name' => 'Dr. Emily Chen',
                'email' => 'emily@dentalcare.com',
                'phone' => '4567890123'
            ],
            [
                'name' => 'Carlos Martinez',
                'email' => 'carlos@fitness.com',
                'phone' => '5678901234'
            ],
            [
                'name' => 'Lisa Wang',
                'email' => 'lisa@beautystore.com',
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
                    'name' => 'Elite Hair Salon',
                    'description' => 'Premium hair styling and beauty services',
                    'address' => '123 Fashion Ave, Downtown',
                    'phone_number' => '555-HAIR-001',
                    'email' => 'info@elitehair.com',
                ],
                'services' => [
                    ['name' => 'Haircut & Style', 'description' => 'Professional haircut with styling', 'price' => 4500, 'duration' => 45],
                    ['name' => 'Hair Coloring', 'description' => 'Full hair color service', 'price' => 8000, 'duration' => 120],
                    ['name' => 'Hair Wash & Blowdry', 'description' => 'Shampoo, condition and blowdry', 'price' => 2500, 'duration' => 30],
                    ['name' => 'Highlights', 'description' => 'Professional highlighting service', 'price' => 9500, 'duration' => 150],
                    ['name' => 'Deep Conditioning', 'description' => 'Intensive hair treatment', 'price' => 3500, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Jessica Smith', 'email' => 'jessica@elitehair.com', 'bio' => 'Senior stylist with 8 years experience'],
                    ['name' => 'David Brown', 'email' => 'david@elitehair.com', 'bio' => 'Color specialist and creative stylist'],
                ]
            ],
            [
                'provider_index' => 1,
                'business' => [
                    'name' => 'AutoFix Pro',
                    'description' => 'Complete automotive repair and maintenance',
                    'address' => '456 Mechanic St, Industrial District',
                    'phone_number' => '555-AUTO-002',
                    'email' => 'service@autofixpro.com',
                ],
                'services' => [
                    ['name' => 'Oil Change', 'description' => 'Standard oil change service', 'price' => 3500, 'duration' => 30],
                    ['name' => 'Brake Inspection', 'description' => 'Complete brake system check', 'price' => 5000, 'duration' => 45],
                    ['name' => 'Tire Rotation', 'description' => 'Tire rotation and balancing', 'price' => 4000, 'duration' => 40],
                    ['name' => 'Engine Diagnostic', 'description' => 'Computer diagnostic scan', 'price' => 7500, 'duration' => 60],
                    ['name' => 'AC Service', 'description' => 'Air conditioning repair and maintenance', 'price' => 8500, 'duration' => 90],
                ],
                'employees' => [
                    ['name' => 'Tony Rodriguez', 'email' => 'tony@autofixpro.com', 'bio' => 'Certified master mechanic with 12 years experience'],
                    ['name' => 'Jake Wilson', 'email' => 'jake@autofixpro.com', 'bio' => 'Specialist in electrical systems and diagnostics'],
                ]
            ],
            [
                'provider_index' => 2,
                'business' => [
                    'name' => 'Smile Dental Care',
                    'description' => 'Comprehensive dental services for the whole family',
                    'address' => '789 Health Plaza, Medical Center',
                    'phone_number' => '555-SMILE-03',
                    'email' => 'appointments@smiledentalcare.com',
                ],
                'services' => [
                    ['name' => 'Dental Cleaning', 'description' => 'Professional teeth cleaning', 'price' => 8000, 'duration' => 60],
                    ['name' => 'Dental Checkup', 'description' => 'Comprehensive oral examination', 'price' => 6000, 'duration' => 45],
                    ['name' => 'Teeth Whitening', 'description' => 'Professional whitening treatment', 'price' => 15000, 'duration' => 90],
                    ['name' => 'Cavity Filling', 'description' => 'Composite filling service', 'price' => 12000, 'duration' => 75],
                    ['name' => 'Root Canal Consultation', 'description' => 'Initial consultation for root canal', 'price' => 10000, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Dr. Robert Kim', 'email' => 'robert@smiledentalcare.com', 'bio' => 'General dentist with focus on preventive care'],
                    ['name' => 'Dr. Maria Lopez', 'email' => 'maria@smiledentalcare.com', 'bio' => 'Specialist in cosmetic dentistry'],
                ]
            ],
            [
                'provider_index' => 3,
                'business' => [
                    'name' => 'FitZone Gym',
                    'description' => 'Personal training and fitness coaching',
                    'address' => '321 Fitness Blvd, Sports Complex',
                    'phone_number' => '555-FITZONE',
                    'email' => 'trainers@fitzonegym.com',
                ],
                'services' => [
                    ['name' => 'Personal Training Session', 'description' => '1-on-1 personal training', 'price' => 6000, 'duration' => 60],
                    ['name' => 'Nutrition Consultation', 'description' => 'Personalized nutrition planning', 'price' => 7500, 'duration' => 45],
                    ['name' => 'Fitness Assessment', 'description' => 'Complete fitness evaluation', 'price' => 5000, 'duration' => 90],
                    ['name' => 'Group Training Class', 'description' => 'Small group training session', 'price' => 3000, 'duration' => 45],
                    ['name' => 'Sports Massage', 'description' => 'Therapeutic sports massage', 'price' => 8000, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Alex Parker', 'email' => 'alex@fitzonegym.com', 'bio' => 'Certified personal trainer and nutrition specialist'],
                    ['name' => 'Sam Taylor', 'email' => 'sam@fitzonegym.com', 'bio' => 'Former athlete specializing in strength training'],
                ]
            ],
            [
                'provider_index' => 4,
                'business' => [
                    'name' => 'Glow Beauty Studio',
                    'description' => 'Full-service beauty and wellness spa',
                    'address' => '654 Beauty Lane, Spa District',
                    'phone_number' => '555-GLOW-555',
                    'email' => 'book@glowbeautystudio.com',
                ],
                'services' => [
                    ['name' => 'Facial Treatment', 'description' => 'Deep cleansing facial with moisturizing', 'price' => 7000, 'duration' => 75],
                    ['name' => 'Manicure', 'description' => 'Complete nail care and polish', 'price' => 3500, 'duration' => 45],
                    ['name' => 'Pedicure', 'description' => 'Foot care treatment with polish', 'price' => 4500, 'duration' => 60],
                    ['name' => 'Eyebrow Shaping', 'description' => 'Professional eyebrow waxing and shaping', 'price' => 2500, 'duration' => 30],
                    ['name' => 'Full Body Massage', 'description' => 'Relaxing full body massage therapy', 'price' => 9000, 'duration' => 90],
                    ['name' => 'Makeup Application', 'description' => 'Professional makeup for special events', 'price' => 6500, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Sophia Davis', 'email' => 'sophia@glowbeautystudio.com', 'bio' => 'Licensed esthetician and makeup artist'],
                    ['name' => 'Maya Patel', 'email' => 'maya@glowbeautystudio.com', 'bio' => 'Certified massage therapist and nail technician'],
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
            if (in_array($business->name, ['Elite Hair Salon', 'Glow Beauty Studio'])) {
                $workDays[] = 'saturday';
            }

            foreach ($workDays as $day) {
                BusinessWorkingHour::create([
                    'business_id' => $business->id,
                    'day_of_week' => $day,
                    'start_time' => $business->name === 'AutoFix Pro' ? '08:00:00' : '09:00:00',
                    'end_time' => $business->name === 'FitZone Gym' ? '21:00:00' : '18:00:00',
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
                        'start_time' => $business->name === 'AutoFix Pro' ? '08:00:00' : '09:00:00',
                        'end_time' => $business->name === 'FitZone Gym' ? '21:00:00' : '18:00:00',
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
            ['name' => 'John Client', 'email' => 'john.client@example.com', 'phone' => '7890123456'],
            ['name' => 'Jane Customer', 'email' => 'jane.customer@example.com', 'phone' => '8901234567'],
            ['name' => 'Bob Smith', 'email' => 'bob.smith@example.com', 'phone' => '9012345678'],
            ['name' => 'Alice Johnson', 'email' => 'alice.johnson@example.com', 'phone' => '0123456789'],
            ['name' => 'Charlie Brown', 'email' => 'charlie.brown@example.com', 'phone' => '1234509876'],
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
