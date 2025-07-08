<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessWorkingHour;
use App\Models\Category;
use App\Models\Employee;
use App\Models\EmployeeWorkingHour;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $providerUsers = User::where('role', 'provider')->get();

        $businessData = [
            [
                'business' => [
                    'name' => 'Elite Hair Salon',
                    'description' => 'Professional hair styling and beauty services',
                    'address' => '123 Main St, Downtown',
                    'phone_number' => '555-0001',
                    'email' => 'info@elitehair.com',
                ],
                'category_slug' => 'beauty-wellness',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Haircut & Style', 'description' => 'Professional haircut and styling', 'price' => 65.00, 'duration' => 60],
                    ['name' => 'Hair Coloring', 'description' => 'Full hair color service', 'price' => 120.00, 'duration' => 150],
                    ['name' => 'Highlights', 'description' => 'Hair highlighting service', 'price' => 95.00, 'duration' => 120],
                ],
                'employees' => [
                    ['name' => 'Sarah Johnson', 'email' => 'sarah@elitehair.com', 'bio' => 'Senior hair stylist with 8 years experience'],
                    ['name' => 'Mike Chen', 'email' => 'mike@elitehair.com', 'bio' => 'Color specialist and creative director'],
                ]
            ],
            [
                'business' => [
                    'name' => 'AutoCare Plus',
                    'description' => 'Complete automotive repair and maintenance services',
                    'address' => '456 Industrial Ave, Mechanics Row',
                    'phone_number' => '555-0002',
                    'email' => 'service@autocareplus.com',
                ],
                'category_slug' => 'automotive',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Oil Change Service', 'description' => 'Full synthetic oil change with filter', 'price' => 45.00, 'duration' => 30],
                    ['name' => 'Brake Inspection & Repair', 'description' => 'Complete brake system service', 'price' => 150.00, 'duration' => 90],
                ],
                'employees' => [
                    ['name' => 'Tom Rodriguez', 'email' => 'tom@autocareplus.com', 'bio' => 'ASE certified master technician'],
                    ['name' => 'Lisa Park', 'email' => 'lisa@autocareplus.com', 'bio' => 'Electrical systems specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Smile Dental Care',
                    'description' => 'Family dentistry and cosmetic dental services',
                    'address' => '789 Health Plaza, Medical District',
                    'phone_number' => '555-0003',
                    'email' => 'appointments@smiledentalcare.com',
                ],
                'category_slug' => 'health-medical',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Dental Cleaning', 'description' => 'Professional teeth cleaning and exam', 'price' => 120.00, 'duration' => 60],
                    ['name' => 'Teeth Whitening', 'description' => 'Professional whitening treatment', 'price' => 350.00, 'duration' => 90],
                    ['name' => 'Dental Filling', 'description' => 'Composite tooth filling', 'price' => 180.00, 'duration' => 45],
                ],
                'employees' => [
                    ['name' => 'Dr. Emily Watson', 'email' => 'emily@smiledentalcare.com', 'bio' => 'General dentist with 12 years experience'],
                    ['name' => 'Dr. James Liu', 'email' => 'james@smiledentalcare.com', 'bio' => 'Cosmetic dentistry specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'FitZone Gym',
                    'description' => 'Personal training and fitness coaching',
                    'address' => '321 Fitness Blvd, Sports Complex',
                    'phone_number' => '555-0004',
                    'email' => 'trainers@fitzonegym.com',
                ],
                'category_slug' => ['fitness-sports', 'health-medical'], // Multiple categories
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Personal Training Session', 'description' => 'One-on-one fitness training', 'price' => 80.00, 'duration' => 60],
                    ['name' => 'Nutrition Consultation', 'description' => 'Personalized nutrition planning', 'price' => 60.00, 'duration' => 45],
                ],
                'employees' => [
                    ['name' => 'Alex Thompson', 'email' => 'alex@fitzonegym.com', 'bio' => 'Certified personal trainer and nutritionist'],
                    ['name' => 'Maria Santos', 'email' => 'maria@fitzonegym.com', 'bio' => 'Strength training and sports performance coach'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Serenity Spa',
                    'description' => 'Luxury spa and wellness treatments',
                    'address' => '654 Wellness Way, Spa District',
                    'phone_number' => '555-0005',
                    'email' => 'book@serenityspa.com',
                ],
                'category_slug' => ['beauty-wellness', 'health-medical'], // Multiple categories
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Deep Tissue Massage', 'description' => 'Therapeutic deep tissue massage', 'price' => 110.00, 'duration' => 90],
                    ['name' => 'Facial Treatment', 'description' => 'Anti-aging facial with organic products', 'price' => 85.00, 'duration' => 75],
                    ['name' => 'Manicure & Pedicure', 'description' => 'Complete nail care service', 'price' => 65.00, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Isabella Moore', 'email' => 'isabella@serenityspa.com', 'bio' => 'Licensed massage therapist and esthetician'],
                    ['name' => 'David Kim', 'email' => 'david@serenityspa.com', 'bio' => 'Nail technician and spa specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'TechFix Solutions',
                    'description' => 'Computer and mobile device repair services',
                    'address' => '987 Tech Avenue, Digital District',
                    'phone_number' => '555-0006',
                    'email' => 'support@techfixsolutions.com',
                ],
                'category_slug' => ['professional-services', 'technology'], // Multiple categories
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Laptop Repair', 'description' => 'Hardware and software troubleshooting', 'price' => 75.00, 'duration' => 60],
                    ['name' => 'Phone Screen Replacement', 'description' => 'Mobile device screen repair', 'price' => 120.00, 'duration' => 45],
                ],
                'employees' => [
                    ['name' => 'Kevin Zhang', 'email' => 'kevin@techfixsolutions.com', 'bio' => 'Computer technician with 6 years experience'],
                    ['name' => 'Rachel Green', 'email' => 'rachel@techfixsolutions.com', 'bio' => 'Mobile device repair specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Bella Vista Restaurant',
                    'description' => 'Fine dining with Italian cuisine',
                    'address' => '234 Gourmet Street, Culinary Quarter',
                    'phone_number' => '555-0007',
                    'email' => 'reservations@bellavista.com',
                ],
                'category_slug' => 'food-dining',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Private Dining Experience', 'description' => 'Exclusive chef-curated meal for 2-8 guests', 'price' => 150.00, 'duration' => 120],
                    ['name' => 'Wine Tasting Session', 'description' => 'Guided wine tasting with sommelier', 'price' => 45.00, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Chef Antonio Rossi', 'email' => 'antonio@bellavista.com', 'bio' => 'Executive chef with Michelin restaurant experience'],
                    ['name' => 'Sophie Laurent', 'email' => 'sophie@bellavista.com', 'bio' => 'Certified sommelier and wine expert'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Paws & Claws Veterinary',
                    'description' => 'Complete veterinary care for pets',
                    'address' => '567 Animal Care Blvd, Pet District',
                    'phone_number' => '555-0008',
                    'email' => 'appointments@pawsandclaws.com',
                ],
                'category_slug' => ['health-medical', 'pet-services'], // Multiple categories
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Pet Wellness Exam', 'description' => 'Complete health checkup for pets', 'price' => 85.00, 'duration' => 45],
                    ['name' => 'Pet Grooming', 'description' => 'Professional pet bathing and grooming', 'price' => 60.00, 'duration' => 90],
                    ['name' => 'Vaccination Service', 'description' => 'Annual pet vaccinations', 'price' => 55.00, 'duration' => 30],
                ],
                'employees' => [
                    ['name' => 'Dr. Jennifer Adams', 'email' => 'jennifer@pawsandclaws.com', 'bio' => 'Veterinarian specializing in small animals'],
                    ['name' => 'Mark Wilson', 'email' => 'mark@pawsandclaws.com', 'bio' => 'Professional pet groomer'],
                ]
            ],
            [
                'business' => [
                    'name' => 'HomeClean Pro',
                    'description' => 'Professional residential and commercial cleaning',
                    'address' => '890 Service Lane, Business Park',
                    'phone_number' => '555-0009',
                    'email' => 'book@homecleanpro.com',
                ],
                'category_slug' => 'home-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Deep House Cleaning', 'description' => 'Comprehensive home cleaning service', 'price' => 180.00, 'duration' => 180],
                    ['name' => 'Carpet Cleaning', 'description' => 'Professional carpet and upholstery cleaning', 'price' => 120.00, 'duration' => 120],
                ],
                'employees' => [
                    ['name' => 'Carmen Rodriguez', 'email' => 'carmen@homecleanpro.com', 'bio' => 'Professional cleaner with 5 years experience'],
                    ['name' => 'Robert Johnson', 'email' => 'robert@homecleanpro.com', 'bio' => 'Carpet cleaning specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'LawFirm Associates',
                    'description' => 'Legal consultation and representation',
                    'address' => '123 Justice Avenue, Legal District',
                    'phone_number' => '555-0010',
                    'email' => 'consultation@lawfirmassociates.com',
                ],
                'category_slug' => 'professional-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Legal Consultation', 'description' => 'Initial legal advice and case evaluation', 'price' => 200.00, 'duration' => 60],
                    ['name' => 'Document Review', 'description' => 'Contract and legal document review', 'price' => 150.00, 'duration' => 90],
                ],
                'employees' => [
                    ['name' => 'Attorney Sarah Davis', 'email' => 'sarah.davis@lawfirmassociates.com', 'bio' => 'Family law and contract specialist'],
                    ['name' => 'Attorney Michael Brown', 'email' => 'michael.brown@lawfirmassociates.com', 'bio' => 'Business law and litigation attorney'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Garden Paradise Landscaping',
                    'description' => 'Professional landscaping and garden design',
                    'address' => '456 Green Thumb Road, Garden District',
                    'phone_number' => '555-0011',
                    'email' => 'design@gardenparadise.com',
                ],
                'category_slug' => 'home-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Garden Design Consultation', 'description' => 'Custom landscape design planning', 'price' => 120.00, 'duration' => 90],
                    ['name' => 'Lawn Maintenance', 'description' => 'Weekly lawn care and maintenance', 'price' => 80.00, 'duration' => 120],
                    ['name' => 'Tree Pruning', 'description' => 'Professional tree trimming and pruning', 'price' => 150.00, 'duration' => 150],
                ],
                'employees' => [
                    ['name' => 'Carlos Martinez', 'email' => 'carlos@gardenparadise.com', 'bio' => 'Landscape designer with 10 years experience'],
                    ['name' => 'Linda Thompson', 'email' => 'linda@gardenparadise.com', 'bio' => 'Certified arborist and plant specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Piano Lessons Studio',
                    'description' => 'Professional piano instruction for all ages',
                    'address' => '789 Music Lane, Arts Quarter',
                    'phone_number' => '555-0012',
                    'email' => 'lessons@pianolessonsstudio.com',
                ],
                'category_slug' => 'education-tutoring',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Private Piano Lesson', 'description' => 'One-on-one piano instruction', 'price' => 60.00, 'duration' => 60],
                    ['name' => 'Music Theory Session', 'description' => 'Music theory and composition lessons', 'price' => 50.00, 'duration' => 45],
                ],
                'employees' => [
                    ['name' => 'Elena Petrov', 'email' => 'elena@pianolessonsstudio.com', 'bio' => 'Concert pianist and music educator'],
                    ['name' => 'Jonathan Reed', 'email' => 'jonathan@pianolessonsstudio.com', 'bio' => 'Jazz pianist and music theory instructor'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Photography by Emma',
                    'description' => 'Professional photography services',
                    'address' => '321 Camera Street, Creative District',
                    'phone_number' => '555-0013',
                    'email' => 'book@photographybyemma.com',
                ],
                'category_slug' => 'creative-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Portrait Photography', 'description' => 'Professional portrait photo session', 'price' => 200.00, 'duration' => 120],
                    ['name' => 'Event Photography', 'description' => 'Special event and party photography', 'price' => 400.00, 'duration' => 240],
                ],
                'employees' => [
                    ['name' => 'Emma Foster', 'email' => 'emma@photographybyemma.com', 'bio' => 'Professional photographer specializing in portraits'],
                    ['name' => 'Jake Miller', 'email' => 'jake@photographybyemma.com', 'bio' => 'Event photographer and photo editor'],
                ]
            ],
            [
                'business' => [
                    'name' => 'QuickFix Handyman',
                    'description' => 'Home repair and maintenance services',
                    'address' => '654 Repair Road, Service District',
                    'phone_number' => '555-0014',
                    'email' => 'service@quickfixhandyman.com',
                ],
                'category_slug' => 'home-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Plumbing Repair', 'description' => 'Basic plumbing fixes and installations', 'price' => 90.00, 'duration' => 60],
                    ['name' => 'Electrical Work', 'description' => 'Light electrical repairs and installations', 'price' => 100.00, 'duration' => 75],
                    ['name' => 'Furniture Assembly', 'description' => 'Professional furniture assembly service', 'price' => 50.00, 'duration' => 45],
                ],
                'employees' => [
                    ['name' => 'Steve Anderson', 'email' => 'steve@quickfixhandyman.com', 'bio' => 'Licensed handyman with 15 years experience'],
                    ['name' => 'Tony Garcia', 'email' => 'tony@quickfixhandyman.com', 'bio' => 'Electrical and plumbing specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Yoga Harmony Studio',
                    'description' => 'Yoga classes and wellness programs',
                    'address' => '987 Zen Circle, Wellness District',
                    'phone_number' => '555-0015',
                    'email' => 'classes@yogaharmonystudio.com',
                ],
                'category_slug' => 'fitness-sports',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Private Yoga Session', 'description' => 'Personalized yoga instruction', 'price' => 70.00, 'duration' => 60],
                    ['name' => 'Meditation Class', 'description' => 'Guided meditation and mindfulness', 'price' => 25.00, 'duration' => 45],
                ],
                'employees' => [
                    ['name' => 'Maya Patel', 'email' => 'maya@yogaharmonystudio.com', 'bio' => 'Certified yoga instructor and meditation teacher'],
                    ['name' => 'Grace Williams', 'email' => 'grace@yogaharmonystudio.com', 'bio' => 'Hatha and Vinyasa yoga specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'BookKeeping Plus',
                    'description' => 'Accounting and bookkeeping services',
                    'address' => '234 Finance Street, Business District',
                    'phone_number' => '555-0016',
                    'email' => 'services@bookkeepingplus.com',
                ],
                'category_slug' => 'professional-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Tax Preparation', 'description' => 'Individual and business tax preparation', 'price' => 150.00, 'duration' => 90],
                    ['name' => 'Financial Consultation', 'description' => 'Business financial planning and advice', 'price' => 120.00, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'CPA Lisa Chang', 'email' => 'lisa@bookkeepingplus.com', 'bio' => 'Certified Public Accountant with 12 years experience'],
                    ['name' => 'Daniel Scott', 'email' => 'daniel@bookkeepingplus.com', 'bio' => 'Financial advisor and tax specialist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Artisan Bakery',
                    'description' => 'Fresh baked goods and custom cakes',
                    'address' => '567 Bakery Lane, Culinary District',
                    'phone_number' => '555-0017',
                    'email' => 'orders@artisanbakery.com',
                ],
                'category_slug' => 'food-dining',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Custom Cake Design', 'description' => 'Personalized cake design and baking', 'price' => 80.00, 'duration' => 60],
                    ['name' => 'Baking Class', 'description' => 'Learn professional baking techniques', 'price' => 45.00, 'duration' => 120],
                ],
                'employees' => [
                    ['name' => 'Chef Marie Dubois', 'email' => 'marie@artisanbakery.com', 'bio' => 'Master baker and pastry chef'],
                    ['name' => 'Paul Henderson', 'email' => 'paul@artisanbakery.com', 'bio' => 'Cake decorator and baking instructor'],
                ]
            ],
            [
                'business' => [
                    'name' => 'TutorWise Education',
                    'description' => 'Academic tutoring and test preparation',
                    'address' => '890 Learning Avenue, Education District',
                    'phone_number' => '555-0018',
                    'email' => 'tutoring@tutorwise.com',
                ],
                'category_slug' => 'education-tutoring',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Math Tutoring', 'description' => 'Personalized mathematics instruction', 'price' => 50.00, 'duration' => 60],
                    ['name' => 'SAT Prep Session', 'description' => 'Standardized test preparation', 'price' => 65.00, 'duration' => 90],
                    ['name' => 'English Writing Help', 'description' => 'Essay writing and grammar instruction', 'price' => 45.00, 'duration' => 60],
                ],
                'employees' => [
                    ['name' => 'Dr. Amanda Taylor', 'email' => 'amanda@tutorwise.com', 'bio' => 'PhD in Mathematics, 8 years tutoring experience'],
                    ['name' => 'Professor Brian Lee', 'email' => 'brian@tutorwise.com', 'bio' => 'English Literature professor and writing coach'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Pet Paradise Grooming',
                    'description' => 'Professional pet grooming and care',
                    'address' => '123 Pet Care Street, Animal District',
                    'phone_number' => '555-0019',
                    'email' => 'grooming@petparadise.com',
                ],
                'category_slug' => 'pet-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Full Service Grooming', 'description' => 'Complete grooming package for dogs', 'price' => 75.00, 'duration' => 120],
                    ['name' => 'Cat Grooming', 'description' => 'Specialized grooming for cats', 'price' => 55.00, 'duration' => 90],
                ],
                'employees' => [
                    ['name' => 'Jessica Martinez', 'email' => 'jessica@petparadise.com', 'bio' => 'Certified professional groomer with 6 years experience'],
                    ['name' => 'Ryan Foster', 'email' => 'ryan@petparadise.com', 'bio' => 'Cat grooming specialist and animal behaviorist'],
                ]
            ],
            [
                'business' => [
                    'name' => 'Coastal Real Estate',
                    'description' => 'Real estate sales and property management',
                    'address' => '456 Property Plaza, Realty District',
                    'phone_number' => '555-0020',
                    'email' => 'agents@coastalrealestate.com',
                ],
                'category_slug' => 'professional-services',
                'currency' => 'USD',
                'services' => [
                    ['name' => 'Property Consultation', 'description' => 'Real estate buying/selling consultation', 'price' => 100.00, 'duration' => 75],
                    ['name' => 'Home Valuation', 'description' => 'Professional property value assessment', 'price' => 200.00, 'duration' => 120],
                ],
                'employees' => [
                    ['name' => 'Agent Patricia Clark', 'email' => 'patricia@coastalrealestate.com', 'bio' => 'Licensed realtor with 10 years experience'],
                    ['name' => 'Agent Marcus Johnson', 'email' => 'marcus@coastalrealestate.com', 'bio' => 'Property investment and commercial real estate specialist'],
                ]
            ],
        ];

        foreach ($businessData as $index => $data) {
            $providerUser = $providerUsers[$index % $providerUsers->count()];
            
            $business = Business::create([
                'user_id' => $providerUser->id,
                ...$data['business']
            ]);

            // Attach categories
            $categories = Category::all();
            $categorySlugs = is_array($data['category_slug']) ? $data['category_slug'] : [$data['category_slug']];
            $attachedCategoryIds = [];
            
            foreach ($categorySlugs as $categorySlug) {
                $category = $categories->where('slug', $categorySlug)->first();
                if ($category && !in_array($category->id, $attachedCategoryIds)) {
                    $business->categories()->attach($category->id);
                    $attachedCategoryIds[] = $category->id;
                } elseif (!$category && empty($attachedCategoryIds)) {
                    // Fallback to random category if specified category doesn't exist and no categories attached yet
                    $randomCategory = $categories->random();
                    if (!in_array($randomCategory->id, $attachedCategoryIds)) {
                        $business->categories()->attach($randomCategory->id);
                        $attachedCategoryIds[] = $randomCategory->id;
                    }
                }
            }

            // Create business working hours
            $workDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            
            // Some businesses also work on weekends
            $categorySlugs = is_array($data['category_slug']) ? $data['category_slug'] : [$data['category_slug']];
            $hasWeekendCategories = array_intersect($categorySlugs, ['beauty-wellness', 'food-dining', 'fitness-sports', 'pet-services']);
            
            if (!empty($hasWeekendCategories)) {
                $workDays[] = 'saturday';
                if (array_intersect($categorySlugs, ['food-dining', 'fitness-sports'])) {
                    $workDays[] = 'sunday';
                }
            }

            foreach ($workDays as $day) {
                $startTime = '09:00:00';
                $endTime = '18:00:00';
                
                // Adjust hours based on business type
                if ($data['category_slug'] === 'automotive') {
                    $startTime = '08:00:00';
                } elseif ($data['category_slug'] === 'fitness-sports') {
                    $startTime = '06:00:00';
                    $endTime = '22:00:00';
                } elseif ($data['category_slug'] === 'food-dining') {
                    $startTime = '11:00:00';
                    $endTime = '23:00:00';
                } elseif ($data['category_slug'] === 'health-medical') {
                    $endTime = '17:00:00';
                }

                BusinessWorkingHour::create([
                    'business_id' => $business->id,
                    'day_of_week' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);
            }

            // Create business settings
            $defaultSettings = [
                'booking_advance_hours' => 2,
                'booking_advance_days' => 30,
                'currency' => $data['currency'],
                'holiday_mode' => false,
                'maintenance_mode' => false,
                'booking_confirmation_required' => false,
            ];

            // Adjust settings based on business type
            if ($data['category_slug'] === 'health-medical') {
                $defaultSettings['booking_advance_hours'] = 24;
                $defaultSettings['booking_confirmation_required'] = true;
            } elseif ($data['category_slug'] === 'professional-services') {
                $defaultSettings['booking_advance_hours'] = 4;
                $defaultSettings['booking_confirmation_required'] = true;
            }

            foreach ($defaultSettings as $key => $value) {
                Setting::create([
                    'business_id' => $business->id,
                    'key' => $key,
                    'value' => $value,
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

            // Create employees and their working hours
            foreach ($data['employees'] as $empIndex => $employeeData) {
                $employeeUser = User::create([
                    'name' => $employeeData['name'],
                    'email' => $employeeData['email'],
                    'password' => bcrypt('password'),
                    'role' => 'employee',
                    'email_verified_at' => Carbon::now(),
                    'phone_number' => '555' . str_pad(($business->id * 100) + $empIndex, 7, '0', STR_PAD_LEFT),
                ]);

                $employee = Employee::create([
                    'business_id' => $business->id,
                    'user_id' => $employeeUser->id,
                    'name' => $employeeData['name'],
                    'email' => $employeeData['email'],
                    'bio' => $employeeData['bio'],
                    'active' => true,
                ]);

                // Create employee working hours (same as business hours)
                foreach ($workDays as $day) {
                    $businessHour = BusinessWorkingHour::where('business_id', $business->id)
                        ->where('day_of_week', $day)
                        ->first();
                    
                    if ($businessHour) {
                        EmployeeWorkingHour::create([
                            'employee_id' => $employee->id,
                            'day_of_week' => $day,
                            'start_time' => $businessHour->start_time,
                            'end_time' => $businessHour->end_time,
                        ]);
                    }
                }

                // Attach services to employee
                foreach ($services as $service) {
                    $service->employees()->attach($employee->id);
                }
            }
        }
    }
}
